<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryAdminController extends Controller
{
    public function store(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
        ]);
        $data['is_active'] = true;
        $category->subcategories()->create($data);

        return back()->with('success', 'Subcategoría creada.');
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $subcategory->update($data);

        return back()->with('success', 'Subcategoría actualizada.');
    }

    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();
        return back()->with('success', 'Subcategoría eliminada.');
    }
}
