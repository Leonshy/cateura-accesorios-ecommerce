@extends('layouts.app')
@section('title', 'Nosotros — Cateura Accesorios')
@section('content')
@php($c = \App\Models\SiteSetting::group('content'))
{{-- Hero --}}
<section class="bg-stone-800 text-white py-20">
    <div class="container mx-auto px-4 max-w-4xl text-center">
        <p class="section-subtitle text-copper-300 mb-3">{{ $c['about_hero_eyebrow'] ?? 'Quiénes somos' }}</p>
        <h1 class="font-display text-4xl md:text-5xl font-medium mb-6">{{ $c['about_hero_title'] ?? 'Una historia de resiliencia y arte' }}</h1>
        <p class="text-stone-300 text-lg leading-relaxed max-w-2xl mx-auto">{{ $c['about_hero_text'] ?? 'Cateura Accesorios nació en el corazón del Bañado Sur de Asunción, donde mujeres emprendedoras transforman materiales reciclados del instrumento más famoso del mundo en joyas y accesorios únicos.' }}</p>
    </div>
</section>

{{-- Nuestra historia --}}
<section class="py-16">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <p class="section-subtitle mb-2">{{ $c['about_historia_eyebrow'] ?? 'Nuestra historia' }}</p>
                <h2 class="section-title mb-6">{{ $c['about_historia_title'] ?? 'Del vertedero al mundo' }}</h2>
                <p class="text-stone-600 leading-relaxed mb-4">{{ $c['about_historia_text1'] ?? 'La Asociación Mujeres Unidas del Bañado Sur es una organización comunitaria que trabaja para generar empleo digno y sustentable para las familias del barrio. Inspiradas por la orquesta de instrumentos reciclados de Cateura, estas mujeres decidieron convertir el material desechado en arte.' }}</p>
                <p class="text-stone-600 leading-relaxed mb-4">{{ $c['about_historia_text2'] ?? 'Cada accesorio que crea Cateura Accesorios lleva consigo una historia de superación, creatividad y comunidad. Los materiales provienen de residuos que son transformados con maestría artesanal en piezas de joyería, objetos para el hogar y textiles únicos.' }}</p>
                <p class="text-stone-600 leading-relaxed">{{ $c['about_historia_text3'] ?? 'Cuando compras un producto Cateura Accesorios, contribuís directamente al sustento de familias paraguayas y a un modelo de economía circular que cuida el planeta.' }}</p>
            </div>
            @if(!empty($c['about_hero_image']))
            <div class="aspect-square overflow-hidden">
                <img src="{{ media_url($c['about_hero_image']) }}" alt="Foto institucional" class="w-full h-full object-cover">
            </div>
            @else
            <div class="bg-stone-100 aspect-square flex items-center justify-center">
                <div class="text-center text-stone-400 p-8">
                    <svg class="w-16 h-16 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm">Foto institucional</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- Valores --}}
<section class="py-16 bg-stone-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <p class="section-subtitle mb-2">{{ $c['about_valores_eyebrow'] ?? 'Lo que nos mueve' }}</p>
            <h2 class="section-title">{{ $c['about_valores_title'] ?? 'Nuestros valores' }}</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
            @foreach($aboutValues as $value)
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-10 h-10 text-copper-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ \App\Models\AboutValue::iconSvgPath($value->icon) }}"/></svg>
                </div>
                <h3 class="font-display text-xl text-stone-800 mb-3">{{ $value->title }}</h3>
                <p class="text-stone-500 text-sm leading-relaxed">{{ $value->text }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 bg-copper-500">
    <div class="container mx-auto px-4 text-center text-white">
        <h2 class="font-display text-3xl font-medium mb-4">{{ $c['about_cta_title'] ?? 'Conocé a nuestras artesanas' }}</h2>
        <p class="text-copper-100 mb-8">{{ $c['about_cta_text'] ?? 'Detrás de cada pieza hay una mujer con una historia extraordinaria.' }}</p>
        <a href="{{ route('artisans.index') }}" class="inline-block bg-white text-copper-600 px-8 py-3 text-sm font-medium tracking-widest uppercase hover:bg-copper-50 transition-colors">Ver artesanas</a>
    </div>
</section>
@endsection
