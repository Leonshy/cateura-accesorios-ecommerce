<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::active()->where('slug', $slug)
            ->with(['category', 'subcategory', 'images', 'colors', 'reviews'])
            ->firstOrFail();

        $related = Product::active()->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();

        return view('shop.product', compact('product', 'related'));
    }
}
