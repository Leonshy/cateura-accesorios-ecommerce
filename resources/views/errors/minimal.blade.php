<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — Cateura Accesorios</title>
    {{-- Esta vista no consulta la base de datos a propósito: si el error 500
    es justamente una caída de la BD, la página de error no puede depender de ella. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden bg-stone-50 font-sans antialiased">
    <div class="h-full w-full flex flex-col items-center justify-center px-6 py-4 text-center">
        <div class="w-full max-w-sm mx-auto flex flex-col items-center">
            <a href="/" class="mb-5">
                <img src="{{ asset('assets/brand/logo-horizontal.png') }}" alt="Cateura Accesorios" class="h-40 w-auto">
            </a>

            <div class="w-16 h-16 rounded-full bg-copper-100 flex items-center justify-center mb-4">
                {!! $icon !!}
            </div>

            <p class="font-display text-6xl text-copper-500 leading-none mb-2">{{ $code }}</p>
            <h1 class="font-display text-2xl text-stone-800 mb-2">{{ $title }}</h1>
            <p class="text-stone-500 text-sm mb-6">{{ $message }}</p>

            <div class="w-full flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/') }}" class="btn-copper">Volver al inicio</a>
                <a href="{{ route('shop.index') }}" class="btn-copper-outline">Ir a la tienda</a>
            </div>
        </div>
    </div>
</body>
</html>
