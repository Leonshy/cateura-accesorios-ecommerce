@extends('layouts.app')
@section('title', 'Pedido ' . $order->order_number . ' — Cateura Accesorios')
@section('content')
<section class="py-12">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('account.orders') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="section-title">{{ $order->order_number }}</h1>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white border border-stone-100 p-4 text-sm">
                <p class="text-xs uppercase tracking-widest text-stone-400 mb-2">Estado del pedido</p>
                <span class="px-3 py-1 {{ $order->status === 'entregado' ? 'bg-green-100 text-green-700' : ($order->status === 'cancelado' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ $order->status_label }}</span>
            </div>
            <div class="bg-white border border-stone-100 p-4 text-sm">
                <p class="text-xs uppercase tracking-widest text-stone-400 mb-2">Estado del pago</p>
                <span class="px-3 py-1 {{ $order->payment_status === 'pagado' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
            </div>
        </div>

        <div class="bg-white border border-stone-100 mb-6 overflow-hidden">
            <div class="px-5 py-3 bg-stone-50 border-b border-stone-100">
                <p class="text-xs uppercase tracking-widest text-stone-500">Productos</p>
            </div>
            @foreach($order->items as $item)
            <div class="flex items-center gap-4 p-4 border-b border-stone-50 last:border-0">
                <div class="w-14 h-14 bg-stone-100 flex-shrink-0">
                    <img src="{{ $item->product?->main_image ?? '/placeholder.jpg' }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <p class="font-medium text-stone-700">{{ $item->product_name }}</p>
                    <p class="text-xs text-stone-400">×{{ $item->quantity }}</p>
                </div>
                <p class="font-medium text-stone-700">Gs. {{ number_format($item->subtotal, 0, ',', '.') }}</p>
            </div>
            @endforeach
            <div class="px-5 py-4 bg-stone-50 flex justify-between font-bold text-stone-800">
                <span>Total</span>
                <span class="text-copper-600">Gs. {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-white border border-stone-100 p-5 text-sm">
            <p class="text-xs uppercase tracking-widest text-stone-400 mb-3">Dirección de envío</p>
            <p class="text-stone-700">{{ $order->customer_name }}</p>
            <p class="text-stone-500">{{ $order->shipping_address }}</p>
            <p class="text-stone-500">{{ $order->shipping_city }}, {{ $order->shipping_department }}</p>
        </div>
    </div>
</section>
@endsection
