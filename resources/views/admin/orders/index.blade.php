@extends('layouts.admin')
@section('title', 'Pedidos')
@section('content')
<div class="flex gap-4 mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex gap-2 flex-wrap">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Número, nombre, email..." class="border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:border-copper-500">
        <select name="estado" class="border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:border-copper-500">
            <option value="">Todos los estados</option>
            @foreach(['pendiente','confirmado','preparando','enviado','entregado','cancelado'] as $s)
            <option value="{{ $s }}" {{ request('estado') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="pago" class="border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:border-copper-500">
            <option value="">Estado de pago</option>
            @foreach(['pendiente','pagado','rechazado','pendiente_confirmacion'] as $p)
            <option value="{{ $p }}" {{ request('pago') === $p ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $p)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-stone-700 text-white px-4 py-2 text-sm hover:bg-stone-800 transition-colors">Filtrar</button>
    </form>
</div>

<div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Pedido</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Cliente</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Items</th>
                <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-stone-500">Total</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Estado</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Pago</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($orders as $order)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-4 py-3">
                    <p class="font-mono font-medium text-stone-700">{{ $order->order_number }}</p>
                    <p class="text-xs text-stone-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </td>
                <td class="px-4 py-3">
                    <p class="text-stone-700">{{ $order->customer_name }}</p>
                    <p class="text-xs text-stone-400">{{ $order->customer_email }}</p>
                </td>
                <td class="px-4 py-3 text-center text-stone-500">{{ $order->items->count() }}</td>
                <td class="px-4 py-3 text-right font-medium text-stone-700">Gs. {{ number_format($order->total, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1
                        {{ $order->status === 'entregado' ? 'bg-green-100 text-green-700' :
                           ($order->status === 'cancelado' ? 'bg-red-100 text-red-700' :
                           ($order->status === 'pendiente' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-blue-100 text-blue-700')) }}">
                        {{ $order->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1 {{ $order->payment_status === 'pagado' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-copper-500 hover:text-copper-600 text-xs font-medium">Ver detalles</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-stone-400">No hay pedidos.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-4 border-t border-stone-100">
        {{ $orders->links() }}
    </div>
</div>
@endsection
