@php
    $cartCount = 0;
    try {
        if (auth()->check()) {
            $userCart = \App\Models\Cart::where('user_id', auth()->id())->with('items')->first();
        } else {
            $userCart = \App\Models\Cart::where('session_id', session()->getId())->with('items')->first();
        }
        $cartCount = $userCart ? $userCart->items->sum('quantity') : 0;
    } catch (\Exception $e) { $cartCount = 0; }
@endphp

<header class="sticky top-0 z-30 bg-white border-b border-stone-100 shadow-sm">
    {{-- Top bar --}}
    <div class="bg-stone-800 text-white text-xs py-2 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <span>Envíos a todo Paraguay · <a href="tel:+595981877315" class="hover:text-copper-300 transition-colors">0981 877 315</a></span>
            <div class="flex items-center gap-4">
                <a href="https://www.instagram.com/cateurapy" target="_blank" rel="noopener" class="hover:text-copper-300 transition-colors">@cateurapy</a>
                @auth
                    <a href="{{ route('account.index') }}" class="hover:text-copper-300 transition-colors">Mi cuenta</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button type="submit" class="hover:text-copper-300 transition-colors">Salir</button></form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-copper-300 transition-colors">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="hover:text-copper-300 transition-colors">Registrarse</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Main header --}}
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-20 md:h-28">
            {{-- Mobile: hamburger --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-stone-600 hover:text-copper-500 transition-colors" aria-label="Menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/brand/logo-horizontal.png') }}" alt="Cateura Accesorios" class="h-14 md:h-20 w-auto">
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-8" x-data="{ shopOpen: false }">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'text-copper-500' : '' }}">Inicio</a>

                <div class="relative" @mouseenter="shopOpen = true" @mouseleave="shopOpen = false">
                    <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.*') ? 'text-copper-500' : '' }}">
                        Tienda
                        <svg class="inline w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <div x-show="shopOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute top-full left-0 w-56 bg-white shadow-xl border border-stone-100 py-2 z-50" style="display:none;">
                        @foreach(\App\Models\Category::active()->orderBy('order')->get() as $cat)
                        <a href="{{ route('shop.index', ['categoria' => $cat->slug]) }}" class="block px-4 py-2 text-sm text-stone-600 hover:bg-copper-50 hover:text-copper-600 transition-colors">{{ $cat->name }}</a>
                        @endforeach
                        <div class="border-t border-stone-100 mt-1 pt-1">
                            <a href="{{ route('shop.index', ['nuevo' => '1']) }}" class="block px-4 py-2 text-sm text-copper-500 hover:bg-copper-50 transition-colors font-medium">✦ Nuevos productos</a>
                            <a href="{{ route('shop.index', ['destacado' => '1']) }}" class="block px-4 py-2 text-sm text-stone-600 hover:bg-copper-50 hover:text-copper-600 transition-colors">Destacados</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('artisans.index') }}" class="nav-link {{ request()->routeIs('artisans.*') ? 'text-copper-500' : '' }}">Artesanas</a>
                <a href="{{ route('posts.index') }}" class="nav-link {{ request()->routeIs('posts.*') ? 'text-copper-500' : '' }}">Noticias</a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'text-copper-500' : '' }}">Nosotros</a>
                <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'text-copper-500' : '' }}">Contacto</a>
            </nav>

            {{-- Icons --}}
            <div class="flex items-center gap-2">
                <button @click="searchOpen = !searchOpen" class="p-2 text-stone-600 hover:text-copper-500 transition-colors" aria-label="Buscar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @auth
                <a href="{{ route('account.index') }}" class="hidden md:flex p-2 text-stone-600 hover:text-copper-500 transition-colors" aria-label="Mi cuenta">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                <a href="{{ route('account.wishlist') }}" class="hidden md:flex p-2 text-stone-600 hover:text-copper-500 transition-colors" aria-label="Lista de deseos">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </a>
                @endauth
                <button @click="cartOpen = !cartOpen" class="relative p-2 text-stone-600 hover:text-copper-500 transition-colors" aria-label="Carrito">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @if($cartCount > 0)
                    <span class="absolute -top-1 -right-1 bg-copper-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-medium">{{ $cartCount }}</span>
                    @endif
                </button>
            </div>
        </div>
    </div>
</header>
