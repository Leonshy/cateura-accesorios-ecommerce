@extends('layouts.admin')
@section('title', 'Editar banner')
@section('content')
<div class="max-w-xl">
<form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf @method('PUT')
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        @if($banner->image)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $banner->image) }}" alt="" class="w-full h-32 object-cover">
            <p class="text-xs text-stone-400 mt-1">Imagen actual</p>
        </div>
        @endif
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Título *</label>
            <input type="text" name="title" value="{{ old('title', $banner->title) }}" required class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Subtítulo</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" class="input-cateura border p-2 w-full">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Texto del botón</label>
                <input type="text" name="cta_label" value="{{ old('cta_label', $banner->cta_label) }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">URL del botón</label>
                <input type="text" name="cta_url" value="{{ old('cta_url', $banner->cta_url) }}" class="input-cateura border p-2 w-full">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Reemplazar imagen</label>
            <input type="file" name="image" accept="image/*" class="text-sm text-stone-600 file:mr-3 file:px-4 file:py-2 file:border-0 file:bg-copper-50 file:text-copper-600 file:text-xs file:cursor-pointer">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Orden</label>
            <input type="number" name="order" value="{{ old('order', $banner->order) }}" min="0" class="input-cateura border p-2 w-full w-24">
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="text-copper-500">
            <span class="text-sm text-stone-600">Mostrar en carrusel</span>
        </label>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-copper">Guardar cambios</button>
        <a href="{{ route('admin.banners.index') }}" class="btn-stone">Cancelar</a>
    </div>
</form>
</div>
@endsection
