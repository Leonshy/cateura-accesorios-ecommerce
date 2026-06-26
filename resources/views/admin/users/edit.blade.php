@extends('layouts.admin')
@section('title', 'Editar rol de usuario')
@section('content')
<div class="max-w-md">
<div class="bg-white border border-stone-100 shadow-sm p-6 mb-5">
    <div class="flex items-center gap-4 pb-4 border-b border-stone-100 mb-4">
        <div class="w-12 h-12 bg-copper-100 rounded-full flex items-center justify-center text-copper-600 font-bold text-xl">{{ substr($user->name, 0, 1) }}</div>
        <div>
            <p class="font-medium text-stone-700">{{ $user->name }}</p>
            <p class="text-sm text-stone-400">{{ $user->email }}</p>
        </div>
    </div>
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf @method('PATCH')
        <div class="space-y-3 mb-4">
            <p class="text-xs font-medium text-stone-600 uppercase tracking-wider">Roles asignados</p>
            @foreach(['admin', 'editor', 'vendedor'] as $role)
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="roles[]" value="{{ $role }}" {{ $user->hasRole($role) ? 'checked' : '' }} class="text-copper-500 border-stone-300">
                <span class="text-sm text-stone-700 capitalize">{{ $role }}</span>
            </label>
            @endforeach
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-copper py-2 px-4 text-xs">Guardar roles</button>
            <a href="{{ route('admin.users.index') }}" class="btn-stone py-2 px-4 text-xs">Cancelar</a>
        </div>
    </form>
</div>
</div>
@endsection
