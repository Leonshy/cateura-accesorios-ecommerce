@extends('layouts.app')
@section('title', 'Finalizar compra — Cateura Accesorios')
@section('content')
<section class="py-12">
    <div class="container mx-auto px-4">
        <h1 class="section-title mb-8">Finalizar compra</h1>
        @if(!$cart || !$cart->items->count())
        <div class="text-center py-16 text-stone-400">
            <p class="mb-4">Tu carrito está vacío.</p>
            <a href="{{ route('shop.index') }}" class="btn-copper">Ver productos</a>
        </div>
        @else
        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-stone-100 p-6 space-y-4">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Datos de contacto</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-stone-600 mb-1">Nombre *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" required class="input-cateura border p-2 w-full">
                                @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-stone-600 mb-1">Email *</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" required class="input-cateura border p-2 w-full">
                                @error('customer_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-stone-600 mb-1">Teléfono</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="input-cateura border p-2 w-full">
                        </div>
                    </div>

                    <div class="bg-white border border-stone-100 p-6 space-y-4">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Dirección de envío</h2>
                        <div>
                            <label class="block text-xs font-medium text-stone-600 mb-1">Dirección completa *</label>
                            <input type="text" name="shipping_address" value="{{ old('shipping_address') }}" required placeholder="Calle, número, barrio" class="input-cateura border p-2 w-full">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-stone-600 mb-1">Ciudad *</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required class="input-cateura border p-2 w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-stone-600 mb-1">Departamento</label>
                                <input type="text" name="shipping_department" value="{{ old('shipping_department', 'Central') }}" class="input-cateura border p-2 w-full">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-stone-600 mb-1">Notas de entrega (opcional)</label>
                            <textarea name="shipping_notes" rows="2" placeholder="Referencia, instrucciones para el repartidor..." class="input-cateura border p-2 w-full">{{ old('shipping_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="bg-white border border-stone-100 p-6 space-y-4">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Método de pago</h2>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 border border-stone-200 cursor-pointer hover:border-copper-300 transition-colors has-[:checked]:border-copper-500 has-[:checked]:bg-copper-50">
                                <input type="radio" name="payment_method" value="transferencia" {{ old('payment_method', 'transferencia') === 'transferencia' ? 'checked' : '' }} class="mt-0.5 text-copper-500">
                                <div>
                                    <p class="font-medium text-sm text-stone-700">Transferencia bancaria</p>
                                    <p class="text-xs text-stone-400 mt-0.5">Recibirás los datos para transferir al confirmar el pedido. Tu pedido se procesará al verificar el pago.</p>
                                </div>
                            </label>
                            @if(\App\Models\PaymentMethod::where('key', 'pagopar')->where('is_active', true)->exists())
                            <label class="flex items-start gap-3 p-3 border border-stone-200 cursor-pointer hover:border-copper-300 transition-colors has-[:checked]:border-copper-500 has-[:checked]:bg-copper-50">
                                <input type="radio" name="payment_method" value="pagopar" {{ old('payment_method') === 'pagopar' ? 'checked' : '' }} class="mt-0.5 text-copper-500">
                                <div>
                                    <p class="font-medium text-sm text-stone-700">Pagopar</p>
                                    <p class="text-xs text-stone-400 mt-0.5">Pago en línea con tarjeta o billetera digital (Pagopar).</p>
                                </div>
                            </label>
                            @endif
                        </div>
                        @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-white border border-stone-100 p-6">
                        <label class="block text-xs font-medium text-stone-600 mb-1">Notas del pedido (opcional)</label>
                        <textarea name="notes" rows="2" class="input-cateura border p-2 w-full">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div>
                    <div class="bg-white border border-stone-100 p-6 sticky top-4">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3 mb-4">Resumen</h2>
                        <div class="space-y-2 mb-4 text-sm">
                            @foreach($cart->items as $item)
                            <div class="flex justify-between text-stone-600">
                                <span class="truncate flex-1 mr-2">{{ $item->product_name }} ×{{ $item->quantity }}</span>
                                <span class="flex-shrink-0">Gs. {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="border-t border-stone-100 pt-3 mb-5">
                            <div class="flex justify-between font-bold text-stone-800">
                                <span>Total</span>
                                <span class="text-copper-600">Gs. {{ number_format($cart->items->sum('subtotal'), 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <button type="submit" class="btn-copper w-full">Confirmar pedido</button>
                        <p class="text-xs text-stone-400 text-center mt-3">Al confirmar aceptás nuestros <a href="{{ route('legal.terminos') }}" class="underline hover:text-stone-600" target="_blank">términos y condiciones</a>.</p>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </div>
</section>
@endsection
