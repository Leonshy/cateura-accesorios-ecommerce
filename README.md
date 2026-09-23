# Cateura Accesorios — E-commerce

E-commerce artesanal para la Asociación Mujeres Unidas del Bañado Sur (Cateura, Paraguay).
Permite a las artesanas vender sus productos reciclados directamente al público con un panel de administración propio.

**Stack:** Laravel 12 · PHP 8.2 · MySQL (prod) / SQLite (dev) · Blade · Tailwind CSS · Alpine.js

---

## Instalación local

### Requisitos
- PHP 8.2+ con extensiones: `mbstring`, `pdo_mysql`, `pdo_sqlite`, `gd`, `zip`, `bcmath`, `exif`
- Composer 2+
- Node.js 20+ y npm

### Pasos

```bash
# 1. Clonar y entrar al directorio
git clone <repo-url> cateura-accesorios-ecommerce
cd cateura-accesorios-ecommerce

# 2. Instalar dependencias PHP
composer install

# 3. Variables de entorno
cp .env.example .env
# Editar .env: cambiar APP_ENV=local, DB_CONNECTION=sqlite, comentar DB_HOST/DB_DATABASE/etc.
php artisan key:generate

# 4. Base de datos SQLite
touch database/database.sqlite
php artisan migrate --seed

# 5. Enlace de almacenamiento
php artisan storage:link

# 6. Assets frontend
npm ci
npm run dev   # o npm run build para compilar sin HMR

# 7. Servidor de desarrollo
php artisan serve
```

Accedé en: http://127.0.0.1:8000

### Usuarios de prueba
| Rol | Email | Contraseña |
|-----|-------|-----------|
| Admin | admin@cateura.test | password |
| Editor | editor@cateura.test | password |
| Vendedor | vendedor@cateura.test | password |
| Cliente | cliente@cateura.test | password |

Panel admin: http://127.0.0.1:8000/admin

---

## Despliegue en Plesk

### Pre-requisitos en el servidor
- PHP 8.2 seleccionado para el dominio
- Extensiones habilitadas: `gd`, `mbstring`, `pdo_mysql`, `zip`, `bcmath`, `exif`, `intl`
- Node.js habilitado (para compilar assets)
- `allow_url_fopen = On`
- MySQL 8+ con base de datos y usuario creados

### 1. Document Root

En Plesk → Dominios → tu-dominio.com.py → Apache & nginx → Document Root:
```
/var/www/vhosts/tu-dominio.com.py/httpdocs/public
```

### 2. Subir el proyecto

Clonar el repositorio en el servidor (recomendado) o subir por FTP/SFTP.
El proyecto debe quedar en `/var/www/vhosts/tu-dominio.com.py/httpdocs/`.

```bash
cd /var/www/vhosts/tu-dominio.com.py/httpdocs
git clone <repo-url> .
```

### 3. Variables de entorno

Crear `.env` en la raíz (nunca dentro de `public/`):

```bash
cp .env.example .env
```

Editar valores críticos:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com.py
APP_KEY=   # Generar con: php artisan key:generate

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cateura_ecommerce
DB_USERNAME=db_user
DB_PASSWORD=db_password

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true   # HTTPS obligatorio en producción
CACHE_STORE=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=mail.tu-dominio.com.py
MAIL_PORT=587
MAIL_USERNAME=hola@tu-dominio.com.py
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hola@cateuraaccesorios.com"
```

### 4. Comandos de instalación (SSH)

```bash
cd /var/www/vhosts/tu-dominio.com.py/httpdocs

# Dependencias PHP (sin dev)
composer install --no-dev --optimize-autoloader

# Generar clave de aplicación
php artisan key:generate

# Migraciones y datos iniciales (solo primera vez)
php artisan migrate --force
php artisan db:seed --force

# Enlace storage
php artisan storage:link

# Compilar assets
npm ci
npm run build

# Optimizar Laravel para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Tareas programadas (cron)

El proyecto tiene 2 tareas programadas (`routes/console.php`) que necesitan el scheduler de Laravel corriendo en el servidor:
- Purga de suscriptores de newsletter sin confirmar en 72hs (cada hora).
- Borrado de carritos de invitado abandonados por más de 5 días (diario).

Agregar en el cron del servidor (Plesk → Tareas programadas):
```bash
* * * * * cd /var/www/vhosts/tu-dominio.com.py/httpdocs && php artisan schedule:run >> /dev/null 2>&1
```

### 6. Configuración Nginx (Plesk → nginx)

En "Directivas adicionales de nginx":
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ /\.env {
    deny all;
}
```

### Actualizaciones futuras

```bash
cd /var/www/vhosts/tu-dominio.com.py/httpdocs

php artisan down

git pull origin main

composer install --no-dev --optimize-autoloader

# Solo si hubo cambios en CSS/JS
npm ci && npm run build

# Solo si hay migraciones nuevas
php artisan migrate --force

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan up
```

---

## Estructura del proyecto

```
app/
  Http/Controllers/        # Controladores públicos (shop, artisans, posts, checkout, sitemap...)
  Http/Controllers/Admin/  # CRUD completo del panel admin (incluye MediaAdminController)
  Models/                  # Product, Category, Order, Artisan, Post, Banner, MediaFile, etc.
  Http/Middleware/          # RoleMiddleware, AdminAccessMiddleware
  Services/                # BancardService, PagoparService (integración real de pasarelas de pago)
  Console/Commands/        # Jobs programados: purga de newsletter sin confirmar, carritos abandonados
  helpers.php              # media_url(), catalog_thumb_url() — resuelven URLs de biblioteca multimedia y paths legado
database/
  migrations/              # Todas las tablas del sistema
  seeders/                 # Datos iniciales (productos, usuarios, configuración)
resources/
  docs/manual-usuario.md   # Manual de uso completo (se sirve desde /admin/manual — ver panel admin)
  views/
    layouts/               # app.blade.php, admin.blade.php, guest.blade.php
    errors/                # 404.blade.php, 403.blade.php, 500.blade.php — páginas de error con la marca del sitio
    components/admin/      # media-picker.blade.php, media-picker-multi.blade.php
    home.blade.php
    shop/                  # index.blade.php, product.blade.php (incluye datos estructurados Schema.org)
    checkout/              # Flujo de compra (index, confirmation)
    artisans/              # index.blade.php, show.blade.php
    posts/                 # Noticias/blog
    pages/                 # about.blade.php, contact.blade.php, legal.blade.php
    account/               # Pedidos, wishlist, perfil, direcciones
    admin/                 # Dashboard y CRUD completo (incluye admin/media/index.blade.php, admin/manual/)
public/
  assets/brand/            # logo-horizontal.png, logo-vertical.png, logo-mark.png (fallback si no hay logo cargado en admin)
  assets/institucional/    # Fotos por defecto de la sección institucional de la home
  storage -> storage/app/public/  # Imágenes subidas (symlink)
storage/app/public/
  products/                # Fotos de productos (legado, previo a la biblioteca multimedia)
  categories/              # Imágenes de categorías (legado)
  artisans/                # Fotos de artesanas (legado)
  posts/                   # Imágenes de noticias (legado)
  banners/                 # Banners del hero (legado)
  media/                   # Biblioteca multimedia — todo archivo nuevo subido vía /admin/multimedia (incluye miniaturas `-thumb.*` autogeneradas)
tests/
  Feature/                 # ~210 tests — checkout (3 pasarelas), auth, sanitización XSS/SVG, sitemap, datos estructurados, etc.
```

---

## Panel administrativo

Accedé en `/admin` con usuario rol `admin`, `editor` o `vendedor`. Al iniciar sesión, cada rol es redirigido automáticamente: admin/editor/vendedor a `/admin`, clientes a `/mi-cuenta`.

| Sección | Ruta |
|---------|------|
| Dashboard | /admin |
| Manual de uso (ver/descargar) | /admin/manual |
| Productos | /admin/products |
| Categorías | /admin/categories |
| Pedidos | /admin/orders |
| Artesanas | /admin/artisans |
| Noticias | /admin/posts |
| Banners hero | /admin/banners |
| Multimedia | /admin/multimedia |
| Textos del sitio (Inicio / Artesanas / Nosotros) | /admin/contenido |
| Mensajes | /admin/contacts |
| Newsletter (incluye estado de confirmación de cada suscriptor) | /admin/newsletter |
| Usuarios (incluye envío de reset de contraseña por un admin) | /admin/users |
| Páginas legales (privacidad/términos siempre visibles, el resto se puede ocultar) | /admin/legal |
| Configuración general (nombre, logo, favicon, contacto, redes, hCaptcha por formulario) | /admin/configuracion |
| Integraciones (pagos, datos bancarios, captcha, analytics) | /admin/integraciones |
| Envíos (zonas por departamento/ciudad, retiro en tienda, AEX) | /admin/envios |

### Cuentas administrativas protegidas

`App\Models\User::PROTECTED_EMAILS` lista los emails de cuentas admin que no pueden eliminarse — hoy solo `webmaster@webparaguay.com`, el acceso de mantenimiento de WebParaguay. La restricción se aplica en el evento `deleting` del modelo `User`, así que cubre cualquier vía de borrado (panel, `tinker`, seeders) y no solo un controlador puntual. Para proteger otra cuenta, agregá su email a esa constante.

La cuenta debe crearse en cada entorno por separado (no viene en ningún seeder, para no exponer credenciales en el repositorio):

```bash
php artisan app:crear-admin
```

### Biblioteca multimedia

Todo campo de imagen del admin (productos, banners, categorías, artesanas, posts, logo del sitio, favicon, contenido de la home) se carga con un mismo selector con 3 opciones:

- **Biblioteca**: elegir un archivo ya subido antes
- **Subir**: subir un archivo nuevo desde la computadora (queda archivado en la biblioteca)
- **URL**: pegar el link de una imagen externa

Desde `/admin/multimedia` se puede ver, buscar, copiar la URL y eliminar cualquier archivo subido.

### Categorías y subcategorías

Las subcategorías se gestionan dentro de la pantalla de edición de cada categoría (`/admin/categories/{id}/edit`): alta, edición de nombre/orden/estado y eliminación, sin necesidad de una pantalla aparte. Se usan como filtro adicional en la tienda (`/tienda?categoria=...&subcategoria=...`) y al asignar un producto.

### Métodos de pago

Configurables desde `/admin/integraciones`:

- **Transferencia bancaria** — manual. Los datos de la cuenta (banco, número, titular) se cargan en `/admin/integraciones` y se muestran al cliente en el checkout apenas elige este método. El cliente sube un comprobante (PDF, JPG o PNG, máx. 5MB) al finalizar la compra; el pedido queda en estado "pendiente de verificación" hasta que un admin lo revisa y aprueba desde `/admin/orders/{id}` (el comprobante aparece como link "Ver comprobante de transferencia").
- **Pagopar** — integración real con la API de Pagopar (tarjetas, Tigo Money, billeteras). Requiere cargar `public_key` y `private_key` reales.
- **Bancard** — integración real con la API VPOS de Bancard (tarjetas de crédito/débito). Requiere cargar `public_key` y `private_key` reales.

Al activar Pagopar o Bancard, la pantalla de integraciones muestra las URLs de webhook que hay que configurar en el panel de cada pasarela.

> **Carrito vs. pago:** para Pagopar y Bancard, el carrito del cliente recién se vacía cuando el webhook de la pasarela confirma el pago como aprobado (`Order.cart_id`) — no al redirigir a la pasarela. Si el pago es rechazado o el cliente lo abandona, el carrito queda intacto para reintentar. Para transferencia bancaria (sin redirección externa) se vacía de inmediato al confirmar el pedido.

### hCaptcha

Configurable por formulario desde `/admin/configuracion`: con las claves cargadas, se puede activar/desactivar independientemente para el formulario de **contacto**, **newsletter** y **registro de cuenta** (`hcaptcha_enabled_contact` / `_newsletter` / `_register` en `site_settings`).

### SEO y descubrimiento por asistentes de IA

Generados dinámicamente, sin pantalla de configuración:
- `/sitemap.xml` — productos activos, categorías, artesanas, noticias publicadas y páginas legales visibles (`SitemapController`).
- `/robots.txt` — permite crawlers de buscadores y de IA (GPTBot, ClaudeBot, etc.), referencia el sitemap.
- `/llms.txt` — resumen del sitio en texto plano para asistentes de IA.
- Datos estructurados Schema.org (JSON-LD): `Product` en cada ficha de producto (precio, stock, `aggregateRating`), `Organization` en todo el sitio (layout principal).

### Envíos

Configurables desde `/admin/envios`:

- **Retiro en tienda** — gratis, on/off.
- **Envío propio** — tarifa por departamento de Paraguay (18 departamentos con sus ciudades/distritos), con tarifa base por departamento y tarifas personalizadas por ciudad/distrito. Permite deshabilitar ciudades puntuales dentro de un departamento activo.
- **Envío gratis** — activable a partir de un monto mínimo configurable.
- **AEX** — credenciales de API (usuario/contraseña, sandbox o producción) para la integración con el courier AEX.

En el checkout, el cliente elige departamento y ciudad; el costo se cotiza en tiempo real vía `/checkout/shipping` contra las tarifas configuradas (no se calcula ni se confía en el cliente).

---

## Testing

```bash
php artisan test
```

~220 tests en `tests/Feature/`, entre otros:
- `CheckoutPaymentGatewaysTest.php` — los 3 métodos de pago (transferencia, Bancard, Pagopar), simulando las pasarelas con `Http::fake()`, incluyendo que el carrito no se vacíe hasta confirmar el pago.
- `SvgUploadSanitizationTest.php`, XSS y sanitización de HTML en contenido editable por el admin.
- `MediaThumbnailTest.php` — generación de miniaturas de imágenes.
- `SitemapTest.php`, `StructuredDataTest.php`, `LlmsTxtTest.php` — SEO y datos estructurados.
- `PruneAbandonedGuestCartsTest.php`, `OrdersTableIndexTest.php` — mantenimiento y rendimiento de BD.
- `CustomErrorPagesTest.php` — páginas 404/403/500 personalizadas.

---

## Licencia

Proyecto propietario — Asociación Mujeres Unidas del Bañado Sur.
