<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $canEditor = $user->isEditor();
        $canVendedor = $user->isVendedor();

        // Cada rol solo ve las tarjetas/accesos directos de las secciones a las
        // que realmente tiene acceso (ver la matriz de roles en routes/web.php).
        $totalProducts = $canEditor ? Product::active()->count() : null;
        $lowStock = $canEditor
            ? Product::where('stock', '<=', 5)->where('is_active', true)->with('category')->latest()->take(5)->get()
            : collect();

        $totalOrders = $canVendedor ? Order::count() : null;
        $pendingOrders = $canVendedor ? Order::where('status', 'pendiente')->count() : null;
        $recentOrders = $canVendedor ? Order::with('items')->latest()->take(8)->get() : collect();
        $unreadMessages = $canVendedor ? ContactMessage::unread()->count() : null;
        $revenue = $canVendedor
            ? Order::whereIn('status', ['confirmado', 'preparando', 'enviado', 'entregado'])->sum('total')
            : null;

        $totalCustomers = $user->isAdmin()
            ? User::whereHas('roles', fn ($q) => $q->where('role', 'cliente'))->orWhereDoesntHave('roles')->count()
            : null;

        return view('admin.dashboard', compact(
            'canEditor', 'canVendedor',
            'totalProducts', 'totalOrders', 'pendingOrders', 'totalCustomers',
            'lowStock', 'recentOrders', 'unreadMessages', 'revenue'
        ));
    }
}
