@extends('layouts.admin')
@section('title', 'Reseñas de producto')
@section('content')

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex border border-stone-200 overflow-hidden text-sm">
        <a href="{{ route('admin.reviews.index', ['estado' => 'pendiente']) }}"
           class="px-4 py-2 transition-colors {{ $status === 'pendiente' ? 'bg-stone-800 text-white' : 'text-stone-600 hover:bg-stone-50' }}">
            Pendientes @if($pendingCount > 0)<span class="ml-1 text-copper-400">({{ $pendingCount }})</span>@endif
        </a>
        <a href="{{ route('admin.reviews.index', ['estado' => 'aprobada']) }}"
           class="px-4 py-2 transition-colors {{ $status === 'aprobada' ? 'bg-stone-800 text-white' : 'text-stone-600 hover:bg-stone-50' }}">
            Aprobadas
        </a>
        <a href="{{ route('admin.reviews.index', ['estado' => 'todas']) }}"
           class="px-4 py-2 transition-colors {{ $status === 'todas' ? 'bg-stone-800 text-white' : 'text-stone-600 hover:bg-stone-50' }}">
            Todas
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Producto</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Autor</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Calificación</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Comentario</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Fecha</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Estado</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($reviews as $review)
            <tr class="hover:bg-stone-50 transition-colors align-top">
                <td class="px-4 py-3">
                    @if($review->product)
                    <a href="{{ route('shop.product', $review->product->slug) }}" target="_blank" class="text-stone-700 hover:text-copper-600">{{ $review->product->name }}</a>
                    @else
                    <span class="text-stone-400">Producto eliminado</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-stone-600">{{ $review->reviewer_name }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-copper-500' : 'text-stone-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </td>
                <td class="px-4 py-3 text-stone-600 max-w-xs">{{ $review->comment ?: '—' }}</td>
                <td class="px-4 py-3 text-center text-xs text-stone-400">{{ $review->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs {{ $review->is_approved ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }} px-2 py-0.5">
                        {{ $review->is_approved ? 'Aprobada' : 'Pendiente' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        @unless($review->is_approved)
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-copper-500 hover:text-copper-600 text-xs">Aprobar</button>
                        </form>
                        @endunless
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('¿Eliminar esta reseña?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-stone-400 hover:text-red-500 text-xs">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-stone-400">No hay reseñas en esta categoría.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-4 border-t border-stone-100">{{ $reviews->links() }}</div>
</div>
@endsection
