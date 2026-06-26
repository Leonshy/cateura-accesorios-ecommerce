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

### 5. Configuración Nginx (Plesk → nginx)

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
  Http/Controllers/        # Controladores públicos (shop, artisans, posts, checkout...)
  Http/Controllers/Admin/  # CRUD completo del panel admin
  Models/                  # Product, Category, Order, Artisan, Post, Banner, etc.
  Http/Middleware/          # RoleMiddleware, AdminAccessMiddleware
database/
  migrations/              # Todas las tablas del sistema
  seeders/                 # Datos iniciales (productos, usuarios, configuración)
resources/
  views/
    layouts/               # app.blade.php, admin.blade.php, guest.blade.php
    home.blade.php
    shop/                  # index.blade.php, product.blade.php
    cart/checkout/         # Flujo de compra
    artisans/              # index.blade.php, show.blade.php
    posts/                 # Noticias/blog
    pages/                 # about.blade.php, contact.blade.php, legal.blade.php
    account/               # Pedidos, wishlist, perfil, direcciones
    admin/                 # Dashboard y CRUD completo
public/
  assets/brand/            # logo-horizontal.png, logo-vertical.png, logo-mark.png
  assets/institucional/    # Fotos de la sección "Manos que transforman"
  storage -> storage/app/public/  # Imágenes subidas (symlink)
storage/app/public/
  products/                # Fotos de productos
  categories/              # Imágenes de categorías
  artisans/                # Fotos de artesanas
  posts/                   # Imágenes de noticias
  banners/                 # Banners del hero
```

---

## Panel administrativo

Accedé en `/admin` con usuario rol `admin`, `editor` o `vendedor`.

| Sección | Ruta |
|---------|------|
| Dashboard | /admin |
| Productos | /admin/products |
| Categorías | /admin/categories |
| Pedidos | /admin/orders |
| Artesanas | /admin/artisans |
| Noticias | /admin/posts |
| Banners hero | /admin/banners |
| Mensajes | /admin/contacts |
| Newsletter | /admin/newsletter |
| Usuarios | /admin/users |
| Páginas legales | /admin/legal |
| Configuración general | /admin/configuracion |
| Integraciones (pagos/captcha) | /admin/integraciones |
| Métodos de envío | /admin/envios |

---

## Licencia

Proyecto propietario — Asociación Mujeres Unidas del Bañado Sur.
