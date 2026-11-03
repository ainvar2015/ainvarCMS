<?php

namespace App\Controllers\Admin;

use App\Models\TemplateModel;
use App\Models\PageModel;

class Templates extends BaseController
{
    protected $helpers = ['url', 'form', 'html'];

    public function index()
    {
        $model = new TemplateModel();
        return $this->render('templates/index', [
            'title'     => 'Plantillas',
            'templates' => $model->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        return $this->render('templates/form', [
            'title'      => 'Nueva Plantilla',
            'template'   => null,
            'fields'     => [],
            'action'     => base_url('admin/templates/store'),
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'slug' => 'required|min_length[2]|max_length[100]|is_unique[templates.slug]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fields = $this->buildFieldsFromPost();

        $model = new TemplateModel();
        $model->insert([
            'name'        => $this->request->getPost('name'),
            'slug'        => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'fields'      => $fields,
            'active'      => $this->request->getPost('active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/templates')->with('success', 'Plantilla creada.');
    }

    public function edit($id)
    {
        $model = new TemplateModel();
        $tpl = $model->find($id);

        if (!$tpl) {
            return redirect()->to('/admin/templates')->with('error', 'Plantilla no encontrada.');
        }

        $fields = json_decode($tpl['fields'] ?? '[]', true) ?: [];

        return $this->render('templates/form', [
            'title'      => 'Editar Plantilla',
            'template'   => $tpl,
            'fields'     => $fields,
            'action'     => base_url('admin/templates/update/' . $id),
        ]);
    }

    public function update($id)
    {
        $model = new TemplateModel();
        $tpl = $model->find($id);

        if (!$tpl) {
            return redirect()->to('/admin/templates')->with('error', 'Plantilla no encontrada.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'slug' => "required|min_length[2]|max_length[100]|is_unique[templates.slug,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fields = $this->buildFieldsFromPost();

        $model->update($id, [
            'name'        => $this->request->getPost('name'),
            'slug'        => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'fields'      => $fields,
            'active'      => $this->request->getPost('active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/templates')->with('success', 'Plantilla actualizada.');
    }

    public function delete($id)
    {
        $model = new TemplateModel();
        $tpl = $model->find($id);

        if (!$tpl) {
            return redirect()->to('/admin/templates')->with('error', 'Plantilla no encontrada.');
        }

        // Verificar que no haya páginas usando esta plantilla
        $pageModel = new PageModel();
        $inUse = $pageModel->where('template_id', $id)->countAllResults();

        if ($inUse > 0) {
            return redirect()->to('/admin/templates')->with('error', 'No se puede eliminar: hay ' . $inUse . ' página(s) usando esta plantilla.');
        }

        $model->delete($id);
        return redirect()->to('/admin/templates')->with('success', 'Plantilla eliminada.');
    }

    /**
     * Construye el JSON de campos desde el POST
     */
    private function buildFieldsFromPost(): string
    {
        $names  = $this->request->getPost('field_name')  ?: [];
        $labels = $this->request->getPost('field_label') ?: [];
        $types  = $this->request->getPost('field_type')  ?: [];

        $fields = [];
        $count  = count($names);

        for ($i = 0; $i < $count; $i++) {
            $name = trim($names[$i] ?? '');
            if ($name === '') continue;

            $fields[] = [
                'name'  => $name,
                'label' => trim($labels[$i] ?? $name),
                'type'  => in_array($types[$i], ['text','email','textarea','number','url','image']) 
                           ? $types[$i] 
                           : 'text',
            ];
        }

        return json_encode($fields, JSON_UNESCAPED_UNICODE);
    }
}