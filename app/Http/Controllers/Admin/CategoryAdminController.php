<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryAdminController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('order')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        $data['is_active'] = $request->boolean('is_active');
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }

    public function show(Category $category)
    {
        return redirect()->route('admin.categories.edit', $category);
    }
}
