@extends('layouts.admin')
@section('title', 'Noticias y eventos')
@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-stone-500 text-sm">{{ $posts->total() }} publicaciones</p>
    <a href="{{ route('admin.posts.create') }}" class="btn-copper">+ Nueva publicación</a>
</div>
<div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Título</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Tipo</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Estado</th>
                <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-stone-500">Publicado</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($posts as $post)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3">
                    <p class="font-medium text-stone-700">{{ $post->title }}</p>
                    <p class="text-xs text-stone-400">/noticias/{{ $post->slug }}</p>
                </td>
                <td class="px-4 py-3 text-center"><span class="text-xs bg-stone-100 text-stone-600 px-2 py-0.5 capitalize">{{ $post->type }}</span></td>
                <td class="px-4 py-3 text-center"><span class="text-xs {{ $post->status === 'publicado' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }} px-2 py-0.5">{{ $post->status === 'publicado' ? 'Publicado' : 'Borrador' }}</span></td>
                <td class="px-4 py-3 text-right text-xs text-stone-400">{{ $post->published_at?->format('d/m/Y') ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-copper-500 hover:text-copper-600 text-xs">Editar</a>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-stone-400 hover:text-red-500 text-xs">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-stone-400">No hay publicaciones.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-4 border-t border-stone-100">{{ $posts->links() }}</div>
</div>
@endsection
