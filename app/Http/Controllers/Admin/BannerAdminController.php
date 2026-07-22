<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerAdminController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'cta_label' => 'nullable|string|max:100',
            'cta_url'  => 'nullable|string|max:255',
            'image'       => 'required|string|max:2048',
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['order']     = $request->input('order', 0);
        Banner::create($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner creado.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'cta_label' => 'nullable|string|max:100',
            'cta_url'  => 'nullable|string|max:255',
            'image'       => 'nullable|string|max:2048',
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $banner->update($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner actualizado.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return back()->with('success', 'Banner eliminado.');
    }

    public function show(Banner $banner)
    {
        return redirect()->route('admin.banners.edit', $banner);
    }
}
