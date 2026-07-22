<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutValue;
use Illuminate\Http\Request;

class AboutValueAdminController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:100',
            'text'  => 'required|string|max:500',
            'icon'  => 'required|string|in:' . implode(',', array_keys(AboutValue::iconOptions())),
            'order' => 'nullable|integer|min:0',
        ]);
        $data['is_active'] = true;
        AboutValue::create($data);

        return back()->with('success', 'Valor agregado.');
    }

    public function update(Request $request, AboutValue $aboutValue)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:100',
            'text'      => 'required|string|max:500',
            'icon'      => 'required|string|in:' . implode(',', array_keys(AboutValue::iconOptions())),
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $aboutValue->update($data);

        return back()->with('success', 'Valor actualizado.');
    }

    public function destroy(AboutValue $aboutValue)
    {
        $aboutValue->delete();
        return back()->with('success', 'Valor eliminado.');
    }
}
