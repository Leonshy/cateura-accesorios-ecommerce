<div class="product-card group cursor-pointer transition-all duration-300 hover:shadow-lg hover:bg-stone-50">
    {{-- Badge --}}
    @if($product->is_new)
    <span class="badge-new">Nuevo</span>
    @elseif($product->is_featured && !$product->is_new)
    <span class="badge-featured">Destacado</span>
    @endif

    {{-- Image --}}
    <a href="{{ route('shop.product', $product->slug) }}" class="block overflow-hidden bg-stone-100 aspect-[3/4] relative">
        <img src="{{ $product->main_image }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
        {{-- Hover actions --}}
        <div class="absolute bottom-0 left-0 right-0 bg-white/95 py-3 px-3 flex gap-2 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
            <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full bg-stone-900 text-white text-xs uppercase tracking-wider py-2 hover:bg-copper-500 transition-colors">
                    Agregar
                </button>
            </form>
            @auth
            <form action="{{ route('account.wishlist.toggle') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="p-2 border border-stone-200 text-stone-500 hover:border-copper-500 hover:text-copper-500 transition-colors" title="Lista de deseos">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
            </form>
            @endauth
        </div>
    </a>

    {{-- Info --}}
    <div class="pt-4 pb-2">
        <h3 class="text-base text-stone-900 font-semibold leading-tight mb-1">
            <a href="{{ route('shop.product', $product->slug) }}" class="hover:text-copper-500 transition-colors">{{ $product->name }}</a>
        </h3>
        @if($product->category)
        <p class="text-xs text-stone-400 tracking-wide mb-2">{{ $product->category->name }}</p>
        @endif
        {{-- Stars --}}
        @if($product->rating_count > 0)
        <div class="flex items-center gap-1 mb-2">
            @for($i = 1; $i <= 5; $i++)
            <svg class="w-3 h-3 {{ $i <= round($product->rating_avg) ? 'text-copper-500' : 'text-stone-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
            <span class="text-xs text-stone-400">({{ $product->rating_count }})</span>
        </div>
        @endif
        <div class="flex items-center gap-2">
            <span class="text-copper-500 font-medium">Gs. {{ number_format($product->price, 0, ',', '.') }}</span>
            @if($product->original_price)
            <span class="text-stone-400 text-xs line-through">Gs. {{ number_format($product->original_price, 0, ',', '.') }}</span>
            @endif
        </div>
    </div>
</div>
