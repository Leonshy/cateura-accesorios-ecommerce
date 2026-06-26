@extends('layouts.app')
@section('title', 'Mis pedidos — Cateura Accesorios')
@section('content')
<section class="py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('account.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="section-title">Mis pedidos</h1>
        </div>
        @forelse($orders as $order)
        <a href="{{ route('account.orders.show', $order->order_number) }}" class="block bg-white border border-stone-100 p-5 mb-3 hover:border-copper-300 hover:shadow-sm transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-mono font-medium text-stone-700 group-hover:text-copper-500 transition-colors">{{ $order->order_number }}</p>
                    <p class="text-xs text-stone-400 mt-0.5">{{ $order->created_at->format('d/m/Y') }} · {{ $order->items->count() }} producto(s)</p>
                </div>
                <div class="text-right">
                    <p class="font-medium text-stone-700">Gs. {{ number_format($order->total, 0, ',', '.') }}</p>
                    <span class="text-xs px-2 py-0.5 mt-1 inline-block {{ $order->status === 'entregado' ? 'bg-green-100 text-green-600' : ($order->status === 'cancelado' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-16">
            <p class="text-stone-400 mb-4">Aún no tenés pedidos.</p>
            <a href="{{ route('shop.index') }}" class="btn-copper">Explorar tienda</a>
        </div>
        @endforelse
        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</section>
@endsection
