@extends('layouts.app')
@section('title', 'Mis direcciones — Cateura Accesorios')
@section('content')
<section class="py-12">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('account.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="section-title">Mis direcciones</h1>
        </div>
        @forelse($addresses as $address)
        <div class="bg-white border border-stone-100 p-5 mb-3">
            <p class="font-medium text-stone-700">{{ $address->label ?? 'Casa' }}</p>
            <p class="text-sm text-stone-500 mt-1">{{ $address->address }}</p>
            <p class="text-sm text-stone-500">{{ $address->city }}, {{ $address->department }}</p>
        </div>
        @empty
        <div class="bg-white border border-stone-100 p-8 text-center text-stone-400">
            <p class="mb-2">No tenés direcciones guardadas.</p>
            <p class="text-xs">Al finalizar una compra, podés guardar tu dirección para próximas compras.</p>
        </div>
        @endforelse
    </div>
</section>
@endsection
