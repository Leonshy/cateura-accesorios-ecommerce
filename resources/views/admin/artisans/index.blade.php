@extends('layouts.admin')
@section('title', 'Artesanas')
@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-stone-500 text-sm">{{ $artisans->total() }} artesanas registradas</p>
    <a href="{{ route('admin.artisans.create') }}" class="btn-copper">+ Nueva artesana</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($artisans as $artisan)
    <div class="bg-white border border-stone-100 shadow-sm p-4 flex items-center gap-4">
        <div class="w-16 h-16 rounded-full overflow-hidden bg-stone-100 flex-shrink-0">
            @if($artisan->photo)
            <img src="{{ asset('storage/' . $artisan->photo) }}" alt="{{ $artisan->name }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex items-center justify-center text-stone-400 text-2xl font-display">{{ substr($artisan->name, 0, 1) }}</div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-medium text-stone-700 truncate">{{ $artisan->name }}</p>
            <p class="text-xs text-stone-400">{{ $artisan->specialty }}</p>
            <span class="{{ $artisan->is_active ? 'bg-green-100 text-green-600' : 'bg-stone-100 text-stone-500' }} text-xs px-2 py-0.5 mt-1 inline-block">{{ $artisan->is_active ? 'Activa' : 'Inactiva' }}</span>
        </div>
        <div class="flex flex-col gap-1">
            <a href="{{ route('admin.artisans.edit', $artisan) }}" class="text-xs text-copper-500 hover:text-copper-600">Editar</a>
            <form action="{{ route('admin.artisans.destroy', $artisan) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-stone-400 hover:text-red-500">Eliminar</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 py-12 text-center text-stone-400">No hay artesanas registradas.</div>
    @endforelse
</div>
<div class="mt-4">{{ $artisans->links() }}</div>
@endsection
