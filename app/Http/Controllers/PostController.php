<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::published()->latest('published_at');
        if ($request->filled('tipo')) $query->where('type', $request->tipo);
        $posts = $query->paginate(9)->withQueryString();
        return view('posts.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $related = Post::published()->where('type', $post->type)->where('id', '!=', $post->id)->latest('published_at')->take(3)->get();
        return view('posts.show', compact('post', 'related'));
    }
}
