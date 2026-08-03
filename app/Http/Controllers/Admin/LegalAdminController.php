<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class LegalAdminController extends Controller
{
    private array $pageKeys = [
        'privacidad'   => ['title' => 'Política de privacidad'],
        'terminos'     => ['title' => 'Términos y condiciones'],
        'compra'       => ['title' => 'Políticas de compra'],
        'envio'        => ['title' => 'Políticas de envío'],
        'devoluciones' => ['title' => 'Cambios y devoluciones'],
    ];

    public function index()
    {
        $pages = $this->pageKeys;
        $legalPages = LegalPage::whereIn('key', array_keys($pages))->get()->keyBy('key');
        return view('admin.legal.index', compact('pages', 'legalPages'));
    }

    public function edit(string $key)
    {
        abort_unless(array_key_exists($key, $this->pageKeys), 404);
        $legalPage = LegalPage::where('key', $key)->first();
        return view('admin.legal.edit', compact('key', 'legalPage'));
    }

    public function update(Request $request, string $key)
    {
        abort_unless(array_key_exists($key, $this->pageKeys), 404);
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $data['content'] = HtmlSanitizer::clean($data['content']);

        // Privacidad y términos son de aceptación obligatoria en el checkout:
        // siempre quedan visibles, sin importar lo que llegue en el formulario.
        $data['is_active'] = in_array($key, LegalPage::ALWAYS_VISIBLE_KEYS, true)
            ? true
            : $request->boolean('is_active');

        LegalPage::updateOrCreate(['key' => $key], $data);
        return back()->with('success', 'Página actualizada correctamente.');
    }
}
