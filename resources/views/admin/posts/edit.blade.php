@extends('layouts.admin')
@section('title', 'Editar publicación')
@section('content')
<div class="max-w-3xl">
<form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-5">
            <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Título *</label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" required class="input-cateura border p-2 w-full">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Resumen</label>
                    <textarea name="excerpt" rows="2" class="input-cateura border p-2 w-full">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Contenido</label>
                    <textarea name="content" rows="10" class="input-cateura border p-2 w-full">{{ old('content', $post->content) }}</textarea>
                </div>
            </div>
        </div>
        <div class="space-y-5">
            <div class="bg-white border border-stone-100 shadow-sm p-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Tipo</label>
                    <select name="type" class="input-cateura border p-2 w-full">
                        <option value="noticia" {{ old('type', $post->type) === 'noticia' ? 'selected' : '' }}>Noticia</option>
                        <option value="evento" {{ old('type', $post->type) === 'evento' ? 'selected' : '' }}>Evento</option>
                    </select>
                </div>
                <x-admin.media-picker name="image" value="{{ old('image', $post->image) }}" label="Imagen" />
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Fecha de publicación</label>
                    <input type="date" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d')) }}" class="input-cateura border p-2 w-full">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Estado</label>
                    <select name="status" class="input-cateura border p-2 w-full">
                        <option value="borrador" {{ old('status', $post->status) !== 'publicado' ? 'selected' : '' }}>Borrador</option>
                        <option value="publicado" {{ old('status', $post->status) === 'publicado' ? 'selected' : '' }}>Publicado</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <button type="submit" class="btn-copper w-full">Guardar cambios</button>
                <a href="{{ route('admin.posts.index') }}" class="btn-stone w-full text-center">Cancelar</a>
            </div>
        </div>
    </div>
</form>
</div>
@endsection
