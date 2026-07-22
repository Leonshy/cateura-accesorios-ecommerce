@extends('layouts.admin')
@section('title', 'Integraciones de pago')
@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @foreach($paymentMethods as $method)
    <form action="{{ route('admin.settings.payment', $method) }}" method="POST">
        @csrf @method('PATCH')
        <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h3 class="font-medium text-stone-700">{{ $method->name }}</h3>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $method->is_active ? 'checked' : '' }} class="text-copper-500">
                    <span class="text-sm text-stone-600">Activo</span>
                </label>
            </div>
            @if($method->key !== 'transferencia')
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="sandbox" value="1" {{ $method->sandbox ? 'checked' : '' }} class="text-copper-500">
                <span class="text-sm text-stone-600">Modo sandbox (pruebas)</span>
            </label>
            @foreach($method->credentials ?? [] as $key => $value)
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                <input type="text" name="credentials[{{ $key }}]" value="{{ $value }}" class="input-cateura border p-2 w-full font-mono text-xs">
            </div>
            @endforeach
            @if($method->key === 'pagopar')
            <div class="bg-stone-50 border border-stone-100 p-3 text-xs text-stone-500 space-y-1">
                <p class="font-medium text-stone-600">Configurá estas URLs en tu panel de Pagopar:</p>
                <p>Webhook: <code class="text-copper-600">{{ url('/checkout/webhooks/pagopar') }}</code></p>
                <p>Retorno: <code class="text-copper-600">{{ url('/checkout/pagopar/retorno/{hash}') }}</code></p>
            </div>
            @endif
            @if($method->key === 'bancard')
            <div class="bg-stone-50 border border-stone-100 p-3 text-xs text-stone-500 space-y-1">
                <p class="font-medium text-stone-600">Configurá esta URL de confirmación en tu panel de Bancard:</p>
                <p>Webhook: <code class="text-copper-600">{{ url('/checkout/webhooks/bancard') }}</code></p>
            </div>
            @endif
            @endif
            <div class="flex justify-end">
                <button type="submit" class="btn-copper py-2 px-4 text-xs">Guardar</button>
            </div>
        </div>
    </form>
    @endforeach
</div>
@endsection
