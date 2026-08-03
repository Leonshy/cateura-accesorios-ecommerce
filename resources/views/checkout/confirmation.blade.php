@extends('layouts.app')
@section('title', 'Pedido confirmado — Cateura Accesorios')
@section('content')
<section class="py-20">
    <div class="container mx-auto px-4 max-w-2xl text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="section-subtitle text-copper-500 mb-2">¡Gracias por tu compra!</p>
        <h1 class="section-title mb-4">Pedido confirmado</h1>
        <p class="text-stone-500 mb-2">Tu número de pedido es:</p>
        <p class="font-mono text-2xl font-bold text-stone-700 mb-6">{{ $order->order_number }}</p>
        <p class="text-stone-500 mb-8">Hemos enviado los detalles a <strong>{{ $order->customer_email }}</strong>. Nos contactaremos en breve para coordinar el envío.</p>

        @if($order->payment_method === 'transferencia')
        @php
            $transferMethod = \App\Models\PaymentMethod::where('key', 'transferencia')->first();
        @endphp
        <div class="bg-amber-50 border border-amber-200 p-5 text-left mb-8 text-sm">
            <p class="font-medium text-amber-800 mb-3">📋 Datos de la transferencia</p>
            <p class="text-amber-700 mb-2">Ya recibimos tu comprobante. Estos son los datos de la cuenta a la que transferiste, por si necesitás revisarlos:</p>
            <div class="space-y-1 text-amber-800">
                <p><strong>Banco:</strong> {{ $transferMethod->credentials['bank_name'] ?? '—' }}</p>
                <p><strong>Cuenta:</strong> {{ $transferMethod->credentials['bank_account'] ?? '—' }}</p>
                <p><strong>Titular:</strong> {{ $transferMethod->credentials['bank_titular'] ?? '—' }}</p>
                <p><strong>CI titular:</strong> {{ $transferMethod->credentials['bank_ci'] ?? '—' }}</p>
                <p><strong>Importe:</strong> Gs. {{ number_format($order->total, 0, ',', '.') }}</p>
                <p><strong>Referencia:</strong> {{ $order->order_number }}</p>
            </div>
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('shop.index') }}" class="btn-copper">Seguir comprando</a>
            @auth
            <a href="{{ route('account.orders') }}" class="btn-stone">Ver mis pedidos</a>
            @endauth
        </div>
    </div>
</section>
@endsection
