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

    <div class="bg-white border border-stone-100 shadow-sm p-6">
        @if(in_array($key, \App\Models\LegalPage::ALWAYS_VISIBLE_KEYS, true))
        <p class="text-sm text-stone-500">Esta página es de aceptación obligatoria en el checkout, así que siempre queda visible en el sitio y no se puede desactivar.</p>
        @else
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $legalPage->is_active ?? true) ? 'checked' : '' }} class="text-copper-500">
            <span class="text-sm text-stone-600">Página visible en el sitio</span>
        </label>
        <p class="text-xs text-stone-400 mt-1">Si la desactivás, el enlace desaparece del sitio y la página deja de ser accesible.</p>
        @endif
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-copper">Guardar</button>
        <a href="{{ route('admin.legal.index') }}" class="btn-stone">Volver</a>
    </div>
</form>
</div>
@endsection
