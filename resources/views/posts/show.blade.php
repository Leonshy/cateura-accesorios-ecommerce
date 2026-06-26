@extends('layouts.app')
@section('title', $post->title . ' — Cateura Accesorios')
@section('content')
<article class="py-16">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-xs uppercase tracking-widest {{ $post->type === 'evento' ? 'text-blue-500' : 'text-copper-500' }}">{{ $post->type }}</span>
                <span class="text-xs text-stone-400">{{ $post->published_at?->format('d \d\e F \d\e Y') }}</span>
            </div>
            <h1 class="section-title mb-4">{{ $post->title }}</h1>
            @if($post->excerpt)
            <p class="text-xl text-stone-500 font-light leading-relaxed">{{ $post->excerpt }}</p>
            @endif
        </div>
        @if($post->image)
        <div class="mb-8 aspect-[16/7] overflow-hidden">
            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
        @endif
        @if($post->content)
        <div class="prose prose-stone max-w-none text-stone-600 leading-relaxed">
            {!! $post->content !!}
        </div>
        @endif
        <div class="mt-10 pt-6 border-t border-stone-100">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-stone-500 hover:text-copper-500 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver a noticias
            </a>
        </div>
    </div>
</article>
@endsection
