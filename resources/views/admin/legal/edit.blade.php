@extends('layouts.admin')
@section('title', 'Editar página legal')
@section('content')
<div class="max-w-3xl">
<form action="{{ route('admin.legal.update', $key) }}" method="POST" class="space-y-5">
    @csrf @method('PATCH')
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Título</label>
            <input type="text" name="title" value="{{ old('title', $legalPage->title ?? '') }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Contenido (HTML permitido)</label>
            <textarea name="content" rows="20" class="input-cateura border p-2 w-full font-mono text-xs">{{ old('content', $legalPage->content ?? '') }}</textarea>
        </div>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-copper">Guardar</button>
        <a href="{{ route('admin.legal.index') }}" class="btn-stone">Volver</a>
    </div>
</form>
</div>
@endsection
