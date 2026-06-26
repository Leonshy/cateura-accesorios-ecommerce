<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->latest();
        if ($request->filled('estado')) $query->where('status', $request->estado);
        if ($request->filled('pago')) $query->where('payment_status', $request->pago);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($s) => $s->where('order_number', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%")
                ->orWhere('customer_email', 'like', "%{$q}%"));
        }
        $orders = $query->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status'         => 'required|in:pendiente,confirmado,preparando,enviado,entregado,cancelado',
            'payment_status' => 'required|in:pendiente,pagado,rechazado,reembolsado,pendiente_confirmacion',
            'internal_notes' => 'nullable|string|max:2000',
        ]);
        $order->update($request->only('status', 'payment_status', 'internal_notes'));
        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
