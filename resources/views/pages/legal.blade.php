@extends('layouts.app')
@section('title', ($legalPage?->title ?? 'Información legal') . ' — Cateura Accesorios')
@section('content')
<section class="py-16">
    <div class="container mx-auto px-4 max-w-3xl">
        @if($legalPage)
        <h1 class="section-title mb-8">{{ $legalPage->title }}</h1>
        <div class="prose prose-stone max-w-none text-stone-600 leading-relaxed">
            {!! $legalPage->content !!}
        </div>
        @else
        <h1 class="section-title mb-4">Página legal</h1>
        <p class="text-stone-400">Este contenido estará disponible próximamente.</p>
        @endif
        <div class="mt-10">
            <a href="{{ route('home') }}" class="text-sm text-stone-500 hover:text-copper-500 transition-colors">← Volver al inicio</a>
        </div>
    </div>
</section>
@endsection
