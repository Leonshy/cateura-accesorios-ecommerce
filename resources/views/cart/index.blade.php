@extends('layouts.app')
@section('title', 'Carrito — Cateura Accesorios')
@section('content')
<section class="py-12">
    <div class="container mx-auto px-4">
        <h1 class="section-title mb-8">Mi carrito</h1>
        @if($cart && $cart->items->count())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                <div class="bg-white border border-stone-100 p-4 flex gap-4">
                    <a href="{{ route('shop.product', $item->product?->slug ?? '#') }}" class="w-20 h-20 bg-stone-100 flex-shrink-0 overflow-hidden">
                        <img src="{{ $item->product?->main_image ?? '/placeholder.jpg' }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                    </a>
                    <div class="flex-1">
                        <p class="font-medium text-stone-700">{{ $item->product_name }}</p>
                        @if($item->product_color)
                        <p class="text-xs text-stone-400">Color: {{ $item->product_color }}</p>
                        @endif
                        <p class="text-copper-500 font-medium mt-1">Gs. {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex flex-col items-end justify-between">
                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-stone-300 hover:text-red-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="w-7 h-7 border border-stone-200 flex items-center justify-center hover:border-copper-300 transition-colors text-stone-500">−</button>
                            <span class="w-8 text-center text-sm font-medium">{{ $item->quantity }}</span>
                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="w-7 h-7 border border-stone-200 flex items-center justify-center hover:border-copper-300 transition-colors text-stone-500">+</button>
                        </form>
                        <p class="font-medium text-stone-700 text-sm">Gs. {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
                <div class="flex justify-between items-center pt-2">
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('¿Vaciar el carrito?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-stone-400 hover:text-red-500 transition-colors">Vaciar carrito</button>
                    </form>
                    <a href="{{ route('shop.index') }}" class="text-xs text-stone-500 hover:text-copper-500 transition-colors">← Seguir comprando</a>
                </div>
            </div>
            <div>
                <div class="bg-white border border-stone-100 p-6 sticky top-4">
                    <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3 mb-4">Resumen del pedido</h2>
                    <div class="space-y-2 text-sm mb-4">
                        <div class="flex justify-between text-stone-600">
                            <span>Subtotal ({{ $cart->items->sum('quantity') }} artículos)</span>
                            <span>Gs. {{ number_format($cart->items->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-stone-400 text-xs">
                            <span>Envío</span>
                            <span>Se calcula al finalizar</span>
                        </div>
                    </div>
                    <div class="flex justify-between font-bold text-stone-800 border-t border-stone-100 pt-3 mb-5">
                        <span>Total</span>
                        <span class="text-copper-600">Gs. {{ number_format($cart->items->sum('subtotal'), 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn-copper w-full">Finalizar compra</a>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 text-stone-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <h2 class="font-display text-2xl text-stone-400 mb-4">Tu carrito está vacío</h2>
            <a href="{{ route('shop.index') }}" class="btn-copper">Ver productos</a>
        </div>
        @endif
    </div>
</section>
@endsection
