<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Panel') · Cateura Accesorios</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 font-sans antialiased" x-data="{ sidebarOpen: true, mobileSidebar: false }">

<div class="flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    <aside class="bg-white border-r border-stone-200 flex-shrink-0 transition-all duration-300 hidden md:flex flex-col"
           :class="sidebarOpen ? 'w-64' : 'w-16'">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-stone-100 h-16">
            <img src="{{ asset('assets/brand/logo-mark.png') }}" alt="C" class="w-8 h-8 flex-shrink-0 object-contain">
            <span x-show="sidebarOpen" class="font-display text-stone-800 font-medium text-sm leading-tight">Cateura<br><span class="text-copper-500 text-xs">Panel Admin</span></span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1">
                <p class="text-xs font-medium text-stone-400 uppercase tracking-widest">Catálogo</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span x-show="sidebarOpen">Productos</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span x-show="sidebarOpen">Categorías</span>
            </a>
            <a href="{{ route('admin.artisans.index') }}" class="admin-nav-link {{ request()->routeIs('admin.artisans*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span x-show="sidebarOpen">Artesanas</span>
            </a>

            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1">
                <p class="text-xs font-medium text-stone-400 uppercase tracking-widest">Ventas</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span x-show="sidebarOpen">Pedidos</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span x-show="sidebarOpen">Clientes</span>
            </a>

            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1">
                <p class="text-xs font-medium text-stone-400 uppercase tracking-widest">Contenido</p>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="admin-nav-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span x-show="sidebarOpen">Noticias/Eventos</span>
            </a>
            <a href="{{ route('admin.banners.index') }}" class="admin-nav-link {{ request()->routeIs('admin.banners*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span x-show="sidebarOpen">Banners</span>
            </a>
            <a href="{{ route('admin.legal.index') }}" class="admin-nav-link {{ request()->routeIs('admin.legal*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-show="sidebarOpen">Páginas legales</span>
            </a>

            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1">
                <p class="text-xs font-medium text-stone-400 uppercase tracking-widest">Comunicación</p>
            </div>
            <a href="{{ route('admin.contacts.index') }}" class="admin-nav-link {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span x-show="sidebarOpen">Mensajes</span>
            </a>
            <a href="{{ route('admin.newsletter.index') }}" class="admin-nav-link {{ request()->routeIs('admin.newsletter*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span x-show="sidebarOpen">Newsletter</span>
            </a>

            @if(auth()->user()->isAdmin())
            <div x-show="sidebarOpen" class="px-3 pt-4 pb-1">
                <p class="text-xs font-medium text-stone-400 uppercase tracking-widest">Configuración</p>
            </div>
            <a href="{{ route('admin.settings.general') }}" class="admin-nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="sidebarOpen">Configuración</span>
            </a>
            <a href="{{ route('admin.settings.integrations') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.integrations') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span x-show="sidebarOpen">Integraciones</span>
            </a>
            @endif
        </nav>

        {{-- User info --}}
        <div class="border-t border-stone-100 px-3 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-copper-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-copper-600 font-medium text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div x-show="sidebarOpen" class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-stone-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-stone-400 capitalize">{{ auth()->user()->getHighestRole() }}</p>
                </div>
            </div>
            <div x-show="sidebarOpen" class="mt-3 flex gap-2">
                <a href="{{ route('home') }}" class="text-xs text-stone-500 hover:text-copper-500 transition-colors">← Sitio</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf<button type="submit" class="text-xs text-stone-500 hover:text-red-500 transition-colors">Salir</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Top bar --}}
        <header class="bg-white border-b border-stone-200 h-16 flex items-center px-6 flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="hidden md:flex p-2 text-stone-400 hover:text-stone-600 transition-colors mr-4" aria-label="Toggle sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="text-lg font-medium text-stone-800">@yield('title', 'Dashboard')</h1>
            <div class="ml-auto flex items-center gap-4">
                @php $pendingOrders = \App\Models\Order::where('status','pendiente')->count(); @endphp
                @if($pendingOrders > 0)
                <a href="{{ route('admin.orders.index', ['estado' => 'pendiente']) }}" class="relative text-stone-400 hover:text-copper-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center">{{ $pendingOrders }}</span>
                </a>
                @endif
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 flex items-center justify-between" style="display:none;">
                <span class="text-sm">{{ session('success') }}</span>
                <button @click="show = false" class="text-green-500 hover:text-green-700">×</button>
            </div>
            @endif
            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 flex items-center justify-between" style="display:none;">
                <span class="text-sm">{{ session('error') }}</span>
                <button @click="show = false" class="text-red-500 hover:text-red-700">×</button>
            </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
