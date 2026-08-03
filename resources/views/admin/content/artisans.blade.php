@extends('layouts.admin')
@section('title', 'Textos')
@section('content')
<div class="max-w-2xl">
@include('admin.content._tabs', ['active' => 'artisans'])
<form action="{{ route('admin.content.artisans.update') }}" method="POST" class="space-y-6">
    @csrf
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Encabezado de la página "Nuestras Artesanas"</h3>
        <p class="text-xs text-stone-400 -mt-2">Este texto aparece arriba del listado público en <span class="font-mono">/artesanas</span>. Los perfiles de cada artesana se cargan aparte, desde el menú "Artesanas" del catálogo.</p>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto pequeño (eyebrow)</label>
            <input type="text" name="artisans_eyebrow" value="{{ $settings['artisans_eyebrow'] ?? 'Mujeres Unidas del Bañado Sur' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Título</label>
            <input type="text" name="artisans_title" value="{{ $settings['artisans_title'] ?? 'Nuestras Artesanas' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Subtítulo</label>
            <textarea name="artisans_subtitle" rows="2" class="input-cateura border p-2 w-full">{{ $settings['artisans_subtitle'] ?? 'Cada pieza lleva las manos y el corazón de mujeres emprendedoras que transforman materiales reciclados en arte.' }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn-copper">Guardar cambios</button>
</form>
</div>
@endsection
