<?php

namespace App\Controllers\Admin;

use App\Models\ThemeModel;

class Themes extends BaseController
{
    protected $helpers = ['url', 'form', 'html'];

    public function index()
    {
        $model = new ThemeModel();
        return $this->render('themes/index', [
            'title'  => 'Temas',
            'themes' => $model->orderBy('name', 'ASC')->findAll(),
            'active' => $model->where('active', 1)->first(),
        ]);
    }

    public function install()
    {
        $rules = [
            'theme_zip' => 'uploaded[theme_zip]|ext_in[theme_zip,zip]|max_size[theme_zip,20480]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Archivo inválido. Debe ser un ZIP de máximo 20MB.');
        }

        $file = $this->request->getFile('theme_zip');
        $zipPath = $file->getTempName();

        $tmpDir = WRITEPATH . 'uploads/themes_tmp/' . date('YmdHis') . '_' . uniqid();
        if (!is_dir($tmpDir) && !mkdir($tmpDir, 0755, true)) {
            return redirect()->back()->with('error', 'No se pudo crear el directorio temporal (permisos en writable/).');
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return redirect()->back()->with('error', 'No se pudo abrir el ZIP.');
        }
        $zip->extractTo($tmpDir);
        $zip->close();

        $jsonFile = $this->findThemeJson($tmpDir);
        if (!$jsonFile) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'El ZIP no contiene theme.json.');
        }

        $raw = preg_replace('/^\xEF\xBB\xBF/', '', (string) file_get_contents($jsonFile));
        $meta = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($meta['name']) || empty($meta['slug'])) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'El theme.json está incompleto (requiere name, slug).');
        }

        $themeModel = new ThemeModel();
        if ($themeModel->where('slug', $meta['slug'])->first()) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'Ya existe un tema con slug "' . $meta['slug'] . '".');
        }

        $publicPath = 'public/themes/' . $meta['slug'];
        $viewsPath  = 'app/Views/front/themes/' . $meta['slug'];

        if (is_dir(ROOTPATH . $publicPath) || is_dir(ROOTPATH . $viewsPath)) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'El tema ya existe en el sistema.');
        }

        $extractedRoot = dirname($jsonFile);

        // Detectar carpetas tolerando mayúsculas/minúsculas (Linux es estricto)
        $assetsSrc = is_dir($extractedRoot . '/assets') ? $extractedRoot . '/assets'
            : (is_dir($extractedRoot . '/Assets') ? $extractedRoot . '/Assets' : null);
        $viewsSrc  = is_dir($extractedRoot . '/views')  ? $extractedRoot . '/views'
            : (is_dir($extractedRoot . '/Views')  ? $extractedRoot . '/Views'  : null);

        if (!$viewsSrc) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'El ZIP no contiene la carpeta Views.');
        }

        // Assets → public/themes/{slug}/assets
        if (!is_dir(ROOTPATH . $publicPath) && !mkdir(ROOTPATH . $publicPath, 0755, true)) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'Sin permisos de escritura en public/themes/ (revisa el servidor).');
        }

        if ($assetsSrc) {
            if (!$this->moveDir($assetsSrc, ROOTPATH . $publicPath . '/assets')) {
                $this->rrmdir($tmpDir);
                return redirect()->back()->with('error', 'No se pudieron copiar los assets (permisos o filesystem).');
            }
        } else {
            mkdir(ROOTPATH . $publicPath . '/assets', 0755, true);
            foreach (['css', 'js', 'img'] as $sub) {
                if (is_dir($extractedRoot . '/' . $sub)) {
                    $this->moveDir($extractedRoot . '/' . $sub, ROOTPATH . $publicPath . '/assets/' . $sub);
                }
            }
            $shot = $meta['screenshot'] ?? '';
            if ($shot !== '' && !str_starts_with($shot, 'assets/') && is_file($extractedRoot . '/' . $shot)) {
                copy($extractedRoot . '/' . $shot, ROOTPATH . $publicPath . '/assets/' . basename($shot));
            }
        }

        // Views → app/Views/front/themes/{slug} (creando el padre si no existe)
        if (!is_dir(ROOTPATH . 'app/Views/front/themes') && !mkdir(ROOTPATH . 'app/Views/front/themes', 0755, true)) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'Sin permisos de escritura en app/Views/front/themes/ (revisa el servidor).');
        }
        if (!$this->moveDir($viewsSrc, ROOTPATH . $viewsPath)) {
            $this->rrmdir($tmpDir);
            return redirect()->back()->with('error', 'No se pudieron copiar las vistas (permisos o filesystem).');
        }

        $this->rrmdir($tmpDir);

        $themeModel->insert([
            'name'        => $meta['name'],
            'slug'        => $meta['slug'],
            'version'     => $meta['version'] ?? '1.0.0',
            'description' => $meta['description'] ?? '',
            'path'        => $meta['slug'],
            'screenshot'  => $meta['screenshot'] ?? null,
            'is_default'  => 0,
            'active'      => 0,
        ]);

        return redirect()->to('/admin/themes')->with('success', 'Tema "' . $meta['name'] . '" instalado. Actívalo para usarlo.');
    }

    /**
     * Mueve un directorio. Si origen y destino están en distintos filesystems
     * (típico en hosting: writable/ vs public/), rename() falla, así que
     * hacemos copia recursiva y borramos el origen.
     */
    private function moveDir(string $src, string $dst): bool
    {
        if (!is_dir($src)) return false;

        if (@rename($src, $dst)) return true;

        if (!is_dir($dst) && !mkdir($dst, 0755, true)) return false;

        $ok = true;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($it as $item) {
            $target = $dst . DIRECTORY_SEPARATOR . $it->getSubPathName();
            if ($item->isDir()) {
                if (!is_dir($target)) $ok = mkdir($target, 0755, true) && $ok;
            } else {
                $ok = copy($item->getPathname(), $target) && $ok;
            }
        }
        if ($ok) $this->rrmdir($src);
        return $ok;
    }

    public function activate($id)
    {
        $model = new ThemeModel();
        $theme = $model->find($id);

        if (!$theme) {
            return redirect()->to('/admin/themes')->with('error', 'Tema no encontrado.');
        }

        // Desactivar todos
        $model->where('active', 1)->set(['active' => 0])->update();

        // Activar el seleccionado
        $model->update($id, ['active' => 1]);

        return redirect()->to('/admin/themes')->with('success', 'Tema "' . $theme['name'] . '" activado.');
    }

    public function delete($id)
    {
        $model = new ThemeModel();
        $theme = $model->find($id);

        if (!$theme) {
            return redirect()->to('/admin/themes')->with('error', 'Tema no encontrado.');
        }

        if ($theme['active']) {
            return redirect()->to('/admin/themes')->with('error', 'No puedes eliminar el tema activo. Activa otro primero.');
        }

        if ($theme['is_default']) {
            return redirect()->to('/admin/themes')->with('error', 'No se puede eliminar el tema por defecto.');
        }

        // Borrar archivos
        $publicDir = ROOTPATH . 'public/themes/' . $theme['path'];
        $viewsDir  = ROOTPATH . 'app/Views/front/themes/' . $theme['path'];

        if (is_dir($publicDir)) $this->rrmdir($publicDir);
        if (is_dir($viewsDir))  $this->rrmdir($viewsDir);

        $model->delete($id);
        return redirect()->to('/admin/themes')->with('success', 'Tema eliminado.');
    }

    /* ------------------------------------------------- */
    private function findThemeJson(string $dir): ?string
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->getFilename() === 'theme.json') {
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
