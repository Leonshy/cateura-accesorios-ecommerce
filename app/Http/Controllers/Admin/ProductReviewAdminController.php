<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('estado', 'pendiente');
        $query  = ProductReview::with('product')->latest();

        if ($status === 'pendiente') {
            $query->where('is_approved', false);
        } elseif ($status === 'aprobada') {
            $query->where('is_approved', true);
        }

        $reviews      = $query->paginate(20)->withQueryString();
        $pendingCount = ProductReview::where('is_approved', false)->count();

        return view('admin.reviews.index', compact('reviews', 'status', 'pendingCount'));
    }

    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Reseña aprobada y publicada en la ficha del producto.');
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return back()->with('success', 'Reseña eliminada.');
    }
}
