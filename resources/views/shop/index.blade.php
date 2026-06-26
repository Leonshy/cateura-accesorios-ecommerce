@extends('layouts.app')
@section('title', 'Tienda')
@section('content')

{{-- Page header --}}
<div class="bg-stone-50 border-b border-stone-200 py-10">
    <div class="max-w-7xl mx-auto px-4">
        <p class="section-subtitle mb-2">{{ $currentCategory ? $currentCategory->name : 'Todos los productos' }}</p>
        <h1 class="section-title">Tienda Cateura</h1>
        <nav class="flex items-center gap-2 mt-3 text-sm text-stone-400">
            <a href="{{ route('home') }}" class="hover:text-copper-500 transition-colors">Inicio</a>
            <span>/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-copper-500 transition-colors">Tienda</a>
            @if($currentCategory)
            <span>/</span>
            <span class="text-stone-700">{{ $currentCategory->name }}</span>
            @endif
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-10">
        {{-- Sidebar filters --}}
        <aside class="lg:w-64 flex-shrink-0">
            <div class="sticky top-24">
                <h3 class="font-medium text-stone-700 mb-5 text-sm uppercase tracking-widest">Filtros</h3>

                {{-- Categories --}}
                <div class="mb-8">
                    <h4 class="text-xs uppercase tracking-widest text-stone-500 mb-3 font-medium">Categorías</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('shop.index') }}" class="text-sm {{ !request('categoria') ? 'text-copper-500 font-medium' : 'text-stone-600 hover:text-copper-500' }} transition-colors">
                                Todos los productos
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('shop.index', ['categoria' => $cat->slug]) }}"
                               class="text-sm {{ request('categoria') === $cat->slug ? 'text-copper-500 font-medium' : 'text-stone-600 hover:text-copper-500' }} transition-colors">
                                {{ $cat->name }}
                            </a>
                            @if(request('categoria') === $cat->slug && $cat->subcategories->count() > 0)
                            <ul class="ml-3 mt-2 space-y-1">
                                @foreach($cat->subcategories as $sub)
                                <li>
                                    <a href="{{ route('shop.index', ['categoria' => $cat->slug, 'subcategoria' => $sub->slug]) }}"
                                       class="text-xs {{ request('subcategoria') === $sub->slug ? 'text-copper-500 font-medium' : 'text-stone-500 hover:text-copper-500' }} transition-colors">
                                        {{ $sub->name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Special filters --}}
                <div class="mb-8">
                    <h4 class="text-xs uppercase tracking-widest text-stone-500 mb-3 font-medium">Tipo</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.index', array_merge(request()->except('nuevo','destacado'), ['nuevo' => '1'])) }}" class="text-sm {{ request()->boolean('nuevo') ? 'text-copper-500 font-medium' : 'text-stone-600 hover:text-copper-500' }} transition-colors">✦ Nuevos productos</a></li>
                        <li><a href="{{ route('shop.index', array_merge(request()->except('nuevo','destacado'), ['destacado' => '1'])) }}" class="text-sm {{ request()->boolean('destacado') ? 'text-copper-500 font-medium' : 'text-stone-600 hover:text-copper-500' }} transition-colors">★ Destacados</a></li>
                    </ul>
                </div>

                @if(request()->hasAny(['categoria','subcategoria','q','nuevo','destacado']))
                <a href="{{ route('shop.index') }}" class="text-xs text-stone-400 hover:text-red-500 transition-colors">✕ Limpiar filtros</a>
                @endif
            </div>
        </aside>

        {{-- Products grid --}}
        <div class="flex-1">
            {{-- Sort + count bar --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 pb-4 border-b border-stone-200">
                <p class="text-sm text-stone-500">
                    <span class="font-medium text-stone-700">{{ $products->total() }}</span> productos
                    @if(request('q'))<span> para "<em>{{ request('q') }}</em>"</span>@endif
                </p>
                <form method="GET" action="{{ route('shop.index') }}" class="flex items-center gap-3">
                    @foreach(request()->except('orden') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <label class="text-xs text-stone-500 uppercase tracking-wider">Ordenar</label>
                    <select name="orden" onchange="this.form.submit()" class="input-cateura text-sm py-2 pr-8 text-stone-700 bg-white border-stone-300">
                        <option value="" {{ !request('orden') ? 'selected' : '' }}>Más recientes</option>
                        <option value="precio_asc" {{ request('orden') === 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                        <option value="precio_desc" {{ request('orden') === 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                        <option value="nombre" {{ request('orden') === 'nombre' ? 'selected' : '' }}>Nombre A-Z</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
            <div class="text-center py-20">
                <svg class="w-16 h-16 text-stone-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="font-display text-2xl text-stone-600 mb-3">No encontramos productos</h3>
                <p class="text-stone-400 mb-6">Probá con otros filtros o explorá toda nuestra tienda.</p>
                <a href="{{ route('shop.index') }}" class="btn-copper">Ver todos los productos</a>
            </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                @foreach($products as $product)
                    @include('partials.product-card', compact('product'))
                @endforeach
            </div>
            <div class="mt-12">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
