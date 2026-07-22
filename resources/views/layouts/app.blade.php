<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cateura Accesorios') — Diseño artesanal que transforma historias</title>
    <meta name="description" content="@yield('meta_description', 'Accesorios, piezas decorativas y prendas creadas por artesanas del Bañado Sur a partir de materiales reciclados.')">
    @stack('meta')
    @if($favicon = \App\Models\SiteSetting::get('site_favicon'))
    <link rel="icon" href="{{ media_url($favicon) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(\App\Models\SiteSetting::get('ga_active') && \App\Models\SiteSetting::get('google_analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\SiteSetting::get('google_analytics_id') }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ \App\Models\SiteSetting::get('google_analytics_id') }}');</script>
    @endif
    @if(\App\Models\SiteSetting::get('pixel_active') && \App\Models\SiteSetting::get('meta_pixel_id'))
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ \App\Models\SiteSetting::get('meta_pixel_id') }}');fbq('track','PageView');</script>
    @endif
</head>
<body class="bg-white font-sans antialiased" x-data="{ mobileMenuOpen: false, cartOpen: false, searchOpen: false }">

@include('partials.header')

<div id="cart-overlay" class="cart-overlay" :class="{ 'open': cartOpen }" @click="cartOpen = false"></div>
@include('partials.cart-sidebar')

<div id="mobile-menu" class="mobile-menu" :class="{ 'open': mobileMenuOpen }">
    @include('partials.mobile-menu')
</div>

<div id="search-overlay" x-show="searchOpen" x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 z-50 flex items-start justify-center pt-24" @click.self="searchOpen = false" style="display:none;">
    <div class="bg-white w-full max-w-2xl mx-4 p-6 shadow-2xl">
        <form action="{{ route('shop.index') }}" method="GET" class="flex gap-3">
            <input type="text" name="q" placeholder="Buscar productos..." class="input-cateura flex-1 px-4 py-3 text-base" autofocus value="{{ request('q') }}">
            <button type="submit" class="btn-copper px-6 py-3">Buscar</button>
        </form>
    </div>
</div>

<main>
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed top-28 md:top-40 right-4 z-50 bg-green-600 text-white px-5 py-3 shadow-lg text-sm flex items-center gap-3 max-w-sm" style="display:none;">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span class="flex-1">{{ session('success') }}</span>
        @if(session('success_link'))
        <a href="{{ session('success_link') }}" class="underline decoration-white/50 hover:decoration-white font-medium whitespace-nowrap flex-shrink-0">{{ session('success_link_label', 'Ver') }}</a>
        @endif
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed top-28 md:top-40 right-4 z-50 bg-red-600 text-white px-5 py-3 shadow-lg text-sm flex items-center gap-3 max-w-sm" style="display:none;">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm1-4a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @yield('content')
</main>

@include('partials.footer')

@if(\App\Models\SiteSetting::get('whatsapp_active', '1'))
<a href="https://wa.me/{{ preg_replace('/\D/', '', \App\Models\SiteSetting::get('site_whatsapp', '0981877315')) }}?text={{ urlencode(\App\Models\SiteSetting::get('whatsapp_message', 'Hola, quiero consultar sobre los productos de Cateura Accesorios.')) }}"
   target="_blank" rel="noopener noreferrer" class="whatsapp-btn" title="Contactar por WhatsApp">
    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>
@endif

@stack('scripts')
</body>
</html>
