@extends('layouts.admin')
@section('title', 'Páginas legales')
@section('content')
<div class="space-y-3">
    @foreach($pages as $key => $page)
    <div class="bg-white border border-stone-100 shadow-sm p-4 flex items-center justify-between">
        <div>
            <p class="font-medium text-stone-700">{{ $page['title'] }}</p>
            <p class="text-xs text-stone-400">/{{ $key }}</p>
        </div>
        <a href="{{ route('admin.legal.edit', $key) }}" class="btn-copper-outline py-1.5 px-4 text-xs">Editar contenido</a>
    </div>
    @endforeach
</div>
@endsection
