@extends('layouts.admin')
@section('title', 'Nuevo producto')
@section('content')
<div class="max-w-4xl">
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
                <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Información básica</h3>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-cateura border p-2 w-full">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-stone-600 mb-1">Categoría *</label>
                        <select name="category_id" required class="input-cateura border p-2 w-full">
                            <option value="">Seleccionar...</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-stone-600 mb-1">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" class="input-cateura border p-2 w-full">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Descripción corta</label>
                    <textarea name="short_description" rows="2" class="input-cateura border p-2 w-full">{{ old('short_description') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Descripción completa</label>
                    <textarea name="description" rows="5" class="input-cateura border p-2 w-full">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
                <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Precio y stock</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-stone-600 mb-1">Precio (Gs.) *</label>
                        <input type="number" name="price" value="{{ old('price') }}" required min="0" class="input-cateura border p-2 w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-stone-600 mb-1">Precio original</label>
                        <input type="number" name="original_price" value="{{ old('original_price') }}" min="0" class="input-cateura border p-2 w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-stone-600 mb-1">Stock *</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0" class="input-cateura border p-2 w-full">
                    </div>
                </div>
            </div>

            <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
                <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Imágenes</h3>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Imagen principal *</label>
                    <input type="file" name="image" accept="image/*" class="text-sm text-stone-600 file:mr-3 file:px-4 file:py-2 file:border-0 file:bg-copper-50 file:text-copper-600 file:text-xs file:cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Galería (múltiple)</label>
                    <input type="file" name="gallery[]" accept="image/*" multiple class="text-sm text-stone-600 file:mr-3 file:px-4 file:py-2 file:border-0 file:bg-stone-50 file:text-stone-600 file:text-xs file:cursor-pointer">
                </div>
            </div>

            <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
                <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">SEO</h3>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Meta título</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="input-cateura border p-2 w-full">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Meta descripción</label>
                    <textarea name="meta_description" rows="2" class="input-cateura border p-2 w-full">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
                <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Estado y visibilidad</h3>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="text-copper-500 border-stone-300">
                    <span class="text-sm text-stone-600">Activo (visible en tienda)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="text-copper-500 border-stone-300">
                    <span class="text-sm text-stone-600">Producto destacado</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_new" value="1" {{ old('is_new') ? 'checked' : '' }} class="text-copper-500 border-stone-300">
                    <span class="text-sm text-stone-600">Marcar como "Nuevo"</span>
                </label>
            </div>

            <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
                <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Colores disponibles</h3>
                <div class="flex flex-col gap-2" id="colors-container">
                    <div class="flex gap-2">
                        <input type="text" name="colors[]" placeholder="Ej: Cobre, Plateado" class="input-cateura border p-2 flex-1 text-sm">
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('colors-container').insertAdjacentHTML('beforeend','<div class=\'flex gap-2\'><input type=\'text\' name=\'colors[]\' class=\'input-cateura border p-2 flex-1 text-sm\'></div>')" class="text-xs text-copper-500 hover:text-copper-600">+ Agregar color</button>
            </div>

            <div class="flex flex-col gap-2">
                <button type="submit" class="btn-copper w-full">Guardar producto</button>
                <a href="{{ route('admin.products.index') }}" class="btn-stone w-full text-center">Cancelar</a>
            </div>
        </div>
    </div>
</form>
</div>
@endsection
