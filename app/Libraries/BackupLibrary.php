<?php

namespace App\Libraries;

class BackupLibrary
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Genera un dump SQL completo (estructura + datos) de la base de datos.
     */
    public function createSqlDump(): string
    {
        $sql  = "-- Backup AinvarCMS\n";
        $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($this->db->listTables() as $table) {
            // Estructura
            $row = $this->db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            $create = $row['Create Table'] ?? $row[1] ?? '';
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $create . ";\n\n";

            // Datos en lotes de 500 filas
            $batchSize = 500;
            $offset    = 0;
            $total     = $this->db->table($table)->countAll();

            while ($offset < $total) {
                $rows = $this->db->table($table)->limit($batchSize, $offset)->get()->getResultArray();
                if ($rows) {
                    $cols    = array_keys($rows[0]);
                    $colList = '`' . implode('`, `', $cols) . '`';
                    $values  = [];

                    foreach ($rows as $r) {
                        $vals = [];
                        foreach ($r as $v) {
                            $vals[] = $v === null ? 'NULL' : $this->db->escape($v);
                        }
                        $values[] = '(' . implode(', ', $vals) . ')';
                    }

                    $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n"
                        . implode(",\n", $values) . ";\n\n";
                }
                $offset += $batchSize;
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    /**
     * Crea el ZIP con el SQL + la carpeta uploads. Devuelve la ruta del ZIP.
     */
    public function createBackup(string $destDir): string
    {
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $stamp   = date('Ymd-His');
        $zipPath = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . "backup-{$stamp}.zip";

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('No se pudo crear el archivo ZIP.');
        }

        // 1. Base de datos
        $zip->addFromString("backup-{$stamp}/database.sql", $this->createSqlDump());

        // 2. Uploads
        $uploads = FCPATH . 'uploads';
        if (is_dir($uploads)) {
            $this->addFolderToZip($zip, $uploads, "backup-{$stamp}/uploads");
        }

        // 3. Módulos: código + estado para poder comparar al restaurar
        $modules = $this->db->table('modules')->get()->getResultArray();
        foreach ($modules as $mod) {
            $modPath = ROOTPATH . $mod['path'];
            if (is_dir($modPath)) {
                $this->addFolderToZip($zip, $modPath, "backup-{$stamp}/modules/" . $mod['slug']);
            }
        }

        // 4. Manifest
        $zip->addFromString("backup-{$stamp}/manifest.json", json_encode([
            'created_at' => date('c'),
            'tables'     => count($this->db->listTables()),
            'modules'    => $modules,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $zip->close();
        return $zipPath;
    }

    /**
     * Información de una copia para la pantalla de confirmación y el estado inicial.
     * - Detecta el prefijo interno del ZIP (backup-YYYYMMDD-His)
     * - Cuenta uploads y detecta módulos escaneando las entradas
     * - Usa el manifest como fuente fiable de la lista de módulos (cubre ZIPs
     *   sin entradas de directorio y copias donde el escaneo falle)
     */
    public function getBackupInfo(string $zipPath): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('No se pudo abrir el ZIP.');
        }

        // Localizar el prefijo interno: backup-YYYYMMDD-His
        $stamp = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            if (preg_match('#^(backup-\d{8}-\d{6})/#', $zip->getNameIndex($i), $m)) {
                $stamp = $m[1];
                break;
            }
        }
        if (!$stamp) {
            $zip->close();
            throw new \RuntimeException('Estructura de copia no reconocida.');
        }

        $prefix = $stamp . '/';
        $info = [
            'stamp'    => $stamp,
            'has_db'   => false,
            'uploads'  => 0,
            'modules'  => [],
            'manifest' => null,
        ];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === $prefix . 'database.sql') {
                $info['has_db'] = true;
            } elseif (str_starts_with($name, $prefix . 'uploads/') && substr($name, -1) !== '/') {
                $info['uploads']++;
            } elseif (preg_match('#^' . preg_quote($prefix, '#') . 'modules/([a-z0-9_-]+)(/|$)#i', $name, $m)) {
                $info['modules'][$m[1]] = true;
            }
        }

        $manifest = $zip->getFromName($prefix . 'manifest.json');
        if ($manifest !== false) {
            $info['manifest'] = json_decode($manifest, true);
            // El manifest refleja los módulos registrados aunque el ZIP no guarde sus entradas
            foreach (($info['manifest']['modules'] ?? []) as $mod) {
                if (!empty($mod['slug'])) {
                    $info['modules'][$mod['slug']] = true;
                }
            }
        }

        $zip->close();
        return $info;
    }

    /**
     * Extrae un trozo de una sección del ZIP.
     * uploads  -> FCPATH/uploads
     * modules  -> ROOTPATH/app/Modules (nunca sobrescribe código existente)
     */
    public function extractSectionChunk(string $zipPath, string $stamp, string $section, int $cursor, int $limit): array
    {
        $destBase = $section === 'uploads'
            ? FCPATH . 'uploads'
            : ROOTPATH . 'app' . DIRECTORY_SEPARATOR . 'Modules';
        $prefix = $stamp . '/' . $section . '/';

        $zip = new \ZipArchive();
        $zip->open($zipPath);

        $entries = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_starts_with($name, $prefix) && substr($name, -1) !== '/') {
                $entries[] = $name;
            }
        }

        $total = count($entries);
        $slice = array_slice($entries, $cursor, $limit);
        $done  = 0;

        foreach ($slice as $name) {
            $rel  = substr($name, strlen($prefix));
            $dest = $destBase . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);

            if ($section === 'modules' && file_exists($dest)) {
                continue; // no degradar módulos existentes
            }

            $content = $zip->getFromName($name);
            if ($content === false) {
                continue;
            }
            if (!is_dir(dirname($dest))) {
                mkdir(dirname($dest), 0755, true);
            }
            file_put_contents($dest, $content);
            $done++;
        }

        $zip->close();

        $newCursor = $cursor + count($slice);
        return [
            'done'      => $newCursor >= $total,
            'processed' => count($slice),   // avanza aunque el archivo ya exista y se salte
            'written'   => $done,           // cuántos realmente se escribieron (informativo)
            'total'     => $total,
        ];
    }

    /**
     * Ejecuta un trozo del SQL del backup.
     * Separa por ";\n": seguro para este formato de dump porque los saltos de
     * línea dentro de cadenas se escapan como \n literal al hacer escape().
     */
    public function runSqlChunk(string $zipPath, string $stamp, int $cursor, int $limit): array
    {
        $tmp = WRITEPATH . 'restore_' . $stamp . '.sql';

        if (!is_file($tmp)) {
            $zip = new \ZipArchive();
            $zip->open($zipPath);
            $sql = $zip->getFromName($stamp . '/database.sql');
            $zip->close();
            if ($sql === false) {
                throw new \RuntimeException('database.sql no encontrado en la copia.');
            }
            file_put_contents($tmp, $sql);
        }

        $sql  = preg_replace('/^\s*--.*$\n?/m', '', file_get_contents($tmp));
        $statements = array_values(array_filter(array_map('trim', explode(";\n", $sql))));

        $total = count($statements);
        $slice = array_slice($statements, $cursor, $limit);

        foreach ($slice as $stmt) {
            $this->db->query($stmt);
        }

        $newCursor = $cursor + count($slice);
        $done = $newCursor >= $total;
        if ($done) {
            @unlink($tmp);
        }

        return ['done' => $done, 'processed' => count($slice), 'total' => $total];
    }

    protected function addFolderToZip(\ZipArchive $zip, string $folder, string $zipFolder): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $rel   = str_replace('\\', '/', $iterator->getSubPathname());
            $first = explode('/', $rel)[0];

            // Excluir miniaturas: son regenerables (php spark thumbs:regenerate --force)
            if ($first === 'thumbs') {
                continue;
            }

            $local = $zipFolder . '/' . $rel;
            if ($file->isDir()) {
                $zip->addEmptyDir($local);
            } else {
                $zip->addFile($file->getPathname(), $local);
            }
        }
    }

    public function listBackups(string $dir): array
    {
        $files = glob(rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . 'backup-*.zip') ?: [];
        rsort($files);
        return $files;
    }
}
