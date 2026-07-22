@props([
    'name'   => 'image',
    'value'  => '',
    'label'  => 'Imagen',
    'accept' => 'image/*',
])

@php
    $pickerId   = 'mp_' . Str::random(8);
    $acceptJson = json_encode($accept);
    $resolvedValue = media_url($value);
@endphp

<div x-data="mediaPicker_{{ $pickerId }}()" x-init="init()">
    <label class="block text-xs font-medium text-stone-600 mb-1">{{ $label }}</label>

    <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
            <div x-show="currentUrl" class="w-20 h-20 border border-stone-200 overflow-hidden bg-stone-50 flex items-center justify-center">
                <img x-show="currentUrl" :src="currentUrl" alt="" class="w-full h-full object-cover">
            </div>
            <div x-show="!currentUrl" class="w-20 h-20 border-2 border-dashed border-stone-200 flex items-center justify-center text-stone-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <div class="flex flex-col gap-2 flex-1 min-w-0">
            <input type="hidden" name="{{ $name }}" :value="currentUrl">
            <div class="flex gap-2 flex-wrap">
                <button type="button" @click="openModal()" class="text-xs bg-stone-800 text-white px-3 py-1.5 hover:bg-stone-700 transition-colors uppercase tracking-wide">
                    Seleccionar / Subir
                </button>
                <button type="button" x-show="currentUrl" @click="currentUrl = ''" class="text-xs text-red-500 hover:text-red-700 transition-colors uppercase tracking-wide">
                    Quitar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div x-show="open" x-cloak style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="open = false">
        <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative bg-white shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col z-10">

            <div class="flex items-center justify-between p-5 border-b border-stone-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="font-medium text-stone-800">{{ $label }}</h3>
                    <div class="flex border border-stone-200 overflow-hidden text-sm">
                        <button type="button" @click="tab = 'library'" :class="tab === 'library' ? 'bg-stone-800 text-white' : 'text-stone-600 hover:bg-stone-50'" class="px-3 py-1.5 transition-colors">Biblioteca</button>
                        <button type="button" @click="tab = 'upload'" :class="tab === 'upload' ? 'bg-stone-800 text-white' : 'text-stone-600 hover:bg-stone-50'" class="px-3 py-1.5 transition-colors">Subir</button>
                        <button type="button" @click="tab = 'url'" :class="tab === 'url' ? 'bg-stone-800 text-white' : 'text-stone-600 hover:bg-stone-50'" class="px-3 py-1.5 transition-colors">URL</button>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-stone-400 hover:text-stone-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Biblioteca --}}
            <div x-show="tab === 'library'" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-4 border-b border-stone-100 flex-shrink-0">
                    <input type="text" x-model.debounce.300ms="pickerSearch" placeholder="Buscar..." class="input-cateura border p-2 w-full text-sm">
                </div>
                <div class="flex-1 overflow-y-auto p-4" @scroll="handleLibraryScroll($event.target)">
                    <div x-show="pickerLoading" class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                        <template x-for="i in 15" :key="i">
                            <div class="aspect-square bg-stone-100 animate-pulse"></div>
                        </template>
                    </div>
                    <div x-show="!pickerLoading && pickerFiles.length === 0" class="text-center py-12 text-stone-400">
                        <p class="text-sm">No hay archivos en la biblioteca.</p>
                        <button type="button" @click="tab = 'upload'" class="text-copper-600 text-sm mt-1 hover:underline">Subir un archivo</button>
                    </div>
                    <div x-show="!pickerLoading" class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                        <template x-for="file in pickerFiles" :key="file.id">
                            <div class="relative aspect-square bg-stone-50 border-2 overflow-hidden cursor-pointer transition-all group"
                                 :class="selectedId === file.id ? 'border-copper-500 ring-2 ring-copper-300' : 'border-stone-200 hover:border-copper-300'"
                                 @click="selectFile(file)">
                                <img x-show="file.is_image" :src="file.file_url" :alt="file.alt_text ?? ''" class="w-full h-full object-cover">
                                <div x-show="!file.is_image" class="w-full h-full flex items-center justify-center text-stone-400 text-xs p-2 text-center font-bold uppercase" x-text="file.file_name.split('.').pop()"></div>
                                <div x-show="selectedId === file.id" class="absolute inset-0 bg-copper-600/20 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-copper-600 bg-white rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" @click.stop="copyUrl(file)" title="Copiar URL"
                                            class="bg-white/90 text-stone-600 hover:text-copper-600 rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                        <svg x-show="copiedId !== file.id" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 4h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2z"/></svg>
                                        <svg x-show="copiedId === file.id" class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button type="button" @click.stop="deleteFile(file)" title="Eliminar de la biblioteca"
                                            class="bg-white/90 text-red-500 hover:text-red-700 rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="loadingMore" class="flex justify-center py-4 mt-2 text-sm text-stone-400">Cargando más...</div>
                </div>
                <div class="p-4 border-t border-stone-100 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" @click="open = false" class="px-4 py-2 border border-stone-300 text-sm text-stone-600 hover:bg-stone-50">Cancelar</button>
                    <button type="button" @click="confirmSelection()" :disabled="!selectedId" class="px-4 py-2 bg-copper-600 text-white text-sm font-medium hover:bg-copper-700 disabled:opacity-40 transition-colors">Seleccionar</button>
                </div>
            </div>

            {{-- Subir --}}
            <div x-show="tab === 'upload'" class="flex flex-col flex-1 overflow-hidden">
                <div class="flex-1 flex flex-col items-center justify-center p-8" @dragover.prevent="uploadDragging = true" @dragleave.prevent="uploadDragging = false" @drop.prevent="handlePickerDrop($event)">
                    <label class="w-full cursor-pointer">
                        <div :class="uploadDragging ? 'border-copper-500 bg-copper-50' : 'border-stone-300 bg-stone-50'" class="border-2 border-dashed p-12 text-center transition-colors">
                            <svg class="w-12 h-12 mx-auto mb-4 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-stone-600 font-medium mb-1">Arrastrá archivos aquí</p>
                            <p class="text-sm text-stone-400">o hacé clic para seleccionar</p>
                            <p class="text-xs text-stone-400 mt-2">Máximo 10 MB</p>
                        </div>
                        <input type="file" class="hidden" accept="{{ $accept }}" @change="handlePickerUpload($event)">
                    </label>
                    <div x-show="pickerUploading" class="mt-4 w-full max-w-sm">
                        <div class="flex justify-between text-sm text-stone-600 mb-1">
                            <span>Subiendo...</span>
                            <span x-text="pickerUploadProgress + '%'"></span>
                        </div>
                        <div class="w-full bg-stone-200 h-2">
                            <div class="bg-copper-600 h-2 transition-all" :style="'width:' + pickerUploadProgress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- URL --}}
            <div x-show="tab === 'url'" class="flex flex-col flex-1 overflow-hidden">
                <div class="flex-1 flex flex-col items-center justify-center p-8 gap-4">
                    <div class="w-full max-w-md">
                        <label class="block text-sm font-medium text-stone-700 mb-2">Pegá la URL de la imagen</label>
                        <input type="url" x-model="manualUrl" placeholder="https://ejemplo.com/imagen.jpg" class="input-cateura border p-2 w-full text-sm">
                        <div x-show="manualUrl" class="mt-3 overflow-hidden border border-stone-200 bg-stone-50 flex items-center justify-center h-32">
                            <img :src="manualUrl" alt="Preview" class="max-h-full max-w-full object-contain" x-on:error="$el.style.display='none'">
                        </div>
                    </div>
                    <button type="button" @click="confirmUrl()" :disabled="!manualUrl" class="px-6 py-2 bg-copper-600 text-white text-sm font-medium hover:bg-copper-700 disabled:opacity-40 transition-colors">Usar esta URL</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function mediaPicker_{{ $pickerId }}() {
    const ACCEPT = {!! $acceptJson !!};

    function mimeAllowed(mime) {
        if (!mime) return true;
        return ACCEPT.split(',').some(pattern => {
            pattern = pattern.trim();
            if (pattern === '*' || pattern === '*/*') return true;
            if (pattern.endsWith('/*')) return mime.startsWith(pattern.slice(0, -1));
            return mime === pattern;
        });
    }

    return {
        open: false,
        tab: 'library',
        currentUrl: {!! json_encode($resolvedValue) !!},
        manualUrl: '',
        pickerSearch: '',
        pickerFiles: [],
        pickerLoading: false,
        loadingMore: false,
        hasMore: false,
        currentPage: 1,
        selectedId: null,
        selectedFile: null,
        uploadDragging: false,
        pickerUploading: false,
        pickerUploadProgress: 0,
        copiedId: null,
        _searchTimer: null,

        init() {
            this.$watch('open', val => { if (val && !this.pickerFiles.length) this.loadPickerFiles(); });
            this.$watch('pickerSearch', () => {
                clearTimeout(this._searchTimer);
                this._searchTimer = setTimeout(() => this.loadPickerFiles(), 350);
            });
        },

        openModal() {
            this.open = true;
            this.tab = 'library';
            this.manualUrl = '';
        },

        async copyUrl(file) {
            try {
                await navigator.clipboard.writeText(file.file_url);
                this.copiedId = file.id;
                setTimeout(() => { if (this.copiedId === file.id) this.copiedId = null; }, 1500);
            } catch {
                alert('No se pudo copiar la URL.');
            }
        },

        async deleteFile(file) {
            if (!confirm(`¿Eliminar "${file.file_name}" de la biblioteca? Esta acción no se puede deshacer.`)) return;
            const res = await fetch(`/admin/multimedia/${file.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            });
            if (res.ok) {
                this.pickerFiles = this.pickerFiles.filter(f => f.id !== file.id);
                if (this.selectedId === file.id) { this.selectedId = null; this.selectedFile = null; }
            } else {
                alert('No se pudo eliminar el archivo.');
            }
        },

        async fetchPage(page) {
            const url = new URL('{{ route('admin.media.picker') }}', window.location.origin);
            url.searchParams.set('page', page);
            if (this.pickerSearch) url.searchParams.set('q', this.pickerSearch);
            const res  = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const files = (data.files ?? []).filter(f => mimeAllowed(f.mime_type));
            this.pickerFiles = page === 1 ? files : [...this.pickerFiles, ...files];
            this.hasMore = data.has_more ?? false;
            this.currentPage = page;
        },

        async loadPickerFiles() {
            this.pickerLoading = true;
            this.pickerFiles = [];
            try { await this.fetchPage(1); }
            finally { this.pickerLoading = false; }
        },

        async loadMore() {
            if (!this.hasMore || this.loadingMore) return;
            this.loadingMore = true;
            try { await this.fetchPage(this.currentPage + 1); }
            finally { this.loadingMore = false; }
        },

        handleLibraryScroll(el) {
            if (!this.hasMore || this.loadingMore || this.pickerLoading) return;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 120) this.loadMore();
        },

        selectFile(file) {
            this.selectedId = file.id;
            this.selectedFile = file;
        },

        confirmSelection() {
            if (this.selectedFile) {
                this.currentUrl = this.selectedFile.file_url;
                this.open = false;
                this.selectedId = null;
                this.selectedFile = null;
            }
        },

        confirmUrl() {
            if (this.manualUrl) {
                this.currentUrl = this.manualUrl;
                this.manualUrl = '';
                this.open = false;
            }
        },

        handlePickerDrop(e) {
            this.uploadDragging = false;
            const files = Array.from(e.dataTransfer.files);
            if (files.length) this.uploadPickerFiles(files);
        },

        handlePickerUpload(e) {
            const files = Array.from(e.target.files);
            if (files.length) this.uploadPickerFiles(files);
            e.target.value = '';
        },

        async uploadPickerFiles(fileList) {
            this.pickerUploading = true;
            this.pickerUploadProgress = 0;
            const formData = new FormData();
            fileList.forEach(f => formData.append('files[]', f));
            formData.append('_token', '{{ csrf_token() }}');
            try {
                const xhr = new XMLHttpRequest();
                xhr.upload.onprogress = e => {
                    if (e.lengthComputable) this.pickerUploadProgress = Math.round((e.loaded / e.total) * 100);
                };
                await new Promise((resolve, reject) => {
                    xhr.onload = () => {
                        if (xhr.status === 201) {
                            const data = JSON.parse(xhr.responseText);
                            const uploaded = data.uploaded ?? [];
                            this.pickerFiles = [...uploaded, ...this.pickerFiles];
                            if (uploaded.length) {
                                this.selectFile(uploaded[0]);
                                this.tab = 'library';
                            }
                            resolve();
                        } else reject(new Error('Upload failed'));
                    };
                    xhr.onerror = reject;
                    xhr.open('POST', '{{ route('admin.media.upload') }}');
                    xhr.send(formData);
                });
            } catch {
                alert('Error al subir el archivo. Verificá el formato e intentá de nuevo.');
            } finally {
                this.pickerUploading = false;
                this.pickerUploadProgress = 0;
            }
        },
    };
}
</script>
