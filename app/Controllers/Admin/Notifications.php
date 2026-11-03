<?php

namespace App\Controllers\Admin;

use App\Models\FormSubmissionModel;

class Notifications extends BaseController
{
    // JSON ligero para el polling del badge
    public function unread()
    {
        $unread = 0;
        try {
            $unread = (new FormSubmissionModel())->where('read', 0)->countAllResults();
        } catch (\Throwable $e) {
            // Tabla no disponible: devolver 0
        }

        return $this->response->setJSON(['unread' => $unread]);
    }
}