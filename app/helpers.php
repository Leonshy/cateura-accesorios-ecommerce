<?php

if (! function_exists('media_url')) {
    /**
     * Resuelve una imagen guardada como URL completa (biblioteca multimedia)
     * o como path relativo dentro de storage/app/public (archivos antiguos).
     */
    function media_url(?string $value, string $default = ''): string
    {
        if (! $value) {
            return $default;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '//')) {
            return $value;
        }

        return asset('storage/' . ltrim($value, '/'));
    }
}

if (! function_exists('paraguay_tel_href')) {
    /**
     * Convierte un teléfono paraguayo en formato local ("0981 877 315") en un
     * enlace tel: con código de país, para que el mismo número guardado en
     * configuración sirva tanto para mostrarse como para poder llamarse.
     */
    function paraguay_tel_href(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        if ($digits === '') {
            return '';
        }
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '595' . substr($digits, 1);
        }
        return 'tel:+' . $digits;
    }
}

if (! function_exists('catalog_thumb_url')) {
    /**
     * Deriva la URL de la miniatura (generada por MediaAdminController al
     * subir la imagen) a partir de la imagen completa guardada en
     * Product::image y similares. Si el archivo no vive en nuestra propia
     * biblioteca de medios, o no tiene miniatura generada (imágenes de
     * catálogo cargadas antes de este cambio, o vía seeder), cae de vuelta a
     * la imagen completa sin romper nada.
     */
    function catalog_thumb_url(?string $imagePath, string $default = ''): string
    {
        $full = media_url($imagePath, $default);
        if (! $full || ! str_contains($full, '/storage/media/')) {
            return $full;
        }

        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return $full;
        }

        $thumbUrl = preg_replace('/\.' . preg_quote($ext, '/') . '$/i', '-thumb.' . $ext, $full);
        $relativePath = 'media/' . basename((string) parse_url($thumbUrl, PHP_URL_PATH));

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath) ? $thumbUrl : $full;
    }
}

if (! function_exists('is_complete_url')) {
    /**
     * Un enlace de red social (Instagram, Facebook, YouTube, TikTok, etc.) solo
     * se considera "completo" si es una URL http(s) absoluta. Se usa para
     * ocultar íconos de redes sociales que quedaron vacíos o a medio cargar.
     */
    function is_complete_url(?string $value): bool
    {
        $value = trim((string) $value);
        return $value !== '' && (bool) preg_match('#^https?://.+#i', $value);
    }
}
