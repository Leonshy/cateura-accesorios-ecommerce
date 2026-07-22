@php
    if (auth()->check()) {
        $sidebarCart = \App\Models\Cart::where('user_id', auth()->id())->with('items.product')->first();
    } else {
        $sidebarCart = \App\Models\Cart::where('session_id', session()->getId())->with('items.product')->first();
    }
    $sidebarItems = $sidebarCart?->items ?? collect();
@endphp
<div class="cart-sidebar" :class="{ 'open': cartOpen }">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
            <h2 class="font-display text-xl text-stone-800">Mi carrito</h2>
            <button @click="cartOpen = false" class="p-1 text-stone-400 hover:text-stone-600 transition-colors" aria-label="Cerrar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-4">
            @if($sidebarItems->isEmpty())
            <p class="text-stone-400 text-sm text-center py-8">Tu carrito está vacío.</p>
            @else
            <div class="space-y-4">
                @foreach($sidebarItems as $item)
                <div class="flex gap-3">
                    <a href="{{ $item->product ? route('shop.product', $item->product->slug) : '#' }}" class="w-16 h-16 bg-stone-100 flex-shrink-0 overflow-hidden">
                        <img src="{{ $item->product?->main_image }}" alt="{{ $item->product?->name }}" class="w-full h-full object-cover">
                    </a>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-stone-700 truncate">{{ $item->product?->name }}</p>
                        @if($item->color)
                        <p class="text-xs text-stone-400">Color: {{ $item->color }}</p>
                        @endif
                        <p class="text-xs text-stone-500 mt-1">{{ $item->quantity }} × Gs. {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-sm font-medium text-stone-700 flex-shrink-0">{{ $item->formatted_subtotal }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @if($sidebarItems->isNotEmpty())
        <div class="px-5 py-4 border-t border-stone-100 space-y-3">
            <div class="flex justify-between items-center">
                <span class="font-medium text-stone-700">Subtotal</span>
                <span class="font-bold text-stone-800 text-lg">Gs. {{ number_format($sidebarCart->subtotal, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn-copper w-full text-center block">Finalizar compra</a>
            <a href="{{ route('cart.index') }}" class="btn-copper-outline w-full text-center block">Ver carrito</a>
        </div>
        @endif
    </div>
</div>
