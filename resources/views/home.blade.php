@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

{{-- Hero Carousel --}}
<section class="relative bg-stone-100 overflow-hidden">
    @if($banners->count() > 0)
    <div x-data="{ current: 0, total: {{ $banners->count() }}, interval: null }"
         x-init="interval = setInterval(() => current = (current + 1) % total, 5000)"
         class="relative" style="min-height: 480px;">

        @foreach($banners as $i => $banner)
        <div x-show="current === {{ $i }}"
             x-transition:enter="transition-opacity duration-700"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="absolute inset-0"
             style="display:{{ $i === 0 ? 'block' : 'none' }}">
            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                 class="w-full h-full object-cover" style="min-height:480px;">
            <div class="absolute inset-0 flex items-center" style="background: linear-gradient(to right, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.50) 60%, rgba(0,0,0,0.30) 100%)">
                <div class="max-w-7xl mx-auto px-8 py-20">
                    <p class="section-subtitle text-copper-300 mb-3">Colección artesanal</p>
                    <h1 class="font-display text-4xl md:text-6xl text-white font-medium leading-tight mb-4 max-w-2xl">{{ $banner->title }}</h1>
                    @if($banner->subtitle)
                    <p class="text-stone-200 text-lg mb-8 max-w-lg">{{ $banner->subtitle }}</p>
                    @endif
                    @if($banner->cta_url)
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ $banner->cta_url }}" class="btn-copper">{{ $banner->cta_label ?? 'Comprar ahora' }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        {{-- Indicators --}}
        @if($banners->count() > 1)
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
            @foreach($banners as $i => $banner)
            <button @click="current = {{ $i }}" class="w-2 h-2 rounded-full transition-all" :class="current === {{ $i }} ? 'bg-white w-6' : 'bg-white/50'"></button>
            @endforeach
        </div>
        @endif
    </div>
    @else
    {{-- Default hero --}}
    <div class="relative bg-stone-900" style="min-height:480px;">
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center px-4">
                <p class="section-subtitle text-copper-400 mb-4">Diseño artesanal paraguayo</p>
                <h1 class="font-display text-5xl md:text-7xl text-white font-medium leading-tight mb-6">
                    Diseño artesanal<br><span class="text-copper-400">que transforma historias</span>
                </h1>
                <p class="text-stone-300 text-lg mb-10 max-w-xl mx-auto">Accesorios, piezas decorativas y prendas creadas por artesanas del Bañado Sur a partir de materiales reciclados.</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('shop.index') }}" class="btn-copper">Comprar ahora</a>
                    <a href="{{ route('artisans.index') }}" class="inline-flex items-center gap-2 text-white border border-white/40 px-6 py-3 text-xs uppercase tracking-widest hover:bg-white/10 transition-colors">Conocer a las artesanas</a>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>

{{-- Categories --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <p class="section-subtitle mb-3">Explora nuestra colección</p>
            <h2 class="section-title">Tiendas por categoría</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($categories->take(3) as $category)
            <a href="{{ route('shop.index', ['categoria' => $category->slug]) }}"
               class="group relative overflow-hidden bg-stone-100 aspect-[4/3] flex items-end">
                @if($category->image)
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                @else
                <div class="absolute inset-0 bg-gradient-to-br from-stone-700 to-stone-900 group-hover:from-copper-700 group-hover:to-stone-900 transition-all duration-500"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent"></div>
                <div class="relative p-6 w-full">
                    <h3 class="font-display text-2xl text-white font-medium">{{ $category->name }}</h3>
                    <p class="text-white/70 text-xs uppercase tracking-widest mt-1 group-hover:text-copper-300 transition-colors">Ver colección →</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Products --}}
@if($featuredProducts->count() > 0)
<section class="py-20 bg-stone-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-end mb-12">
            <div>
                <p class="section-subtitle mb-3">Selección especial</p>
                <h2 class="section-title">Productos destacados</h2>
            </div>
            <a href="{{ route('shop.index', ['destacado' => '1']) }}" class="btn-copper-outline hidden md:inline-flex">Ver todos</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($featuredProducts as $product)
                @include('partials.product-card', compact('product'))
            @endforeach
        </div>
        <div class="text-center mt-10 md:hidden">
            <a href="{{ route('shop.index', ['destacado' => '1']) }}" class="btn-copper-outline">Ver todos los destacados</a>
        </div>
    </div>
</section>
@endif

{{-- New Products --}}
@if($newProducts->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-end mb-12">
            <div>
                <p class="section-subtitle mb-3">Recién llegados</p>
                <h2 class="section-title">Nuevos productos</h2>
            </div>
            <a href="{{ route('shop.index', ['nuevo' => '1']) }}" class="btn-copper-outline hidden md:inline-flex">Ver todos</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($newProducts as $product)
                @include('partials.product-card', compact('product'))
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Institutional block --}}
<section class="py-24 bg-stone-900 text-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            @php
                $historia = \App\Models\SiteSetting::group('content');
                $historiaImgs = [
                    !empty($historia['historia_img1']) ? media_url($historia['historia_img1']) : asset('assets/institucional/artesana-boceto.jpg'),
                    !empty($historia['historia_img2']) ? media_url($historia['historia_img2']) : asset('assets/institucional/taller-mujeres.jpg'),
                    !empty($historia['historia_img3']) ? media_url($historia['historia_img3']) : asset('assets/institucional/taller-ceramica.jpg'),
                    !empty($historia['historia_img4']) ? media_url($historia['historia_img4']) : asset('assets/institucional/artesanas-tejiendo.jpg'),
                ];
            @endphp
            <div>
                <p class="section-subtitle text-copper-400 mb-4">{{ $historia['historia_eyebrow'] ?? 'Nuestra historia' }}</p>
                <h2 class="font-display text-4xl md:text-5xl font-medium leading-tight mb-6">
                    {{ $historia['historia_title_line1'] ?? 'Manos que transforman' }}<br><span class="text-copper-400">{{ $historia['historia_title_line2'] ?? 'el mundo' }}</span>
                </h2>
                <p class="text-stone-300 leading-relaxed mb-6">
                    {{ $historia['historia_text'] ?? 'Cateura Accesorios es la marca de los productos elaborados por artesanas de la Asociación Mujeres Unidas del Bañado Sur. A partir de materiales reciclados, transforman con sus manos objetos descartados en artículos de gran belleza, transmitiendo esperanza, dignidad y compromiso con la comunidad.' }}
                </p>
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="text-center">
                        <div class="font-display text-3xl text-copper-400 font-medium">{{ $historia['historia_stat1_value'] ?? '+50' }}</div>
                        <div class="text-xs text-stone-400 uppercase tracking-wider mt-1">{{ $historia['historia_stat1_label'] ?? 'Artesanas' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="font-display text-3xl text-copper-400 font-medium">{{ $historia['historia_stat2_value'] ?? '100%' }}</div>
                        <div class="text-xs text-stone-400 uppercase tracking-wider mt-1">{{ $historia['historia_stat2_label'] ?? 'Reciclado' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="font-display text-3xl text-copper-400 font-medium">{{ $historia['historia_stat3_value'] ?? 'PY' }}</div>
                        <div class="text-xs text-stone-400 uppercase tracking-wider mt-1">{{ $historia['historia_stat3_label'] ?? 'Hecho en PY' }}</div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('about') }}" class="btn-copper">Conocer más</a>
                    <a href="{{ route('artisans.index') }}" class="inline-flex items-center gap-2 text-white border border-stone-600 px-6 py-3 text-xs uppercase tracking-widest hover:border-copper-400 transition-colors">Las artesanas</a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="aspect-square overflow-hidden">
                    <img src="{{ $historiaImgs[0] }}" alt="Artesana diseñando" class="w-full h-full object-cover">
                </div>
                <div class="aspect-square overflow-hidden col-start-2 row-start-2">
                    <img src="{{ $historiaImgs[1] }}" alt="Mujeres en taller" class="w-full h-full object-cover">
                </div>
                <div class="aspect-square overflow-hidden col-start-2 row-start-1">
                    <img src="{{ $historiaImgs[2] }}" alt="Taller de cerámica" class="w-full h-full object-cover">
                </div>
                <div class="aspect-square overflow-hidden">
                    <img src="{{ $historiaImgs[3] }}" alt="Artesanas tejiendo" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Featured artisans --}}
@if($featuredArtisans->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <p class="section-subtitle mb-3">Las manos detrás de cada pieza</p>
            <h2 class="section-title">Nuestras artesanas</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($featuredArtisans as $artisan)
            <a href="{{ route('artisans.show', $artisan->slug) }}" class="group text-center">
                <div class="w-40 h-40 rounded-full overflow-hidden mx-auto mb-5 bg-stone-100">
                    <img src="{{ $artisan->photo_url }}" alt="{{ $artisan->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <h3 class="font-display text-xl text-stone-800 group-hover:text-copper-600 transition-colors">{{ $artisan->name }}</h3>
                <p class="text-sm text-copper-500 mt-1">{{ $artisan->specialty }}</p>
                @if($artisan->quote)
                <p class="text-stone-500 text-sm mt-3 italic">"{{ Str::limit($artisan->quote, 80) }}"</p>
                @endif
            </a>
            @endforeach
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('artisans.index') }}" class="btn-copper">Conocer a todas las artesanas</a>
        </div>
    </div>
</section>
@endif

{{-- Recent posts --}}
@if($recentPosts->count() > 0)
<section class="py-20 bg-stone-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-end mb-12">
            <div>
                <p class="section-subtitle mb-3">Últimas novedades</p>
                <h2 class="section-title">Noticias y eventos</h2>
            </div>
            <a href="{{ route('posts.index') }}" class="btn-copper-outline hidden md:inline-flex">Ver todas</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentPosts as $post)
            <article class="group">
                <a href="{{ route('posts.show', $post->slug) }}" class="block overflow-hidden bg-stone-200 aspect-video mb-4">
                    @if($post->image)
                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-stone-700 to-stone-900 flex items-center justify-center">
                        <span class="text-stone-500 text-xs uppercase tracking-widest">{{ $post->type === 'evento' ? 'Evento' : 'Noticia' }}</span>
                    </div>
                    @endif
                </a>
                <span class="text-xs text-copper-500 uppercase tracking-widest">{{ $post->type === 'evento' ? 'Evento' : 'Noticia' }}</span>
                <h3 class="font-display text-xl text-stone-800 mt-2 mb-2 group-hover:text-copper-600 transition-colors">
                    <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                @if($post->excerpt)
                <p class="text-stone-500 text-sm leading-relaxed">{{ Str::limit($post->excerpt, 100) }}</p>
                @endif
                <p class="text-xs text-stone-400 mt-3">{{ $post->published_at?->format('d/m/Y') }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Newsletter --}}
<section class="py-16 bg-copper-500">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <p class="text-copper-100 text-xs uppercase tracking-widest mb-3">Mantente informada</p>
        <h2 class="font-display text-3xl md:text-4xl text-white font-medium mb-4">Suscribite al newsletter</h2>
        <p class="text-copper-100 mb-8">Recibí noticias, nuevos productos y eventos especiales de Cateura Accesorios.</p>
        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
            @csrf
            <input type="email" name="email" placeholder="Tu correo electrónico" required
                   class="flex-1 bg-white/20 border border-white/40 text-white placeholder-copper-200 px-4 py-3 focus:outline-none focus:bg-white/30 focus:border-white transition-colors">
            <button type="submit" class="bg-stone-900 text-white px-6 py-3 text-xs uppercase tracking-widest hover:bg-stone-800 transition-colors">
                Suscribirse
            </button>
        </form>
    </div>
</section>

@endsection
