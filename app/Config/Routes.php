<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================================

$routes->get('/', 'Front\Page::home');
$routes->get('login', 'Auth::login');
$routes->post('authenticate', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');
$routes->post('form/submit', 'Front\Page::submitForm');
$routes->get('sitemap.xml', 'Sitemap::index');
// $routes->post('cookies/consent', 'CookieConsent::save');
$routes->post('cookies/consent', 'Front\Cookies::save');

// ============================================================
// RUTAS DEL ADMIN (con filtro auth)
// ============================================================

$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('help', 'Help::index');

    $routes->get('pages', 'Pages::index');
    $routes->get('pages/create', 'Pages::create');
    $routes->post('pages/store', 'Pages::store');
    $routes->get('pages/edit/(:num)', 'Pages::edit/$1');
    $routes->post('pages/update/(:num)', 'Pages::update/$1');
    $routes->get('pages/delete/(:num)', 'Pages::delete/$1');
    $routes->post('pages/delete/(:num)', 'Pages::delete/$1');
    $routes->get('pages/template-fields/(:num)', 'Pages::getTemplateFields/$1');
    $routes->post('pages/restore/(:num)', 'Pages::restore/$1');
    $routes->get('pages/revision/(:num)', 'Pages::viewRevision/$1');

    $routes->get('menus', 'Menus::index');
    $routes->post('menus/save', 'Menus::save');
    $routes->post('menus/reorder', 'Menus::reorder');
    $routes->post('menus/delete/(:num)', 'Menus::delete/$1');

    $routes->get('templates', 'Templates::index');
    $routes->get('templates/create', 'Templates::create');
    $routes->post('templates/store', 'Templates::store');
    $routes->get('templates/edit/(:num)', 'Templates::edit/$1');
    $routes->post('templates/update/(:num)', 'Templates::update/$1');
    $routes->get('templates/delete/(:num)', 'Templates::delete/$1');

    // === MÓDULOS: solo superadmin ===
    $routes->get('modules', 'Modules::index', ['filter' => 'auth:admin']);
    $routes->post('modules/install', 'Modules::install', ['filter' => 'auth:admin']);
    $routes->get('modules/toggle/(:num)', 'Modules::toggle/$1', ['filter' => 'auth:admin']);
    $routes->post('modules/uninstall/(:num)', 'Modules::uninstall/$1', ['filter' => 'auth:superadmin']);
    $routes->post('modules/run-migrations', 'Modules::runMigrations', ['filter' => 'auth:admin']);

    // === TEMAS: solo superadmin ===
    $routes->get('themes', 'Themes::index', ['filter' => 'auth:superadmin']);
    $routes->post('themes/install', 'Themes::install', ['filter' => 'auth:superadmin']);
    $routes->get('themes/activate/(:num)', 'Themes::activate/$1', ['filter' => 'auth:superadmin']);
    $routes->post('themes/activate/(:num)', 'Themes::activate/$1', ['filter' => 'auth:superadmin']);
    $routes->get('themes/delete/(:num)', 'Themes::delete/$1', ['filter' => 'auth:superadmin']);
    $routes->post('themes/delete/(:num)', 'Themes::delete/$1', ['filter' => 'auth:superadmin']);


    // === AJUSTES: admin y superior ===
    $routes->get('settings', 'Settings::index', ['filter' => 'auth:admin']);
    $routes->post('settings/save', 'Settings::save', ['filter' => 'auth:admin']);

    // === PLANTILLAS: admin y superior ===
    $routes->get('templates', 'Templates::index', ['filter' => 'auth:admin']);
    $routes->get('templates/create', 'Templates::create', ['filter' => 'auth:admin']);
    $routes->post('templates/store', 'Templates::store', ['filter' => 'auth:admin']);
    $routes->get('templates/edit/(:num)', 'Templates::edit/$1', ['filter' => 'auth:admin']);
    $routes->post('templates/update/(:num)', 'Templates::update/$1', ['filter' => 'auth:admin']);
    $routes->get('templates/delete/(:num)', 'Templates::delete/$1', ['filter' => 'auth:admin']);

    $routes->get('users', 'Users::index', ['filter' => 'auth:admin']);
    $routes->get('users/create', 'Users::create', ['filter' => 'auth:admin']);
    $routes->post('users/store', 'Users::store', ['filter' => 'auth:admin']);
    $routes->get('users/edit/(:num)', 'Users::edit/$1', ['filter' => 'auth:admin']);
    $routes->post('users/update/(:num)', 'Users::update/$1', ['filter' => 'auth:admin']);
    $routes->get('users/toggle/(:num)', 'Users::toggle/$1', ['filter' => 'auth:admin']);
    $routes->get('users/delete/(:num)', 'Users::delete/$1', ['filter' => 'auth:admin']);
    $routes->post('users/generate-pin/(:num)', 'Users::generatePin/$1', ['filter' => 'auth:admin']);

    $routes->get('submissions', 'Submissions::index');
    $routes->get('submissions/read/(:num)', 'Submissions::markRead/$1');
    $routes->get('submissions/delete/(:num)', 'Submissions::delete/$1');

    $routes->get('lock', 'Lockscreen::index');
    $routes->post('unlock', 'Lockscreen::unlock');

    $routes->get('backups', 'Backups::index', ['filter' => 'auth:superadmin']);
    $routes->post('backups/create', 'Backups::create', ['filter' => 'auth:superadmin']);
    $routes->get('backups/download/(:segment)', 'Backups::download/$1', ['filter' => 'auth:superadmin']);
    $routes->post('backups/delete/(:segment)', 'Backups::delete/$1', ['filter' => 'auth:superadmin']);
    $routes->get('backups/restore-info/(:segment)', 'Backups::restoreInfo/$1', ['filter' => 'auth:superadmin']);
    $routes->post('backups/restore-step/(:segment)', 'Backups::restoreStep/$1', ['filter' => 'auth:superadmin']);

    $routes->get('media', 'Media::index');
    $routes->get('media/folder/(:num)', 'Media::index/$1');
    $routes->post('media/upload', 'Media::upload');
    $routes->post('media/upload/(:num)', 'Media::upload/$1');
    $routes->post('media/update/(:num)', 'Media::update/$1');
    $routes->post('media/delete/(:num)', 'Media::delete/$1');
    $routes->post('media/folders/create', 'Media::createFolder');
    $routes->post('media/folders/delete/(:num)', 'Media::deleteFolder/$1');
    $routes->get('media/api', 'Media::apiList');
    $routes->get('media/api/(:num)', 'Media::apiList/$1');
    $routes->post('media/sync-gallery', 'Media::syncFromGallery');

    $routes->get('notifications/unread', 'Notifications::unread');

    $routes->get('search', [\App\Controllers\Admin\Search::class, 'index']);
});


// ============================================================
// RUTAS DE MÓDULOS ACTIVOS (se cargan desde cada módulo)
// ============================================================

try {
    $db = \Config\Database::connect();
    $tables = $db->listTables();
    $hasModules = in_array('modules', $tables) || in_array($db->getDatabase() . '.modules', $tables);

    if ($hasModules) {
        $modules = $db->table('modules')->where('active', 1)->get()->getResultArray();
        foreach ($modules as $mod) {
            $routesFile = ROOTPATH . $mod['path'] . '/Routes.php';
            if (is_file($routesFile)) {
                require $routesFile;
            }
        }
    }
} catch (\Exception $e) {
    // Silenciar si la BD no está lista
}


// ============================================================
// AYUDANTE: sub-rutas de páginas-módulo (álbum / categoría / producto)
// con el slug traducido de cada página. $prefix: 'es/', 'en/' o ''
// ============================================================

$registerModuleSubRoutes = function (string $prefix) use ($routes): void {
    try {
        $db = \Config\Database::connect();
        $tables = $db->listTables();

        if (!in_array('pages', $tables) && !in_array($db->getDatabase() . '.pages', $tables)) {
            return;
        }

        $modulePages = $db->table('pages')
            ->where('type', 'module')
            ->where('status', 'published')
            ->get()
            ->getResultArray();

        foreach ($modulePages as $mp) {
            $module = ucfirst($mp['module_name']);
            $ctrl   = "App\\Modules\\{$module}\\Controllers\\Front\\{$module}";

            // Slugs en todos los idiomas (canónico + traducciones)
            $slugs = [$mp['slug']];
            if (in_array('page_translations', $tables) || in_array($db->getDatabase() . '.page_translations', $tables)) {
                foreach ($db->table('page_translations')->where('page_id', $mp['id'])->get()->getResultArray() as $tr) {
                    $slugs[] = $tr['slug'];
                }
            }

            foreach (array_unique(array_filter($slugs)) as $s) {
                if ($mp['module_name'] === 'gallery') {
                    $routes->get($prefix . $s . '/album/(:any)', [$ctrl, 'album/$1']);
                } elseif ($mp['module_name'] === 'catalog') {
                    $routes->get($prefix . $s . '/categoria/(:any)', [$ctrl, 'category/$1']);
                    $routes->get($prefix . $s . '/producto/(:any)', [$ctrl, 'product/$1']);
                } elseif ($mp['module_name'] === 'news') {                    // ← NUEVO
                    $routes->get($prefix . $s . '/noticia/(:any)', [$ctrl, 'article/$1']);
                } elseif ($mp['module_name'] === 'agenda') {
                    $routes->get($prefix . $s . '/evento/(:any)', [$ctrl, 'event/$1']);
                }
            }
        }
    } catch (\Throwable $e) {
        // Sin BD o tablas: no registrar nada
    }
};


// ============================================================
// RUTAS I18N (con prefijo de idioma)
// ============================================================

try {
    $db = \Config\Database::connect();
    $tables = $db->listTables();
    $hasLangs = in_array('languages', $tables) || in_array($db->getDatabase() . '.languages', $tables);

    if ($hasLangs) {
        $langs = $db->table('languages')->where('active', 1)->get()->getResultArray();

        foreach ($langs as $l) {
            $code = $l['code'];

            // === SUB-RUTAS DIRECTAS DE MÓDULOS (rutas fijas, compatibilidad) ===

            // Catálogo
            $routes->get($code . '/catalogo/categoria/(:any)', [App\Modules\Catalog\Controllers\Front\Catalog::class, 'category/$1']);
            $routes->get($code . '/catalogo/producto/(:any)', [App\Modules\Catalog\Controllers\Front\Catalog::class, 'product/$1']);

            // Galería
            $routes->get($code . '/galeria/album/(:any)', [App\Modules\Gallery\Controllers\Front\Gallery::class, 'album/$1']);

            // === BÚSQUEDA ===
            $routes->get($code . '/buscar', 'Front\Search::index/' . $code);

            // === SUB-RUTAS DE PÁGINAS-MÓDULO (con slug traducido) ===
            $registerModuleSubRoutes($code . '/');

            // === PORTADA PARA ESTE IDIOMA ===
            $routes->get($code, 'Front\Page::home/' . $code);
            $routes->get($code . '/', 'Front\Page::home/' . $code);

            // === RUTA CATCH-ALL DE PÁGINAS (al final) ===
            $routes->get($code . '/(:any)', 'Front\Page::render/$1/' . $code);
        }
        $routes->get('buscar', 'Front\Search::index');
    }
} catch (\Exception $e) {
    // Silenciar
}


// ============================================================
// SUB-RUTAS DE PÁGINAS-MÓDULO (idioma por defecto, sin prefijo)
// ============================================================

$registerModuleSubRoutes('');


// ============================================================
// RUTAS PÚBLICAS DEL FRONT (catch-all — SIEMPRE AL FINAL)
// ============================================================

$routes->get('(:any)', 'Front\Page::render/$1');
