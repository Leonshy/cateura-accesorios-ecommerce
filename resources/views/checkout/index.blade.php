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
        @php
            $subtotal = $cart->items->sum('subtotal');
            $defaultShippingType = old('shipping_type', $shippingSettings->store_pickup_enabled ? 'pickup' : 'envio');
        @endphp
        <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data"
              x-data="checkoutShipping({{ Js::from($departments) }}, {{ Js::from($subtotal) }}, {{ Js::from(old('address_department', '')) }}, {{ Js::from(old('address_city', '')) }}, {{ Js::from($defaultShippingType) }}, {{ Js::from(old('payment_method', $paymentMethods->first()?->key)) }})"
              x-init="init()">
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
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="0981 234 567" class="input-cateura border p-2 w-full">
                        </div>
                    </div>

                    <div class="bg-white border border-stone-100 p-6 space-y-4" x-show="shippingType === 'envio'">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Dirección de envío</h2>
                        <div>
                            <label class="block text-xs font-medium text-stone-600 mb-1">Dirección completa *</label>
                            <input type="text" name="address_line1" value="{{ old('address_line1') }}" :required="shippingType === 'envio'" placeholder="Calle, número, barrio" class="input-cateura border p-2 w-full">
                            @error('address_line1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-stone-600 mb-1">Departamento *</label>
                                <select name="address_department" x-model="department" @change="onDepartmentChange" :required="shippingType === 'envio'" class="input-cateura border p-2 w-full bg-white">
                                    <option value="">Seleccioná un departamento</option>
                                    <template x-for="dept in departments" :key="dept.id">
                                        <option :value="dept.id" x-text="dept.name"></option>
                                    </template>
                                </select>
                                @error('address_department')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-stone-600 mb-1">Ciudad / Distrito *</label>
                                <select name="address_city" x-model="city" @change="onCityChange" :required="shippingType === 'envio'" :disabled="!department" class="input-cateura border p-2 w-full bg-white">
                                    <option value="">Seleccioná una ciudad</option>
                                    <template x-for="c in cities" :key="c">
                                        <option :value="c" x-text="c"></option>
                                    </template>
                                </select>
                                @error('address_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <p class="text-xs" x-show="quoteLoaded" x-cloak>
                            <span x-show="!shippingUnavailable" class="text-stone-500">
                                Costo de envío: <span class="font-medium text-copper-600" x-text="'Gs. ' + new Intl.NumberFormat('es-PY').format(shippingCost)"></span>
                                <template x-if="deliveryTime"><span> · <span x-text="deliveryTime"></span></span></template>
                            </span>
                            <span x-show="shippingUnavailable" class="text-red-500">No realizamos envíos a esa ciudad por el momento.</span>
                        </p>
                        <div>
                            <label class="block text-xs font-medium text-stone-600 mb-1">Notas de entrega (opcional)</label>
                            <textarea name="address_notes" rows="2" placeholder="Referencia, instrucciones para el repartidor..." class="input-cateura border p-2 w-full">{{ old('address_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="bg-white border border-stone-100 p-6 space-y-4">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Facturación (opcional)</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-stone-600 mb-1">RUC / CI</label>
                                <input type="text" name="billing_ruc" value="{{ old('billing_ruc') }}" class="input-cateura border p-2 w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-stone-600 mb-1">Razón social / Nombre</label>
                                <input type="text" name="billing_name" value="{{ old('billing_name') }}" class="input-cateura border p-2 w-full">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-stone-100 p-6 space-y-4">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Método de envío</h2>
                        <div class="space-y-3">
                            @if($shippingSettings->store_pickup_enabled)
                            <label class="flex items-start justify-between gap-3 p-3 border border-stone-200 cursor-pointer hover:border-copper-300 transition-colors has-[:checked]:border-copper-500 has-[:checked]:bg-copper-50">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="shipping_type" value="pickup" x-model="shippingType" required class="mt-0.5 text-copper-500">
                                    <div>
                                        <p class="font-medium text-sm text-stone-700">Retiro en tienda</p>
                                        <p class="text-xs text-stone-400 mt-0.5">Retirá tu pedido en nuestro local</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-stone-600 flex-shrink-0">Gratis</span>
                            </label>
                            @endif
                            @if($shippingSettings->envio_propio_enabled)
                            <label class="flex items-start justify-between gap-3 p-3 border border-stone-200 cursor-pointer hover:border-copper-300 transition-colors has-[:checked]:border-copper-500 has-[:checked]:bg-copper-50">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="shipping_type" value="envio" x-model="shippingType" required class="mt-0.5 text-copper-500">
                                    <div>
                                        <p class="font-medium text-sm text-stone-700">Envío a domicilio</p>
                                        <p class="text-xs text-stone-400 mt-0.5">Elegí tu departamento y ciudad para calcular el costo</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-stone-600 flex-shrink-0" x-show="shippingType === 'envio' && quoteLoaded" x-text="'Gs. ' + new Intl.NumberFormat('es-PY').format(shippingCost)"></span>
                            </label>
                            @endif
                            @if(!$shippingSettings->store_pickup_enabled && !$shippingSettings->envio_propio_enabled)
                            <p class="text-sm text-stone-400">No hay métodos de envío disponibles por el momento.</p>
                            @endif
                        </div>
                        @error('shipping_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-white border border-stone-100 p-6 space-y-4">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Método de pago</h2>
                        <div class="space-y-3">
                            @foreach($paymentMethods as $method)
                            <label class="flex items-start gap-3 p-3 border border-stone-200 cursor-pointer hover:border-copper-300 transition-colors has-[:checked]:border-copper-500 has-[:checked]:bg-copper-50">
                                <input type="radio" name="payment_method" value="{{ $method->key }}" x-model="paymentMethod"
                                       {{ old('payment_method', $paymentMethods->first()?->key) === $method->key ? 'checked' : '' }} required class="mt-0.5 text-copper-500">
                                <div>
                                    <p class="font-medium text-sm text-stone-700">{{ $method->name }}</p>
                                    <p class="text-xs text-stone-400 mt-0.5">
                                        @switch($method->key)
                                            @case('transferencia')
                                                Recibirás los datos para transferir al confirmar el pedido. Tu pedido se procesará al verificar el pago.
                                                @break
                                            @case('pagopar')
                                                Pago en línea con tarjeta, Tigo Money o billetera digital vía Pagopar.
                                                @break
                                            @case('bancard')
                                                Pago en línea con tarjeta de crédito o débito vía Bancard.
                                                @break
                                            @default
                                                {{ $method->description }}
                                        @endswitch
                                    </p>
                                </div>
                            </label>
                            @endforeach
                            <div x-show="paymentMethod === 'transferencia'" x-cloak class="p-3 bg-stone-50 border border-stone-200 space-y-2">
                                <label class="block text-xs font-medium text-stone-600 mb-1">Comprobante de transferencia (PDF o imagen) *</label>
                                <input type="file" name="transfer_receipt" accept=".pdf,image/jpeg,image/png"
                                       :required="paymentMethod === 'transferencia'"
                                       class="input-cateura border p-2 w-full text-xs bg-white">
                                <p class="text-xs text-stone-400">Subí el comprobante de tu transferencia. Máx. 5MB (JPG, PNG o PDF). Tu pedido quedará pendiente de verificación hasta que confirmemos el pago.</p>
                                @error('transfer_receipt')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            </div>
                            @if($paymentMethods->isEmpty())
                            <p class="text-sm text-stone-400">No hay métodos de pago disponibles por el momento. Contactanos para coordinar tu compra.</p>
                            @endif
                        </div>
                        @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <div class="bg-white border border-stone-100 p-6 sticky top-4">
                        <h2 class="font-medium text-stone-700 border-b border-stone-100 pb-3 mb-4">Resumen</h2>
                        <div class="space-y-2 mb-4 text-sm">
                            @foreach($cart->items as $item)
                            <div class="flex justify-between text-stone-600">
                                <span class="truncate flex-1 mr-2">{{ $item->product?->name }} ×{{ $item->quantity }}</span>
                                <span class="flex-shrink-0">Gs. {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="border-t border-stone-100 pt-3 space-y-2 mb-3 text-sm">
                            <div class="flex justify-between text-stone-600">
                                <span>Subtotal</span>
                                <span>Gs. {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-stone-600">
                                <span>Envío</span>
                                <span x-text="'Gs. ' + new Intl.NumberFormat('es-PY').format(shippingType === 'pickup' ? 0 : shippingCost)"></span>
                            </div>
                        </div>
                        <div class="flex justify-between font-bold text-stone-800 border-t border-stone-100 pt-3 mb-5">
                            <span>Total</span>
                            <span class="text-copper-600" x-text="'Gs. ' + new Intl.NumberFormat('es-PY').format(subtotal + (shippingType === 'pickup' ? 0 : shippingCost))"></span>
                        </div>
                        <label class="flex items-start gap-2 mb-4 cursor-pointer">
                            <input type="checkbox" name="accept_terms" value="1" {{ old('accept_terms') ? 'checked' : '' }} required class="mt-0.5 text-copper-500">
                            <span class="text-xs text-stone-500">Acepto los <a href="{{ route('legal.terminos') }}" class="underline hover:text-stone-700" target="_blank">términos y condiciones</a> y las <a href="{{ route('legal.compra') }}" class="underline hover:text-stone-700" target="_blank">políticas de compra</a>.</span>
                        </label>
                        @error('accept_terms')<p class="text-red-500 text-xs mb-3">Debés aceptar los términos y condiciones.</p>@enderror
                        <button type="submit" class="btn-copper w-full">Confirmar pedido</button>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </div>
</section>

<script>
function checkoutShipping(departments, subtotal, initialDept, initialCity, initialShippingType, initialPaymentMethod) {
    return {
        departments,
        subtotal,
        department: initialDept || '',
        city: initialCity || '',
        shippingType: initialShippingType,
        paymentMethod: initialPaymentMethod || '',
        shippingCost: 0,
        deliveryTime: '',
        shippingUnavailable: false,
        quoteLoaded: false,

        get cities() {
            const dept = this.departments.find(d => d.id === this.department);
            return dept ? dept.districts : [];
        },

        init() {
            if (this.department && this.city) this.fetchQuote();
        },

        onDepartmentChange() {
            this.city = '';
            this.quoteLoaded = false;
            this.shippingCost = 0;
        },

        onCityChange() {
            if (this.department && this.city) this.fetchQuote();
        },

        async fetchQuote() {
            try {
                const response = await fetch('{{ route('checkout.shipping') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ department: this.department, city: this.city, subtotal: this.subtotal }),
                });
                const data = await response.json();
                this.shippingCost = data.shipping.cost ?? 0;
                this.deliveryTime = data.shipping.delivery_time ?? '';
                this.shippingUnavailable = !!data.shipping.unavailable;
                this.quoteLoaded = true;
            } catch (e) {
                this.quoteLoaded = false;
            }
        },
    };
}
</script>
@endsection
