<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use Illuminate\Http\Request;

class ArtisanAdminController extends Controller
{
    public function index()
    {
        $artisans = Artisan::orderBy('order')->paginate(20);
        return view('admin.artisans.index', compact('artisans'));
    }

    public function create()
    {
        return view('admin.artisans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'specialty'        => 'nullable|string|max:100',
            'bio'              => 'required|string',
            'quote'            => 'nullable|string|max:300',
            'years_experience' => 'nullable|integer|min:0',
            'photo'            => 'nullable|image|max:5120',
            'is_active'        => 'boolean',
            'order'            => 'nullable|integer',
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('artisans', 'public');
        }
        $data['is_active'] = $request->boolean('is_active');
        Artisan::create($data);
        return redirect()->route('admin.artisans.index')->with('success', 'Artesana creada correctamente.');
    }

    public function edit(Artisan $artisan)
    {
        return view('admin.artisans.edit', compact('artisan'));
    }

    public function update(Request $request, Artisan $artisan)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'specialty'        => 'nullable|string|max:100',
            'bio'              => 'required|string',
            'quote'            => 'nullable|string|max:300',
            'years_experience' => 'nullable|integer|min:0',
            'photo'            => 'nullable|image|max:5120',
            'is_active'        => 'boolean',
            'order'            => 'nullable|integer',
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('artisans', 'public');
        }
        $data['is_active'] = $request->boolean('is_active');
        $artisan->update($data);
        return redirect()->route('admin.artisans.index')->with('success', 'Artesana actualizada.');
    }

    public function destroy(Artisan $artisan)
    {
        $artisan->delete();
        return back()->with('success', 'Artesana eliminada.');
    }

    public function show(Artisan $artisan) { return redirect()->route('admin.artisans.edit', $artisan); }
}
