<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'subcategory'])->inStock();

        if ($request->filled('categoria')) {
            $category = Category::where('slug', $request->categoria)->firstOrFail();
            $query->where('category_id', $category->id);
            if ($request->filled('subcategoria')) {
                $query->whereHas('subcategory', fn($q) => $q->where('slug', $request->subcategoria));
            }
        }

        if ($request->boolean('nuevo')) $query->new();
        if ($request->boolean('destacado')) $query->featured();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%"));
        }

        $query->when($request->orden, fn($q, $orden) => match($orden) {
            'precio_asc'  => $q->orderBy('price', 'asc'),
            'precio_desc' => $q->orderBy('price', 'desc'),
            'nombre'      => $q->orderBy('name', 'asc'),
            default       => $q->latest(),
        }, fn($q) => $q->latest());

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->with('subcategories')->orderBy('order')->get();
        $currentCategory = $request->filled('categoria')
            ? Category::where('slug', $request->categoria)->first()
            : null;

        return view('shop.index', compact('products', 'categories', 'currentCategory'));
    }
}
