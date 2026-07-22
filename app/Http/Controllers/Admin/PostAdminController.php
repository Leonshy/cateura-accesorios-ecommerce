<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostAdminController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string',
            'content'      => 'nullable|string',
            'type'         => 'required|in:noticia,evento',
            'image'        => 'nullable|string|max:2048',
            'published_at' => 'nullable|date',
            'status'       => 'nullable|in:publicado,borrador',
        ]);
        $data['status'] = $request->input('status', 'borrador');
        Post::create($data);
        return redirect()->route('admin.posts.index')->with('success', 'Publicación creada.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string',
            'content'      => 'nullable|string',
            'type'         => 'required|in:noticia,evento',
            'image'        => 'nullable|string|max:2048',
            'published_at' => 'nullable|date',
            'status'       => 'nullable|in:publicado,borrador',
        ]);
        $data['status'] = $request->input('status', 'borrador');
        $post->update($data);
        return redirect()->route('admin.posts.index')->with('success', 'Publicación actualizada.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Publicación eliminada.');
    }

    public function show(Post $post)
    {
        return redirect()->route('admin.posts.edit', $post);
    }
}
