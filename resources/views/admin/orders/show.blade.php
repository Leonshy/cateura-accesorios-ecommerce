@extends('layouts.admin')
@section('title', 'Pedido ' . $order->order_number)
@section('content')
<div class="max-w-5xl space-y-6">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs text-stone-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            <h2 class="font-mono font-bold text-2xl text-stone-700">{{ $order->order_number }}</h2>
        </div>
        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center gap-3">
            @csrf @method('PATCH')
            <select name="status" class="border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:border-copper-500">
                @foreach(['pendiente','confirmado','preparando','enviado','entregado','cancelado'] as $s)
                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:border-copper-500">
                @foreach(['pendiente','pagado','rechazado','pendiente_confirmacion'] as $p)
                <option value="{{ $p }}" {{ $order->payment_status === $p ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $p)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-copper py-2 px-4 text-xs">Actualizar</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-stone-100 shadow-sm p-5">
            <h3 class="text-xs uppercase tracking-widest text-stone-400 mb-3">Cliente</h3>
            <p class="font-medium text-stone-700">{{ $order->customer_name }}</p>
            <p class="text-sm text-stone-500">{{ $order->customer_email }}</p>
            <p class="text-sm text-stone-500">{{ $order->customer_phone }}</p>
        </div>
        <div class="bg-white border border-stone-100 shadow-sm p-5">
            <h3 class="text-xs uppercase tracking-widest text-stone-400 mb-3">Dirección de envío</h3>
            <p class="text-sm text-stone-600">{{ $order->shipping_address }}</p>
            <p class="text-sm text-stone-600">{{ $order->shipping_city }}, {{ $order->shipping_department }}</p>
            @if($order->shipping_notes)
            <p class="text-xs text-stone-400 mt-1 italic">{{ $order->shipping_notes }}</p>
            @endif
        </div>
        <div class="bg-white border border-stone-100 shadow-sm p-5">
            <h3 class="text-xs uppercase tracking-widest text-stone-400 mb-3">Pago</h3>
            <p class="text-sm text-stone-600 capitalize">{{ $order->payment_method }}</p>
            <p class="text-xs text-stone-400">Estado: <span class="{{ $order->payment_status === 'pagado' ? 'text-green-600' : 'text-orange-500' }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></p>
            @if($order->payment_reference)
            <p class="text-xs text-stone-400 mt-1">Ref: {{ $order->payment_reference }}</p>
            @endif
        </div>
    </div>

    <div class="bg-white border border-stone-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-stone-100 bg-stone-50">
            <h3 class="text-xs uppercase tracking-widest text-stone-500">Productos</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="border-b border-stone-100">
                <tr class="text-xs uppercase tracking-wider text-stone-400">
                    <th class="px-5 py-3 text-left">Producto</th>
                    <th class="px-5 py-3 text-center">Cant.</th>
                    <th class="px-5 py-3 text-right">Precio unit.</th>
                    <th class="px-5 py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <p class="font-medium text-stone-700">{{ $item->product_name }}</p>
                        @if($item->product_color)
                        <p class="text-xs text-stone-400">Color: {{ $item->product_color }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">{{ $item->quantity }}</td>
                    <td class="px-5 py-3 text-right">Gs. {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right font-medium">Gs. {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-stone-200 bg-stone-50">
                <tr>
                    <td colspan="3" class="px-5 py-3 text-right font-medium">Subtotal:</td>
                    <td class="px-5 py-3 text-right">Gs. {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($order->shipping_cost)
                <tr>
                    <td colspan="3" class="px-5 py-2 text-right text-stone-500 text-sm">Envío:</td>
                    <td class="px-5 py-2 text-right text-sm">Gs. {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="text-copper-600 font-bold">
                    <td colspan="3" class="px-5 py-3 text-right">TOTAL:</td>
                    <td class="px-5 py-3 text-right text-lg">Gs. {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($order->notes)
    <div class="bg-white border border-stone-100 shadow-sm p-5">
        <h3 class="text-xs uppercase tracking-widest text-stone-400 mb-2">Notas del cliente</h3>
        <p class="text-sm text-stone-600">{{ $order->notes }}</p>
    </div>
    @endif

    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-sm text-stone-500 hover:text-stone-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver a pedidos
    </a>
</div>
@endsection
