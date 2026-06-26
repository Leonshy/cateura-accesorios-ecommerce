@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white border border-stone-100 shadow-sm p-5 rounded-lg">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-copper-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-copper-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-stone-800">{{ $totalProducts }}</p>
        <p class="text-xs text-stone-400 uppercase tracking-wider mt-1">Productos activos</p>
    </div>
    <div class="bg-white border border-stone-100 shadow-sm p-5 rounded-lg">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-stone-800">{{ $pendingOrders }}</p>
        <p class="text-xs text-stone-400 uppercase tracking-wider mt-1">Pedidos pendientes</p>
    </div>
    <div class="bg-white border border-stone-100 shadow-sm p-5 rounded-lg">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-stone-800">{{ $totalCustomers }}</p>
        <p class="text-xs text-stone-400 uppercase tracking-wider mt-1">Clientes registrados</p>
    </div>
    <div class="bg-white border border-stone-100 shadow-sm p-5 rounded-lg">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-lg font-bold text-stone-800">Gs. {{ number_format($revenue, 0, ',', '.') }}</p>
        <p class="text-xs text-stone-400 uppercase tracking-wider mt-1">Ingresos confirmados</p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-8">
    {{-- Recent orders --}}
    <div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
            <h3 class="font-medium text-stone-700">Pedidos recientes</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-copper-500 hover:text-copper-600">Ver todos →</a>
        </div>
        <div class="divide-y divide-stone-50">
            @forelse($recentOrders as $order)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-stone-700 hover:text-copper-500">{{ $order->order_number }}</a>
                    <p class="text-xs text-stone-400">{{ $order->customer_name }} · {{ $order->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 px-2 py-1">{{ $order->status_label }}</span>
                    <span class="text-sm font-medium text-stone-700">Gs. {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
            @empty
            <p class="px-5 py-8 text-sm text-stone-400 text-center">No hay pedidos aún.</p>
            @endforelse
        </div>
    </div>

    {{-- Low stock --}}
    <div class="bg-white border border-stone-100 shadow-sm rounded-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
            <h3 class="font-medium text-stone-700">Stock bajo <span class="text-red-500 text-xs ml-1">(≤5 unidades)</span></h3>
            <a href="{{ route('admin.products.index') }}" class="text-xs text-copper-500 hover:text-copper-600">Ver productos →</a>
        </div>
        <div class="divide-y divide-stone-50">
            @forelse($lowStock as $product)
            <div class="px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-stone-100 flex-shrink-0 overflow-hidden">
                        <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-sm font-medium text-stone-700 hover:text-copper-500">{{ $product->name }}</a>
                        <p class="text-xs text-stone-400">{{ $product->category?->name }}</p>
                    </div>
                </div>
                <span class="text-sm font-bold {{ $product->stock === 0 ? 'text-red-500' : 'text-yellow-600' }}">{{ $product->stock }} uds.</span>
            </div>
            @empty
            <p class="px-5 py-8 text-sm text-stone-400 text-center">No hay productos con stock bajo.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Quick actions --}}
<div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
    <a href="{{ route('admin.products.create') }}" class="bg-copper-500 text-white p-4 rounded-lg text-center hover:bg-copper-600 transition-colors">
        <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span class="text-xs uppercase tracking-wider">Nuevo producto</span>
    </a>
    <a href="{{ route('admin.orders.index', ['estado' => 'pendiente']) }}" class="bg-yellow-500 text-white p-4 rounded-lg text-center hover:bg-yellow-600 transition-colors">
        <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span class="text-xs uppercase tracking-wider">Pedidos pendientes ({{ $pendingOrders }})</span>
    </a>
    <a href="{{ route('admin.contacts.index') }}" class="bg-blue-500 text-white p-4 rounded-lg text-center hover:bg-blue-600 transition-colors">
        <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <span class="text-xs uppercase tracking-wider">Mensajes ({{ $unreadMessages }})</span>
    </a>
    <a href="{{ route('admin.settings.general') }}" class="bg-stone-700 text-white p-4 rounded-lg text-center hover:bg-stone-800 transition-colors">
        <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span class="text-xs uppercase tracking-wider">Configuración</span>
    </a>
</div>
@endsection
