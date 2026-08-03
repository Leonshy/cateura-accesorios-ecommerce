@extends('layouts.app')
@section('title', 'Suscripción confirmada')
@section('content')
<section class="py-24">
    <div class="container mx-auto px-4 max-w-lg text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="section-subtitle text-copper-500 mb-2">¡Listo!</p>
        <h1 class="section-title mb-4">Suscripción confirmada</h1>
        <p class="text-stone-500 mb-8">Gracias, <strong>{{ $subscriber->email }}</strong>. Ya vas a empezar a recibir las novedades de Cateura Accesorios.</p>
        <a href="{{ route('home') }}" class="btn-copper">Volver al inicio</a>
    </div>
</section>
@endsection
