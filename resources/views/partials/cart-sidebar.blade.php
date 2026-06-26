<div class="cart-sidebar" :class="{ 'open': cartOpen }">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
            <h2 class="font-display text-xl text-stone-800">Mi carrito</h2>
            <button @click="cartOpen = false" class="p-1 text-stone-400 hover:text-stone-600 transition-colors" aria-label="Cerrar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-4" id="cart-sidebar-content">
            <p class="text-stone-400 text-sm text-center py-8">Cargando carrito...</p>
        </div>
        <div class="px-5 py-4 border-t border-stone-100 space-y-3" id="cart-sidebar-footer" style="display:none;">
            <div class="flex justify-between items-center">
                <span class="font-medium text-stone-700">Subtotal</span>
                <span class="font-bold text-stone-800 text-lg" id="cart-sidebar-total"></span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn-copper w-full text-center block">Finalizar compra</a>
            <a href="{{ route('cart.index') }}" class="btn-copper-outline w-full text-center block">Ver carrito</a>
        </div>
    </div>
</div>
