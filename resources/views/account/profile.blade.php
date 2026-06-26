@extends('layouts.app')
@section('title', 'Mi perfil — Cateura Accesorios')
@section('content')
<section class="py-12">
    <div class="container mx-auto px-4 max-w-xl">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('account.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="section-title">Mi perfil</h1>
        </div>
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm mb-5">{{ session('success') }}</div>
        @endif
        <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')
            <div class="bg-white border border-stone-100 p-6 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="input-cateura border p-2 w-full">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}" disabled class="input-cateura border p-2 w-full bg-stone-50 text-stone-400 cursor-not-allowed">
                    <p class="text-xs text-stone-400 mt-0.5">El email no puede modificarse desde aquí.</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->profile?->phone) }}" class="input-cateura border p-2 w-full" placeholder="+595 981 000 000">
                </div>
            </div>
            <button type="submit" class="btn-copper">Guardar cambios</button>
        </form>
    </div>
</section>
@endsection
