<?php

namespace App\Controllers\Admin;

use App\Models\MediaModel;
use App\Models\MediaFolderModel;

class Media extends BaseController
{
    public function index($folderId = null)
    {
        $folderModel = new MediaFolderModel();
        $mediaModel  = new MediaModel();

        $folder = $folderId ? $folderModel->find($folderId) : null;

        $folders = $folderModel->where('parent_id', $folderId)->orderBy('name', 'ASC')->findAll();
        $media   = $mediaModel->where('folder_id', $folderId)->orderBy('id', 'DESC')->findAll();

        // Migas de pan
        $breadcrumb = [];
        $current = $folder;
        while ($current) {
            array_unshift($breadcrumb, $current);
            $current = $current['parent_id'] ? $folderModel->find($current['parent_id']) : null;
        }

        return $this->render('media/index', [
            'title'      => 'Medios',
            'folder'     => $folder,
            'folders'    => $folders,
            'media'      => $media,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public function upload($folderId = null)
    {
        try {
            $folderId = $folderId ?: $this->request->getPost('folder_id');
            if (! is_numeric($folderId)) {   // 'undefined', '', null, texto suelto → raíz
                $folderId = null;
            } else {
                $folderId = (int) $folderId;
            }
            $errors   = [];
            $uploaded = [];

            foreach ($this->request->getFiles() as $fieldFiles) {
                $list = is_array($fieldFiles) ? $fieldFiles : [$fieldFiles];
                foreach ($list as $file) {
                    if (!$file || !$file->isValid()) {
                        continue;
                    }

                    // MIME real: debe leerse ANTES de move()
                    $mime = (string) $file->getMimeType();

                    if (!str_starts_with($mime, 'image/') || $file->getSize() > 5 * 1024 * 1024) {
                        $errors[] = $file->getClientName() . ' (no es imagen válida o supera 5MB)';
                        continue;
                    }

                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/media', $newName);
                    $fullPath = FCPATH . 'uploads/media/' . $newName;
                    $dims = @getimagesize($fullPath);

                    \App\Libraries\ImageThumb::generate($fullPath);

                    $relPath = 'uploads/media/' . $newName;
                    $mediaId = (new MediaModel())->insert([
                        'folder_id'     => $folderId ?: null,
                        'file_path'     => $relPath,
                        'original_name' => $file->getClientName(),
                        'mime_type'     => $mime,
                        'size'          => $file->getSize(),
                        'width'         => $dims[0] ?? null,
                        'height'        => $dims[1] ?? null,
                        'uploaded_by'   => session()->get('user_id'),
                    ]);

                    // Datos para pintar la card en el grid sin recargar
                    $uploaded[] = [
                        'id'            => $mediaId,
                        'original_name' => $file->getClientName(),
                        'url'           => base_url($relPath),
                        'thumb'         => base_url(\App\Libraries\ImageThumb::thumbRel($relPath) ?? $relPath),
                        'size'          => $file->getSize(),
                        'width'         => $dims[0] ?? null,
                        'height'        => $dims[1] ?? null,
                        'folder_id'     => $folderId ?: null,
                    ];
                }
            }

            return $this->response->setJSON([
                'ok'       => empty($errors),
                'uploaded' => $uploaded,   // ahora es array de objetos, no un contador
                'errors'   => $errors,
                'csrf'     => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[media upload] ' . $e->getMessage() . ' :: ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'ok'     => false,
                'errors' => ['EXCEPCIÓN: ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'],
                'csrf'   => csrf_hash(),
            ], 500);
        }
    }

    public function update($id)
    {
        $model = new MediaModel();
        if (!$model->find($id)) {
            return redirect()->back()->with('error', 'Archivo no encontrado.');
        }

        $model->update($id, [
            'title'     => $this->request->getPost('title'),
            'alt_text'  => $this->request->getPost('alt_text'),
            'folder_id' => $this->request->getPost('folder_id') ?: null,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
        }
        return redirect()->back()->with('success', 'Metadatos actualizados.');
    }

    public function delete($id)
    {
        $model = new MediaModel();
        $item  = $model->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Archivo no encontrado.');
        }

        if (is_file(FCPATH . $item['file_path'])) {
            unlink(FCPATH . $item['file_path']);
        }

        // Limpiar su miniatura si existe
        $thumbRel = \App\Libraries\ImageThumb::thumbRel($item['file_path']);
        if ($thumbRel && is_file(FCPATH . $thumbRel)) {
            unlink(FCPATH . $thumbRel);
        }

        $model->delete($id);

        return redirect()->back()->with('success', 'Archivo eliminado.');
    }

    // Importa a la biblioteca las imágenes de Gallery que aún no estén
    public function syncFromGallery()
    {
        if (!class_exists('\App\Modules\Gallery\Models\ImageModel')) {
            return redirect()->to('/admin/media')->with(
                'error',
                'El módulo Gallery no está instalado. Instálalo primero desde el gestor de módulos.'
            );
        }

        try {
            $imageModel = new \App\Modules\Gallery\Models\ImageModel();
            $mediaModel = new \App\Models\MediaModel();

            $added  = 0;
            $images = $imageModel->findAll();

            foreach ($images as $img) {
                // Ya está en la biblioteca → saltar
                if ($mediaModel->where('file_path', $img['file_path'])->first()) {
                    continue;
                }

                $fullPath = FCPATH . $img['file_path'];
                $exists   = is_file($fullPath);
                $dims     = $exists ? @getimagesize($fullPath) : [null, null];
                $mime     = $exists ? (string) mime_content_type($fullPath) : null;
                if ($exists) {
                    \App\Libraries\ImageThumb::generate($fullPath);
                }

                $mediaModel->insert([
                    'folder_id'     => null,
                    'file_path'     => $img['file_path'],
                    'original_name' => $img['title'] ?: basename($img['file_path']),
                    'mime_type'     => $mime,
                    'size'          => $exists ? filesize($fullPath) : 0,
                    'width'         => $dims[0] ?? null,
                    'height'        => $dims[1] ?? null,
                    'title'         => $img['title'],
                    'alt_text'      => $img['title'],
                    'uploaded_by'   => null,
                ]);
                $added++;
            }

            return redirect()->to('/admin/media')->with(
                'success',
                $added > 0 ? "{$added} imagen(es) de Gallery importadas a la biblioteca."
                    : 'Todo estaba ya sincronizado. No hay nada nuevo que importar.'
            );
        } catch (\Throwable $e) {
            log_message('error', '[media sync] ' . $e->getMessage() . ' :: ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->to('/admin/media')->with('error', 'Error al sincronizar con Gallery. Revisa los logs.');
        }
    }

    public function createFolder()
    {
        $name     = trim((string) $this->request->getPost('name'));
        $parentId = $this->request->getPost('parent_id') ?: null;

        if ($name === '') {
            return redirect()->back()->with('error', 'El nombre de la carpeta es obligatorio.');
        }

        (new MediaFolderModel())->insert([
            'name'      => $name,
            'parent_id' => $parentId,
        ]);

        return redirect()->back()->with('success', 'Carpeta creada.');
    }

    public function deleteFolder($id)
    {
        $model  = new MediaFolderModel();
        $folder = $model->find($id);
        if (!$folder) {
            return redirect()->back()->with('error', 'Carpeta no encontrada.');
        }

        // Solo carpetas vacías (las imágenes pasan a raíz vía SET_NULL)
        if ((new MediaModel())->where('folder_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'La carpeta no está vacía. Mueve o elimina sus imágenes primero.');
        }
        if ($model->where('parent_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'La carpeta tiene subcarpetas. Elimínalas primero.');
        }

        $model->delete($id);
        return redirect()->back()->with('success', 'Carpeta eliminada.');
    }

    // JSON para el picker (Summernote, Gallery, etc.)
    public function apiList($folderId = null)
    {
        $folderModel = new MediaFolderModel();
        $mediaModel  = new MediaModel();

        return $this->response->setJSON([
            'folder'  => $folderId,
            'folders' => array_map(
                fn($f) => ['id' => $f['id'], 'name' => $f['name']],
                $folderModel->where('parent_id', $folderId)->orderBy('name', 'ASC')->findAll()
            ),
            'media' => array_map(
                fn($m) => [
                    'id'    => $m['id'],
                    'url'   => base_url($m['file_path']),
                    'title' => $m['title'],
                    'alt'   => $m['alt_text'],
                    'size'  => $m['size'],
                ],
                $mediaModel->where('folder_id', $folderId)->orderBy('id', 'DESC')->limit(100)->findAll()
            ),
            'csrf' => csrf_hash(),
        ]);
    }
}
