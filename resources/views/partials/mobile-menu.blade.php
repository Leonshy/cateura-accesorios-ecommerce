<div class="flex flex-col h-full">
    <div class="flex items-center justify-between px-4 py-4 border-b border-stone-100">
        <img src="{{ media_url(\App\Models\SiteSetting::get('site_logo'), asset('assets/brand/logo-horizontal.png')) }}" alt="Cateura Accesorios" class="h-8 w-auto">
        <button @click="mobileMenuOpen = false" class="p-2 text-stone-500" aria-label="Cerrar menú">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="flex-1 px-4 py-6 overflow-y-auto">
        <ul class="space-y-1">
            <li><a href="{{ route('home') }}" class="block py-3 text-stone-700 font-medium border-b border-stone-100">Inicio</a></li>
            <li x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex justify-between items-center py-3 text-stone-700 font-medium border-b border-stone-100">
                    Tienda <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" class="pl-4 py-2 space-y-2">
                    @foreach(\App\Models\Category::active()->orderBy('order')->get() as $cat)
                    <a href="{{ route('shop.index', ['categoria' => $cat->slug]) }}" class="block py-2 text-stone-600 text-sm">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </li>
            <li><a href="{{ route('artisans.index') }}" class="block py-3 text-stone-700 font-medium border-b border-stone-100">Artesanas</a></li>
            <li><a href="{{ route('posts.index') }}" class="block py-3 text-stone-700 font-medium border-b border-stone-100">Noticias</a></li>
            <li><a href="{{ route('about') }}" class="block py-3 text-stone-700 font-medium border-b border-stone-100">Nosotros</a></li>
            <li><a href="{{ route('contact') }}" class="block py-3 text-stone-700 font-medium">Contacto</a></li>
        </ul>
    </nav>
    <div class="px-4 py-4 border-t border-stone-100 space-y-3">
        @auth
        <a href="{{ route('account.index') }}" class="btn-stone w-full text-center">Mi cuenta</a>
        @else
        <a href="{{ route('login') }}" class="btn-copper-outline w-full text-center">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="btn-copper w-full text-center">Registrarse</a>
        @endauth
    </div>
</div>
