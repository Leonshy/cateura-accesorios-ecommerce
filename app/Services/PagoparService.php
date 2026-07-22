<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;

class PagoparService
{
    private const API_BASE     = 'https://api.pagopar.com/api';
    private const CHECKOUT_URL = 'https://www.pagopar.com/pagos/';
    private const FORMA_PAGO   = 9; // Tarjetas / billeteras (Bancard vía Pagopar)

    private string $publicKey;
    private string $privateKey;

    public function __construct(PaymentMethod $method)
    {
        $this->publicKey  = $method->credentials['public_key'] ?? '';
        $this->privateKey = $method->credentials['private_key'] ?? '';
    }

    private function tokenOrden(string $idPedido, float $monto): string
    {
        return sha1($this->privateKey . $idPedido . strval(floatval($monto)));
    }

    private function tokenConsulta(): string
    {
        return sha1($this->privateKey . 'CONSULTA');
    }

    public function tokenWebhook(string $hashPedido): string
    {
        return sha1($this->privateKey . $hashPedido);
    }

    /**
     * Inicia una transacción en Pagopar y devuelve la URL de checkout externa.
     */
    public function createOrder(Order $order): array
    {
        if (! $this->publicKey || ! $this->privateKey) {
            throw new \RuntimeException('Pagopar no está configurado (faltan credenciales).');
        }

        $token = $this->tokenOrden($order->order_number, (float) $order->total);

        $items = $order->items->map(fn ($item) => [
            'id_producto'        => (string) $item->product_id,
            'nombre'             => $item->product_name,
            'descripcion'        => $item->product_name,
            'cantidad'           => (string) $item->quantity,
            'precio_total'       => (string) $item->subtotal,
            'categoria'          => '909',
            'ciudad'             => '1',
            'public_key'         => $this->publicKey,
            'url_imagen'         => $item->product_image ? media_url($item->product_image) : '',
            'vendedor_direccion' => '',
            'vendedor_telefono'  => '',
            'vendedor_email'     => '',
            'vendedor_nombre'    => '',
        ])->values()->all();

        // Ajustar el último ítem para que la suma cuadre con el total (incluye envío).
        $itemsTotal = array_sum(array_map(fn ($i) => (float) $i['precio_total'], $items));
        $diff       = (float) $order->total - $itemsTotal;
        if ($diff != 0.0 && count($items)) {
            $last = count($items) - 1;
            $items[$last]['precio_total'] = (string) ((float) $items[$last]['precio_total'] + $diff);
        }

        $documento = preg_replace('/\D/', '', (string) $order->billing_ruc) ?: '0';
        $telefono  = $this->sanitizePhone($order->customer_phone);

        $payload = [
            'token'               => $token,
            'public_key'          => $this->publicKey,
            'monto_total'         => (string) $order->total,
            'tipo_pedido'         => 'VENTA-COMERCIO',
            'id_pedido_comercio'  => $order->order_number,
            'descripcion_resumen' => 'Compra en Cateura Accesorios',
            'fecha_maxima_pago'   => now()->addHours(48)->format('d/m/Y H:i:s'),
            'forma_pago'          => self::FORMA_PAGO,
            'comprador'           => [
                'ruc'            => $documento,
                'email'          => $order->customer_email,
                'nombre'         => $order->customer_name,
                'apellido'       => '',
                'telefono'       => $telefono,
                'direccion'      => $order->address_line1 ?? '',
                'documento'      => $documento,
                'tipo_documento' => 'CI',
                'coordenadas'    => '',
            ],
            'compras_items' => $items,
        ];

        $response = Http::timeout(15)->post(self::API_BASE . '/comercios/2.0/iniciar-transaccion', $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Pagopar: error al iniciar la transacción.');
        }

        $data = $response->json() ?? [];

        if (! ($data['respuesta'] ?? false)) {
            $mensaje = $data['resultado'][0]['mensaje'] ?? 'transacción rechazada.';
            throw new \RuntimeException('Pagopar: ' . $mensaje);
        }

        $hash = $data['resultado'][0]['data'] ?? null;
        if (! $hash) {
            throw new \RuntimeException('Pagopar: respuesta inválida al iniciar la transacción.');
        }

        return [
            'hash'         => $hash,
            'redirect_url' => self::CHECKOUT_URL . $hash,
        ];
    }

    /**
     * Consulta el estado real de un pedido en Pagopar (requerido por su proceso de certificación de webhook).
     */
    public function queryOrder(string $hashPedido): array
    {
        $response = Http::timeout(15)->post(self::API_BASE . '/pedidos/1.1/traer', [
            'hash_pedido'   => $hashPedido,
            'token'         => $this->tokenConsulta(),
            'token_publico' => $this->publicKey,
        ]);

        $data      = $response->json() ?? [];
        $resultado = $data['resultado'][0] ?? [];

        return [
            'pagado'    => (bool) ($resultado['pagado'] ?? false),
            'cancelado' => (bool) ($resultado['cancelado'] ?? false),
            'raw'       => $data,
        ];
    }

    /**
     * Valida la firma del webhook con comparación resistente a timing attacks.
     */
    public function validateWebhook(array $payload): bool
    {
        $resultado     = $payload['resultado'][0] ?? [];
        $hashPedido    = $resultado['hash_pedido'] ?? null;
        $tokenRecibido = $resultado['token'] ?? null;

        if (! $hashPedido || ! $tokenRecibido) {
            return false;
        }

        return hash_equals($this->tokenWebhook($hashPedido), $tokenRecibido);
    }

    private function sanitizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone ?? '');
        if (! $digits) {
            return '+595981000000';
        }
        if (str_starts_with($digits, '0')) {
            $digits = '595' . substr($digits, 1);
        }
        if (! str_starts_with($digits, '595')) {
            $digits = '595' . $digits;
        }
        return '+' . $digits;
    }
}
