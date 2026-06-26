<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
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
        $fields = ['site_name','site_email','site_phone','site_whatsapp','site_address',
                   'site_facebook','site_instagram','site_tiktok',
                   'whatsapp_message','whatsapp_active',
                   'transfer_banco','transfer_cuenta','transfer_titular',
                   'google_analytics_id','meta_pixel_id','hcaptcha_site_key','hcaptcha_secret_key',
                   'hcaptcha_active','ga_active','pixel_active'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                SiteSetting::set($field, $request->input($field), 'general');
            }
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
        $methods = ShippingMethod::orderBy('order')->get();
        return view('admin.settings.shipping', compact('methods'));
    }

    public function updateShipping(Request $request)
    {
        foreach ($request->input('methods', []) as $id => $data) {
            ShippingMethod::where('id', $id)->update([
                'name'      => $data['name'] ?? '',
                'cost'      => $data['cost'] ?? 0,
                'is_active' => isset($data['is_active']),
            ]);
        }
        return back()->with('success', 'Métodos de envío actualizados.');
    }
}
