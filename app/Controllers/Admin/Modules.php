<?php

namespace App\Controllers\Admin;

use App\Models\ModuleModel;

class Modules extends BaseController
{
    protected $helpers = ['url', 'form', 'html'];

    protected array $coreModules = ['i18n'];

    public function index()
    {
        $model = new ModuleModel();

        return $this->render('modules/index', [
            'title'   => 'Módulos',
            'modules' => $model->orderBy('name', 'ASC')->findAll(),
            'coreModules' => $this->coreModules,
            'pendingMigrations' => $this->getPendingMigrations(),
        ]);
    }

    /**
     * Devuelve las migraciones encontradas que no están en la tabla migrations
     */
    private function getPendingMigrations(): array
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('migrations')) {
                return [];
            }

            $runner     = new \CodeIgniter\Database\MigrationRunner(config('Migrations'));
            $migrations = $runner->findMigrations();

            $done = $db->table('migrations')->select('version, class')->get()->getResultArray();
            $doneKeys = array_map(static fn($r) => $r['version'] . '|' . $r['class'], $done);

            $pending = [];
            foreach ($migrations as $m) {
                if (!in_array($m['version'] . '|' . $m['class'], $doneKeys, true)) {
                    $pending[] = $m;
                }
            }
            return $pending;
        } catch (\Throwable $e) {
            log_message('error', '[modules] Error calculando migraciones pendientes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ejecuta las migraciones pendientes (botón desde la vista de módulos)
     */
    public function runMigrations()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/admin/modules');
        }

        try {
            $runner = new \CodeIgniter\Database\MigrationRunner(config('Migrations'));
            $result = $runner->latest();

            if ($result === false) {
                return redirect()->to('/admin/modules')
                    ->with('error', 'La ejecución de migraciones falló. Revisa los logs de CI4.');
            }

            return redirect()->to('/admin/modules')
                ->with('success', 'Migraciones ejecutadas correctamente.');
        } catch (\Throwable $e) {
            log_message('error', '[modules] runMigrations: ' . $e->getMessage());
            return redirect()->to('/admin/modules')
                ->with('error', 'Error ejecutando migraciones: ' . $e->getMessage());
        }
    }
    public function install()
    {
        $rules = [
            'module_zip' => 'uploaded[module_zip]|ext_in[module_zip,zip]|max_size[module_zip,10240]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Archivo inválido. Debe ser un ZIP de máximo 10MB.');
        }

        $file = $this->request->getFile('module_zip');
        $zipPath = $file->getTempName();

        $tmpDir = WRITEPATH . 'uploads/modules_tmp/' . date('YmdHis') . '_' . uniqid();
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0755, true);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return redirect()->back()->with('error', 'No se pudo abrir el ZIP.');
        }

        $zip->extractTo($tmpDir);
        $zip->close();

        $found = $this->findModuleManifest($tmpDir);
        if (!$found) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'No se encontró el manifest del módulo (un .json con name, slug y namespace) en el ZIP.');
        }

        $jsonFile = $found['path'];
        $meta     = $found['meta'];

        // $raw = file_get_contents($jsonFile);
        // $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        // $meta = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'El catalog.json no es válido: ' . json_last_error_msg());
        }

        if (empty($meta['name']) || empty($meta['slug']) || empty($meta['namespace'])) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'Faltan campos obligatorios en catalog.json (name, slug, namespace).');
        }

        $moduleModel = new ModuleModel();
        if ($moduleModel->where('slug', $meta['slug'])->first()) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'Ya existe un módulo con slug "' . $meta['slug'] . '".');
        }

        $destPath = 'app/Modules/' . $meta['slug'];
        if (is_dir(ROOTPATH . $destPath)) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'El directorio ya existe: ' . $destPath);
        }

        $extractedRoot = dirname($jsonFile);
        rename($extractedRoot, ROOTPATH . $destPath);
        $this->rrmdir($tmpDir);

        // ============================================
        // MIGRACIONES: copiar a app/Database/Migrations
        // ============================================
        $migrationsResult = $this->runModuleMigrations(ROOTPATH . $destPath);

        // Registrar en BD
        $moduleModel->insert([
            'name'        => $meta['name'],
            'slug'        => $meta['slug'],
            'version'     => $meta['version'] ?? '1.0.0',
            'description' => $meta['description'] ?? '',
            'namespace'   => $meta['namespace'],
            'path'        => $destPath,
            'has_admin'   => $meta['has_admin'] ?? 1,
            'has_front'   => $meta['has_front'] ?? 1,
            'active'      => 0,
            'installed_at' => date('Y-m-d H:i:s'),
        ]);

        $msg = 'Módulo "' . $meta['name'] . '" instalado.';
        if ($migrationsResult) {
            $msg .= ' ' . $migrationsResult;
        }

        return redirect()->to('/admin/modules')->with('success', $msg);
    }

    /**
     * Copia migraciones del módulo al core, corrige namespace y las ejecuta
     */
    private function runModuleMigrations(string $modulePath): ?string
    {
        $sourceDir = $modulePath . '/Database/Migrations';
        if (!is_dir($sourceDir)) {
            return null;
        }

        $files = glob($sourceDir . '/*.php');
        if (empty($files)) {
            return null;
        }

        $targetDir = ROOTPATH . 'app/Database/Migrations';
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
            return 'ERROR: no se pudo crear la carpeta de migraciones del core: ' . $targetDir;
        }

        sort($files);
        $copied = [];

        foreach ($files as $file) {
            $basename = basename($file);
            $basename = preg_replace('/(\.php)+$/i', '', $basename) . '.php';
            $destFile = $targetDir . '/' . $basename;

            if (file_exists($destFile)) {
                continue;
            }

            $content = file_get_contents($file);
            if ($content === false) {
                return 'ERROR: no se pudo leer ' . basename($file) . ' del módulo.';
            }

            // Normalizar namespace al del core
            $content = preg_replace(
                '/namespace App\\\\Modules\\\\[^;]+\\\\Database\\\\Migrations;/',
                'namespace App\\Database\\Migrations;',
                $content
            );

            $written = @file_put_contents($destFile, $content);
            if ($written === false || !file_exists($destFile)) {
                return 'ERROR: no se pudo copiar ' . $basename . ' a ' . $targetDir
                    . '. Revisa permisos de la carpeta y que ningún programa (editor, antivirus) tenga bloqueado el archivo.';
            }
            $copied[] = $basename;
        }

        if (empty($copied)) {
            return 'Migraciones ya existentes en el core (no se copió nada).';
        }

        // Ejecutar migraciones con el runner de CI4 (sin depender de PHP CLI)
        try {
            $runner = new \CodeIgniter\Database\MigrationRunner(config('Migrations'));
            $result = $runner->latest();

            if ($result === false) {
                log_message('error', '[modules] runModuleMigrations: el runner devolvió false.');
                return 'Copiadas ' . count($copied) . ' migración(es) pero falló la ejecución. '
                    . 'Revisa los logs de CI4.';
            }

            return 'Migraciones copiadas y ejecutadas: ' . implode(', ', $copied) . '.';
        } catch (\Throwable $e) {
            log_message('error', '[modules] Error ejecutando migraciones: ' . $e->getMessage());
            return 'Copiadas ' . count($copied) . ' migración(es) pero la ejecución falló: '
                . $e->getMessage();
        }
    }
    private function runModuleMigrationsDown(array $mod): array
    {
        $log  = [];
        $base = rtrim(ROOTPATH . $mod['path'], '/');

        $dirs = array_filter([$base . '/Database/Migrations', $base . '/Migrations'], 'is_dir');
        if (!$dirs) {
            return ['sin migraciones que revertir'];
        }

        $db       = \Config\Database::connect();
        $forge    = \Config\Database::forge();
        $migTable = $db->table('migrations');

        $files = [];
        foreach ($dirs as $d) {
            $files = array_merge($files, glob($d . '/*.php') ?: []);
        }
        rsort($files); // down en orden inverso al up

        foreach ($files as $file) {
            if (!preg_match('/^(\d{4}-\d{2}-\d{2}-\d{6})_(.+)\.php$/', basename($file), $m)) {
                continue;
            }
            [$full, $version, $class] = $m;
            require_once $file;

            // 1) Resolver la clase: puede estar con namespace del core, del módulo o sin namespace
            $fqcn = null;
            $candidates = [
                'App\\Database\\Migrations\\' . $class,
                trim($mod['namespace'], '\\') . '\\Database\\Migrations\\' . $class,
                $class,
            ];
            foreach ($candidates as $candidate) {
                if (class_exists($candidate)) {
                    $fqcn = $candidate;
                    break;
                }
            }

            if (!$fqcn) {
                $log[] = "CLASE NO ENCONTRADA: {$class}";
                continue;
            }

            // 2) La tabla migrations guarda la clase CON namespace (App\Database\Migrations\X)
            $fqcnRecord = 'App\\Database\\Migrations\\' . $class;

            $row = $migTable->where('class', $fqcnRecord)->where('version', $version)->get()->getRowArray();

            if ($row) {
                try {
                    (new $fqcn($forge, $db))->down();
                    $log[] = "down() OK: {$class}";
                } catch (\Throwable $e) {
                    // Si el down falla NO limpiamos nada: la tabla sigue y el registro también
                    $log[] = "ERROR en down() de {$class}: " . $e->getMessage();
                    continue;
                }
            }

            $migTable->where('class', $fqcnRecord)->where('version', $version)->delete();

            // 3) Copia que el instalador dejó en el core
            foreach (glob(APPPATH . "Database/Migrations/{$version}_{$class}.php") as $coreFile) {
                @unlink($coreFile);
                $log[] = 'copia del core eliminada: ' . basename($coreFile);
            }
        }

        return $log;
    }

    /**
     * Extrae el nombre de clase de un archivo de migración
     */
    private function getMigrationClassName(string $file): ?string
    {
        $content = file_get_contents($file);
        if (
            preg_match('/namespace\s+([^;]+);/', $content, $nsMatch) &&
            preg_match('/class\s+(\w+)/', $content, $classMatch)
        ) {
            return trim($nsMatch[1]) . '\\' . $classMatch[1];
        }
        return null;
    }

    public function toggle($id)
    {
        $model = new ModuleModel();
        $mod = $model->find($id);

        if (!$mod) {
            return redirect()->to('/admin/modules')->with('error', 'Módulo no encontrado.');
        }

        if (in_array($mod['slug'], $this->coreModules, true)) {
            return redirect()->to('/admin/modules')
                ->with('error', '«' . $mod['name'] . '» es un módulo de núcleo: siempre está activo.');
        }

        $newStatus = $mod['active'] ? 0 : 1;
        $model->update($id, ['active' => $newStatus]);

        $msg = $newStatus ? 'activado' : 'desactivado';
        return redirect()->to('/admin/modules')->with('success', 'Módulo ' . $msg . '. Recarga la página si ves errores de ruta.');
    }

    public function uninstall($id)
    {
        $model = new ModuleModel();
        $mod = $model->find($id);

        if (!$mod) {
            return redirect()->to('/admin/modules')->with('error', 'Módulo no encontrado.');
        }

        if (in_array($mod['slug'], $this->coreModules, true)) {
            return redirect()->to('/admin/modules')
                ->with('error', '«' . $mod['name'] . '» es un módulo de núcleo: no se puede desinstalar.');
        }

        // 1. Migraciones: down() + limpieza del registro + copias del core
        $log = $this->runModuleMigrationsDown($mod);

        // 2. Eliminar archivos del módulo
        $fullPath = rtrim(ROOTPATH . $mod['path'], '/');
        if (is_dir($fullPath)) {
            $this->rrmdir($fullPath);
        }

        // 3. Fila del registro
        $model->delete($id);

        return redirect()->to('/admin/modules')
            ->with('success', 'Módulo desinstalado. ' . implode(' · ', $log));
    }

    /* -------------------------------------------------
     * MÉTODOS PRIVADOS
     * ------------------------------------------------*/

    /**
     * Busca el manifest del módulo dentro del ZIP extraído.
     * Acepta cualquier <cualquier-nombre>.json con los campos obligatorios,
     * soportando el convenio <slug>.json y ZIPs con carpeta envoltorio.
     * Devuelve ['path' => ruta, 'meta' => array] o null.
     */
    private function findModuleManifest(string $dir): ?array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) continue;
            if (strtolower($fileInfo->getExtension()) !== 'json') continue;
            if ($iterator->getDepth() > 2) continue;   // máx. 2 niveles, aplica también a archivos

            $raw = file_get_contents($fileInfo->getPathname());
            $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);   // quitar BOM
            $meta = json_decode($raw, true);

            // ¿Es un manifest de módulo? (mismos campos obligatorios que validas en install)
            if (is_array($meta) && !empty($meta['name']) && !empty($meta['slug']) && !empty($meta['namespace'])) {
                return ['path' => $fileInfo->getPathname(), 'meta' => $meta];
            }
        }

        return null;
    }

    private function findCatalogJson(string $dir): ?string
    {
        // Buscar recursivamente (máximo 2 niveles de profundidad para evitar lentitud)
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $depth = 0;
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                $depth = $iterator->getDepth();
                if ($depth > 2) continue;
            }
            if ($fileInfo->getFilename() === 'catalog.json') {
                return $fileInfo->getPathname();
            }
        }
        return null;
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->rrmdir($path) : unlink($path);
        }
        rmdir($dir);
    }
}
