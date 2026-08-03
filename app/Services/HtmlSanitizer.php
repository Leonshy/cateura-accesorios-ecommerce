<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Limpia el HTML que carga un rol Editor (noticias, páginas legales) antes de
 * guardarlo. Sin esto, cualquiera con ese rol podría insertar <script> u otro
 * HTML activo que se ejecutaría en el navegador de cualquier visitante público
 * de esa página (XSS almacenado).
 */
class HtmlSanitizer
{
    private static ?HTMLPurifier $purifier = null;

    public static function clean(?string $html): string
    {
        if (! $html) {
            return '';
        }

        return self::purifier()->purify($html);
    }

    private static function purifier(): HTMLPurifier
    {
        if (self::$purifier) {
            return self::$purifier;
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', storage_path('app/htmlpurifier'));
        $config->set('HTML.Allowed', implode(',', [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u',
            'h2', 'h3', 'h4', 'blockquote',
            'ul', 'ol', 'li',
            'a[href|title|target|rel]',
            'img[src|alt|width|height]',
        ]));
        $config->set('HTML.TargetBlank', true);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

        return self::$purifier = new HTMLPurifier($config);
    }
}
