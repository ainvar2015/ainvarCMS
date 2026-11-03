# AinvarCMS

CMS personal desarrollado en PHP sobre **CodeIgniter 4**, con **Bootstrap 5** en el frontend y **CKEditor 5** como editor de contenidos. Pensado para gestionar los sitios de la familia `ainvar.net` desde un único panel, con sistema de módulos instalables, multi-idioma nativo y galería propia.

## Características principales

- **Páginas**: CRUD completo con estados (borrador/publicado), previsualización y editor con subida de imágenes.
- **Multi-idioma (i18n)**: núcleo protegido del sistema (no desinstalable ni desactivable). URLs por idioma y modo mono-idioma automático cuando solo hay un idioma activo.
- **Galería / Media Library**: subida secuencial de imágenes, metadatos y organización por carpetas.
- **Temas**: vistas del frontend en `public/themes/`, con layout dinámico según el tema activo.
- **Shortcodes**: `[productos]` y `[galeria]` insertables desde el editor.
- **Sistema de módulos**: instalación desde ZIP, activación/desactivación y migraciones propias por módulo. El CMS funciona con cero módulos opcionales instalados.
- **Páginas-módulo**: páginas con flag `embed` y `content=NULL` que incrustan la salida de un módulo.
- **GDPR**: banner de consentimiento de cookies por categorías (`analytics` / `marketing`), scripts de terceros diferidos hasta obtener consentimiento y política de cookies configurable.

## Módulos disponibles

| Módulo | Descripción | Estado |
|---|---|---|
| Forms | Formularios con envío y gestión desde el panel | Cerrado |
| FAQ | Preguntas frecuentes | Cerrado |
| Redirects | Redirecciones gestionadas | Cerrado |
| Agenda | Eventos con lista `/agenda` y ficha por slug | Terminado |
| Buscador | Búsqueda en el frontend | Cerrado |
| Newsletter | Suscripción y envío de boletines | Pendiente |

## Stack técnico

- **Backend**: PHP + CodeIgniter 4
- **Frontend**: Bootstrap 5, vistas por tema en `public/themes/`
- **Editor**: CKEditor 5 (contenidos) y Summernote (admin de algunos módulos)
- **Base de datos**: MySQL/MariaDB, migraciones con el sistema de CI4
- **Analítica**: GA4 y Google Ads cargados solo tras consentimiento (categorías analytics/marketing)

## Entorno local (Windows + WAMP)

1. Clonar el repositorio en la carpeta de WAMP:

   ```cmd
   git clone https://github.com/ainvar2015/ainvarCMS.git C:\wamp64\www\ainvarCMS
   ```

2. Crear la base de datos en MySQL y configurar credenciales en `.env` (copiar desde `env` de CI4):

   ```
   database.default.hostname = localhost
   database.default.database = ainvarcms
   database.default.username = root
   database.default.password =
   ```

3. Ejecutar migraciones:

   ```cmd
   php spark migrate
   ```

4. Levantar con el servidor de WAMP (virtualhost apuntando a `public/`) o con el servidor integrado:

   ```cmd
   php spark serve
   ```

## Producción

Servidor Linux, desplegado en `/home/ainvare/cms` sirviendo la carpeta `public/` como document root. Puntos a vigilar al subir desde Windows:

- **Sensibilidad a mayúsculas**: en Linux los nombres de archivo y directorio distinguen mayúsculas/minúsculas; una vista o controlador con el case incorrecto funciona en local y falla en producción.
- **Permisos**: `writable/` debe tener permisos de escritura para el usuario del servidor web.
- **Migraciones**: ejecutar `php spark migrate` tras cada subida que incluya cambios de esquema (los módulos traen las suyas).

## Estructura del proyecto

```
app/
  Modules/          Módulos instalables (Agenda, Forms, FAQ, ...)
  Views/
    front/          Vistas del frontend (incluye cookie_banner.php)
public/
  themes/           Temas del frontend (layout dinámico por tema activo)
writable/           Caché, logs y subidas
```

## Notas de desarrollo

- Los **nombres de archivo de las vistas los fija el controlador**: renombrar una vista sin tocar el `$this->render()`/helper correspondiente rompe la resolución.
- La subida de imágenes del editor es **secuencial**: el token CSRF se regenera en cada POST, no se pueden paralelizar subidas con el mismo token.
- Al añadir un módulo con rutas de frontend, hay que registrar su rama en `$registerModuleSubRoutes`.
