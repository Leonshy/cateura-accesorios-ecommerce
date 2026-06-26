@extends('layouts.admin')
@section('title', 'Productos')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar producto..." class="border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:border-copper-500">
            <select name="categoria" class="border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:border-copper-500">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-stone-700 text-white px-4 py-2 text-sm hover:bg-stone-800 transition-colors">Filtrar</button>
        </form>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn-copper">+ Nuevo producto</a>
</div>

<div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Producto</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Categoría</th>
                <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-stone-500">Precio</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Stock</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Estado</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($products as $product)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-stone-100 flex-shrink-0 overflow-hidden">
                            <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-medium text-stone-700">{{ $product->name }}</p>
                            <div class="flex gap-1 mt-0.5">
                                @if($product->is_new)<span class="text-xs bg-copper-100 text-copper-600 px-1.5 py-0.5">Nuevo</span>@endif
                                @if($product->is_featured)<span class="text-xs bg-stone-100 text-stone-600 px-1.5 py-0.5">Destacado</span>@endif
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-stone-500">{{ $product->category?->name }}</td>
                <td class="px-4 py-3 text-right font-medium text-stone-700">Gs. {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="{{ $product->stock === 0 ? 'text-red-500 font-bold' : ($product->stock <= 5 ? 'text-yellow-600 font-bold' : 'text-stone-600') }}">{{ $product->stock }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    @if($product->trashed())
                    <span class="bg-red-100 text-red-600 text-xs px-2 py-1">Eliminado</span>
                    @elseif($product->is_active)
                    <span class="bg-green-100 text-green-600 text-xs px-2 py-1">Activo</span>
                    @else
                    <span class="bg-stone-100 text-stone-500 text-xs px-2 py-1">Inactivo</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-stone-400 hover:text-copper-500 transition-colors" title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @if(!$product->trashed())
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-stone-400 hover:text-red-500 transition-colors" title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-stone-400">No se encontraron productos.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-4 border-t border-stone-100">
        {{ $products->links() }}
    </div>
</div>
@endsection
