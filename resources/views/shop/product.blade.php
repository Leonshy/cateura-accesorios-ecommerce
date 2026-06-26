@extends('layouts.app')
@section('title', $product->name)
@push('meta')
<meta name="description" content="{{ $product->short_description ?? $product->meta_description }}">
<meta property="og:title" content="{{ $product->meta_title ?? $product->name }}">
<meta property="og:description" content="{{ $product->meta_description ?? $product->short_description }}">
@if($product->image)<meta property="og:image" content="{{ $product->main_image }}">@endif
@endpush
@section('content')

<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 mb-8 text-sm text-stone-400">
        <a href="{{ route('home') }}" class="hover:text-copper-500">Inicio</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-copper-500">Tienda</a>
        @if($product->category)
        <span>/</span>
        <a href="{{ route('shop.index', ['categoria' => $product->category->slug]) }}" class="hover:text-copper-500">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-stone-700">{{ $product->name }}</span>
    </nav>

    <div class="grid md:grid-cols-2 gap-12 mb-20" x-data="{ selectedImage: '{{ $product->main_image }}', selectedColor: '' }">
        {{-- Images --}}
        <div>
            <div class="aspect-[4/5] bg-stone-100 overflow-hidden mb-4">
                <img :src="selectedImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            @if($product->images->count() > 0)
            <div class="grid grid-cols-5 gap-2">
                <button @click="selectedImage = '{{ $product->main_image }}'"
                        class="aspect-square bg-stone-100 overflow-hidden border-2 transition-colors"
                        :class="selectedImage === '{{ $product->main_image }}' ? 'border-copper-500' : 'border-transparent hover:border-stone-300'">
                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </button>
                @foreach($product->images->take(4) as $img)
                <button @click="selectedImage = '{{ $img->url }}'"
                        class="aspect-square bg-stone-100 overflow-hidden border-2 transition-colors"
                        :class="selectedImage === '{{ $img->url }}' ? 'border-copper-500' : 'border-transparent hover:border-stone-300'">
                    <img src="{{ $img->url }}" alt="{{ $img->alt ?? $product->name }}" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Product info --}}
        <div>
            @if($product->category)
            <p class="section-subtitle mb-3">{{ $product->category->name }}{{ $product->subcategory ? ' / ' . $product->subcategory->name : '' }}</p>
            @endif

            <h1 class="font-display text-3xl md:text-4xl text-stone-800 font-medium mb-4">{{ $product->name }}</h1>

            {{-- Stars --}}
            @if($product->rating_count > 0)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= round($product->rating_avg) ? 'text-copper-500' : 'text-stone-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <span class="text-sm text-stone-400">({{ $product->rating_count }} reseñas)</span>
            </div>
            @endif

            {{-- Price --}}
            <div class="flex items-baseline gap-3 mb-4">
                <span class="text-3xl text-copper-500 font-medium">Gs. {{ number_format($product->price, 0, ',', '.') }}</span>
                @if($product->original_price)
                <span class="text-xl text-stone-400 line-through">Gs. {{ number_format($product->original_price, 0, ',', '.') }}</span>
                <span class="bg-copper-100 text-copper-700 text-xs px-2 py-1 font-medium">-{{ $product->discount_percent }}%</span>
                @endif
            </div>

            {{-- Stock --}}
            <p class="text-sm mb-6 {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ $product->stock > 0 ? "✓ En stock ({$product->stock} disponibles)" : '✕ Sin stock' }}
            </p>

            @if($product->short_description)
            <p class="text-stone-500 leading-relaxed mb-8 border-l-2 border-copper-200 pl-4">{{ $product->short_description }}</p>
            @endif

            @if($product->stock > 0)
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- Color selector --}}
                @if($product->colors->count() > 0)
                <div>
                    <label class="text-xs uppercase tracking-widest text-stone-500 font-medium block mb-3">
                        Color: <span x-text="selectedColor || 'Seleccionar'"></span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->colors as $color)
                        <button type="button"
                                @click="selectedColor = '{{ $color->name }}'"
                                :class="selectedColor === '{{ $color->name }}' ? 'border-copper-500 text-copper-600' : 'border-stone-300 text-stone-600'"
                                class="border px-4 py-2 text-sm transition-colors hover:border-copper-400">
                            {{ $color->name }}
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="color" :value="selectedColor">
                </div>
                @endif

                {{-- Quantity --}}
                <div>
                    <label class="text-xs uppercase tracking-widest text-stone-500 font-medium block mb-3">Cantidad</label>
                    <div class="flex items-center border border-stone-300 w-32">
                        <button type="button" onclick="const q=this.nextElementSibling; if(q.value>1) q.value--;" class="px-3 py-2 text-stone-500 hover:text-copper-500 text-lg">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="flex-1 text-center border-0 border-x border-stone-300 py-2 focus:ring-0 text-sm">
                        <button type="button" onclick="const q=this.previousElementSibling; if(q.value<{{ $product->stock }}) q.value++;" class="px-3 py-2 text-stone-500 hover:text-copper-500 text-lg">+</button>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-copper flex-1">Agregar al carrito</button>
                    @auth
                    <div class="flex-shrink-0">
                        <form action="{{ route('account.wishlist.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="border border-stone-300 hover:border-copper-500 hover:text-copper-500 p-3 transition-colors" title="Lista de deseos">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
                <a href="{{ route('checkout.index') }}" onclick="this.closest('form').submit(); return false;" class="btn-stone w-full text-center block">Comprar ahora</a>
            </form>
            @else
            <div class="bg-stone-50 border border-stone-200 p-4 text-center">
                <p class="text-stone-500 text-sm">Este producto no tiene stock disponible actualmente.</p>
            </div>
            @endif

            {{-- Share --}}
            <div class="mt-8 pt-6 border-t border-stone-100 flex items-center gap-3">
                <span class="text-xs text-stone-400 uppercase tracking-wider">Compartir:</span>
                <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . url()->current()) }}" target="_blank" class="text-stone-400 hover:text-green-500 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Description + Reviews --}}
    <div class="grid md:grid-cols-3 gap-10 mb-20" x-data="{ tab: 'description' }">
        <div class="md:col-span-3">
            <div class="flex border-b border-stone-200 mb-8">
                <button @click="tab = 'description'" :class="tab === 'description' ? 'border-copper-500 text-copper-500' : 'border-transparent text-stone-500'" class="border-b-2 px-6 py-3 text-sm font-medium uppercase tracking-wider transition-colors">Descripción</button>
                <button @click="tab = 'reviews'" :class="tab === 'reviews' ? 'border-copper-500 text-copper-500' : 'border-transparent text-stone-500'" class="border-b-2 px-6 py-3 text-sm font-medium uppercase tracking-wider transition-colors">Reseñas ({{ $product->reviews->count() }})</button>
            </div>
            <div x-show="tab === 'description'" class="prose prose-stone max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
            <div x-show="tab === 'reviews'" style="display:none;">
                @if($product->reviews->isEmpty())
                <p class="text-stone-400 text-center py-8">Aún no hay reseñas para este producto.</p>
                @else
                <div class="space-y-6">
                    @foreach($product->reviews as $review)
                    <div class="border-b border-stone-100 pb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-stone-700">{{ $review->reviewer_name }}</span>
                            <span class="text-xs text-stone-400">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex gap-0.5 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-copper-500' : 'text-stone-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        @if($review->comment)<p class="text-stone-600 text-sm">{{ $review->comment }}</p>@endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Related products --}}
    @if($related->count() > 0)
    <section>
        <h2 class="section-title mb-8">Productos relacionados</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($related as $product)
                @include('partials.product-card', compact('product'))
            @endforeach
        </div>
    </section>
    @endif
</div>

@endsection
