<?php
namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Artisan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->where('position', 'home_hero')->orderBy('order')->get();
        $categories = Category::active()->orderBy('order')->get();
        $featuredProducts = Product::active()->featured()->with('category')->inStock()->latest()->take(8)->get();
        $newProducts = Product::active()->new()->with('category')->inStock()->latest()->take(8)->get();
        $featuredArtisans = Artisan::active()->orderBy('order')->take(3)->get();
        $recentPosts = Post::published()->latest('published_at')->take(3)->get();

        return view('home', compact(
            'banners', 'categories', 'featuredProducts', 'newProducts',
            'featuredArtisans', 'recentPosts'
        ));
    }
}
