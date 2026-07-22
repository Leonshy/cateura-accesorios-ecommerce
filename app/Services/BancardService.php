<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;

class BancardService
{
    private string $publicKey;
    private string $privateKey;
    private bool $sandbox;

    public function __construct(PaymentMethod $method)
    {
        $this->publicKey  = $method->credentials['public_key'] ?? '';
        $this->privateKey = $method->credentials['private_key'] ?? '';
        $this->sandbox    = $method->sandbox;
    }

    private function baseUrl(): string
    {
        return $this->sandbox
            ? 'https://vpos.infonet.com.py:8888/vpos/api/0.3'
            : 'https://vpos.infonet.com.py/vpos/api/0.3';
    }

    private function generateToken(string ...$parts): string
    {
        return md5($this->privateKey . implode('', $parts));
    }

    /**
     * Inicia un pago single_buy y devuelve la URL de pago hospedada por Bancard.
     */
    public function createPayment(Order $order): array
    {
        if (! $this->publicKey || ! $this->privateKey) {
            throw new \RuntimeException('Bancard no está configurado (faltan credenciales).');
        }

        $shopProcessId = time();
        $amount        = number_format($order->total, 2, '.', '');
        $token         = $this->generateToken((string) $shopProcessId, $amount, 'PYG');

        $response = Http::timeout(15)->post($this->baseUrl() . '/single_buy', [
            'public_key' => $this->publicKey,
            'operation'  => [
                'token'           => $token,
                'shop_process_id' => $shopProcessId,
                'currency'        => 'PYG',
                'amount'          => $amount,
                'description'     => 'Pedido ' . $order->order_number,
                'return_url'      => route('checkout.confirmation', $order->order_number),
                'cancel_url'      => route('checkout.index'),
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Bancard: error al iniciar el pago.');
        }

        $data      = $response->json();
        $processId = $data['process_id'] ?? null;

        if (! $processId) {
            throw new \RuntimeException('Bancard: respuesta inválida al iniciar el pago.');
        }

        $iframeUrl = $this->sandbox
            ? "https://vpos.infonet.com.py:8888/payment/card?process_id={$processId}"
            : "https://vpos.infonet.com.py/payment/card?process_id={$processId}";

        return [
            'shop_process_id' => $shopProcessId,
            'process_id'      => $processId,
            'payment_url'     => $iframeUrl,
        ];
    }

    /**
     * Confirma el resultado de un pago (Bancard exige esta llamada tras recibir el webhook o el retorno).
     */
    public function confirmPayment(string $shopProcessId): array
    {
        $token = $this->generateToken($shopProcessId, 'get_confirmation');

        $response = Http::timeout(15)->post($this->baseUrl() . '/single_buy/confirmations', [
            'public_key' => $this->publicKey,
            'operation'  => [
                'token'           => $token,
                'shop_process_id' => $shopProcessId,
            ],
        ]);

        $data         = $response->json() ?? [];
        $responseCode = $data['confirmation']['response_code']
            ?? $data['operation']['response_code']
            ?? null;

        return [
            'approved' => $responseCode === '00',
            'raw'      => $data,
        ];
    }

    /**
     * Interpreta el payload recibido en el webhook de Bancard.
     */
    public function isWebhookApproved(array $payload): bool
    {
        $operation = $payload['operation'] ?? [];
        return ($operation['response'] ?? null) === 'S' && ($operation['response_code'] ?? null) === '00';
    }
}
