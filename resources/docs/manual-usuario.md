# Manual de uso — Cateura Accesorios

Asociación Mujeres Unidas del Bañado Sur

Guía completa para usar la tienda en línea y el panel administrativo: desde cómo compra un cliente hasta cómo se cargan productos, se gestionan pedidos y se configuran los envíos y pagos.

---

## 1. Introducción

Cateura Accesorios es la tienda en línea de la Asociación, donde se venden los accesorios elaborados por las artesanas. La plataforma tiene dos partes:

- **Tienda pública** — la ven todos los visitantes: catálogo, fichas de producto, carrito, checkout y la cuenta de cada cliente.
- **Panel administrativo** — solo para el equipo de la Asociación: carga de productos, pedidos, contenido del sitio, pagos y envíos.

Este manual está organizado en ese mismo orden: primero la experiencia del cliente, después cada sección del panel administrativo.

## 2. Acceso e ingreso

Para comprar no hace falta tener una cuenta, pero para gestionar el panel administrativo sí es obligatorio iniciar sesión.

1. **Ir a la página de inicio de sesión** — hacé clic en "Iniciar sesión" desde el menú superior, o entrá directamente a `/login`.
2. **Ingresar email y contraseña** — usá el correo con el que te registraste (clientes) o el correo asignado por la Asociación (equipo administrativo).
3. **Sos redirigido según tu rol** — un cliente entra a "Mi cuenta". Un usuario con rol administrativo (Admin, Editor o Vendedor) entra directamente al panel en `/admin`.

> ¿Olvidaste tu contraseña? Desde la pantalla de inicio de sesión podés pedir un enlace de recuperación a tu correo. Si un cliente no puede recibir ese correo, cualquier Admin puede enviarle el enlace directamente desde **Usuarios** (ver sección 17).

Al registrarse, cada cliente debe confirmar su correo antes de poder finalizar una compra con cuenta iniciada (el checkout como invitado, sin cuenta, no lo exige).

## 3. Roles y permisos

Cada usuario del equipo tiene uno o más roles. Los tres roles administrativos pueden entrar al panel; lo que cambia es el criterio de uso recomendado para cada uno (la plataforma no bloquea secciones entre ellos, así que el orden importa por buenas prácticas internas, no por restricción técnica).

| Rol | Uso recomendado | Acceso al panel |
|---|---|---|
| **Admin** | Control total: usuarios, configuración, pagos, envíos y contenido. | Completo |
| **Editor** | Contenido del sitio: productos, artesanas, noticias, banners, páginas. | Completo |
| **Vendedor** | Operación diaria de ventas: pedidos, mensajes de contacto. | Completo |
| **Cliente** | Sin rol administrativo. Solo accede a "Mi cuenta" y a la tienda. | Sin acceso |

A un usuario se le pueden asignar varios roles a la vez desde **Usuarios** en el panel.

## 4. Navegar la tienda

*Para: cualquier visitante*

**Catálogo** — en `/tienda` se listan todos los productos activos, organizados por categoría y subcategoría. Cada producto puede tener colores disponibles, imágenes de galería y una etiqueta de "Nuevo" o "Destacado" si el equipo administrativo lo marcó así.

**Ficha de producto** — al entrar a un producto (`/tienda/nombre-del-producto`) se ve la descripción completa, el precio (y el precio anterior tachado si tiene descuento), el stock disponible, los colores para elegir y las imágenes de la galería.

**Otras secciones públicas:**
- **Artesanas** (`/artesanas`): perfiles con la biografía de cada artesana.
- **Noticias** (`/noticias`): novedades y eventos de la Asociación.
- **Nosotros** (`/nosotros`): historia y valores de la Asociación.
- **Contacto** (`/contacto`): formulario de consultas y suscripción al boletín.

> **Suscripción al boletín con confirmación:** al suscribirse, la persona recibe un correo con un enlace de confirmación. Hasta que lo confirme, aparece como "sin confirmar" en el panel (ver sección 16). Si no confirma dentro de **72 horas**, su correo se elimina automáticamente de la lista.

## 5. Carrito de compras

*Para: cualquier visitante*

El carrito (`/carrito`) no requiere haber iniciado sesión — se guarda en el navegador hasta el pago.

- **Agregar:** desde la ficha de producto, eligiendo color y cantidad.
- **Modificar cantidad:** desde el propio carrito, con los controles de cada línea.
- **Quitar un producto:** con el botón de eliminar de esa línea.
- **Vaciar todo:** con el botón de vaciar carrito.

> Los carritos de invitados (sin cuenta) que quedan sin actividad por más de **5 días** se eliminan automáticamente para no acumular datos viejos. Los carritos de clientes con cuenta nunca se borran solos.

## 6. Finalizar compra (checkout)

*Para: cualquier visitante*

1. **Datos de contacto** — nombre, correo y teléfono.
2. **Tipo de entrega** — elegir entre *retiro en el local* o *envío a domicilio*. Si es envío, se completa dirección, ciudad y departamento; el costo se calcula automáticamente según la zona.
3. **Envío gratis** — si el pedido supera el monto mínimo configurado por la Asociación, el envío se descuenta solo.
4. **Datos de facturación (opcionales)** — RUC y razón social, si se necesita factura.
5. **Método de pago** — transferencia bancaria, Pagopar o Bancard, según lo que la Asociación tenga activado. Al elegir "Transferencia bancaria" se muestran ahí mismo los datos de la cuenta (banco, número, titular) para que el cliente sepa a quién transferir.
6. **Comprobante de transferencia** — si se paga por transferencia, es obligatorio adjuntar el comprobante (imagen o PDF, máx. 5 MB) para que el equipo confirme el pago manualmente.
7. **Confirmación** — al enviar el pedido se genera un número de pedido y se muestra la pantalla de confirmación.

> **Pagopar y Bancard** redirigen a una pasarela externa para completar el pago. El **carrito recién se vacía cuando la pasarela confirma el pago como aprobado** — si el cliente abandona el pago o la pasarela lo rechaza, el carrito queda intacto para reintentar sin perder nada. La **transferencia bancaria** queda como "pago pendiente de confirmación" hasta que el equipo administrativo revise el comprobante, y ahí sí se vacía el carrito de inmediato.

## 7. Mi cuenta

*Para: cliente con sesión iniciada*

Desde `/mi-cuenta` cada cliente puede:
- **Mis pedidos** — historial completo y detalle de cada pedido con su estado actual.
- **Lista de deseos** — guardar productos para comprar más adelante.
- **Direcciones** — direcciones guardadas para agilizar futuros envíos.
- **Perfil** — editar nombre, correo y demás datos personales.

## 8. Panel administrativo — Panel principal

*Para: Admin, Editor, Vendedor*

Al iniciar sesión con un usuario administrativo, se entra directamente a `/admin`: un resumen con lo más relevante (pedidos recientes, mensajes sin leer, estado general de la tienda). Desde el menú lateral se accede a todas las secciones descritas a continuación.

## 9. Multimedia

*Para: Admin, Editor*

La biblioteca de multimedia (`/admin/multimedia`) centraliza todas las imágenes del sitio: fotos de producto, banners, fotos de artesanas, etc.

- **Subir:** se puede arrastrar o seleccionar un archivo; el sistema limpia automáticamente los metadatos (EXIF) por privacidad.
- **Buscar y reutilizar:** el selector ("picker") permite elegir una imagen ya subida en vez de repetir archivos, desde cualquier formulario que necesite una imagen (producto, banner, artesana, etc.).
- **Texto alternativo:** cada imagen puede tener una descripción (alt) para accesibilidad y buscadores.
- **Eliminar:** borra el archivo de la biblioteca.

## 10. Productos

*Para: Admin, Editor*

Desde `/admin/products` se gestiona todo el catálogo.

**Crear o editar un producto:**
1. **Datos básicos** — nombre, categoría, subcategoría (opcional), descripción corta y descripción completa.
2. **Precio y stock** — precio de venta, precio anterior (opcional, para mostrar un descuento tachado) y cantidad en stock.
3. **Imagen principal y galería** — elegidas desde la biblioteca de multimedia.
4. **Colores disponibles** — se pueden agregar varios colores como opciones para el cliente.
5. **Visibilidad** — marcar como *Activo* (visible en la tienda), *Destacado* o *Nuevo* según corresponda.
6. **SEO (opcional)** — título y descripción meta para buscadores.

> **Importante:** un producto marcado como inactivo no aparece en la tienda, pero sigue existiendo en pedidos pasados que ya lo incluían.

> **Imágenes livianas automáticas:** al subir una foto de producto, el sistema genera además una miniatura liviana que se usa en el catálogo y listados (la ficha del producto sigue mostrando la imagen a resolución completa). No requiere ninguna acción del equipo administrativo.

## 11. Categorías y subcategorías

*Para: Admin, Editor*

Desde `/admin/categories` se crean y ordenan las categorías principales de la tienda. Cada categoría puede tener subcategorías, que se agregan directamente desde la pantalla de edición de esa categoría y se pueden reordenar con las flechas de subir/bajar.

> Al crear un producto, además de la categoría se puede elegir una subcategoría (opcional) para clasificarlo con más detalle.

> **Sobre las URLs:** cada categoría genera automáticamente su dirección web (slug) a partir del nombre. Si renombrás una categoría, esa dirección cambia también — los enlaces viejos que ya estuvieran compartidos o indexados en buscadores dejan de funcionar (no hay redirección automática todavía).

## 12. Artesanas

*Para: Admin, Editor*

Desde `/admin/artisans` se crea y edita el perfil público de cada artesana: nombre, foto y biografía. Estos perfiles se muestran en la sección "Artesanas" de la tienda.

## 13. Noticias y eventos

*Para: Admin, Editor*

Desde `/admin/posts` se redactan noticias y eventos. Cada publicación tiene un estado:

| Estado | Qué significa |
|---|---|
| Borrador | Guardado pero no visible todavía en la tienda pública. |
| Publicado | Visible en `/noticias` para cualquier visitante. |

## 14. Banners

*Para: Admin, Editor*

Desde `/admin/banners` se administran las imágenes destacadas de la portada (hero). Cada banner tiene una imagen, un subtítulo corto, una descripción y un botón de llamada a la acción (texto y enlace) configurable.

## 15. Pedidos

*Para: Admin, Vendedor*

Desde `/admin/orders` se ve la lista completa de pedidos, con filtros por estado, por estado de pago y por búsqueda (número de pedido, nombre o correo del cliente).

**Estados del pedido:**

| Estado | Significado |
|---|---|
| Pendiente | Pedido recién recibido, sin procesar aún. |
| Confirmado | Pago verificado, listo para preparar. |
| Preparando | En preparación por el equipo. |
| Enviado | Despachado al cliente o listo para retiro. |
| Entregado | Pedido finalizado. |
| Cancelado | Pedido anulado. |

**Estados del pago:**

| Estado | Significado |
|---|---|
| Pendiente | Aún no se registró el pago. |
| Pendiente de confirmación | Se subió un comprobante de transferencia y espera revisión manual. |
| Pagado | Pago confirmado. |
| Rechazado | El pago no pudo procesarse. |
| Reembolsado | Se devolvió el dinero al cliente. |

**Flujo recomendado:**
1. **Abrir el pedido** — desde la lista, clic en el número de pedido para ver el detalle completo: productos, cantidades, datos de envío y de contacto.
2. **Revisar el comprobante (si es transferencia)** — verificar que el monto y los datos coincidan con el pedido.
3. **Actualizar estado** — cambiar el estado del pedido y del pago según corresponda, y agregar notas internas si hace falta (por ejemplo, coordinar el envío).

## 16. Mensajes de contacto y newsletter

*Para: Admin, Vendedor*

Desde `/admin/contactos` se leen y responden (fuera de la plataforma, por correo) las consultas enviadas desde el formulario de contacto público. Los mensajes leídos pueden eliminarse una vez resueltos.

Desde `/admin/newsletter` se ve la lista de personas suscriptas al boletín y se puede exportar en un archivo CSV (correo y fecha de suscripción) para usar en campañas de email marketing. Cada suscriptor muestra si ya **confirmó** su correo o si todavía está **pendiente de confirmación** (y se elimina solo si no confirma dentro de 72 horas — ver sección 4).

## 17. Usuarios

*Para: Admin*

Desde `/admin/usuarios` se ve la lista de todas las cuentas registradas, con la cantidad de pedidos que hizo cada una. Al editar un usuario se le pueden asignar uno o varios roles administrativos: **Admin**, **Editor** o **Vendedor**. Un usuario sin ningún rol es un cliente normal, sin acceso al panel.

**Restablecer la contraseña de alguien** — si un usuario (cliente o del equipo) no puede acceder a su cuenta, un Admin puede enviarle un enlace de restablecimiento de contraseña directamente desde su ficha de edición, sin que la persona tenga que pedirlo ella misma desde el login.

> **Precaución:** asignar el rol Admin da control total sobre pagos, envíos y el resto de los usuarios. Reservarlo solo para las personas de máxima confianza en la Asociación.

## 18. Textos del sitio

*Para: Admin*

Desde **Textos** (`/admin/contenido`) se edita el contenido escrito e imágenes de las secciones fijas del sitio, organizado en tres pestañas:

- **Inicio** (`/admin/contenido/inicio`) — textos e imágenes de la portada.
- **Artesanas** (`/admin/contenido/artesanas`) — texto introductorio de la sección "Artesanas".
- **Nosotros** (`/admin/contenido/nosotros`) — historia de la Asociación, estadísticas (por ejemplo cantidad de artesanas o años de trabajo) y los **valores** que se muestran ahí (sostenibilidad, comunidad, artesanía, etc.), que se agregan, editan y ordenan desde la misma pantalla.

**Páginas legales** — política de privacidad, términos y condiciones, políticas de compra, de envío y de cambios/devoluciones — se editan por separado desde `/admin/legal`. Todas se pueden ocultar del sitio si no aplican (por ejemplo, si no hay política de envío propia todavía), **excepto** privacidad y términos y condiciones, que siempre quedan visibles porque son de aceptación obligatoria en el checkout.

## 19. Configuración general

*Para: Admin*

Desde `/admin/configuracion` se ajustan los datos globales del sitio:

- Logo (versión de cabecera y de pie de página) y favicon.
- Nombre y descripción del sitio.
- Correo, teléfono y dirección de contacto.
- Enlaces a Instagram, Facebook, YouTube y TikTok, y número/mensaje predefinido de WhatsApp. Un ícono de red social solo se muestra en el sitio si su enlace está completo — si queda vacío o a medio cargar, se oculta solo.
- Google Analytics y Meta Pixel, para medir visitas y campañas.
- **hCaptcha** — con las claves cargadas, se puede elegir en qué formularios públicos exigirlo por separado: contacto, newsletter y registro de cuenta. Un formulario sin marcar no lo pide.

## 20. SEO y visibilidad para buscadores e inteligencia artificial

*Para: Admin (informativo — no requiere configuración manual)*

El sitio genera automáticamente, sin que el equipo tenga que hacer nada:

- **`/sitemap.xml`** — mapa con todas las páginas públicas (productos activos, categorías, artesanas, noticias publicadas, páginas legales visibles), para que los buscadores las encuentren más rápido.
- **`/robots.txt`** — permite el acceso a buscadores y también a los rastreadores de asistentes de inteligencia artificial (ChatGPT, Claude, Perplexity, etc.), y apunta al sitemap.
- **`/llms.txt`** — un resumen del sitio pensado específicamente para que asistentes de IA lo lean y puedan recomendar la tienda con información correcta.
- **Datos estructurados (Schema.org)** — cada ficha de producto incluye precio, stock y valoraciones en un formato que buscadores y comparadores entienden automáticamente; el sitio completo también informa sus datos de organización (nombre, contacto, redes).

Todo esto se actualiza solo a medida que se cargan o cambian productos, categorías y noticias — no hay ninguna pantalla del panel para editarlo.

## 21. Métodos de pago y envíos

*Para: Admin*

**Métodos de pago** — desde `/admin/integraciones` se activa o desactiva cada método y se cargan sus credenciales:

| Método | Cómo funciona |
|---|---|
| Transferencia bancaria | No requiere credenciales; el cliente adjunta un comprobante que el equipo confirma manualmente. |
| Pagopar | Requiere clave pública y privada de Pagopar. Tiene modo "sandbox" para hacer pruebas antes de cobrar de verdad. |
| Bancard | Requiere clave pública y privada de Bancard. También tiene modo "sandbox". |

> **Modo sandbox:** mientras esté activado, los pagos con Pagopar o Bancard son simulados y no mueven dinero real. Desactivarlo recién cuando se confirme que las credenciales de producción funcionan correctamente.

**Envíos** — desde `/admin/envios` se configura:

- **Envío gratis:** activar y definir el monto mínimo de compra a partir del cual el envío no tiene costo.
- **Retiro en tienda:** habilitar la opción de que el cliente pase a buscar su pedido sin costo de envío.
- **Envío propio por zonas:** definir zonas (departamento/ciudad) con su costo correspondiente, que se calcula automáticamente en el checkout.
- **AEX:** integración con la empresa de encomiendas AEX, con usuario, contraseña y entorno (pruebas o producción).

## 22. Preguntas frecuentes

**Un cliente pagó por transferencia, ¿qué hago?**
Entrá al pedido en Pedidos, revisá el comprobante adjunto y, si corresponde, cambiá el estado de pago a "Pagado" y el estado del pedido a "Confirmado".

**¿Cómo oculto un producto sin borrarlo?**
Editá el producto y desmarcá la opción *Activo*. Deja de mostrarse en la tienda pero sigue existiendo en el historial de pedidos.

**¿Cómo le doy acceso al panel a alguien nuevo del equipo?**
La persona debe registrarse (o el Admin crea la cuenta), y luego desde Usuarios se le asigna el rol correspondiente (Editor o Vendedor, según su tarea).

**¿Por qué no veo la opción de Pagopar o Bancard en el checkout?**
Revisá en Pagos y envíos que el método esté marcado como activo y que sus credenciales estén cargadas.

**Un cliente dice que pagó con Pagopar/Bancard pero el carrito no se vació, ¿es un error?**
No necesariamente. El carrito solo se vacía cuando la pasarela confirma el pago como aprobado. Si el cliente cerró la pestaña de pago antes de terminar, o el pago quedó rechazado, el carrito queda intacto a propósito para que pueda reintentar sin rearmar la compra.

**¿Qué pasa si alguien entra a una página que no existe?**
Ve una página de error con la marca del sitio (no la pantalla genérica de Laravel), con un botón para volver al inicio o a la tienda.

## 23. Accesos de prueba (entorno local)

Estas cuentas existen únicamente en el entorno de desarrollo local (sembradas por el seeder), para probar cada rol antes de publicar cambios. No existen en el sitio en producción salvo que se ejecute el mismo proceso de siembra ahí.

| Rol | Correo | Contraseña |
|---|---|---|
| Admin | admin@cateura.test | password |
| Editor | editor@cateura.test | password |
| Vendedor | vendedor@cateura.test | password |
| Cliente | cliente@cateura.test | password |

---

*Manual de uso interno — Cateura Accesorios · Asociación Mujeres Unidas del Bañado Sur*
