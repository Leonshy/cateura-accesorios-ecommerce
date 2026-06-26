<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
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
        $shippingMethods = ShippingMethod::active()->orderBy('order')->get();
        $user = auth()->user();

        return view('checkout.index', compact('cart', 'paymentMethods', 'shippingMethods', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_email'   => 'required|email|max:100',
            'customer_phone'   => 'nullable|string|max:30',
            'address_line1'    => 'nullable|string|max:200',
            'address_city'     => 'nullable|string|max:100',
            'payment_method'   => 'required|string',
            'shipping_method'  => 'required|string',
            'accept_terms'     => 'accepted',
        ]);

        $cart = $this->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = $cart->subtotal;
        $shipping = ShippingMethod::where('key', $request->shipping_method)->first();
        $shippingCost = $shipping ? $shipping->cost : 0;
        $total = $subtotal + $shippingCost;

        $order = Order::create([
            'order_number'     => Order::generateNumber(),
            'user_id'          => auth()->id(),
            'guest_token'      => auth()->check() ? null : Str::random(64),
            'customer_name'    => $request->customer_name,
            'customer_email'   => $request->customer_email,
            'customer_phone'   => $request->customer_phone,
            'address_line1'    => $request->address_line1,
            'address_city'     => $request->address_city,
            'address_department' => $request->address_department,
            'address_notes'    => $request->address_notes,
            'billing_ruc'      => $request->billing_ruc,
            'billing_name'     => $request->billing_name,
            'payment_method'   => $request->payment_method,
            'payment_status'   => $request->payment_method === 'transferencia' ? 'pendiente_confirmacion' : 'pendiente',
            'shipping_method'  => $request->shipping_method,
            'shipping_cost'    => $shippingCost,
            'subtotal'         => $subtotal,
            'total'            => $total,
            'status'           => 'pendiente',
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
        }

        $cart->items()->delete();
        $cart->delete();

        session(['last_order_id' => $order->id]);

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $canView = auth()->check() && $order->user_id === auth()->id()
            || session('last_order_id') === $order->id
            || ($order->guest_token && request('token') === $order->guest_token);

        abort_if(!$canView, 403);

        $transferData = $order->payment_method === 'transferencia'
            ? [
                'banco'   => \App\Models\SiteSetting::get('transfer_banco', 'Banco Itaú'),
                'cuenta'  => \App\Models\SiteSetting::get('transfer_cuenta', ''),
                'titular' => \App\Models\SiteSetting::get('transfer_titular', 'Asociación Mujeres Unidas del Bañado Sur'),
            ]
            : null;

        $order->load('items');
        return view('checkout.confirmation', compact('order', 'transferData'));
    }
}
