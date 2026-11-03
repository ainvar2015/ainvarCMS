<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying the core files.
 */

Events::on('pre_system', function () {
    // Registrar namespaces de módulos activos
    try {
        $db = \Config\Database::connect();
        if ($db->DBDriver !== 'MySQLi' && $db->DBDriver !== 'Postgre') {
            return; // evitar errores si la BD no está configurada
        }

        // Verificar si la tabla existe
        $tables = $db->listTables();
        if (!in_array('modules', $tables) && !in_array($db->getDatabase() . '.modules', $tables)) {
            return;
        }

        $modules = $db->table('modules')->where('active', 1)->get()->getResultArray();
        $loader = service('autoloader');

        foreach ($modules as $mod) {
            $path = realpath(ROOTPATH . $mod['path']);
            if ($path && is_dir($path)) {
                $loader->addNamespace(trim($mod['namespace'], '\\'), $path);
            }
        }
    } catch (\Throwable $e) {
        // Silenciar si la BD no está disponible (instalación inicial)
    }
});

/*
 * --------------------------------------------------------------------
 * Debug Toolbar (bloque estándar del skeleton de CI4)
 * --------------------------------------------------------------------
 * Si se elimina este listener, ?debugbar deja de devolver el JS del
 * loader y la barra nunca aparece (además de un SyntaxError en consola:
 * "expected expression, got '<'").
 */
Events::on('pre_system', static function (): void {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        ob_start(static fn($buffer) => $buffer);
    }

    if (CI_DEBUG) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        service('toolbar')->respond();
    }
});
