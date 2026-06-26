@extends('layouts.app')
@section('title', 'Lista de deseos — Cateura Accesorios')
@section('content')
<section class="py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('account.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="section-title">Lista de deseos</h1>
        </div>
        @forelse($items as $item)
        <div class="flex items-center gap-4 bg-white border border-stone-100 p-4 mb-3">
            <a href="{{ route('shop.product', $item->product?->slug ?? '#') }}" class="w-16 h-16 bg-stone-100 flex-shrink-0 overflow-hidden">
                <img src="{{ $item->product?->main_image ?? '/placeholder.jpg' }}" alt="{{ $item->product?->name }}" class="w-full h-full object-cover">
            </a>
            <div class="flex-1">
                <a href="{{ route('shop.product', $item->product?->slug ?? '#') }}" class="font-medium text-stone-700 hover:text-copper-500 transition-colors">{{ $item->product?->name }}</a>
                <p class="text-copper-500 font-medium text-sm mt-0.5">{{ $item->product?->formatted_price }}</p>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-copper py-2 px-4 text-xs">Agregar al carrito</button>
                </form>
                <form action="{{ route('account.wishlist.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <button type="submit" class="text-stone-300 hover:text-red-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <p class="text-stone-400 mb-4">Tu lista de deseos está vacía.</p>
            <a href="{{ route('shop.index') }}" class="btn-copper">Explorar tienda</a>
        </div>
        @endforelse
    </div>
</section>
@endsection
