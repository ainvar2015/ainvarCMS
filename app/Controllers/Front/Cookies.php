<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class Cookies extends BaseController
{
    public function save()
    {
        $categories = (array) $this->request->getPost('categories');
        $allowed    = ['analytics', 'marketing'];   // las necesarias no se piden: van siempre
        $categories = array_values(array_intersect($categories, $allowed));

        try {
            (new \App\Models\CookieConsentModel())->insert([
                'categories' => json_encode($categories, JSON_UNESCAPED_UNICODE),
                'ip'         => $this->request->getIPAddress(),
                'user_agent' => substr((string) $this->request->getUserAgent(), 255 * -1),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[cookies] consent: ' . $e->getMessage());
        }

        // Cookie 1 año (la lee el filtro para no volver a mostrar el banner)
        $this->response->setCookie('cookie_consent', json_encode($categories), 60 * 60 * 24 * 365, httponly: false, secure: $this->request->isSecure(), samesite: 'Lax');
        return $this->response->setJSON(['ok' => true, 'categories' => $categories]);
    }
}
