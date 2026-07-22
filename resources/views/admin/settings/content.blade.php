@extends('layouts.admin')
@section('title', 'Contenido de páginas')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.settings.content.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Sección "Nuestra historia" (portada)</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto pequeño (eyebrow)</label>
            <input type="text" name="historia_eyebrow" value="{{ $settings['historia_eyebrow'] ?? 'Nuestra historia' }}" class="input-cateura border p-2 w-full">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Título línea 1</label>
                <input type="text" name="historia_title_line1" value="{{ $settings['historia_title_line1'] ?? 'Manos que transforman' }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Título línea 2 (color)</label>
                <input type="text" name="historia_title_line2" value="{{ $settings['historia_title_line2'] ?? 'el mundo' }}" class="input-cateura border p-2 w-full">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto descriptivo</label>
            <textarea name="historia_text" rows="4" class="input-cateura border p-2 w-full">{{ $settings['historia_text'] ?? 'Cateura Accesorios es la marca de los productos elaborados por artesanas de la Asociación Mujeres Unidas del Bañado Sur. A partir de materiales reciclados, transforman con sus manos objetos descartados en artículos de gran belleza, transmitiendo esperanza, dignidad y compromiso con la comunidad.' }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Estadística 1</label>
                <input type="text" name="historia_stat1_value" placeholder="Valor" value="{{ $settings['historia_stat1_value'] ?? '+50' }}" class="input-cateura border p-2 w-full mb-2">
                <input type="text" name="historia_stat1_label" placeholder="Etiqueta" value="{{ $settings['historia_stat1_label'] ?? 'Artesanas' }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Estadística 2</label>
                <input type="text" name="historia_stat2_value" placeholder="Valor" value="{{ $settings['historia_stat2_value'] ?? '100%' }}" class="input-cateura border p-2 w-full mb-2">
                <input type="text" name="historia_stat2_label" placeholder="Etiqueta" value="{{ $settings['historia_stat2_label'] ?? 'Reciclado' }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Estadística 3</label>
                <input type="text" name="historia_stat3_value" placeholder="Valor" value="{{ $settings['historia_stat3_value'] ?? 'PY' }}" class="input-cateura border p-2 w-full mb-2">
                <input type="text" name="historia_stat3_label" placeholder="Etiqueta" value="{{ $settings['historia_stat3_label'] ?? 'Hecho en PY' }}" class="input-cateura border p-2 w-full">
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
            @foreach([1,2,3,4] as $i)
            <x-admin.media-picker name="historia_img{{ $i }}" value="{{ $settings['historia_img'.$i] ?? '' }}" label="Foto {{ $i }}" />
            @endforeach
        </div>
    </div>

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Encabezado "Nuestras Artesanas"</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto pequeño (eyebrow)</label>
            <input type="text" name="artisans_eyebrow" value="{{ $settings['artisans_eyebrow'] ?? 'Mujeres Unidas del Bañado Sur' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Título</label>
            <input type="text" name="artisans_title" value="{{ $settings['artisans_title'] ?? 'Nuestras Artesanas' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Subtítulo</label>
            <textarea name="artisans_subtitle" rows="2" class="input-cateura border p-2 w-full">{{ $settings['artisans_subtitle'] ?? 'Cada pieza lleva las manos y el corazón de mujeres emprendedoras que transforman materiales reciclados en arte.' }}</textarea>
        </div>
    </div>

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Página "Nosotros" — Hero</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto pequeño (eyebrow)</label>
            <input type="text" name="about_hero_eyebrow" value="{{ $settings['about_hero_eyebrow'] ?? 'Quiénes somos' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Título</label>
            <input type="text" name="about_hero_title" value="{{ $settings['about_hero_title'] ?? 'Una historia de resiliencia y arte' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto</label>
            <textarea name="about_hero_text" rows="3" class="input-cateura border p-2 w-full">{{ $settings['about_hero_text'] ?? 'Cateura Accesorios nació en el corazón del Bañado Sur de Asunción, donde mujeres emprendedoras transforman materiales reciclados del instrumento más famoso del mundo en joyas y accesorios únicos.' }}</textarea>
        </div>
    </div>

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Página "Nosotros" — Nuestra historia</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto pequeño (eyebrow)</label>
            <input type="text" name="about_historia_eyebrow" value="{{ $settings['about_historia_eyebrow'] ?? 'Nuestra historia' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Título</label>
            <input type="text" name="about_historia_title" value="{{ $settings['about_historia_title'] ?? 'Del vertedero al mundo' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Párrafo 1</label>
            <textarea name="about_historia_text1" rows="3" class="input-cateura border p-2 w-full">{{ $settings['about_historia_text1'] ?? 'La Asociación Mujeres Unidas del Bañado Sur es una organización comunitaria que trabaja para generar empleo digno y sustentable para las familias del barrio. Inspiradas por la orquesta de instrumentos reciclados de Cateura, estas mujeres decidieron convertir el material desechado en arte.' }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Párrafo 2</label>
            <textarea name="about_historia_text2" rows="3" class="input-cateura border p-2 w-full">{{ $settings['about_historia_text2'] ?? 'Cada accesorio que crea Cateura Accesorios lleva consigo una historia de superación, creatividad y comunidad. Los materiales provienen de residuos que son transformados con maestría artesanal en piezas de joyería, objetos para el hogar y textiles únicos.' }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Párrafo 3</label>
            <textarea name="about_historia_text3" rows="3" class="input-cateura border p-2 w-full">{{ $settings['about_historia_text3'] ?? 'Cuando compras un producto Cateura Accesorios, contribuís directamente al sustento de familias paraguayas y a un modelo de economía circular que cuida el planeta.' }}</textarea>
        </div>
        <x-admin.media-picker name="about_hero_image" value="{{ $settings['about_hero_image'] ?? '' }}" label="Foto institucional" />
    </div>

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Página "Nosotros" — Nuestros valores</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Texto pequeño (eyebrow)</label>
                <input type="text" name="about_valores_eyebrow" value="{{ $settings['about_valores_eyebrow'] ?? 'Lo que nos mueve' }}" class="input-cateura border p-2 w-full">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Título</label>
                <input type="text" name="about_valores_title" value="{{ $settings['about_valores_title'] ?? 'Nuestros valores' }}" class="input-cateura border p-2 w-full">
            </div>
        </div>
        @php
            $defaultValores = [
                1 => ['Sostenibilidad', 'Trabajamos con materiales reciclados para crear productos que cuidan el medio ambiente y reducen los residuos en nuestra comunidad.'],
                2 => ['Comercio justo', 'Cada venta impacta directamente en la economía de las artesanas y sus familias, garantizando un precio justo por su trabajo.'],
                3 => ['Arte y tradición', 'Fusionamos técnicas artesanales tradicionales con diseños contemporáneos para crear piezas únicas con identidad paraguaya.'],
            ];
        @endphp
        @foreach($defaultValores as $i => [$defaultTitle, $defaultText])
        <div class="border-t border-stone-100 pt-4">
            <label class="block text-xs font-medium text-stone-600 mb-1">Valor {{ $i }} — título</label>
            <input type="text" name="about_valor{{ $i }}_title" value="{{ $settings['about_valor'.$i.'_title'] ?? $defaultTitle }}" class="input-cateura border p-2 w-full mb-2">
            <label class="block text-xs font-medium text-stone-600 mb-1">Valor {{ $i }} — texto</label>
            <textarea name="about_valor{{ $i }}_text" rows="2" class="input-cateura border p-2 w-full">{{ $settings['about_valor'.$i.'_text'] ?? $defaultText }}</textarea>
        </div>
        @endforeach
    </div>

    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <h3 class="font-medium text-stone-700 border-b border-stone-100 pb-3">Página "Nosotros" — Llamado a la acción final</h3>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Título</label>
            <input type="text" name="about_cta_title" value="{{ $settings['about_cta_title'] ?? 'Conocé a nuestras artesanas' }}" class="input-cateura border p-2 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-600 mb-1">Texto</label>
            <input type="text" name="about_cta_text" value="{{ $settings['about_cta_text'] ?? 'Detrás de cada pieza hay una mujer con una historia extraordinaria.' }}" class="input-cateura border p-2 w-full">
        </div>
    </div>

    <button type="submit" class="btn-copper">Guardar contenido</button>
</form>
</div>
@endsection
