<?php

namespace App\Controllers;

use App\Models\CookieConsentModel;

class CookieConsent extends BaseController
{
    public function save()
    {
        $json = $this->request->getJSON(true) ?? [];
        $analytics = !empty($json['analytics']) ? 1 : 0;
        $marketing = !empty($json['marketing']) ? 1 : 0;
        $token     = preg_match('/^[a-f0-9]{32}$/', $json['token'] ?? '') ? $json['token'] : bin2hex(random_bytes(16));

        (new CookieConsentModel())->insert([
            'token'      => $token,
            'necessary'  => 1,
            'analytics'  => $analytics,
            'marketing'  => $marketing,
            'language'   => function_exists('current_lang') ? current_lang() : 'es',
            'user_agent' => substr((string) $this->request->getUserAgent(), 0, 250),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $consent = [
            'token'     => $token,
            'necessary' => 1,
            'analytics' => $analytics,
            'marketing' => $marketing,
            'ts'        => time(),
        ];

        return $this->response->setJSON([
            'consent' => $consent,
            'csrf'    => csrf_hash(),   // <-- necesario para poder guardar cambios después
        ]);
    }
}
