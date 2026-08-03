<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use League\CommonMark\CommonMarkConverter;

class ManualAdminController extends Controller
{
    private function markdown(): string
    {
        return file_get_contents(resource_path('docs/manual-usuario.md'));
    }

    public function index()
    {
        $converter = new CommonMarkConverter();
        $html = (string) $converter->convert($this->markdown());
        return view('admin.manual.index', compact('html'));
    }

    public function print()
    {
        $converter = new CommonMarkConverter();
        $html = (string) $converter->convert($this->markdown());
        return view('admin.manual.print', compact('html'));
    }

    public function downloadMarkdown()
    {
        return response($this->markdown(), 200, [
            'Content-Type'        => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="manual-usuario-cateura.md"',
        ]);
    }
}
