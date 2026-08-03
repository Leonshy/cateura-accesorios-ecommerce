@extends('layouts.admin')

@section('title', 'Manual de uso')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="font-display text-2xl text-stone-800">Manual de uso de la plataforma</h2>
            <p class="text-sm text-stone-500 mt-1">Guía para la tienda pública y el panel administrativo.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.manual.download-md') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-stone-300 text-stone-600 text-sm hover:border-copper-400 hover:text-copper-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"/></svg>
                Descargar .md
            </a>
            <a href="{{ route('admin.manual.print') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-copper-500 text-white text-sm hover:bg-copper-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a1 1 0 00-1-1H8a1 1 0 00-1 1v4h10z"/></svg>
                Ver / Descargar PDF
            </a>
        </div>
    </div>

    <div class="bg-white border border-stone-200 p-8 manual-content">
        {!! $html !!}
    </div>
</div>

<style>
    .manual-content h1 { font-family: 'Cormorant Garamond', serif; font-size: 1.9rem; color: #2c2219; margin: 0 0 .5rem; }
    .manual-content > h1:first-child + p { color: #7a6c5d; margin-bottom: 1.5rem; }
    .manual-content h2 { font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; color: #2c2219; margin: 2.25rem 0 1rem; padding-bottom: .5rem; border-bottom: 1px solid #e8dcc9; }
    .manual-content h2:first-of-type { margin-top: 1rem; }
    .manual-content h3 { font-size: 1.05rem; font-weight: 600; color: #2c2219; margin: 1.25rem 0 .5rem; }
    .manual-content p { color: #44403c; margin: 0 0 .85rem; line-height: 1.7; }
    .manual-content ul, .manual-content ol { margin: 0 0 1rem; padding-left: 1.35rem; color: #44403c; line-height: 1.7; }
    .manual-content li { margin-bottom: .3rem; }
    .manual-content li > ul, .manual-content li > ol { margin-top: .3rem; }
    .manual-content strong { color: #2c2219; font-weight: 600; }
    .manual-content code { font-family: ui-monospace, monospace; font-size: .85em; background: #f7f0e6; border: 1px solid #e8dcc9; border-radius: 4px; padding: .1em .4em; color: #b5521a; }
    .manual-content blockquote { border-left: 3px solid #b5521a; background: #fdf6f0; padding: .8rem 1.1rem; margin: 1rem 0; color: #58250d; font-size: .93rem; border-radius: 0 6px 6px 0; }
    .manual-content blockquote p:last-child { margin-bottom: 0; }
    .manual-content table { width: 100%; border-collapse: collapse; margin: 1rem 0 1.5rem; font-size: .87rem; }
    .manual-content th, .manual-content td { border: 1px solid #e8dcc9; padding: .5rem .75rem; text-align: left; }
    .manual-content th { background: #f7f0e6; color: #7a6c5d; font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; }
    .manual-content hr { border: none; border-top: 1px solid #e8dcc9; margin: 2rem 0; }
</style>
@endsection
