@extends('layouts.admin')
@section('title', 'Configuración general')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Identidad del sitio</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-admin.media-picker name="site_logo" value="{{ $settings['site_logo'] ?? '' }}" label="Logo (encabezado)" />
            <x-admin.media-picker name="site_logo_footer" value="{{ $settings['site_logo_footer'] ?? '' }}" label="Logo (pie de página)" />
            <x-admin.media-picker name="site_favicon" value="{{ $settings['site_favicon'] ?? '' }}" label="Favicon" accept="image/png,image/x-icon,image/svg+xml" />
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Nombre del sitio</label>
            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Cateura Accesorios' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Descripción del sitio (meta)</label>
            <textarea name="site_description" rows="2" class="input-cateura border p-2 w-full">{{ $settings['site_description'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Email de contacto</label>
            <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Teléfono</label>
            <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Dirección</label>
            <input type="text" name="contact_address" value="{{ $settings['contact_address'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
    </div>

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Redes sociales</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Instagram (URL)</label>
            <input type="url" name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Facebook (URL)</label>
            <input type="url" name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">WhatsApp (número con código país, ej: 595981234567)</label>
            <input type="text" name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Mensaje predeterminado WhatsApp</label>
            <input type="text" name="whatsapp_message" value="{{ $settings['whatsapp_message'] ?? 'Hola, me interesa un producto' }}" class="input-cateura border p-2 w-full">
        </div>
    </div>

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Integraciones de analytics</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Google Analytics ID (ej: G-XXXXXXXX)</label>
            <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Meta Pixel ID</label>
            <input type="text" name="meta_pixel_id" value="{{ $settings['meta_pixel_id'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">hCaptcha Site Key</label>
            <input type="text" name="hcaptcha_site_key" value="{{ $settings['hcaptcha_site_key'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">hCaptcha Secret Key</label>
            <input type="text" name="hcaptcha_secret_key" value="{{ $settings['hcaptcha_secret_key'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
    </div>

    <button type="submit" class="btn-copper">Guardar configuración</button>
</form>
</div>
@endsection
