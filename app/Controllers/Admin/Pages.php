<?php

namespace App\Controllers\Admin;

use App\Models\PageModel;
use App\Models\MenuModel;
use App\Models\TemplateModel;
use App\Models\ModuleModel;

class Pages extends BaseController
{
    protected $helpers = ['url', 'form', 'html', 'i18n'];

    /* -------------------------------------------------
     * LISTADO
     * ------------------------------------------------*/
    public function index()
    {
        $pageModel = new PageModel();
        $pages = $pageModel->orderBy('sort_order', 'ASC')->findAll();

        return $this->render('pages/index', [
            'title' => 'Gestión de Páginas',
            'pages' => $pages,
        ]);
    }

    /* -------------------------------------------------
     * CREAR
     * ------------------------------------------------*/
    public function create()
    {
        return $this->render('pages/form', [
            'title'      => 'Nueva Página',
            'page'       => null,
            'templates'  => (new TemplateModel())->where('active', 1)->findAll(),
            'modules'    => (new ModuleModel())->where('active', 1)->findAll(),
            'menus'      => (new MenuModel())->where('active', 1)->where('parent_id IS NULL')->findAll(),
            'action'     => base_url('admin/pages/store'),
            'menuItem'          => null,      // ← en alta no hay fila de menú
            'menu_translations' => [],        // ← ni traducciones
        ]);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[2]|max_length[255]',
            'slug'  => 'permit_empty|max_length[255]',
            'type'  => 'required|in_list[normal,template,module]',
            'status' => 'required|in_list[published,draft,hidden]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pageModel = new PageModel();

        $slug = $this->request->getPost('slug');
        if (empty($slug)) {
            $slug = $this->generateSlug($this->request->getPost('title'));
        }
        $slug = $this->ensureUniqueSlug($slug);

        $type = $this->request->getPost('type');

        $data = [
            'title'       => $this->request->getPost('title'),
            'slug'        => $slug,
            'type'        => $type,
            'template_id' => $this->request->getPost('template_id') ?: null,
            'module_name' => $this->request->getPost('module_name') ?: null,
            'meta_title'  => $this->request->getPost('meta_title'),
            'meta_desc'   => $this->request->getPost('meta_desc'),
            'status'      => $this->request->getPost('status'),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
        ];

        // CONTENT según tipo (nada de 'content' antes en el array)
        if ($type === 'template') {
            $fields = $this->request->getPost('template_fields');
            $data['content'] = json_encode(is_array($fields) ? $fields : [], JSON_UNESCAPED_UNICODE);
        } elseif ($type === 'module') {
            $data['content'] = null;
        } else {
            $data['content'] = $this->request->getPost('content');
        }

        $pageModel->insert($data);
        $newId = $pageModel->getInsertID();   // o el valor que devuelva tu insert

        if ($this->request->getPost('add_to_menu')) {
            $this->syncMenu($newId, $data['title'], $slug);

            // Guardar traducciones de la etiqueta
            $menuItem = (new \App\Models\MenuModel())->where('page_id', $newId)->first();
            if ($menuItem) {
                $this->saveMenuTranslations((int) $menuItem['id']);
            }
        }

        $this->saveTranslations($newId);

        return redirect()->to('/admin/pages')->with('success', 'Página creada correctamente.');
    }
    /* -------------------------------------------------
     * EDITAR
     * ------------------------------------------------*/
    public function edit($id)
    {
        $pageModel = new PageModel();
        $page = $pageModel->find($id);

        if (!$page) {
            return redirect()->to('/admin/pages')->with('error', 'Página no encontrada.');
        }

        // Decodificar valores de template si los hay
        $templateData = [];
        if ($page['type'] === 'template' && !empty($page['content'])) {
            $decoded = json_decode($page['content'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $templateData = $decoded;
            }
        }

        $revModel = new \App\Models\PageRevisionModel();
        $revisions = $revModel->select('page_revisions.*, users.username')
            ->join('users', 'users.id = page_revisions.user_id', 'left')
            ->where('page_id', $id)
            ->orderBy('page_revisions.id', 'DESC')
            ->findAll();

        $translations = [];
        foreach ((new \App\Models\PageTranslationModel())->where('page_id', $id)->findAll() as $t) {
            $translations[$t['lang']] = $t;
        }

        $menuItem = (new \App\Models\MenuModel())->where('page_id', $id)->first();
        $menuTranslations = [];
        if ($menuItem) {
            foreach ((new \App\Models\MenuTranslationModel())->where('menu_id', $menuItem['id'])->findAll() as $mt) {
                $menuTranslations[$mt['lang']] = $mt;
            }
        }

        return $this->render('pages/form', [
            'title'            => 'Editar página',
            'p'                => $page,
            'isEdit'           => true,
            'action'           => base_url('admin/pages/update/' . $id),   // ← AÑADIR
            'menus'            => (new MenuModel())->getAllWithSlug(),
            'menuItem'         => $menuItem,
            'menu_translations' => $menuTranslations,
            'page'           => $page,
            'templates'      => (new TemplateModel())->where('active', 1)->findAll(),
            'modules'        => (new ModuleModel())->where('active', 1)->findAll(),
            'template_data'  => $templateData,
            'revisions'      => $revisions,
            'translations'   => $translations,
            'languages_list' => active_languages(),

        ]);
    }

    public function update($id)
    {
        $pageModel = new PageModel();
        $page = $pageModel->find($id);

        if (!$page) {
            return redirect()->to('/admin/pages')->with('error', 'Página no encontrada.');
        }

        $rules = [
            'title' => 'required|min_length[2]|max_length[255]',
            'slug'  => "permit_empty|max_length[255]|is_unique[pages.slug,id,{$id}]",
            'type'  => 'required|in_list[normal,template,module]',
            'status' => 'required|in_list[published,draft,hidden]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = $this->request->getPost('slug');
        if (empty($slug)) {
            $slug = $this->generateSlug($this->request->getPost('title'));
        }
        if ($slug !== $page['slug']) {
            $slug = $this->ensureUniqueSlug($slug, $id);
        }

        $type = $this->request->getPost('type');

        $data = [
            'title'       => $this->request->getPost('title'),
            'slug'        => $slug,
            'type'        => $type,
            'template_id' => $this->request->getPost('template_id') ?: null,
            'module_name' => $this->request->getPost('module_name') ?: null,
            'meta_title'  => $this->request->getPost('meta_title'),
            'meta_desc'   => $this->request->getPost('meta_desc'),
            'status'      => $this->request->getPost('status'),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
        ];

        // CONTENT: según tipo (MUY IMPORTANTE — no pongas 'content' antes en el array)
        if ($type === 'template') {
            $fields = $this->request->getPost('template_fields');
            $data['content'] = json_encode(is_array($fields) ? $fields : [], JSON_UNESCAPED_UNICODE);
        } elseif ($type === 'module') {
            $data['content'] = null;
        } else {
            $data['content'] = $this->request->getPost('content');
        }

        // === VERSIONES: guardar el estado ANTES de cambiarlo ===
        $current = $pageModel->find($id);   // o new PageModel() si no usas $this->pageModel
        if ($current) {
            $revModel = new \App\Models\PageRevisionModel();
            $revModel->createSnapshot($current, session()->get('user_id'));
            $revModel->prune($id, 20);
        }

        $pageModel->update($id, $data);

        if ($this->request->getPost('add_to_menu')) {
            $this->syncMenu($id, $data['title'], $slug);

            $menuItem = (new MenuModel())->where('page_id', $id)->first();
            if ($menuItem) {
                $this->saveMenuTranslations((int) $menuItem['id']);
            }
        } else {
            // Desmarcado: quitar la página del menú
            $menuItem = (new MenuModel())->where('page_id', $id)->first();
            if ($menuItem) {
                (new \App\Models\MenuTranslationModel())->where('menu_id', $menuItem['id'])->delete();
                (new MenuModel())->delete($menuItem['id']);
            }
        }
        $this->saveTranslations($id);

        return redirect()->to('/admin/pages')->with('success', 'Página actualizada correctamente.');
    }

    /**-----------------------------------------------
     * RESTAURAR VERSIÓN
     * -----------------------------------------------*/
    public function restore($revisionId)
    {
        $revModel = new \App\Models\PageRevisionModel();
        $revision = $revModel->find($revisionId);

        if (!$revision) {
            return redirect()->back()->with('error', 'Revisión no encontrada.');
        }

        $pageModel = new \App\Models\PageModel();
        $page = $pageModel->find($revision['page_id']);
        if (!$page) {
            return redirect()->back()->with('error', 'La página ya no existe.');
        }

        // Foto del estado actual ANTES de restaurar → la restauración es deshacible
        $revModel->createSnapshot($page, session()->get('user_id'));

        $pageModel->update($page['id'], [
            'title'      => $revision['title'],
            'content'    => $revision['content'],
            'meta_title' => $revision['meta_title'],
            'meta_desc'  => $revision['meta_desc'],
        ]);

        return redirect()->to('/admin/pages/edit/' . $page['id'])
            ->with('success', 'Versión restaurada. El estado anterior quedó guardado en el historial.');
    }

    /**
     * Guardar traducciones de la página en la tabla page_translations.
     * Se llama desde store() y update().
     */
    private function saveTranslations(int $pageId): void
    {
        $transModel  = new \App\Models\PageTranslationModel();
        $trans       = $this->request->getPost('trans') ?: [];
        $defaultLang = default_lang();

        foreach (active_languages() as $l) {
            $code = $l['code'];
            if ($code === $defaultLang) {
                continue;   // el idioma por defecto vive en pages
            }

            $data = $trans[$code] ?? null;

            // Sin título → borrar traducción (si existía)
            if (!$data || trim((string) ($data['title'] ?? '')) === '') {
                $transModel->where('page_id', $pageId)->where('lang', $code)->delete();
                continue;
            }

            $slug = trim((string) ($data['slug'] ?? ''));
            if ($slug === '') {
                $slug = url_title($data['title'], '-', true);
            }

            $row = [
                'title'      => $data['title'],
                'slug'       => $slug,
                'content'    => $data['content'] ?? null,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_desc'  => $data['meta_desc'] ?? null,
            ];

            $existing = $transModel->where('page_id', $pageId)->where('lang', $code)->first();
            try {
                if ($existing) {
                    $transModel->update($existing['id'], $row);
                } else {
                    $transModel->insert(array_merge($row, ['page_id' => $pageId, 'lang' => $code]));
                }
            } catch (\Throwable $e) {
                // Slug duplicado en ese idioma: reintentar con sufijo
                $row['slug'] .= '-' . substr(md5((string) microtime(true)), 0, 4);
                if ($existing) {
                    $transModel->update($existing['id'], $row);
                } else {
                    $transModel->insert(array_merge($row, ['page_id' => $pageId, 'lang' => $code]));
                }
            }
        }
    }

    private function saveMenuTranslations(int $menuId): void
    {
        $transModel = new \App\Models\MenuTranslationModel();
        $labels = (array) $this->request->getPost('menu_label_trans');

        foreach (active_languages() as $l) {
            if ($l['code'] === default_lang()) continue;
            $val = trim((string) ($labels[$l['code']] ?? ''));
            if ($val === '') continue;

            $existing = $transModel->where('menu_id', $menuId)->where('lang', $l['code'])->first();
            if ($existing) {
                $transModel->update($existing['id'], ['label' => $val]);
            } else {
                $transModel->insert(['menu_id' => $menuId, 'lang' => $l['code'], 'label' => $val]);
            }
        }
    }

    /* -------------------------------------------------
     * VER VERSIÓN
     * ------------------------------------------------*/
    // Devuelve el HTML del diff (para el modal)
    public function viewRevision($revisionId)
    {
        $revModel = new \App\Models\PageRevisionModel();
        $revision = $revModel->find($revisionId);

        if (!$revision) {
            return $this->response->setStatusCode(404)->setBody('<div class="alert alert-danger">Revisión no encontrada.</div>');
        }

        $page = (new \App\Models\PageModel())->find($revision['page_id']);
        $user = $revision['user_id']
            ? (new \App\Models\UserModel())->find($revision['user_id'])
            : null;

        $html = view('admin/pages/revision_diff', [
            'revision' => $revision,
            'page'     => $page,
            'user'     => $user,
            'diff'     => \App\Libraries\Diff::words(
                $revision['content'] ?? '',
                $page['content'] ?? ''
            ),
        ]);

        return $this->response->setBody($html);
    }

    /* -------------------------------------------------
     * ELIMINAR
     * ------------------------------------------------*/
    public function delete($id)
    {
        $pageModel = new PageModel();
        $page = $pageModel->find($id);

        if (!$page) {
            return redirect()->to('/admin/pages')->with('error', 'Página no encontrada.');
        }

        $menuItem = (new MenuModel())->where('page_id', $id)->first();
        if ($menuItem) {
            (new \App\Models\MenuTranslationModel())->where('menu_id', $menuItem['id'])->delete();
            (new MenuModel())->delete($menuItem['id']);
        }

        $pageModel->delete($id);
        return redirect()->to('/admin/pages')->with('success', 'Página eliminada correctamente.');
    }

    /* -------------------------------------------------------
     * AJAX: devuelve los campos de una plantilla en JSON
     * -----------------------------------------------------*/
    public function getTemplateFields($templateId)
    {
        $templateModel = new TemplateModel();
        $template = $templateModel->find($templateId);

        if (!$template) {
            return $this->response->setJSON(['fields' => []]);
        }

        $fields = json_decode($template['fields'] ?? '[]', true) ?: [];
        return $this->response->setJSON(['fields' => $fields]);
    }

    /* -------------------------------------------------
     * MÉTODOS PRIVADOS
     * ------------------------------------------------*/
    // private function generateSlug(string $text): string
    // {
    //     $text = strtolower($text);
    //     $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text); // quitar tildes
    //     $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    //     $text = preg_replace('/[\s-]+/', '-', $text);
    //     return trim($text, '-');
    // }

    private function generateSlug(string $text): string
    {
        return slugify($text);
    }

    private function ensureUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $pageModel = new PageModel();
        $original = $slug;
        $counter  = 1;

        while (true) {
            $builder = $pageModel->where('slug', $slug);
            if ($excludeId) {
                $builder->where('id !=', $excludeId);
            }
            if (!$builder->first()) {
                break;
            }
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }

    private function syncMenu(int $pageId, string $title, string $slug): void
    {
        $menuModel = new MenuModel();
        $exists = $menuModel->where('page_id', $pageId)->first();

        $icon = null;
        $file = $this->request->getFile('menu_icon');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/menu-icons', $newName);
            $icon = 'uploads/menu-icons/' . $newName;
        }

        $menuData = [
            'page_id'    => $pageId,
            'label'      => $this->request->getPost('menu_label') ?: $title,
            'url'        => null,
            'icon'       => $icon,
            'parent_id'  => $this->request->getPost('menu_parent_id') ?: null,
            'sort_order' => (int) ($this->request->getPost('menu_sort_order') ?: 0),
            'target'     => '_self',
            'active' => $this->request->getPost('status') === 'published' ? 1 : 0,
        ];

        if ($exists) {
            // Si no subió nueva imagen, conservar la anterior
            if (empty($icon) && !empty($exists['icon'])) {
                $menuData['icon'] = $exists['icon'];
            }
            $menuModel->update($exists['id'], $menuData);
        } else {
            $menuModel->insert($menuData);
        }
    }
}
