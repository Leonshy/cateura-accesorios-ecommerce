@extends('layouts.admin')
@section('title', 'Nueva artesana')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.artisans.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-stone-600 mb-1">Nombre completo *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Especialidad</label>
                <input type="text" name="specialty" value="{{ old('specialty') }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Años de experiencia</label>
                <input type="number" name="years_experience" value="{{ old('years_experience') }}" min="0" class="input-cateura border p-2 w-full">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Historia / Historia</label>
            <textarea name="bio" rows="4" class="input-cateura border p-2 w-full">{{ old('story') }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Foto</label>
            <input type="file" name="photo" accept="image/*" class="text-sm text-stone-600 file:mr-3 file:px-4 file:py-2 file:border-0 file:bg-copper-50 file:text-copper-600 file:text-xs file:cursor-pointer">
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="text-copper-500">
            <span class="text-sm text-stone-600">Mostrar en el sitio</span>
        </label>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-copper">Guardar artesana</button>
        <a href="{{ route('admin.artisans.index') }}" class="btn-stone">Cancelar</a>
    </div>
</form>
</div>
@endsection
