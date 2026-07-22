@extends('layouts.app')
@section('title', 'Nuestras Artesanas — Cateura Accesorios')
@section('content')
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">
        @php($content = \App\Models\SiteSetting::group('content'))
        <div class="text-center mb-12">
            <p class="section-subtitle mb-2">{{ $content['artisans_eyebrow'] ?? 'Mujeres Unidas del Bañado Sur' }}</p>
            <h1 class="section-title">{{ $content['artisans_title'] ?? 'Nuestras Artesanas' }}</h1>
            <p class="text-stone-500 mt-4 max-w-2xl mx-auto">{{ $content['artisans_subtitle'] ?? 'Cada pieza lleva las manos y el corazón de mujeres emprendedoras que transforman materiales reciclados en arte.' }}</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @forelse($artisans as $artisan)
            <a href="{{ route('artisans.show', $artisan->slug) }}" class="group text-center">
                <div class="w-36 h-36 rounded-full overflow-hidden mx-auto mb-4 border-2 border-transparent group-hover:border-copper-300 transition-all duration-300">
                    @if($artisan->photo)
                    <img src="{{ $artisan->photo_url }}" alt="{{ $artisan->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full bg-copper-100 flex items-center justify-center text-copper-500 text-5xl font-display">{{ substr($artisan->name, 0, 1) }}</div>
                    @endif
                </div>
                <p class="font-medium text-stone-700 group-hover:text-copper-500 transition-colors">{{ $artisan->name }}</p>
                @if($artisan->specialty)
                <p class="text-xs text-stone-400 mt-0.5">{{ $artisan->specialty }}</p>
                @endif
                @if($artisan->years_experience)
                <p class="text-xs text-copper-500 mt-1">{{ $artisan->years_experience }} años de experiencia</p>
                @endif
            </a>
            @empty
            <div class="col-span-4 py-12 text-center text-stone-400">Información próximamente.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
