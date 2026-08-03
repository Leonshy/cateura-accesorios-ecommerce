@php
    $tabs = [
        'home'     => ['label' => 'Inicio',    'route' => 'admin.content.home'],
        'artisans' => ['label' => 'Artesanas', 'route' => 'admin.content.artisans'],
        'about'    => ['label' => 'Nosotros',  'route' => 'admin.content.about'],
    ];
@endphp
<div class="border-b border-stone-200 mb-6">
    <nav class="flex gap-6 -mb-px">
        @foreach($tabs as $key => $tab)
        <a href="{{ route($tab['route']) }}"
           class="py-3 text-sm border-b-2 transition-colors {{ $active === $key ? 'border-copper-500 text-copper-600 font-medium' : 'border-transparent text-stone-500 hover:text-stone-700 hover:border-stone-300' }}">
            {{ $tab['label'] }}
        </a>
        @endforeach
    </nav>
</div>
