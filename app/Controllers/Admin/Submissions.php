<?php

namespace App\Controllers\Admin;

use App\Models\FormSubmissionModel;
use App\Models\PageModel;

class Submissions extends BaseController
{
    public function index()
    {
        $model = new FormSubmissionModel();
        $submissions = $model->orderBy('created_at', 'DESC')->findAll(50);

        // Decodificar JSON para mostrar
        foreach ($submissions as &$s) {
            $s['data_decoded'] = json_decode($s['data'], true) ?: [];
        }

        return $this->render('submissions/index', [
            'title'       => 'Envíos de formularios',
            'submissions' => $submissions,
        ]);
    }

    public function markRead($id)
    {
        $model = new FormSubmissionModel();
        $model->update($id, ['read' => 1]);
        return redirect()->to('/admin/submissions')->with('success', 'Marcado como leído.');
    }

    public function delete($id)
    {
        $model = new FormSubmissionModel();
        $model->delete($id);
        return redirect()->to('/admin/submissions')->with('success', 'Envío eliminado.');
    }
}