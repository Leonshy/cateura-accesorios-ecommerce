@extends('layouts.admin')
@section('title', 'Biblioteca multimedia')
@section('content')
<div x-data="mediaLibraryPage()" x-init="init()">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="font-display text-2xl text-stone-800">Biblioteca multimedia</h1>
            <p class="text-sm text-stone-500 mt-1">{{ $total }} archivo(s) en total</p>
        </div>
        <label class="btn-copper cursor-pointer">
            Subir archivos
            <input type="file" class="hidden" multiple @change="handleUpload($event)">
        </label>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre..."
               class="input-cateura border p-2 flex-1 min-w-[200px]">
        <select name="type" class="input-cateura border p-2">
            <option value="">Todos los tipos</option>
            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Imágenes</option>
            <option value="application" {{ request('type') === 'application' ? 'selected' : '' }}>Documentos</option>
        </select>
        <button type="submit" class="btn-copper-outline">Filtrar</button>
    </form>

    <div x-show="uploading" class="mb-4 max-w-sm">
        <div class="flex justify-between text-xs text-stone-500 mb-1">
            <span>Subiendo...</span>
            <span x-text="progress + '%'"></span>
        </div>
        <div class="w-full bg-stone-200 h-1.5">
            <div class="bg-copper-500 h-1.5 transition-all" :style="'width:' + progress + '%'"></div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm mb-4">{{ session('success') }}</div>
    @endif

    @if($files->isEmpty())
    <div class="text-center py-20 text-stone-400 border border-dashed border-stone-200">
        <p>No hay archivos en la biblioteca todavía.</p>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-4">
        @foreach($files as $file)
        <div class="border border-stone-100 bg-white group relative">
            <div class="aspect-square bg-stone-50 overflow-hidden flex items-center justify-center">
                @if(str_starts_with($file->mime_type ?? '', 'image/'))
                <img src="{{ $file->file_url }}" alt="{{ $file->alt_text }}" class="w-full h-full object-cover">
                @else
                <span class="text-xs uppercase text-stone-400 font-medium">{{ pathinfo($file->file_name, PATHINFO_EXTENSION) }}</span>
                @endif
                <div class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button type="button" @click="copyUrl('{{ addslashes($file->file_url) }}', {{ $file->id }})" title="Copiar URL"
                            class="bg-white/90 text-stone-600 hover:text-copper-600 rounded-full w-6 h-6 flex items-center justify-center text-xs">
                        <svg x-show="copiedId !== {{ $file->id }}" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 4h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2z"/></svg>
                        <svg x-show="copiedId === {{ $file->id }}" class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <button type="button" @click="remove({{ $file->id }}, $el.closest('.group'))"
                            class="bg-white/90 text-red-500 rounded-full w-6 h-6 flex items-center justify-center text-xs">
                        ✕
                    </button>
                </div>
            </div>
            <div class="p-2">
                <p class="text-xs text-stone-600 truncate" :title="'{{ addslashes($file->file_name) }}'">{{ $file->file_name }}</p>
                <p class="text-[10px] text-stone-400">{{ $file->size_label }}</p>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-8">{{ $files->links() }}</div>
    @endif
</div>

<script>
function mediaLibraryPage() {
    return {
        uploading: false,
        progress: 0,
        copiedId: null,
        init() {},
        async copyUrl(url, id) {
            try {
                await navigator.clipboard.writeText(url);
                this.copiedId = id;
                setTimeout(() => { if (this.copiedId === id) this.copiedId = null; }, 1500);
            } catch {
                alert('No se pudo copiar la URL.');
            }
        },
        handleUpload(e) {
            const files = Array.from(e.target.files);
            e.target.value = '';
            if (!files.length) return;
            this.uploading = true;
            this.progress = 0;
            const formData = new FormData();
            files.forEach(f => formData.append('files[]', f));
            formData.append('_token', '{{ csrf_token() }}');
            const xhr = new XMLHttpRequest();
            xhr.upload.onprogress = (ev) => {
                if (ev.lengthComputable) this.progress = Math.round((ev.loaded / ev.total) * 100);
            };
            xhr.onload = () => {
                this.uploading = false;
                window.location.reload();
            };
            xhr.onerror = () => { this.uploading = false; alert('Error al subir el archivo.'); };
            xhr.open('POST', '{{ route('admin.media.upload') }}');
            xhr.send(formData);
        },
        async remove(id, el) {
            if (!confirm('¿Eliminar este archivo de la biblioteca?')) return;
            const res = await fetch(`/admin/multimedia/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            });
            if (res.ok) el.remove();
        },
    };
}
</script>
@endsection
