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
