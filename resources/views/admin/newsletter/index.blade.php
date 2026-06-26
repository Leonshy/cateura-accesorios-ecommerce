@extends('layouts.admin')
@section('title', 'Suscriptores newsletter')
@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-stone-500 text-sm">{{ $subscribers->total() }} suscriptores</p>
    <a href="{{ route('admin.newsletter.index') }}?export=csv" class="btn-stone text-xs py-2 px-4">Exportar CSV</a>
</div>
<div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Email</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Estado</th>
                <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-stone-500">Fecha</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($subscribers as $sub)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 text-stone-700">{{ $sub->email }}</td>
                <td class="px-4 py-3 text-center"><span class="text-xs {{ $sub->is_active ? 'bg-green-100 text-green-600' : 'bg-stone-100 text-stone-500' }} px-2 py-0.5">{{ $sub->is_active ? 'Activo' : 'Cancelado' }}</span></td>
                <td class="px-4 py-3 text-right text-xs text-stone-400">{{ $sub->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-12 text-center text-stone-400">No hay suscriptores.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-4 border-t border-stone-100">{{ $subscribers->links() }}</div>
</div>
@endsection
