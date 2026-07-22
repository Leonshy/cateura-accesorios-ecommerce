<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Admin\AboutValueAdminController;
use App\Http\Controllers\Admin\ArtisanAdminController;
use App\Http\Controllers\Admin\BannerAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\SubcategoryAdminController;
use App\Http\Controllers\Admin\ContactAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LegalAdminController;
use App\Http\Controllers\Admin\MediaAdminController;
use App\Http\Controllers\Admin\NewsletterAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\PostAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\SettingsAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use Illuminate\Support\Facades\Route;

// ─── Rutas públicas ──────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('tienda')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('shop.product');
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
Route::post('/contacto', [ContactController::class, 'store'])->name('contact.store');
Route::post('/newsletter', [ContactController::class, 'newsletter'])->name('newsletter.subscribe');

// Legal pages
Route::get('/politica-de-privacidad', fn() => view('pages.legal', ['key' => 'privacidad']))->name('legal.privacidad');
Route::get('/terminos-y-condiciones', fn() => view('pages.legal', ['key' => 'terminos']))->name('legal.terminos');
Route::get('/politicas-de-compra', fn() => view('pages.legal', ['key' => 'compra']))->name('legal.compra');
Route::get('/politicas-de-envio', fn() => view('pages.legal', ['key' => 'envio']))->name('legal.envio');
Route::get('/cambios-y-devoluciones', fn() => view('pages.legal', ['key' => 'devoluciones']))->name('legal.devoluciones');

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
Route::prefix('mi-cuenta')->name('account.')->middleware('auth')->group(function () {
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
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

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

    // Artesanas
    Route::resource('artisans', ArtisanAdminController::class)->names('artisans');

    // Noticias/eventos
    Route::resource('posts', PostAdminController::class)->names('posts');

    // Banners
    Route::resource('banners', BannerAdminController::class)->names('banners');

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

    // Usuarios
    Route::get('/usuarios', [UserAdminController::class, 'index'])->name('users.index');
    Route::get('/usuarios/{user}/edit', [UserAdminController::class, 'edit'])->name('users.edit');
    Route::patch('/usuarios/{user}', [UserAdminController::class, 'update'])->name('users.update');

    // Legal
    Route::get('/legal', [LegalAdminController::class, 'index'])->name('legal.index');
    Route::get('/legal/{key}/edit', [LegalAdminController::class, 'edit'])->name('legal.edit');
    Route::patch('/legal/{key}', [LegalAdminController::class, 'update'])->name('legal.update');

    // Contenido de páginas
    Route::get('/contenido', [SettingsAdminController::class, 'content'])->name('settings.content');
    Route::post('/contenido', [SettingsAdminController::class, 'updateContent'])->name('settings.content.update');

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

require __DIR__.'/auth.php';
