<?php
namespace App\Http\Controllers;

use App\Data\ParaguayLocations;
use App\Exceptions\InsufficientStockException;
use App\Mail\OrderConfirmed;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Services\BancardService;
use App\Services\PagoparService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private function getCart(): ?Cart
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->with('items.product')->first();
        }
        return Cart::where('session_id', session()->getId())->with('items.product')->first();
    }

    public function index()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $paymentMethods = PaymentMethod::active()->orderBy('order')->get();
        $shippingSettings = ShippingSetting::getDefault();
        $departments = collect(ParaguayLocations::departments())
            ->filter(fn ($d) => in_array($d['id'], $shippingSettings->getActiveDepartmentIds()))
            ->values();
        $user = auth()->user();

        return view('checkout.index', compact('cart', 'paymentMethods', 'shippingSettings', 'departments', 'user'));
    }

    public function calculateShipping(Request $request)
    {
        $request->validate([
            'department' => 'required|string',
            'city'       => 'required|string',
            'subtotal'   => 'required|numeric|min:0',
        ]);

        $settings = ShippingSetting::getDefault();
        $result = $settings->calculateShipping($request->department, $request->city, (float) $request->subtotal);

        return response()->json(['shipping' => $result]);
    }

    public function store(Request $request)
    {
        $settings = ShippingSetting::getDefault();

        $request->validate([
            'customer_name'       => 'required|string|max:100',
            'customer_email'      => 'required|email|max:100',
            'customer_phone'      => 'nullable|string|max:30',
            'shipping_type'       => 'required|in:pickup,envio',
            'address_line1'       => 'required_if:shipping_type,envio|nullable|string|max:200',
            'address_city'        => 'required_if:shipping_type,envio|nullable|string|max:100',
            'address_department'  => 'required_if:shipping_type,envio|nullable|string|max:100',
            'address_notes'       => 'nullable|string|max:500',
            'billing_ruc'         => 'nullable|string|max:30',
            'billing_name'        => 'nullable|string|max:150',
            'payment_method'      => 'required|string|exists:payment_methods,key',
            'transfer_receipt'    => 'required_if:payment_method,transferencia|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'accept_terms'        => 'accepted',
        ]);

        $cart = $this->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $paymentMethod = PaymentMethod::where('key', $request->payment_method)->where('is_active', true)->first();
        if (!$paymentMethod) {
            return back()->withInput()->with('error', 'El método de pago seleccionado no está disponible.');
        }

        $subtotal = $cart->subtotal;

        if ($request->shipping_type === 'pickup') {
            if (!$settings->store_pickup_enabled) {
                return back()->withInput()->with('error', 'El retiro en tienda no está disponible.');
            }
            $shippingMethodLabel = 'retiro_tienda';
            $shippingCost = 0;
        } else {
            if (!$settings->envio_propio_enabled) {
                return back()->withInput()->with('error', 'El envío a domicilio no está disponible.');
            }
            $result = $settings->calculateShipping($request->address_department, $request->address_city, $subtotal);
            if (!empty($result['unavailable'])) {
                return back()->withInput()->with('error', 'No realizamos envíos a esa ciudad por el momento.');
            }
            $shippingMethodLabel = 'envio_propio';
            $shippingCost = (int) $result['cost'];
        }

        $total = $subtotal + $shippingCost;

        $transferReceiptPath = null;
        if ($request->payment_method === 'transferencia' && $request->hasFile('transfer_receipt')) {
            $transferReceiptPath = $request->file('transfer_receipt')->store('receipts', 'public');
        }

        try {
            $order = DB::transaction(function () use ($request, $cart, $subtotal, $shippingMethodLabel, $shippingCost, $total, $transferReceiptPath) {
                // Bloquear cada producto y revalidar stock recién acá, dentro de la
                // transacción: si dos compras del último ítem llegan al mismo tiempo,
                // la segunda encuentra el stock ya descontado por la primera y falla
                // en vez de vender de más.
                foreach ($cart->items as $item) {
                    $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                    if (!$product || $product->stock < $item->quantity) {
                        throw new InsufficientStockException($item->product->name ?? 'producto');
                    }
                }

                $order = Order::create([
                    'order_number'        => Order::generateNumber(),
                    'user_id'             => auth()->id(),
                    'cart_id'             => $cart->id,
                    'guest_token'         => auth()->check() ? null : Str::random(64),
                    'customer_name'       => $request->customer_name,
                    'customer_email'      => $request->customer_email,
                    'customer_phone'      => $request->customer_phone,
                    'address_line1'       => $request->address_line1,
                    'address_city'        => $request->address_city,
                    'address_department'  => $request->address_department,
                    'address_notes'       => $request->address_notes,
                    'billing_ruc'         => $request->billing_ruc,
                    'billing_name'        => $request->billing_name,
                    'payment_method'      => $request->payment_method,
                    'payment_status'      => 'pendiente',
                    'transfer_receipt'    => $transferReceiptPath,
                    'shipping_method'     => $shippingMethodLabel,
                    'shipping_cost'       => $shippingCost,
                    'subtotal'            => $subtotal,
                    'total'               => $total,
                    'status'              => 'pendiente',
                ]);

                foreach ($cart->items as $item) {
                    OrderItem::create([
                        'order_id'      => $order->id,
                        'product_id'    => $item->product_id,
                        'product_name'  => $item->product->name,
                        'product_image' => $item->product->image,
                        'color'         => $item->color,
                        'quantity'      => $item->quantity,
                        'unit_price'    => $item->unit_price,
                        'subtotal'      => $item->subtotal,
                    ]);
                    Product::whereKey($item->product_id)->decrement('stock', $item->quantity);
                }

                return $order;
            });
        } catch (InsufficientStockException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage() . ' Ajustá tu carrito e intentá de nuevo.');
        }

        $order->load('items');

        return match ($request->payment_method) {
            'bancard' => $this->startBancardPayment($order, $paymentMethod),
            'pagopar' => $this->startPagoparPayment($order, $paymentMethod),
            default   => $this->finalizeManualOrder($order, $cart),
        };
    }

    private function finalizeManualOrder(Order $order, Cart $cart)
    {
        $order->update(['payment_status' => 'pendiente_confirmacion']);
        $this->clearCart($cart);
        session(['last_order_id' => $order->id]);
        $this->sendOrderConfirmedEmail($order);
        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    private function startBancardPayment(Order $order, PaymentMethod $method)
    {
        try {
            $result = (new BancardService($method))->createPayment($order);
            $order->update(['bancard_process_id' => $result['shop_process_id']]);
            // El carrito se vacía recién cuando Bancard confirme el pago
            // (bancardWebhook). Si el cliente abandona o el pago es
            // rechazado, el carrito sigue intacto para reintentar.
            session(['last_order_id' => $order->id]);
            $this->sendOrderConfirmedEmail($order);
            return redirect()->away($result['payment_url']);
        } catch (\Throwable $e) {
            $previousStatus = $order->status;
            $order->update(['status' => 'cancelado', 'payment_status' => 'rechazado']);
            StockService::restoreIfNewlyCancelled($order, $previousStatus, 'cancelado');
            return redirect()->route('checkout.index')->withInput()
                ->with('error', 'No se pudo iniciar el pago con Bancard: ' . $e->getMessage());
        }
    }

    private function startPagoparPayment(Order $order, PaymentMethod $method)
    {
        try {
            $result = (new PagoparService($method))->createOrder($order);
            $order->update(['pagopar_hash' => $result['hash']]);
            // El carrito se vacía recién cuando Pagopar confirme el pago
            // (pagoparWebhook / pagoparReturn). Si el cliente abandona o el
            // pago es rechazado, el carrito sigue intacto para reintentar.
            session(['last_order_id' => $order->id]);
            $this->sendOrderConfirmedEmail($order);
            return redirect()->away($result['redirect_url']);
        } catch (\Throwable $e) {
            $previousStatus = $order->status;
            $order->update(['status' => 'cancelado', 'payment_status' => 'rechazado']);
            StockService::restoreIfNewlyCancelled($order, $previousStatus, 'cancelado');
            return redirect()->route('checkout.index')->withInput()
                ->with('error', 'No se pudo iniciar el pago con Pagopar: ' . $e->getMessage());
        }
    }

    private function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->delete();
    }

    /**
     * Vacía el carrito de un pedido recién cuando el pago ya fue confirmado
     * como aprobado por la pasarela (webhook o retorno), nunca antes.
     */
    private function clearCartOnceConfirmed(Order $order): void
    {
        if ($order->cart) {
            $this->clearCart($order->cart);
        }
    }

    /**
     * Un fallo de SMTP no debe tirar abajo un checkout que ya se completó
     * correctamente: se registra el error y se sigue.
     */
    private function sendOrderConfirmedEmail(Order $order): void
    {
        try {
            Mail::to($order->customer_email)->send(new OrderConfirmed($order));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de confirmación de pedido.', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $canView = (auth()->check() && $order->user_id === auth()->id())
            || session('last_order_id') === $order->id
            || ($order->guest_token && request('token') === $order->guest_token);

        abort_if(!$canView, 403);

        $order->load('items');
        return view('checkout.confirmation', compact('order'));
    }

    /**
     * Bancard invoca esta URL desde su plataforma para confirmar el resultado del pago.
     */
    public function bancardWebhook(Request $request)
    {
        $payload = $request->all();
        $shopProcessId = $payload['operation']['shop_process_id'] ?? null;

        if (!$shopProcessId) {
            return response()->json(['status' => 'error', 'message' => 'shop_process_id faltante'], 400);
        }

        $order = Order::where('bancard_process_id', $shopProcessId)->first();
        if (!$order) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $method = PaymentMethod::getProvider('bancard');
        // El payload que llega acá es del atacante en potencia: solo se usa para
        // ubicar el pedido. El estado de aprobación se valida siempre contra el
        // propio servidor de Bancard (firmado con la private_key), nunca contra
        // lo que diga este body.
        $approved = $method && (new BancardService($method))->confirmPayment((string) $shopProcessId)['approved'];

        $previousStatus = $order->status;
        $newStatus = $approved ? 'confirmado' : 'cancelado';
        $order->update([
            'payment_status' => $approved ? 'pagado' : 'rechazado',
            'status'         => $newStatus,
        ]);
        StockService::restoreIfNewlyCancelled($order, $previousStatus, $newStatus);
        if ($approved) {
            $this->clearCartOnceConfirmed($order);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Pagopar invoca esta URL para notificar el resultado del pago.
     * Debe responder exactamente con el "resultado" recibido (requisito de certificación de Pagopar).
     */
    public function pagoparWebhook(Request $request)
    {
        $payload = $request->all();
        $method = PaymentMethod::getProvider('pagopar');

        if (!$method) {
            return response()->json($payload['resultado'] ?? [], 200);
        }

        $service = new PagoparService($method);

        if (!$service->validateWebhook($payload)) {
            return response()->json(['error' => 'token inválido'], 403);
        }

        $hashPedido = $payload['resultado'][0]['hash_pedido'] ?? null;
        $order = $hashPedido ? Order::where('pagopar_hash', $hashPedido)->first() : null;

        if ($order) {
            $check = $service->queryOrder($hashPedido);
            $previousStatus = $order->status;
            $newStatus = $check['pagado'] ? 'confirmado' : ($check['cancelado'] ? 'cancelado' : 'pendiente');
            $order->update([
                'payment_status' => $check['pagado'] ? 'pagado' : ($check['cancelado'] ? 'rechazado' : 'pendiente'),
                'status'         => $newStatus,
            ]);
            StockService::restoreIfNewlyCancelled($order, $previousStatus, $newStatus);
            if ($check['pagado']) {
                $this->clearCartOnceConfirmed($order);
            }
        }

        return response()->json($payload['resultado'] ?? [], 200);
    }

    /**
     * El comprador vuelve aquí desde Pagopar tras pagar (o cancelar).
     */
    public function pagoparReturn(string $hash)
    {
        $order = Order::where('pagopar_hash', $hash)->firstOrFail();

        if ($order->payment_status === 'pendiente') {
            $method = PaymentMethod::getProvider('pagopar');
            if ($method) {
                $check = (new PagoparService($method))->queryOrder($hash);
                $previousStatus = $order->status;
                $newStatus = $check['pagado'] ? 'confirmado' : ($check['cancelado'] ? 'cancelado' : 'pendiente');
                $order->update([
                    'payment_status' => $check['pagado'] ? 'pagado' : ($check['cancelado'] ? 'rechazado' : 'pendiente'),
                    'status'         => $newStatus,
                ]);
                StockService::restoreIfNewlyCancelled($order, $previousStatus, $newStatus);
                if ($check['pagado']) {
                    $this->clearCartOnceConfirmed($order);
                }
            }
        }

        session(['last_order_id' => $order->id]);
        return redirect()->route('checkout.confirmation', $order->order_number);
    }
}
