<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutValue;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

/**
 * Edición de los textos e imágenes de las páginas públicas fijas del sitio.
 * Cada página pública tiene su propia pantalla y su propio grupo de
 * configuración (`content_home`, `content_artisans`, `content_about`) para
 * que editar una no toque ni mezcle los datos de las otras.
 */
class ContentAdminController extends Controller
{
    public function home()
    {
        $settings = SiteSetting::group('content_home');
        return view('admin.content.home', compact('settings'));
    }

    public function updateHome(Request $request)
    {
        $request->validate([
            'historia_img1' => 'nullable|string|max:2048',
            'historia_img2' => 'nullable|string|max:2048',
            'historia_img3' => 'nullable|string|max:2048',
            'historia_img4' => 'nullable|string|max:2048',
        ]);

        $fields = [
            'historia_eyebrow', 'historia_title_line1', 'historia_title_line2', 'historia_text',
            'historia_stat1_value', 'historia_stat1_label',
            'historia_stat2_value', 'historia_stat2_label',
            'historia_stat3_value', 'historia_stat3_label',
            'historia_img1', 'historia_img2', 'historia_img3', 'historia_img4',
        ];
        $this->saveFields($request, $fields, 'content_home');

        return back()->with('success', 'Contenido de la página de inicio actualizado.');
    }

    public function artisans()
    {
        $settings = SiteSetting::group('content_artisans');
        return view('admin.content.artisans', compact('settings'));
    }

    public function updateArtisans(Request $request)
    {
        $fields = ['artisans_eyebrow', 'artisans_title', 'artisans_subtitle'];
        $this->saveFields($request, $fields, 'content_artisans');

        return back()->with('success', 'Contenido de la página de artesanas actualizado.');
    }

    public function about()
    {
        $settings = SiteSetting::group('content_about');
        $aboutValues = AboutValue::orderBy('order')->get();
        return view('admin.content.about', compact('settings', 'aboutValues'));
    }

    public function updateAbout(Request $request)
    {
        $request->validate([
            'about_hero_image' => 'nullable|string|max:2048',
        ]);

        $fields = [
            'about_hero_eyebrow', 'about_hero_title', 'about_hero_text',
            'about_historia_eyebrow', 'about_historia_title',
            'about_historia_text1', 'about_historia_text2', 'about_historia_text3',
            'about_hero_image',
            'about_valores_eyebrow', 'about_valores_title',
            'about_cta_title', 'about_cta_text',
        ];
        $this->saveFields($request, $fields, 'content_about');

        return back()->with('success', 'Contenido de la página Nosotros actualizado.');
    }

    private function saveFields(Request $request, array $fields, string $group): void
    {
        foreach ($fields as $field) {
            if ($request->has($field)) {
                SiteSetting::set($field, $request->input($field), $group);
            }
        }
    }
}
