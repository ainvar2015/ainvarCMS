<?php

namespace App\Controllers\Admin;

class Search extends BaseController
{
    // GET admin/search?q=... → JSON agrupado
    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));

        $out = ['pages' => [], 'media' => [], 'products' => [], 'gallery' => []];
        if (mb_strlen($q) < 2) {
            return $this->response->setJSON($out);
        }

        // Páginas (título o slug)
        try {
            $rows = (new \App\Models\PageModel())
                ->groupStart()->like('title', $q)->orLike('slug', $q)->groupEnd()
                ->limit(5)->findAll();
            foreach ($rows as $r) {
                $out['pages'][] = [
                    'label' => $r['title'],
                    'meta'  => $r['slug'],
                    'url'   => base_url('admin/pages/edit/' . $r['id']),
                ];
            }
        } catch (\Throwable $e) {
        }

        // Medios (nombre original o título)
        try {
            $rows = (new \App\Models\MediaModel())
                ->groupStart()->like('original_name', $q)->orLike('title', $q)->groupEnd()
                ->limit(5)->findAll();
            foreach ($rows as $r) {
                $out['media'][] = [
                    'label' => $r['title'] ?: $r['original_name'],
                    'meta'  => $r['original_name'],
                    'url'   => base_url('admin/media'),
                ];
            }
        } catch (\Throwable $e) {
        }

        // Productos
        try {
            $rows = (new \App\Modules\Catalog\Models\ProductModel())
                ->like('name', $q)
                ->limit(5)->findAll();
            foreach ($rows as $r) {
                $out['products'][] = [
                    'label' => $r['name'],
                    'meta'  => number_format((float) $r['price'], 2, ',', '.') . ' €',
                    'url'   => base_url('admin/catalog/products/edit/' . $r['id']),
                ];
            }
        } catch (\Throwable $e) {
        }

        // Galería (título o álbum)
        try {
            $rows = (new \App\Modules\Gallery\Models\ImageModel())
                ->groupStart()->like('title', $q)->orLike('album', $q)->groupEnd()
                ->limit(5)->findAll();
            foreach ($rows as $r) {
                $out['gallery'][] = [
                    'label' => $r['title'] ?: basename($r['file_path']),
                    'meta'  => $r['album'],
                    'url'   => base_url('admin/gallery/images'),
                ];
            }
        } catch (\Throwable $e) {
        }

        return $this->response->setJSON($out);
    }
}