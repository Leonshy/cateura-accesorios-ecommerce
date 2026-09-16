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
            <x-admin.media-picker name="site_logo" value="{{ $settings['site_logo'] ?? '' }}" label="Logo (encabezado)" hint="Horizontal, fondo transparente (PNG), sin recorte fijo" />
            <x-admin.media-picker name="site_logo_footer" value="{{ $settings['site_logo_footer'] ?? '' }}" label="Logo (pie de página)" hint="Horizontal, fondo transparente (PNG), sin recorte fijo" />
            <x-admin.media-picker name="site_favicon" value="{{ $settings['site_favicon'] ?? '' }}" label="Favicon" accept="image/png,image/x-icon,image/svg+xml" hint="512×512 px recomendado (cuadrado)" aspect="1/1" />
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Nombre del sitio</label>
            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Cateura Accesorios' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Descripción del sitio (meta)</label>
            <textarea name="site_description" rows="2" class="input-cateura border p-2 w-full">{{ $settings['site_description'] ?? '' }}</textarea>
            <p class="text-xs text-stone-400 mt-1">Se usa para buscadores (SEO), no se muestra en el sitio.</p>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Descripción del pie de página</label>
            <textarea name="footer_description" rows="2" class="input-cateura border p-2 w-full">{{ $settings['footer_description'] ?? 'Accesorios, piezas decorativas y prendas creadas por artesanas del Bañado Sur a partir de materiales reciclados.' }}</textarea>
            <p class="text-xs text-stone-400 mt-1">Este texto aparece debajo del logo, en la primera columna del pie de página del sitio.</p>
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
            <textarea name="contact_address" rows="3" class="input-cateura border p-2 w-full">{{ $settings['contact_address'] ?? '' }}</textarea>
            <p class="text-xs text-stone-400 mt-1">Podés usar varias líneas; se muestran igual en el pie de página y en la página de Contacto.</p>
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
            <label class="block text-xs font-medium text-stone-600 mb-1">YouTube (URL)</label>
            <input type="url" name="youtube_url" value="{{ $settings['youtube_url'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">TikTok (URL)</label>
            <input type="url" name="tiktok_url" value="{{ $settings['tiktok_url'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <p class="text-xs text-stone-400 -mt-2">Cada red social solo aparece en el sitio si su enlace está completo (empieza con http:// o https://). Dejá el campo vacío para que ese ícono no se muestre.</p>
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
    </div>

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">hCaptcha (protección anti-spam)</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Site Key</label>
            <input type="text" name="hcaptcha_site_key" value="{{ $settings['hcaptcha_site_key'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Secret Key</label>
            <input type="text" name="hcaptcha_secret_key" value="{{ $settings['hcaptcha_secret_key'] ?? '' }}" class="input-cateura border p-2 w-full">
        </div>
        <p class="text-xs text-stone-400 -mt-2">Con las dos claves cargadas, elegí en qué formularios público mostrar y exigir hCaptcha. Un formulario sin marcar no lo pide.</p>
        <div class="space-y-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="hcaptcha_enabled_contact" value="1" {{ ($settings['hcaptcha_enabled_contact'] ?? false) ? 'checked' : '' }} class="text-copper-500">
                <span class="text-sm text-stone-600">Formulario de contacto</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="hcaptcha_enabled_newsletter" value="1" {{ ($settings['hcaptcha_enabled_newsletter'] ?? false) ? 'checked' : '' }} class="text-copper-500">
                <span class="text-sm text-stone-600">Suscripción al newsletter</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="hcaptcha_enabled_register" value="1" {{ ($settings['hcaptcha_enabled_register'] ?? false) ? 'checked' : '' }} class="text-copper-500">
                <span class="text-sm text-stone-600">Registro de cuenta de cliente</span>
            </label>
        </div>
    </div>

    <button type="submit" class="btn-copper">Guardar configuración</button>
</form>
</div>
@endsection
