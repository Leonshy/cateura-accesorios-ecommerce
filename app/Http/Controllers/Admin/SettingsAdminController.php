<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\ShippingSetting;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsAdminController extends Controller
{
    public function general()
    {
        $settings = SiteSetting::group('general');
        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_logo'        => 'nullable|string|max:2048',
            'site_logo_footer' => 'nullable|string|max:2048',
            'site_favicon'     => 'nullable|string|max:2048',
        ]);

        $fields = ['site_name','site_description','footer_description','contact_email','contact_phone','contact_address',
                   'instagram_url','facebook_url','youtube_url','tiktok_url','whatsapp_number','whatsapp_message',
                   'google_analytics_id','meta_pixel_id','hcaptcha_site_key','hcaptcha_secret_key',
                   'site_logo','site_logo_footer','site_favicon'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                SiteSetting::set($field, $request->input($field), 'general');
            }
        }

        // Checkboxes: a diferencia de los campos de texto de arriba, un checkbox
        // destildado no viaja en el POST, así que hay que grabar explícitamente
        // el valor false en vez de depender de has().
        foreach (\App\Services\HCaptchaVerifier::FORMS as $form) {
            SiteSetting::set('hcaptcha_enabled_' . $form, $request->boolean('hcaptcha_enabled_' . $form) ? '1' : '0', 'general');
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }

    public function integrations()
    {
        $settings = SiteSetting::group('integrations');
        $paymentMethods = PaymentMethod::orderBy('order')->get();
        return view('admin.settings.integrations', compact('settings', 'paymentMethods'));
    }

    public function updatePaymentMethod(Request $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update([
            'is_active'   => $request->boolean('is_active'),
            'sandbox'     => $request->boolean('sandbox'),
            'credentials' => $request->input('credentials', []),
        ]);
        return back()->with('success', 'Método de pago actualizado.');
    }

    public function shipping()
    {
        $settings = ShippingSetting::getDefault();
        return view('admin.settings.shipping', compact('settings'));
    }

    public function updateShipping(Request $request)
    {
        $settings = ShippingSetting::getDefault();

        $zones = json_decode($request->input('zones_json', '[]'), true) ?? [];

        $settings->update([
            'free_shipping_enabled'    => $request->boolean('free_shipping_enabled'),
            'free_shipping_min_amount' => (int) $request->input('free_shipping_min_amount', 0),
            'store_pickup_enabled'     => $request->boolean('store_pickup_enabled'),
            'envio_propio_enabled'     => $request->boolean('envio_propio_enabled'),
            'zones'                    => $zones,
            'aex_enabled'              => $request->boolean('aex_enabled'),
            'aex_api_user'             => $request->input('aex_api_user'),
            'aex_api_password'         => $request->input('aex_api_password'),
            'aex_environment'          => $request->input('aex_environment', 'sandbox'),
        ]);

        return back()->with('success', 'Configuración de envíos actualizada.');
    }
}
