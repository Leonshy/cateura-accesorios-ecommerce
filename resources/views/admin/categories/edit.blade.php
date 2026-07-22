@extends('layouts.admin')
@section('title', 'Editar categoría')
@section('content')
<div class="max-w-xl">
<form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf @method('PUT')
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Nombre *</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Descripción</label>
            <textarea name="description" rows="2" class="input-cateura border p-2 w-full">{{ old('description', $category->description) }}</textarea>
        </div>
        <x-admin.media-picker name="image" value="{{ old('image', $category->image) }}" label="Imagen de portada" />
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Orden</label>
            <input type="number" name="order" value="{{ old('order', $category->order) }}" min="0" class="input-cateura border p-2 w-24">
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="text-copper-500">
            <span class="text-sm text-stone-600">Categoría activa</span>
        </label>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-copper">Guardar cambios</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-stone">Cancelar</a>
    </div>
</form>

<div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4 mt-6">
    <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Subcategorías</h3>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="space-y-2">
        @forelse($category->subcategories as $sub)
        <form action="{{ route('admin.subcategories.update', $sub) }}" method="POST" class="flex items-center gap-3 p-3 border border-stone-100">
            @csrf @method('PATCH')
            <input type="text" name="name" value="{{ $sub->name }}" class="input-cateura border p-2 flex-1 text-sm">
            <input type="number" name="order" value="{{ $sub->order }}" min="0" class="input-cateura border p-2 w-20 text-sm" title="Orden">
            <label class="flex items-center gap-1.5 text-xs text-stone-500 cursor-pointer flex-shrink-0">
                <input type="checkbox" name="is_active" value="1" {{ $sub->is_active ? 'checked' : '' }} class="text-copper-500">
                Activa
            </label>
            <button type="submit" class="text-copper-500 hover:text-copper-600 text-xs flex-shrink-0">Guardar</button>
        </form>
        <form action="{{ route('admin.subcategories.destroy', $sub) }}" method="POST" onsubmit="return confirm('¿Eliminar esta subcategoría?')" class="-mt-2 mb-1 pl-3">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs text-stone-400 hover:text-red-500">Eliminar "{{ $sub->name }}"</button>
        </form>
        @empty
        <p class="text-sm text-stone-400 py-2">Todavía no hay subcategorías en esta categoría.</p>
        @endforelse
    </div>

    <form action="{{ route('admin.subcategories.store', $category) }}" method="POST" class="flex items-center gap-3 pt-3 border-t border-stone-100">
        @csrf
        <input type="text" name="name" placeholder="Nombre de la nueva subcategoría" required class="input-cateura border p-2 flex-1 text-sm">
        <input type="number" name="order" value="0" min="0" class="input-cateura border p-2 w-20 text-sm" title="Orden">
        <button type="submit" class="btn-copper py-2 px-4 text-xs flex-shrink-0">+ Agregar</button>
    </form>
</div>
</div>
@endsection
