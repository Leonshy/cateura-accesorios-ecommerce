<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts   = Product::active()->count();
        $totalOrders     = Order::count();
        $pendingOrders   = Order::where('status', 'pendiente')->count();
        $totalCustomers  = User::whereHas('roles', fn($q) => $q->where('role', 'cliente'))->orWhereDoesntHave('roles')->count();
        $lowStock        = Product::where('stock', '<=', 5)->where('is_active', true)->with('category')->latest()->take(5)->get();
        $recentOrders    = Order::with('items')->latest()->take(8)->get();
        $unreadMessages  = ContactMessage::unread()->count();
        $revenue         = Order::whereIn('status', ['confirmado','preparando','enviado','entregado'])->sum('total');

        return view('admin.dashboard', compact(
            'totalProducts', 'totalOrders', 'pendingOrders', 'totalCustomers',
            'lowStock', 'recentOrders', 'unreadMessages', 'revenue'
        ));
    }
}
