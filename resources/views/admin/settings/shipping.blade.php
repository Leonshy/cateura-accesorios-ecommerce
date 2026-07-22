@extends('layouts.admin')
@section('title', 'Configuración de envíos')

@section('content')
@php
    $allDepts   = \App\Data\ParaguayLocations::departments();
    $savedZones = $settings->zones ?? [];
@endphp

<div class="max-w-4xl space-y-6">

    <form method="POST" action="{{ route('admin.settings.shipping.update') }}"
          x-data="shippingZonesEditor({{ json_encode($allDepts) }}, {{ json_encode($savedZones) }}, {{ $settings->envio_propio_enabled ? 'true' : 'false' }})"
          x-on:submit="onSubmit">
        @csrf

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Opciones generales --}}
        <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-5">
            <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Opciones generales</h3>

            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-stone-700">Envío gratis</p>
                    <p class="text-xs text-stone-400 mt-0.5">Habilitar envío gratuito a partir de un monto mínimo</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="free_shipping_enabled" value="0">
                    <input type="checkbox" name="free_shipping_enabled" value="1" class="sr-only peer"
                           {{ $settings->free_shipping_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-stone-200 peer-checked:bg-copper-500 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Monto mínimo para envío gratis (Gs.)</label>
                <input type="number" name="free_shipping_min_amount" value="{{ $settings->free_shipping_min_amount ?? 0 }}"
                       class="input-cateura border p-2 w-48">
            </div>

            <div class="flex items-start justify-between pt-4 border-t border-stone-100">
                <div>
                    <p class="text-sm font-medium text-stone-700">Retiro en tienda</p>
                    <p class="text-xs text-stone-400 mt-0.5">El cliente puede retirar su pedido en el local</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="store_pickup_enabled" value="0">
                    <input type="checkbox" name="store_pickup_enabled" value="1" class="sr-only peer"
                           {{ $settings->store_pickup_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-stone-200 peer-checked:bg-copper-500 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div class="flex items-start justify-between pt-4 border-t border-stone-100">
                <div>
                    <p class="text-sm font-medium text-stone-700">Envío propio (por departamento/ciudad)</p>
                    <p class="text-xs text-stone-400 mt-0.5">La tienda gestiona sus propios repartidores con tarifas configurables</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="envio_propio_enabled" value="0">
                    <input type="checkbox" name="envio_propio_enabled" value="1" class="sr-only peer"
                           x-model="envioPropio" :checked="envioPropio">
                    <div class="w-11 h-6 bg-stone-200 peer-checked:bg-copper-500 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>

        {{-- Zonas de envío --}}
        <div x-show="envioPropio"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white border border-stone-100 shadow-sm p-6">
            <div class="flex items-start justify-between border-b border-stone-100 pb-4 mb-5">
                <div>
                    <h3 class="font-medium text-stone-700">Tarifas por departamento y ciudad — Paraguay</h3>
                    <p class="text-xs text-stone-400 mt-0.5">
                        Habilitá los departamentos donde realizás envíos y configurá tarifas base.
                        Podés agregar tarifas personalizadas por ciudad o distrito dentro de cada departamento.
                    </p>
                </div>
                <div class="text-right text-xs text-stone-400 shrink-0 ml-4 mt-0.5">
                    <span x-text="activeDeptCount()"></span> / {{ count($allDepts) }} activos
                    <template x-if="totalCustomRatesCount() > 0">
                        <span class="block text-copper-600">
                            <span x-text="totalCustomRatesCount()"></span> tarifa(s) personalizada(s)
                        </span>
                    </template>
                </div>
            </div>

            <div class="bg-copper-50 border border-copper-100 px-4 py-3 text-xs text-copper-700 mb-4">
                <strong>¿Cómo funciona?</strong> Habilitá un departamento y configurá su tarifa base.
                Si alguna ciudad tiene un precio diferente, agregá una <strong>tarifa personalizada</strong>.
                Podés <strong>deshabilitar ciudades</strong> para que no aparezcan en el checkout.
            </div>

            <input type="hidden" name="zones_json" id="zones_json_input">

            <div class="space-y-2">
                <template x-for="(zone, zIdx) in zones" :key="zone.departmentId">
                    <div :class="zone.active ? 'border-copper-200 bg-white' : 'border-stone-200 bg-stone-50'"
                         class="border overflow-hidden transition-colors">

                        <div class="flex items-center gap-3 p-4">
                            <button type="button"
                                    @click="zone.active = !zone.active; if (!zone.active) zone.open = false"
                                    :class="zone.active ? 'bg-copper-500' : 'bg-stone-300'"
                                    class="relative inline-flex w-10 h-6 rounded-full transition-colors shrink-0 focus:outline-none">
                                <span :class="zone.active ? 'translate-x-5' : 'translate-x-1'"
                                      class="inline-block w-4 h-4 mt-1 bg-white rounded-full shadow transition-transform"></span>
                            </button>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-stone-800" x-text="zone.name"></p>
                                <p class="text-xs text-stone-400">
                                    <span x-text="getActiveCities(zIdx).length"></span> /
                                    <span x-text="zone.districts.length"></span> ciudades activas
                                    <template x-if="zone.customRates.length > 0">
                                        <span class="text-copper-600 ml-1">
                                            · <span x-text="zone.customRates.length"></span> tarifa(s) personalizada(s)
                                        </span>
                                    </template>
                                </p>
                            </div>

                            <template x-if="zone.active && zone.price > 0">
                                <span class="hidden sm:inline-flex text-xs font-medium text-stone-600 bg-stone-100 px-2 py-0.5 rounded-full">
                                    Gs. <span x-text="Number(zone.price).toLocaleString('es-PY')"></span>
                                </span>
                            </template>

                            <span :class="zone.active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500'"
                                  class="text-xs font-medium px-2 py-0.5 rounded-full shrink-0"
                                  x-text="zone.active ? 'Activo' : 'Inactivo'"></span>

                            <button type="button"
                                    @click="if(zone.active) zone.open = !zone.open"
                                    :disabled="!zone.active"
                                    class="p-1.5 rounded-lg hover:bg-stone-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                <svg :class="zone.open ? 'rotate-180' : ''" class="w-4 h-4 text-stone-500 transition-transform"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        <div x-show="zone.open" class="border-t border-stone-100 p-4 space-y-4 bg-white">

                            <div class="border border-stone-200 overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-2.5 bg-stone-50 border-b border-stone-200">
                                    <p class="text-sm font-semibold text-stone-700">Ciudades / Distritos</p>
                                    <div class="flex items-center gap-3 text-xs text-stone-500">
                                        <span>
                                            <span x-text="getActiveCities(zIdx).length" class="font-medium text-green-700"></span> activas ·
                                            <span x-text="zone.inactiveCities.length" class="font-medium text-red-500"></span> deshabilitadas
                                        </span>
                                        <button type="button" @click="enableAllCities(zIdx)" class="text-copper-600 hover:underline">Habilitar todas</button>
                                    </div>
                                </div>
                                <div class="p-3 grid grid-cols-2 sm:grid-cols-3 gap-1 max-h-48 overflow-y-auto">
                                    <template x-for="city in zone.districts" :key="city">
                                        <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-stone-50 select-none"
                                               :class="isCityActive(zIdx, city) ? '' : 'opacity-60'">
                                            <button type="button"
                                                    @click="toggleCity(zIdx, city)"
                                                    :class="isCityActive(zIdx, city) ? 'bg-green-500' : 'bg-stone-300'"
                                                    class="relative inline-flex w-8 h-4 rounded-full transition-colors shrink-0 focus:outline-none">
                                                <span :class="isCityActive(zIdx, city) ? 'translate-x-4' : 'translate-x-0.5'"
                                                      class="inline-block w-3 h-3 mt-0.5 bg-white rounded-full shadow transition-transform"></span>
                                            </button>
                                            <span class="text-xs truncate" :class="isCityActive(zIdx, city) ? 'text-stone-800' : 'text-stone-400 line-through'" x-text="city"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <div class="bg-copper-50 border border-copper-100 p-4 space-y-3">
                                <p class="text-sm font-semibold text-copper-900">Tarifa del departamento (por defecto)</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-stone-600 mb-1">Costo de envío (Gs.)</label>
                                        <input type="number" x-model="zone.price" placeholder="25000" min="0" class="input-cateura border p-2 w-full bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-stone-600 mb-1">Tiempo de entrega</label>
                                        <input type="text" x-model="zone.deliveryTime" placeholder="ej: 24-48 horas" class="input-cateura border p-2 w-full bg-white">
                                    </div>
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" x-model="zone.freeShippingEligible" class="rounded border-stone-300 text-copper-600 focus:ring-copper-500">
                                    <span class="text-xs text-stone-700">Aplica envío gratis por monto mínimo</span>
                                </label>
                                <div x-show="getDistrictsWithDefaultRate(zIdx).length > 0">
                                    <p class="text-xs text-stone-500 mb-1">Aplica a:</p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="d in getDistrictsWithDefaultRate(zIdx)" :key="d">
                                            <span class="text-xs bg-white border border-copper-200 text-copper-700 px-1.5 py-0.5 rounded" x-text="d"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <template x-if="zone.customRates.length > 0">
                                <div class="space-y-3">
                                    <p class="text-sm font-semibold text-stone-700">Tarifas personalizadas por área</p>
                                    <template x-for="(rate, rIdx) in zone.customRates" :key="rate.id">
                                        <div class="border border-dashed border-stone-300 p-4 space-y-3 bg-stone-50">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-copper-700">Tarifa personalizada #<span x-text="rIdx + 1"></span></span>
                                                <button type="button" @click="removeCustomRate(zIdx, rIdx)" class="text-red-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-stone-600 mb-1">Costo de envío (Gs.)</label>
                                                    <input type="number" x-model="rate.price" placeholder="30000" min="0" class="input-cateura border p-2 w-full bg-white">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-stone-600 mb-1">Tiempo de entrega</label>
                                                    <input type="text" x-model="rate.deliveryTime" placeholder="48-72 horas" class="input-cateura border p-2 w-full bg-white">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-stone-600 mb-1">
                                                    Ciudades/distritos con esta tarifa <span class="text-stone-400 font-normal">(solo ciudades habilitadas)</span>
                                                </label>
                                                <div class="max-h-48 overflow-y-auto border border-stone-200 p-2 bg-white grid grid-cols-2 sm:grid-cols-3 gap-1">
                                                    <template x-for="district in getActiveCities(zIdx)" :key="district">
                                                        <label :class="isDistrictInOtherRate(zIdx, rIdx, district) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer hover:bg-copper-50 rounded'"
                                                               class="flex items-center gap-1.5 px-1.5 py-1 text-xs select-none">
                                                            <input type="checkbox"
                                                                   :checked="rate.districtIds.includes(district)"
                                                                   :disabled="isDistrictInOtherRate(zIdx, rIdx, district)"
                                                                   @change="toggleDistrict(zIdx, rIdx, district)"
                                                                   class="rounded border-stone-300 text-copper-600 focus:ring-copper-500 w-3 h-3 shrink-0">
                                                            <span x-text="district" class="truncate"></span>
                                                        </label>
                                                    </template>
                                                </div>
                                                <p class="text-xs text-stone-400 mt-1"><span x-text="rate.districtIds.length"></span> área(s) seleccionada(s)</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="getActiveCities(zIdx).length > 1">
                                <button type="button" @click="addCustomRate(zIdx)"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 border border-dashed border-copper-300 text-sm text-copper-600 hover:bg-copper-50 transition-colors font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Agregar tarifa personalizada por área
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- AEX --}}
        <div class="bg-white border border-stone-100 shadow-sm p-6">
            <div class="flex items-start justify-between border-b border-stone-100 pb-4 mb-4">
                <div>
                    <h3 class="font-medium text-stone-700">Integración AEX</h3>
                    <p class="text-xs text-stone-400 mt-0.5">Courier para cotización y gestión de envíos vía API de AEX</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer mt-1">
                    <input type="hidden" name="aex_enabled" value="0">
                    <input type="checkbox" name="aex_enabled" value="1" class="sr-only peer" {{ $settings->aex_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-stone-200 peer-checked:bg-copper-500 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Usuario API</label>
                    <input type="text" name="aex_api_user" value="{{ $settings->aex_api_user }}" class="input-cateura border p-2 w-full">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Contraseña API</label>
                    <input type="password" name="aex_api_password" value="{{ $settings->aex_api_password }}" class="input-cateura border p-2 w-full">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Entorno</label>
                    <select name="aex_environment" class="input-cateura border p-2 w-full bg-white">
                        <option value="sandbox" {{ $settings->aex_environment === 'sandbox' ? 'selected' : '' }}>Sandbox (pruebas)</option>
                        <option value="production" {{ $settings->aex_environment === 'production' ? 'selected' : '' }}>Producción</option>
                    </select>
                </div>
                @if($settings->aex_is_validated)
                <div class="flex items-end">
                    <span class="text-xs font-medium text-green-700 bg-green-100 px-2 py-1 rounded-full">Credenciales validadas</span>
                </div>
                @endif
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-copper">Guardar configuración</button>
        </div>
    </form>
</div>

<script>
function shippingZonesEditor(allDepts, savedZones, envioPropio) {
    const initZones = allDepts.map(dept => {
        const saved = savedZones.find(z => z.departmentId === dept.id) || {};
        return {
            departmentId: dept.id,
            name:         dept.name,
            districts:    dept.districts,
            active:               saved.active               ?? false,
            price:                saved.price                ?? 0,
            deliveryTime:         saved.deliveryTime         ?? '',
            freeShippingEligible: saved.freeShippingEligible ?? true,
            inactiveCities:       saved.inactiveCities       ?? [],
            customRates: (saved.customRates || []).map(r => ({
                id:           r.id || Math.random().toString(36).slice(2, 11),
                price:        r.price        ?? 0,
                deliveryTime: r.deliveryTime ?? '',
                districtIds:  r.districtIds  ?? [],
            })),
            open: false,
        };
    });

    return {
        zones: initZones,
        envioPropio: envioPropio,

        activeDeptCount() {
            return this.zones.filter(z => z.active).length;
        },

        totalCustomRatesCount() {
            return this.zones.reduce((sum, z) => sum + z.customRates.length, 0);
        },

        isCityActive(zIdx, city) {
            return !this.zones[zIdx].inactiveCities.includes(city);
        },

        toggleCity(zIdx, city) {
            const zone = this.zones[zIdx];
            const idx  = zone.inactiveCities.indexOf(city);
            if (idx === -1) {
                zone.inactiveCities.push(city);
                zone.customRates.forEach(r => {
                    const di = r.districtIds.indexOf(city);
                    if (di !== -1) r.districtIds.splice(di, 1);
                });
            } else {
                zone.inactiveCities.splice(idx, 1);
            }
        },

        enableAllCities(zIdx) {
            this.zones[zIdx].inactiveCities = [];
        },

        getActiveCities(zIdx) {
            const zone = this.zones[zIdx];
            return zone.districts.filter(d => !zone.inactiveCities.includes(d));
        },

        addCustomRate(zIdx) {
            this.zones[zIdx].customRates.push({
                id:           Math.random().toString(36).slice(2, 11),
                price:        0,
                deliveryTime: '',
                districtIds:  [],
            });
        },

        removeCustomRate(zIdx, rIdx) {
            this.zones[zIdx].customRates.splice(rIdx, 1);
        },

        toggleDistrict(zIdx, rIdx, district) {
            const rate = this.zones[zIdx].customRates[rIdx];
            const idx  = rate.districtIds.indexOf(district);
            if (idx === -1) rate.districtIds.push(district);
            else            rate.districtIds.splice(idx, 1);
        },

        isDistrictInOtherRate(zIdx, currentRateIdx, district) {
            return this.zones[zIdx].customRates.some((r, i) =>
                i !== currentRateIdx && r.districtIds.includes(district)
            );
        },

        getDistrictsWithDefaultRate(zIdx) {
            const zone        = this.zones[zIdx];
            const allInCustom = zone.customRates.flatMap(r => r.districtIds);
            return this.getActiveCities(zIdx).filter(d => !allInCustom.includes(d));
        },

        getSerializedZones() {
            return JSON.stringify(this.zones.map(({ name, districts, open, ...zone }) => zone));
        },

        onSubmit() {
            document.getElementById('zones_json_input').value = this.getSerializedZones();
        },
    };
}
</script>
@endsection
