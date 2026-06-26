@extends('layouts.admin')
@section('title', 'Categorías')
@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-stone-500 text-sm">{{ $categories->total() }} categorías</p>
    <a href="{{ route('admin.categories.create') }}" class="btn-copper">+ Nueva categoría</a>
</div>
<div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Nombre</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Productos</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Estado</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($categories as $cat)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($cat->image)
                        <img src="{{ asset('storage/' . $cat->image) }}" alt="" class="w-10 h-10 object-cover">
                        @else
                        <div class="w-10 h-10 bg-stone-100"></div>
                        @endif
                        <div>
                            <p class="font-medium text-stone-700">{{ $cat->name }}</p>
                            <p class="text-xs text-stone-400">/tienda?categoria={{ $cat->slug }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-center text-stone-500">{{ $cat->products_count }}</td>
                <td class="px-4 py-3 text-center"><span class="text-xs {{ $cat->is_active ? 'bg-green-100 text-green-600' : 'bg-stone-100 text-stone-500' }} px-2 py-0.5">{{ $cat->is_active ? 'Activa' : 'Inactiva' }}</span></td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="text-copper-500 hover:text-copper-600 text-xs">Editar</a>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-stone-400 hover:text-red-500 text-xs">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-12 text-center text-stone-400">No hay categorías.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-4 border-t border-stone-100">{{ $categories->links() }}</div>
</div>
@endsection
