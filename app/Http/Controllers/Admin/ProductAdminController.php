<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category','subcategory'])->withTrashed();
        if ($request->filled('q')) $query->where('name', 'like', '%' . $request->q . '%');
        if ($request->filled('categoria')) $query->where('category_id', $request->categoria);
        if ($request->filled('estado')) {
            if ($request->estado === 'activo') $query->where('is_active', true)->whereNull('deleted_at');
            elseif ($request->estado === 'inactivo') $query->where('is_active', false);
            elseif ($request->estado === 'eliminado') $query->onlyTrashed();
        }
        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::active()->orderBy('order')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->with('subcategories')->orderBy('order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:200',
            'category_id'       => 'required|exists:categories,id',
            'subcategory_id'    => 'nullable|exists:subcategories,id',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'price'             => 'required|integer|min:0',
            'original_price'    => 'nullable|integer|min:0',
            'stock'             => 'required|integer|min:0',
            'image'             => 'nullable|string|max:2048',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'is_new'            => 'boolean',
            'meta_title'        => 'nullable|string|max:200',
            'meta_description'  => 'nullable|string|max:500',
            'colors'            => 'nullable|array',
            'colors.*'          => 'nullable|string|max:50',
            'gallery_images'    => 'nullable|array',
            'gallery_images.*'  => 'nullable|string|max:2048',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_new']     = $request->boolean('is_new');

        $product = Product::create($data);

        if ($request->colors) {
            foreach (array_filter($request->colors) as $color) {
                $product->colors()->create(['name' => $color]);
            }
        }

        foreach (array_filter($request->input('gallery_images', [])) as $i => $url) {
            ProductImage::create(['product_id' => $product->id, 'path' => $url, 'order' => $i]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $product->load('colors', 'images');
        $categories = Category::active()->with('subcategories')->orderBy('order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:200',
            'category_id'       => 'required|exists:categories,id',
            'subcategory_id'    => 'nullable|exists:subcategories,id',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'price'             => 'required|integer|min:0',
            'original_price'    => 'nullable|integer|min:0',
            'stock'             => 'required|integer|min:0',
            'image'             => 'nullable|string|max:2048',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'is_new'            => 'boolean',
            'meta_title'        => 'nullable|string|max:200',
            'meta_description'  => 'nullable|string|max:500',
            'colors'            => 'nullable|array',
            'colors.*'          => 'nullable|string|max:50',
            'gallery_images'    => 'nullable|array',
            'gallery_images.*'  => 'nullable|string|max:2048',
        ]);

        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_new']      = $request->boolean('is_new');

        $product->update($data);

        if ($request->has('colors')) {
            $product->colors()->delete();
            foreach (array_filter($request->input('colors', [])) as $color) {
                $product->colors()->create(['name' => $color]);
            }
        }

        if ($request->has('gallery_images')) {
            $product->images()->delete();
            foreach (array_filter($request->input('gallery_images', [])) as $i => $url) {
                ProductImage::create(['product_id' => $product->id, 'path' => $url, 'order' => $i]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Producto eliminado.');
    }
}
