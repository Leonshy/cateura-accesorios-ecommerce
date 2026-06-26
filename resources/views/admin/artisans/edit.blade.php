@extends('layouts.admin')
@section('title', 'Editar artesana')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.artisans.update', $artisan) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf @method('PUT')
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        @if($artisan->photo)
        <div class="flex items-center gap-4 pb-4 border-b border-stone-100">
            <img src="{{ asset('storage/' . $artisan->photo) }}" alt="{{ $artisan->name }}" class="w-16 h-16 rounded-full object-cover">
            <p class="text-xs text-stone-400">Foto actual</p>
        </div>
        @endif
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-stone-600 mb-1">Nombre completo *</label>
                <input type="text" name="name" value="{{ old('name', $artisan->name) }}" required class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Especialidad</label>
                <input type="text" name="specialty" value="{{ old('specialty', $artisan->specialty) }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Años de experiencia</label>
                <input type="number" name="years_experience" value="{{ old('years_experience', $artisan->years_experience) }}" min="0" class="input-cateura border p-2 w-full">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Historia / Historia</label>
            <textarea name="bio" rows="4" class="input-cateura border p-2 w-full">{{ old('story', $artisan->bio) }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Reemplazar foto</label>
            <input type="file" name="photo" accept="image/*" class="text-sm text-stone-600 file:mr-3 file:px-4 file:py-2 file:border-0 file:bg-copper-50 file:text-copper-600 file:text-xs file:cursor-pointer">
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $artisan->is_active) ? 'checked' : '' }} class="text-copper-500">
            <span class="text-sm text-stone-600">Mostrar en el sitio</span>
        </label>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-copper">Guardar cambios</button>
        <a href="{{ route('admin.artisans.index') }}" class="btn-stone">Cancelar</a>
    </div>
</form>
</div>
@endsection
