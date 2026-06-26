<?php
namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $user = auth()->user();
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();
        return view('account.index', compact('user', 'recentOrders', 'wishlistCount'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())->with('items')->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }

    public function orderShow(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with('items.product')
            ->firstOrFail();
        return view('account.order-show', compact('order'));
    }

    public function wishlist()
    {
        $items = Wishlist::where('user_id', auth()->id())->with('product.category')->paginate(12);
        return view('account.wishlist', compact('items'));
    }

    public function wishlistToggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $existing = Wishlist::where('user_id', auth()->id())->where('product_id', $request->product_id)->first();
        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Producto eliminado de tu lista de deseos.');
        }
        Wishlist::create(['user_id' => auth()->id(), 'product_id' => $request->product_id]);
        return back()->with('success', '¡Producto añadido a tu lista de deseos!');
    }

    public function addresses()
    {
        $addresses = CustomerAddress::where('user_id', auth()->id())->get();
        return view('account.addresses', compact('addresses'));
    }

    public function profile()
    {
        $user = auth()->user()->load('profile');
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);
        auth()->user()->update(['name' => $request->name]);
        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['phone' => $request->phone]
        );
        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
