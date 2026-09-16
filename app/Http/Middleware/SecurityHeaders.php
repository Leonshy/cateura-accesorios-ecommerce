<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * No incluye Content-Security-Policy: el sitio usa scripts inline (media
 * picker, editor de imagen) y varios dominios externos opcionales
 * (Google Fonts, hCaptcha, Google Analytics, Meta Pixel, WhatsApp) que
 * cambian según lo que el admin active desde /admin/integraciones. Armar
 * una CSP correcta requiere enumerar y probar cada uno de esos casos; se
 * deja fuera de este middleware para no romper nada a ciegas.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Solo en producción: en local (HTTP) forzaría HTTPS y rompería el desarrollo.
        if (app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
