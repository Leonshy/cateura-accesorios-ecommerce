@include('errors.minimal', [
    'code' => '404',
    'title' => 'Página no encontrada',
    'message' => 'La página que buscás no existe o fue movida. Puede que el enlace esté roto o el producto ya no esté disponible.',
    'icon' => '<svg class="w-9 h-9 text-copper-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
])
