<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::active()->where('slug', $slug)
            ->with(['category', 'subcategory', 'images', 'colors', 'reviews'])
            ->firstOrFail();

        $related = Product::active()->inStock()
            ->with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();

        // Un usuario autenticado solo puede dejar una reseña por producto —
        // esto oculta el formulario si ya tiene una cargada (aprobada o no).
        $userHasReviewed = auth()->check()
            && ProductReview::where('product_id', $product->id)->where('user_id', auth()->id())->exists();

        return view('shop.product', compact('product', 'related', 'userHasReviewed'));
    }
}
