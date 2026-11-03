<?php

namespace App\Controllers\Admin;

use App\Libraries\BackupLibrary;

class Backups extends BaseController
{
    protected string $backupDir;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->backupDir = WRITEPATH . 'backups';
    }

    public function index()
    {
        $lib = new BackupLibrary();

        $backups = [];
        foreach ($lib->listBackups($this->backupDir) as $path) {
            $backups[] = [
                'file' => basename($path),
                'size' => filesize($path),
                'date' => filemtime($path),
            ];
        }

        return $this->render('backups/index', [
            'title'   => 'Copias de seguridad',
            'backups' => $backups,
        ]);
    }

    public function restoreInfo($file)
    {
        $path = $this->validateBackupFile($file);
        if (!$path) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Archivo no válido.']);
        }

        try {
            $lib  = new BackupLibrary();
            $info = $lib->getBackupInfo($path);

            // Comparar la copia con el estado actual de la web
            $db      = \Config\Database::connect();
            $current = array_column(
                $db->table('modules')->select('slug')->get()->getResultArray(),
                'slug'
            );

            $missing = []; // en la copia pero no en la web -> se restaurarán
            $extra   = []; // en la web pero no en la copia

            foreach (array_keys($info['modules']) as $slug) {
                $dirExists = is_dir(ROOTPATH . 'app/Modules/' . $slug);
                if (!in_array($slug, $current) || !$dirExists) {
                    $missing[] = $slug;
                }
            }
            foreach ($current as $slug) {
                if (!isset($info['modules'][$slug])) {
                    $extra[] = $slug;
                }
            }

            $info['missing_modules'] = $missing;
            $info['extra_modules']   = $extra;

            return $this->response->setJSON(['ok' => true] + $info);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    /** Un paso de la restauración; se llama en bucle desde JS hasta 'finished' */
    public function restoreStep($file)
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Método no permitido.']);
        }

        $path = $this->validateBackupFile($file);
        if (!$path) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Archivo no válido.']);
        }

        set_time_limit(120);
        $lib   = new BackupLibrary();
        $state = session()->get('restore_state');

        try {
            // Inicializar si es el primer paso, cambia el archivo o la anterior ya terminó
            if (
                !$state
                || ($state['file'] ?? null) !== $file
                || ($state['phase'] ?? null) === 'finished'
            ) {
                $info = $lib->getBackupInfo($path);
                $state = [
                    'file'    => $file,
                    'stamp'   => $info['stamp'],
                    'phase'   => $info['uploads'] > 0 ? 'uploads' : ($info['has_db'] ? 'db' : 'finished'),
                    'uploads' => 0,
                    'modules' => 0,
                    'db'      => 0,
                ];
            }

            switch ($state['phase']) {
                case 'uploads':
                    $r = $lib->extractSectionChunk($path, $state['stamp'], 'uploads', $state['uploads'], 100);
                    $state['uploads'] += $r['processed'];
                    if ($r['done']) {
                        $state['phase'] = 'modules';
                        $state['label'] = 'Restaurando módulos...';
                    } else {
                        $state['label'] = 'Restaurando archivos (' . $state['uploads'] . '/' . $r['total'] . ')...';
                    }
                    break;

                case 'modules':
                    $r = $lib->extractSectionChunk($path, $state['stamp'], 'modules', $state['modules'], 100);
                    $state['modules'] += $r['processed'];
                    $state['label'] = 'Restaurando módulos...';
                    if ($r['done']) {
                        $state['phase'] = $this->dbBackupExists($path, $state['stamp']) ? 'db' : 'finished';
                    }
                    break;

                case 'db':
                    $r = $lib->runSqlChunk($path, $state['stamp'], $state['db'], 50);
                    $state['db'] += $r['processed'];
                    $state['label'] = 'Restaurando base de datos (' . $state['db'] . '/' . $r['total'] . ' consultas)...';
                    if ($r['done']) {
                        $state['phase'] = 'finished';
                        $state['label'] = 'Restauración completada.';
                        session()->remove('restore_state');
                    }
                    break;

                default:
                    // Estado 'finished' residual o desconocido: cerrar limpio
                    $state['phase'] = 'finished';
                    $state['label'] = 'Restauración completada.';
                    session()->remove('restore_state');
                    break;
            }

            session()->set('restore_state', $state);
            return $this->response->setJSON(['ok' => true, 'state' => $state]);
        } catch (\Throwable $e) {
            log_message('error', '[backups] restore: ' . $e->getMessage());
            session()->remove('restore_state');
            return $this->response->setJSON(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    private function dbBackupExists(string $path, string $stamp): bool
    {
        $zip = new \ZipArchive();
        $zip->open($path);
        $ok = $zip->locateName($stamp . '/database.sql') !== false;
        $zip->close();
        return $ok;
    }

    private function validateBackupFile($file): ?string
    {
        if (!preg_match('/^backup-\d{8}-\d{6}\.zip$/', $file)) {
            return null;
        }
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $file;
        return is_file($path) ? $path : null;
    }

    public function create()
    {
        try {
            (new BackupLibrary())->createBackup($this->backupDir);
            return redirect()->to('/admin/backups')->with('success', 'Copia de seguridad creada correctamente.');
        } catch (\Throwable $e) {
            log_message('error', '[backups] ' . $e->getMessage());
            return redirect()->to('/admin/backups')->with('error', 'Error al crear la copia.');
        }
    }

    public function download($file)
    {
        if (!preg_match('/^backup-\d{8}-\d{6}\.zip$/', $file)) {
            return redirect()->to('/admin/backups')->with('error', 'Archivo no válido.');
        }

        $path = $this->backupDir . DIRECTORY_SEPARATOR . $file;
        if (!is_file($path)) {
            return redirect()->to('/admin/backups')->with('error', 'Archivo no encontrado.');
        }

        return $this->response->download($path, null)->setFileName($file);
    }

    public function delete($file)
    {
        if (!preg_match('/^backup-\d{8}-\d{6}\.zip$/', $file)) {
            return redirect()->to('/admin/backups')->with('error', 'Archivo no válido.');
        }

        $path = $this->backupDir . DIRECTORY_SEPARATOR . $file;
        if (is_file($path)) {
            unlink($path);
        }

        return redirect()->to('/admin/backups')->with('success', 'Copia eliminada.');
    }
}
