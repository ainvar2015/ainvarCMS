<?php

namespace App\Controllers\Admin;

use App\Models\MenuModel;
use App\Models\PageModel;

class Menus extends BaseController
{
    protected $helpers = ['url', 'form', 'html','i18n'];

    public function index()
    {
        $menuModel = new MenuModel();
        $menus = $menuModel->getAllWithSlug();
        $tree  = $this->buildTree($menus);

        $treeHtml = $this->renderTreeHtml($tree);

        // ← NUEVO: traducciones de etiquetas por item (para repoblar el modal)
        $menuTranslations = [];
        $transModel = new \App\Models\MenuTranslationModel();
        foreach ($menus as $m) {
            foreach ($transModel->where('menu_id', $m['id'])->findAll() as $t) {
                $menuTranslations[$m['id']][$t['lang']] = $t['label'];
            }
        }

        return $this->render('menus/index', [
            'title'            => 'Gestión de Menús',
            'menus'            => $menus,
            'tree'             => $treeHtml,
            'pages'            => (new PageModel())->where('status', 'published')->findAll(),
            'menuTranslations' => $menuTranslations,   // ← NUEVO
        ]);
    }

    private function renderTreeHtml(array $items): string
    {
        $html = '';
        foreach ($items as $item) {
            $url = $item['url'] ?? ($item['page_slug'] ?? '#');
            $icon = !empty($item['icon'])
                ? '<img src="' . base_url($item['icon']) . '" height="20" class="me-2">'
                : '<i class="bi bi-link-45deg me-2 text-muted"></i>';

            $html .= '<li data-id="' . $item['id'] . '">';
            $html .= '<div class="menu-item">';
            $html .= '<div class="d-flex align-items-center flex-grow-1">';
            $html .= '<span class="handle"><i class="bi bi-grip-vertical"></i></span>';
            $html .= '<div class="menu-info">';
            $html .= '<strong>' . esc($item['label']) . '</strong>';
            $html .= '<br><small><code>' . esc($url) . '</code>';
            if ($item['target'] === '_blank') $html .= ' <i class="bi bi-box-arrow-up-right"></i>';
            if (empty($item['active'])) $html .= ' <span class="badge bg-secondary">Inactivo</span>';
            $html .= '</small>';
            $html .= '</div></div>';
            $html .= '<div class="menu-actions">';
            $html .= '<button type="button" class="btn btn-sm btn-outline-primary me-1 edit-btn" ';
            $html .= 'data-id="' . $item['id'] . '" ';
            $html .= 'data-label="' . esc($item['label'], 'attr') . '" ';
            $html .= 'data-url="' . esc($item['url'] ?? '', 'attr') . '" ';
            $html .= 'data-page_id="' . ($item['page_id'] ?? '') . '" ';
            $html .= 'data-parent_id="' . ($item['parent_id'] ?? '') . '" ';
            $html .= 'data-target="' . ($item['target'] ?? '_self') . '" ';
            $html .= 'data-active="' . ($item['active'] ?? 1) . '" ';
            $html .= 'data-icon="' . esc($item['icon'] ?? '', 'attr') . '">';
            $html .= '<i class="bi bi-pencil"></i></button>';
            $html .= '<form action="' . base_url('admin/menus/delete/' . $item['id']) . '" method="post" class="d-inline" '
                . 'onsubmit="return confirm(\'¿Eliminar este item?\')">'
                . csrf_field()
                . '<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>';
            $html .= '</div></div>';

            if (!empty($item['children'])) {
                $html .= '<ul>' . $this->renderTreeHtml($item['children']) . '</ul>';
            }
            $html .= '</li>';
        }
        return $html;
    }

    public function save()
    {
        $id = $this->request->getPost('id');

        $rules = [
            'label' => 'required|min_length[1]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $menuModel = new MenuModel();

        $pageId = $this->request->getPost('page_id') ?: null;

        $data = [
            'label'      => $this->request->getPost('label'),
            'url'        => $pageId ? null : ($this->request->getPost('url') ?: null),  // ← NUEVO: si enlaza a página, sin URL manual (el front resuelve el slug traducido)
            'page_id'    => $pageId,
            'parent_id'  => $this->request->getPost('parent_id') ?: null,
            'target'     => $this->request->getPost('target') ?: '_self',
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
            'active'     => $this->request->getPost('active') ? 1 : 0,
        ];

        // ← NUEVO: un item no puede ser padre de sí mismo (bucle infinito en el árbol)
        if ($id && (int) $data['parent_id'] === (int) $id) {
            $data['parent_id'] = null;
        }

        // Icono
        $file = $this->request->getFile('icon');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/menu-icons', $newName);
            $data['icon'] = 'uploads/menu-icons/' . $newName;
        } elseif ($this->request->getPost('remove_icon')) {
            $data['icon'] = null;
        } elseif ($id) {
            $existing = $menuModel->find($id);
            if ($existing && !empty($existing['icon'])) {
                $data['icon'] = $existing['icon'];
            }
        }

        if ($id) {
            $menuModel->update($id, $data);
            $menuId = (int) $id;
            $msg = 'Menú actualizado.';
        } else {
            $menuModel->insert($data);
            $menuId = (int) $menuModel->getInsertID();
            $msg = 'Menú creado.';
        }

        // ← NUEVO: traducciones de la etiqueta (para enlaces personalizados y por si acaso)
        $transModel = new \App\Models\MenuTranslationModel();
        $labels = (array) $this->request->getPost('label_trans');
        foreach (active_languages() as $l) {
            if ($l['code'] === default_lang()) continue;
            $val = trim((string) ($labels[$l['code']] ?? ''));
            if ($val === '') continue;   // vacío = no traducir (fallback al label base)

            $existing = $transModel->where('menu_id', $menuId)->where('lang', $l['code'])->first();
            if ($existing) {
                $transModel->update($existing['id'], ['label' => $val]);
            } else {
                $transModel->insert(['menu_id' => $menuId, 'lang' => $l['code'], 'label' => $val]);
            }
        }

        return redirect()->to('/admin/menus')->with('success', $msg);
    }

    public function reorder()
    {
        $order = $this->request->getPost('order');

        if (empty($order)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sin datos']);
        }

        $order = json_decode($order, true);
        if (!is_array($order)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Formato inválido']);
        }

        $menuModel = new MenuModel();
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            foreach ($order as $item) {
                $menuModel->update($item['id'], [
                    'parent_id'  => $item['parent_id'] ?: null,
                    'sort_order' => (int) $item['sort_order'],
                ]);
            }

            $db->transComplete();
            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $menuModel = new MenuModel();
        $item = $menuModel->find($id);

        if (!$item) {
            return redirect()->to('/admin/menus')->with('error', 'Item no encontrado.');
        }

        $children = $menuModel->where('parent_id', $id)->findAll();
        if (!empty($children)) {
            return redirect()->to('/admin/menus')->with('error', 'Elimina los submenús primero.');
        }

        $menuModel->delete($id);
        return redirect()->to('/admin/menus')->with('success', 'Menú eliminado.');
    }

    private function buildTree(array $items, $parentId = null): array
    {
        $tree = [];
        foreach ($items as $item) {
            if (($item['parent_id'] ?? null) == $parentId) {
                $item['children'] = $this->buildTree($items, $item['id']);
                $tree[] = $item;
            }
        }
        return $tree;
    }
}
