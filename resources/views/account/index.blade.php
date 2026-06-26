@extends('layouts.app')
@section('title', 'Mi cuenta — Cateura Accesorios')
@section('content')
<section class="py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="section-subtitle mb-1">Bienvenida</p>
                <h1 class="section-title">{{ auth()->user()->name }}</h1>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-stone-400 hover:text-stone-600 transition-colors">Cerrar sesión</button>
            </form>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['route' => 'account.orders', 'icon' => '📦', 'label' => 'Mis pedidos', 'count' => $recentOrders->count()],
                ['route' => 'account.wishlist', 'icon' => '❤️', 'label' => 'Lista de deseos', 'count' => $wishlistCount],
                ['route' => 'account.addresses', 'icon' => '📍', 'label' => 'Direcciones', 'count' => null],
                ['route' => 'account.profile', 'icon' => '👤', 'label' => 'Mi perfil', 'count' => null],
            ] as $item)
            <a href="{{ route($item['route']) }}" class="bg-white border border-stone-100 p-6 text-center hover:border-copper-300 hover:shadow-sm transition-all group">
                <div class="text-3xl mb-3">{{ $item['icon'] }}</div>
                <p class="font-medium text-stone-700 group-hover:text-copper-500 transition-colors">{{ $item['label'] }}</p>
                @if($item['count'] !== null)
                <p class="text-2xl font-bold text-copper-500 mt-1">{{ $item['count'] }}</p>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
