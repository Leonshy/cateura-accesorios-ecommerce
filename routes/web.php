<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Admin\AboutValueAdminController;
use App\Http\Controllers\Admin\ArtisanAdminController;
use App\Http\Controllers\Admin\BannerAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\SubcategoryAdminController;
use App\Http\Controllers\Admin\ContactAdminController;
use App\Http\Controllers\Admin\ContentAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LegalAdminController;
use App\Http\Controllers\Admin\ManualAdminController;
use App\Http\Controllers\Admin\MediaAdminController;
use App\Http\Controllers\Admin\NewsletterAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\PostAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ProductReviewAdminController;
use App\Http\Controllers\Admin\SettingsAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use Illuminate\Support\Facades\Route;

// ─── Rutas públicas ──────────────────────────────────────────────────────────
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    return response("User-agent: *\nDisallow:\n\nSitemap: " . route('sitemap') . "\n")
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/llms.txt', function () {
    $categories = \App\Models\Category::active()->orderBy('order')->get();

    $lines = [
        '# Cateura Accesorios',
        '',
        '> Tienda en línea de accesorios, piezas decorativas y prendas artesanales hechas con materiales reciclados por mujeres artesanas del Bañado Sur, Asunción, Paraguay. Cada compra apoya directamente a la Asociación Mujeres Unidas del Bañado Sur.',
        '',
        '## Páginas principales',
        '- [Tienda](' . route('shop.index') . '): catálogo completo de productos disponibles.',
        '- [Artesanas](' . route('artisans.index') . '): historias de las mujeres que elaboran cada pieza.',
        '- [Noticias](' . route('posts.index') . '): novedades y eventos de la asociación.',
        '- [Nosotros](' . route('about') . '): la historia y misión del proyecto.',
        '- [Contacto](' . route('contact') . '): medios de contacto y ubicación.',
    ];

    if ($categories->isNotEmpty()) {
        $lines[] = '';
        $lines[] = '## Categorías de productos';
        foreach ($categories as $category) {
            $lines[] = '- [' . $category->name . '](' . route('shop.index', ['categoria' => $category->slug]) . ')';
        }
    }

    $lines[] = '';
    $lines[] = '## Datos estructurados';
    $lines[] = 'Cada página de producto incluye datos estructurados Schema.org (JSON-LD, tipo Product) con precio, disponibilidad y valoraciones. El sitio completo incluye datos Schema.org de tipo Organization.';
    $lines[] = '';
    $lines[] = '## Mapa del sitio';
    $lines[] = route('sitemap');

    return response(implode("\n", $lines) . "\n")->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('llms-txt');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('tienda')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('shop.product');
    Route::post('/{slug}/resenas', [ProductReviewController::class, 'store'])
        ->middleware('throttle:5,1')->name('shop.product.reviews.store');
});

Route::prefix('artesanas')->group(function () {
    Route::get('/', [ArtisanController::class, 'index'])->name('artisans.index');
    Route::get('/{slug}', [ArtisanController::class, 'show'])->name('artisans.show');
});

Route::prefix('noticias')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('posts.index');
    Route::get('/{slug}', [PostController::class, 'show'])->name('posts.show');
});

Route::get('/nosotros', fn() => view('pages.about', [
    'aboutValues' => \App\Models\AboutValue::active()->orderBy('order')->get(),
]))->name('about');
Route::get('/contacto', [ContactController::class, 'index'])->name('contact');
Route::post('/contacto', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/newsletter', [ContactController::class, 'newsletter'])->middleware('throttle:5,1')->name('newsletter.subscribe');
Route::get('/newsletter/confirmar/{token}', [ContactController::class, 'confirmNewsletter'])->name('newsletter.confirm');

// Legal pages
$legalPageView = function (string $key) {
    $legalPage = \App\Models\LegalPage::where('key', $key)->first();
    // Una página desactivada por el equipo administrativo no debe quedar accesible por URL directa.
    abort_if($legalPage && !$legalPage->is_active, 404);
    return view('pages.legal', ['key' => $key, 'legalPage' => $legalPage]);
};
Route::get('/politica-de-privacidad', fn() => $legalPageView('privacidad'))->name('legal.privacidad');
Route::get('/terminos-y-condiciones', fn() => $legalPageView('terminos'))->name('legal.terminos');
Route::get('/politicas-de-compra', fn() => $legalPageView('compra'))->name('legal.compra');
Route::get('/politicas-de-envio', fn() => $legalPageView('envio'))->name('legal.envio');
Route::get('/cambios-y-devoluciones', fn() => $legalPageView('devoluciones'))->name('legal.devoluciones');

// ─── Carrito ─────────────────────────────────────────────────────────────────
Route::prefix('carrito')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/agregar', [CartController::class, 'add'])->name('add');
    Route::patch('/item/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/item/{item}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

// ─── Checkout ────────────────────────────────────────────────────────────────
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/shipping', [CheckoutController::class, 'calculateShipping'])->name('shipping');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/confirmacion/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('confirmation');
    Route::get('/pagopar/retorno/{hash}', [CheckoutController::class, 'pagoparReturn'])->name('pagopar.return');
    Route::post('/webhooks/bancard', [CheckoutController::class, 'bancardWebhook'])->name('webhooks.bancard');
    Route::post('/webhooks/pagopar', [CheckoutController::class, 'pagoparWebhook'])->name('webhooks.pagopar');
});

// ─── Mi cuenta (clientes autenticados) ───────────────────────────────────────
Route::prefix('mi-cuenta')->name('account.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/pedidos', [AccountController::class, 'orders'])->name('orders');
    Route::get('/pedidos/{orderNumber}', [AccountController::class, 'orderShow'])->name('orders.show');
    Route::get('/lista-de-deseos', [AccountController::class, 'wishlist'])->name('wishlist');
    Route::post('/lista-de-deseos', [AccountController::class, 'wishlistToggle'])->name('wishlist.toggle');
    Route::get('/direcciones', [AccountController::class, 'addresses'])->name('addresses');
    Route::get('/perfil', [AccountController::class, 'profile'])->name('profile');
    Route::patch('/perfil', [AccountController::class, 'updateProfile'])->name('profile.update');
});

// ─── Panel Administrativo ────────────────────────────────────────────────────
// Cada sección lleva además el rol que puede usarla (el middleware `role` deja
// pasar siempre a un Admin). Ver resources/docs/manual-usuario.md para la
// matriz de roles documentada: Editor = catálogo/contenido, Vendedor = ventas,
// Admin = todo lo demás (usuarios, textos, configuración, pagos, envíos).
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Manual de uso — disponible para cualquier rol administrativo.
    Route::prefix('manual')->name('manual.')->group(function () {
        Route::get('/', [ManualAdminController::class, 'index'])->name('index');
        Route::get('/imprimir', [ManualAdminController::class, 'print'])->name('print');
        Route::get('/descargar.md', [ManualAdminController::class, 'downloadMarkdown'])->name('download-md');
    });

    // ── Catálogo y contenido (Editor + Admin) ──────────────────────────────
    Route::middleware('role:editor')->group(function () {
        // Multimedia
        Route::prefix('multimedia')->name('media.')->group(function () {
            Route::get('/', [MediaAdminController::class, 'index'])->name('index');
            Route::post('/upload', [MediaAdminController::class, 'upload'])->name('upload');
            Route::get('/picker', [MediaAdminController::class, 'picker'])->name('picker');
            Route::patch('/{media}/alt', [MediaAdminController::class, 'updateAlt'])->name('alt');
            Route::delete('/{media}', [MediaAdminController::class, 'destroy'])->name('destroy');
        });

        // Productos
        Route::resource('products', ProductAdminController::class)->names('products');

        // Categorías
        Route::resource('categories', CategoryAdminController::class)->names('categories');

        // Subcategorías
        Route::post('categories/{category}/subcategories', [SubcategoryAdminController::class, 'store'])->name('subcategories.store');
        Route::patch('subcategories/{subcategory}', [SubcategoryAdminController::class, 'update'])->name('subcategories.update');
        Route::delete('subcategories/{subcategory}', [SubcategoryAdminController::class, 'destroy'])->name('subcategories.destroy');
        Route::post('subcategories/{subcategory}/subir', [SubcategoryAdminController::class, 'moveUp'])->name('subcategories.move-up');
        Route::post('subcategories/{subcategory}/bajar', [SubcategoryAdminController::class, 'moveDown'])->name('subcategories.move-down');

        // Artesanas
        Route::resource('artisans', ArtisanAdminController::class)->names('artisans');

        // Reseñas de producto
        Route::prefix('resenas')->name('reviews.')->group(function () {
            Route::get('/', [ProductReviewAdminController::class, 'index'])->name('index');
            Route::patch('/{review}/aprobar', [ProductReviewAdminController::class, 'approve'])->name('approve');
            Route::delete('/{review}', [ProductReviewAdminController::class, 'destroy'])->name('destroy');
        });

        // Noticias/eventos
        Route::resource('posts', PostAdminController::class)->names('posts');

        // Banners
        Route::resource('banners', BannerAdminController::class)->names('banners');
    });

    // ── Ventas (Vendedor + Admin) ──────────────────────────────────────────
    Route::middleware('role:vendedor')->group(function () {
        // Pedidos
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderAdminController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.status');

        // Contacto
        Route::get('/contactos', [ContactAdminController::class, 'index'])->name('contacts.index');
        Route::get('/contactos/{message}', [ContactAdminController::class, 'show'])->name('contacts.show');
        Route::delete('/contactos/{message}', [ContactAdminController::class, 'destroy'])->name('contacts.destroy');

        // Newsletter
        Route::get('/newsletter', [NewsletterAdminController::class, 'index'])->name('newsletter.index');
    });

    // ── Solo Admin ──────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        // Usuarios
        Route::get('/usuarios', [UserAdminController::class, 'index'])->name('users.index');
        Route::get('/usuarios/{user}/edit', [UserAdminController::class, 'edit'])->name('users.edit');
        Route::patch('/usuarios/{user}', [UserAdminController::class, 'update'])->name('users.update');
        Route::post('/usuarios/{user}/restablecer-contrasena', [UserAdminController::class, 'sendPasswordReset'])->name('users.send-password-reset');

        // Legal
        Route::get('/legal', [LegalAdminController::class, 'index'])->name('legal.index');
        Route::get('/legal/{key}/edit', [LegalAdminController::class, 'edit'])->name('legal.edit');
        Route::patch('/legal/{key}', [LegalAdminController::class, 'update'])->name('legal.update');

        // Contenido de páginas públicas (cada página pública tiene su propia pantalla)
        Route::prefix('contenido')->name('content.')->group(function () {
            Route::get('/inicio', [ContentAdminController::class, 'home'])->name('home');
            Route::post('/inicio', [ContentAdminController::class, 'updateHome'])->name('home.update');
            Route::get('/artesanas', [ContentAdminController::class, 'artisans'])->name('artisans');
            Route::post('/artesanas', [ContentAdminController::class, 'updateArtisans'])->name('artisans.update');
            Route::get('/nosotros', [ContentAdminController::class, 'about'])->name('about');
            Route::post('/nosotros', [ContentAdminController::class, 'updateAbout'])->name('about.update');
        });

        // Valores (página Nosotros)
        Route::post('valores', [AboutValueAdminController::class, 'store'])->name('about-values.store');
        Route::patch('valores/{aboutValue}', [AboutValueAdminController::class, 'update'])->name('about-values.update');
        Route::delete('valores/{aboutValue}', [AboutValueAdminController::class, 'destroy'])->name('about-values.destroy');

        // Configuración
        Route::get('/configuracion', [SettingsAdminController::class, 'general'])->name('settings.general');
        Route::post('/configuracion', [SettingsAdminController::class, 'updateGeneral'])->name('settings.update');
        Route::get('/integraciones', [SettingsAdminController::class, 'integrations'])->name('settings.integrations');
        Route::patch('/metodo-pago/{paymentMethod}', [SettingsAdminController::class, 'updatePaymentMethod'])->name('settings.payment');
        Route::get('/envios', [SettingsAdminController::class, 'shipping'])->name('settings.shipping');
        Route::post('/envios', [SettingsAdminController::class, 'updateShipping'])->name('settings.shipping.update');
    });
});

require __DIR__.'/auth.php';
