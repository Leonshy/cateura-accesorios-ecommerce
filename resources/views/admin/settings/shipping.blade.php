@extends('layouts.admin')
@section('title', 'Métodos de envío')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.settings.shipping.update') }}" method="POST">
    @csrf
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm mb-5">{{ session('success') }}</div>
    @endif
    <div class="space-y-4">
        @forelse($methods as $method)
        <div class="bg-white border border-stone-100 shadow-sm p-5">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer flex-shrink-0">
                    <input type="checkbox" name="methods[{{ $method->id }}][is_active]" value="1" {{ $method->is_active ? 'checked' : '' }} class="text-copper-500">
                    <span class="text-sm text-stone-600">Activo</span>
                </label>
                <div class="flex-1">
                    <input type="text" name="methods[{{ $method->id }}][name]" value="{{ $method->name }}" placeholder="Nombre del método" class="input-cateura border p-2 w-full text-sm">
                </div>
                <div class="w-40">
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-stone-400">Gs.</span>
                        <input type="number" name="methods[{{ $method->id }}][cost]" value="{{ $method->cost }}" min="0" class="input-cateura border p-2 w-full text-sm">
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-stone-400 text-sm py-8 text-center">No hay métodos de envío configurados.</p>
        @endforelse
    </div>
    <div class="mt-5">
        <button type="submit" class="btn-copper">Guardar métodos de envío</button>
    </div>
</form>
</div>
@endsection
