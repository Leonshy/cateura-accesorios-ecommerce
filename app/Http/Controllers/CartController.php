<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getOrCreateCart(): Cart
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        $sessionId = session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId, 'user_id' => null]);
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product');
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
            'color'      => 'nullable|string|max:50',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active || $product->stock < $request->quantity) {
            return back()->with('error', 'Producto no disponible o sin stock suficiente.');
        }

        $cart = $this->getOrCreateCart();
        $color = $request->color ?? '';

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('color', $color)
            ->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', 'No hay suficiente stock disponible.');
            }
            $item->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'color'      => $color,
                'quantity'   => $request->quantity,
                'unit_price' => $product->price,
            ]);
        }

        return back()->with('success', '¡Producto agregado al carrito!')
            ->with('success_link', route('cart.index'))
            ->with('success_link_label', 'Ver carrito');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $this->authorizeItem($item);
        $item->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Carrito actualizado.');
    }

    public function remove(CartItem $item)
    {
        $this->authorizeItem($item);
        $item->delete();
        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function clear()
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();
        return back()->with('success', 'Carrito vaciado.');
    }

    private function authorizeItem(CartItem $item): void
    {
        $cart = $this->getOrCreateCart();
        abort_if($item->cart_id !== $cart->id, 403);
    }

    public function mini()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product');
        return view('partials.cart-mini', compact('cart'));
    }
}
