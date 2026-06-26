@extends('layouts.admin')
@section('title', 'Nuevo banner')
@section('content')
<div class="max-w-xl">
<form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Título principal *</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Subtítulo</label>
            <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="input-cateura border p-2 w-full">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Texto del botón</label>
                <input type="text" name="cta_label" value="{{ old('cta_label', 'Ver colección') }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">URL del botón</label>
                <input type="text" name="cta_url" value="{{ old('cta_url', '/tienda') }}" class="input-cateura border p-2 w-full">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Imagen (1920×600 px recomendado)</label>
            <input type="file" name="image" accept="image/*" required class="text-sm text-stone-600 file:mr-3 file:px-4 file:py-2 file:border-0 file:bg-copper-50 file:text-copper-600 file:text-xs file:cursor-pointer">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Orden de aparición</label>
                <input type="number" name="order" value="{{ old('order', 0) }}" min="0" class="input-cateura border p-2 w-full">
            </div>
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="text-copper-500">
            <span class="text-sm text-stone-600">Mostrar en carrusel</span>
        </label>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-copper">Guardar banner</button>
        <a href="{{ route('admin.banners.index') }}" class="btn-stone">Cancelar</a>
    </div>
</form>
</div>
@endsection
