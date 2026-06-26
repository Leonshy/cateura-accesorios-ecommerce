@extends('layouts.admin')
@section('title', 'Nueva categoría')
@section('content')
<div class="max-w-xl">
<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Nombre *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Descripción</label>
            <textarea name="description" rows="2" class="input-cateura border p-2 w-full">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Imagen de portada</label>
            <input type="file" name="image" accept="image/*" class="text-sm text-stone-600 file:mr-3 file:px-4 file:py-2 file:border-0 file:bg-copper-50 file:text-copper-600 file:text-xs file:cursor-pointer">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Orden</label>
            <input type="number" name="order" value="{{ old('order', 0) }}" min="0" class="input-cateura border p-2 w-24">
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="text-copper-500">
            <span class="text-sm text-stone-600">Categoría activa</span>
        </label>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-copper">Guardar categoría</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-stone">Cancelar</a>
    </div>
</form>
</div>
@endsection
