@extends('layouts.app')
@section('title', 'Noticias y Eventos — Cateura Accesorios')
@section('content')
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <p class="section-subtitle mb-2">Novedades</p>
            <h1 class="section-title">Noticias y Eventos</h1>
        </div>
        <div class="flex gap-3 justify-center mb-8">
            <a href="{{ route('posts.index') }}" class="{{ !request('tipo') ? 'bg-copper-500 text-white' : 'border border-stone-300 text-stone-600 hover:border-copper-300' }} px-5 py-2 text-xs tracking-widest uppercase transition-colors">Todos</a>
            <a href="{{ route('posts.index', ['tipo' => 'noticia']) }}" class="{{ request('tipo') === 'noticia' ? 'bg-copper-500 text-white' : 'border border-stone-300 text-stone-600 hover:border-copper-300' }} px-5 py-2 text-xs tracking-widest uppercase transition-colors">Noticias</a>
            <a href="{{ route('posts.index', ['tipo' => 'evento']) }}" class="{{ request('tipo') === 'evento' ? 'bg-copper-500 text-white' : 'border border-stone-300 text-stone-600 hover:border-copper-300' }} px-5 py-2 text-xs tracking-widest uppercase transition-colors">Eventos</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
            <article class="group">
                <a href="{{ route('posts.show', $post->slug) }}" class="block overflow-hidden mb-4 aspect-[4/3] bg-stone-100">
                    @if($post->image)
                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full bg-stone-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    @endif
                </a>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-xs uppercase tracking-widest {{ $post->type === 'evento' ? 'text-blue-500' : 'text-copper-500' }}">{{ $post->type }}</span>
                    <span class="text-xs text-stone-400">{{ $post->published_at?->format('d/m/Y') }}</span>
                </div>
                <h2 class="font-display text-xl text-stone-800 group-hover:text-copper-500 transition-colors mb-2">
                    <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                </h2>
                @if($post->excerpt)
                <p class="text-sm text-stone-500 leading-relaxed line-clamp-2">{{ $post->excerpt }}</p>
                @endif
                <a href="{{ route('posts.show', $post->slug) }}" class="inline-flex items-center gap-1 mt-3 text-xs text-copper-500 hover:text-copper-600 tracking-wider uppercase font-medium">
                    Leer más
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </article>
            @empty
            <div class="col-span-3 py-12 text-center text-stone-400">No hay publicaciones disponibles.</div>
            @endforelse
        </div>
        <div class="mt-10">{{ $posts->links() }}</div>
    </div>
</section>
@endsection
