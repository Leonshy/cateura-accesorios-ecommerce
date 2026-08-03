@extends('layouts.admin')
@section('title', 'Textos')
@section('content')
<div class="max-w-2xl">
@include('admin.content._tabs', ['active' => 'home'])
<form action="{{ route('admin.content.home.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Sección "Nuestra historia" (portada)</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto pequeño (eyebrow)</label>
            <input type="text" name="historia_eyebrow" value="{{ $settings['historia_eyebrow'] ?? 'Nuestra historia' }}" class="input-cateura border p-2 w-full">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Título línea 1</label>
                <input type="text" name="historia_title_line1" value="{{ $settings['historia_title_line1'] ?? 'Manos que transforman' }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Título línea 2 (color)</label>
                <input type="text" name="historia_title_line2" value="{{ $settings['historia_title_line2'] ?? 'el mundo' }}" class="input-cateura border p-2 w-full">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto descriptivo</label>
            <textarea name="historia_text" rows="4" class="input-cateura border p-2 w-full">{{ $settings['historia_text'] ?? 'Cateura Accesorios es la marca de los productos elaborados por artesanas de la Asociación Mujeres Unidas del Bañado Sur. A partir de materiales reciclados, transforman con sus manos objetos descartados en artículos de gran belleza, transmitiendo esperanza, dignidad y compromiso con la comunidad.' }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Estadística 1</label>
                <input type="text" name="historia_stat1_value" placeholder="Valor" value="{{ $settings['historia_stat1_value'] ?? '+50' }}" class="input-cateura border p-2 w-full mb-2">
                <input type="text" name="historia_stat1_label" placeholder="Etiqueta" value="{{ $settings['historia_stat1_label'] ?? 'Artesanas' }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Estadística 2</label>
                <input type="text" name="historia_stat2_value" placeholder="Valor" value="{{ $settings['historia_stat2_value'] ?? '100%' }}" class="input-cateura border p-2 w-full mb-2">
                <input type="text" name="historia_stat2_label" placeholder="Etiqueta" value="{{ $settings['historia_stat2_label'] ?? 'Reciclado' }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Estadística 3</label>
                <input type="text" name="historia_stat3_value" placeholder="Valor" value="{{ $settings['historia_stat3_value'] ?? 'PY' }}" class="input-cateura border p-2 w-full mb-2">
                <input type="text" name="historia_stat3_label" placeholder="Etiqueta" value="{{ $settings['historia_stat3_label'] ?? 'Hecho en PY' }}" class="input-cateura border p-2 w-full">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
            @foreach([1,2,3,4] as $i)
            <x-admin.media-picker name="historia_img{{ $i }}" value="{{ $settings['historia_img'.$i] ?? '' }}" label="Foto {{ $i }}" />
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn-copper">Guardar cambios</button>
</form>
</div>
@endsection
