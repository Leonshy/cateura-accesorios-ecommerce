@extends('layouts.app')
@section('title', $artisan->name . ' — Cateura Accesorios')
@section('content')
<section class="py-16">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="flex flex-col md:flex-row gap-10 items-start">
            <div class="flex-shrink-0 text-center">
                <div class="w-48 h-48 rounded-full overflow-hidden mx-auto border-4 border-copper-100">
                    @if($artisan->photo)
                    <img src="{{ $artisan->photo_url }}" alt="{{ $artisan->name }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-copper-100 flex items-center justify-center text-copper-500 text-7xl font-display">{{ substr($artisan->name, 0, 1) }}</div>
                    @endif
                </div>
                @if($artisan->specialty)
                <p class="section-subtitle mt-4">{{ $artisan->specialty }}</p>
                @endif
                @if($artisan->years_experience)
                <p class="text-sm text-stone-500 mt-1">{{ $artisan->years_experience }} años de experiencia</p>
                @endif
            </div>
            <div class="flex-1">
                <h1 class="section-title mb-4">{{ $artisan->name }}</h1>
                @if($artisan->bio)
                <div class="text-stone-600 leading-relaxed space-y-3">
                    {!! nl2br(e($artisan->bio)) !!}
                </div>
                @endif
            </div>
        </div>
        <div class="mt-10 pt-6 border-t border-stone-100">
            <a href="{{ route('artisans.index') }}" class="inline-flex items-center gap-2 text-stone-500 hover:text-copper-500 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Ver todas las artesanas
            </a>
        </div>
    </div>
</section>
@endsection
