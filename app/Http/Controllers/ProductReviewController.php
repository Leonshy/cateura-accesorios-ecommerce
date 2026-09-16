<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Services\HCaptchaVerifier;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $product = Product::active()->where('slug', $slug)->firstOrFail();

        $rules = [
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ];
        if (! auth()->check()) {
            $rules['reviewer_name'] = 'required|string|max:100';
        }

        $validated = $request->validate($rules);

        if (auth()->check() && ProductReview::where('product_id', $product->id)->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'Ya dejaste una reseña para este producto.');
        }

        if (! $this->passesHCaptcha($request)) {
            return back()->withInput()->with('error', 'No pudimos verificar que sos una persona. Intentá de nuevo.');
        }

        ProductReview::create([
            'product_id'    => $product->id,
            'user_id'       => auth()->id(),
            'reviewer_name' => auth()->check() ? auth()->user()->name : $validated['reviewer_name'],
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'] ?? null,
            'is_approved'   => false,
        ]);

        return redirect()->route('shop.product', ['slug' => $slug, 'tab' => 'reviews'])
            ->with('success', '¡Gracias por tu reseña! Se publicará luego de ser revisada por el equipo.');
    }

    private function passesHCaptcha(Request $request): bool
    {
        if (! HCaptchaVerifier::isEnabledFor('review')) {
            return true;
        }

        return HCaptchaVerifier::verify($request->input('h-captcha-response'));
    }
}
