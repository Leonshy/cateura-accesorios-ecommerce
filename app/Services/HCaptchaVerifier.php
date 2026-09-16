<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * hCaptcha se puede activar o desactivar de forma independiente en cada
 * formulario público (contacto, newsletter, registro) desde
 * /admin/configuracion. Si a un formulario no se le activó, esta clase se
 * comporta como si hCaptcha no existiera para él.
 */
class HCaptchaVerifier
{
    public const FORMS = ['contact', 'newsletter', 'register', 'review'];

    public static function isConfigured(): bool
    {
        return filled(SiteSetting::get('hcaptcha_site_key')) && filled(SiteSetting::get('hcaptcha_secret_key'));
    }

    public static function isEnabledFor(string $form): bool
    {
        if (! self::isConfigured()) {
            return false;
        }

        return (bool) SiteSetting::get('hcaptcha_enabled_' . $form, false);
    }

    public static function siteKey(): ?string
    {
        return SiteSetting::get('hcaptcha_site_key');
    }

    /**
     * Si algún formulario público lo tiene activado, hay que cargar el script
     * de hCaptcha en el layout global (el newsletter vive en el footer, que
     * está en todas las páginas).
     */
    public static function anyEnabled(): bool
    {
        foreach (self::FORMS as $form) {
            if (self::isEnabledFor($form)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica el token del widget contra la API de hCaptcha. Se llama solo
     * cuando isEnabledFor() ya dio true para ese formulario.
     */
    public static function verify(?string $token): bool
    {
        $secret = SiteSetting::get('hcaptcha_secret_key');
        if (! $secret || ! $token) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(10)->post('https://hcaptcha.com/siteverify', [
                'secret'   => $secret,
                'response' => $token,
            ]);

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            Log::error('No se pudo verificar hCaptcha.', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
