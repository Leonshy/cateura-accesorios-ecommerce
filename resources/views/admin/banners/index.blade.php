@extends('layouts.admin')
@section('title', 'Banners del carrusel')
@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-stone-500 text-sm">Banners del hero</p>
    <a href="{{ route('admin.banners.create') }}" class="btn-copper">+ Nuevo banner</a>
</div>
<div class="space-y-3">
    @forelse($banners as $banner)
    <div class="bg-white border border-stone-100 shadow-sm p-4 flex items-center gap-4">
        <div class="w-24 h-14 bg-stone-100 overflow-hidden flex-shrink-0">
            @if($banner->image)
            <img src="{{ $banner->image_url }}" alt="" class="w-full h-full object-cover">
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-medium text-stone-700">{{ $banner->title }}</p>
            <p class="text-xs text-stone-400">{{ $banner->subtitle }}</p>
            <div class="flex gap-2 mt-1">
                <span class="text-xs {{ $banner->is_active ? 'bg-green-100 text-green-600' : 'bg-stone-100 text-stone-500' }} px-2 py-0.5">{{ $banner->is_active ? 'Activo' : 'Inactivo' }}</span>
                <span class="text-xs bg-stone-100 text-stone-500 px-2 py-0.5">Orden: {{ $banner->order }}</span>
            </div>
        </div>
        <div class="flex flex-col gap-1">
            <a href="{{ route('admin.banners.edit', $banner) }}" class="text-xs text-copper-500 hover:text-copper-600">Editar</a>
            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-stone-400 hover:text-red-500">Eliminar</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white border border-stone-100 p-12 text-center text-stone-400">No hay banners. El sitio mostrará el hero estático.</div>
    @endforelse
</div>
@endsection
