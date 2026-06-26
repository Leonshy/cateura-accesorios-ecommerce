@extends('layouts.admin')
@section('title', 'Mensajes de contacto')
@section('content')
<div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Remitente</th>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Asunto</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Fecha</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Estado</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($messages as $msg)
            <tr class="hover:bg-stone-50 {{ !$msg->read_at ? 'font-medium' : '' }}">
                <td class="px-4 py-3">
                    <p class="text-stone-700">{{ $msg->name }}</p>
                    <p class="text-xs text-stone-400">{{ $msg->email }}</p>
                </td>
                <td class="px-4 py-3 text-stone-600">{{ Str::limit($msg->subject, 40) }}</td>
                <td class="px-4 py-3 text-center text-xs text-stone-400">{{ $msg->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-center"><span class="text-xs {{ $msg->read_at ? 'bg-stone-100 text-stone-500' : 'bg-blue-100 text-blue-600' }} px-2 py-0.5">{{ $msg->read_at ? 'Leído' : 'Nuevo' }}</span></td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.contacts.show', $msg) }}" class="text-copper-500 hover:text-copper-600 text-xs">Ver</a>
                        <form action="{{ route('admin.contacts.destroy', $msg) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-stone-400 hover:text-red-500 text-xs">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-stone-400">No hay mensajes.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-4 border-t border-stone-100">{{ $messages->links() }}</div>
</div>
@endsection
