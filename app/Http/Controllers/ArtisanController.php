<?php
namespace App\Http\Controllers;

use App\Models\Artisan;

class ArtisanController extends Controller
{
    public function index()
    {
        $artisans = Artisan::active()->orderBy('order')->paginate(12);
        return view('artisans.index', compact('artisans'));
    }

    public function show(string $slug)
    {
        $artisan = Artisan::active()->where('slug', $slug)->firstOrFail();
        return view('artisans.show', compact('artisan'));
    }
}
