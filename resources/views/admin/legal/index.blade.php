@extends('layouts.admin')
@section('title', 'Páginas legales')
@section('content')
<div class="space-y-3">
    @foreach($pages as $key => $page)
    @php($legalPage = $legalPages->get($key))
    @php($isActive = $legalPage?->is_active ?? true)
    @php($toggleable = !in_array($key, \App\Models\LegalPage::ALWAYS_VISIBLE_KEYS, true))
    <div class="bg-white border border-stone-100 shadow-sm p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div>
                <p class="font-medium text-stone-700">{{ $page['title'] }}</p>
                <p class="text-xs text-stone-400">/{{ $key }}</p>
            </div>
            @if($toggleable)
                @if($isActive)
                <span class="text-xs px-2 py-0.5 bg-green-50 text-green-700 border border-green-200">Visible</span>
                @else
                <span class="text-xs px-2 py-0.5 bg-stone-100 text-stone-500 border border-stone-200">Oculta</span>
                @endif
            @else
            <span class="text-xs px-2 py-0.5 bg-copper-50 text-copper-600 border border-copper-200">Siempre visible</span>
            @endif
        </div>
        <a href="{{ route('admin.legal.edit', $key) }}" class="btn-copper-outline py-1.5 px-4 text-xs">Editar contenido</a>
    </div>
    @endforeach
</div>
@endsection
