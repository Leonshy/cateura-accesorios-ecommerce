@extends('layouts.app')
@section('title', 'Contacto')
@section('content')
<div class="bg-stone-50 border-b border-stone-200 py-10">
    <div class="max-w-7xl mx-auto px-4">
        <p class="section-subtitle mb-2">Estamos para ayudarte</p>
        <h1 class="section-title">Contacto</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-16">
    <div class="grid md:grid-cols-2 gap-16">
        {{-- Info --}}
        <div>
            <h2 class="font-display text-2xl text-stone-800 mb-8">Información de contacto</h2>
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-copper-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-copper-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-stone-700 mb-1">Dirección</p>
                        <p class="text-stone-500 text-sm">Taller Productivo 43 Proyectadas c/ Capitán Figari<br>Barrio San Cayetano, Bañado Sur<br>Asunción, Paraguay</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-copper-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-copper-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-stone-700 mb-1">Teléfono / WhatsApp</p>
                        <a href="tel:+595981877315" class="text-copper-500 hover:text-copper-600 transition-colors">0981 877 315</a>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-copper-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-copper-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-stone-700 mb-1">Correo</p>
                        <a href="mailto:asomujeresunidas22@gmail.com" class="text-copper-500 hover:text-copper-600 transition-colors">asomujeresunidas22@gmail.com</a>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-copper-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-copper-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-stone-700 mb-1">Redes sociales</p>
                        <a href="https://www.instagram.com/cateurapy" target="_blank" rel="noopener" class="text-copper-500 hover:text-copper-600 transition-colors">@cateurapy</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div>
            <h2 class="font-display text-2xl text-stone-800 mb-8">Envianos un mensaje</h2>
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 mb-6 text-sm">{{ session('success') }}</div>
            @endif
            <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-stone-500 mb-2" for="name">Nombre *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input-cateura px-4 py-3">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-stone-500 mb-2" for="email">Correo *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="input-cateura px-4 py-3">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-500 mb-2" for="phone">Teléfono</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input-cateura px-4 py-3">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-500 mb-2" for="subject">Asunto</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="input-cateura px-4 py-3">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-500 mb-2" for="message">Mensaje *</label>
                    <textarea id="message" name="message" rows="5" required class="input-cateura px-4 py-3">{{ old('message') }}</textarea>
                    @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-copper w-full">Enviar mensaje</button>
            </form>
        </div>
    </div>
</div>
@endsection
