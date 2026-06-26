@extends('layouts.admin')
@section('title', 'Usuarios')
@section('content')
<div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-stone-500">Usuario</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Rol</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Pedidos</th>
                <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-stone-500">Registrado</th>
                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-stone-500">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($users as $user)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3">
                    <p class="font-medium text-stone-700">{{ $user->name }}</p>
                    <p class="text-xs text-stone-400">{{ $user->email }}</p>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-0.5
                        {{ $user->getHighestRole() === 'admin' ? 'bg-copper-100 text-copper-700' :
                           ($user->getHighestRole() === 'editor' ? 'bg-blue-100 text-blue-700' :
                           ($user->getHighestRole() === 'vendedor' ? 'bg-purple-100 text-purple-700' :
                           'bg-stone-100 text-stone-500')) }}">
                        {{ ucfirst($user->getHighestRole()) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-stone-500">{{ $user->orders_count }}</td>
                <td class="px-4 py-3 text-right text-xs text-stone-400">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-copper-500 hover:text-copper-600 text-xs">Editar rol</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-stone-400">No hay usuarios.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-4 border-t border-stone-100">{{ $users->links() }}</div>
</div>
@endsection
