<?php

namespace App\Controllers\Admin;

use App\Models\ConfigModel;

class Settings extends BaseController
{
    public function index()
    {
        $config = new ConfigModel();

        return $this->render('settings/index', [
            'title'            => 'Ajustes',
            'site_name'        => $config->get('site_name', 'CMS Ainvar'),
            'site_language'    => $config->get('site_language', 'es'),
            'contact_email'    => $config->get('contact_email', ''),
            'site_meta_desc'   => $config->get('site_meta_desc', ''),
            'social_facebook'  => $config->get('social_facebook', ''),
            'social_instagram' => $config->get('social_instagram', ''),
            'social_bluesky'   => $config->get('social_bluesky', ''),
            'social_x'         => $config->get('social_x', ''),
            'social_youtube'   => $config->get('social_youtube', ''),
            // Empresa
            'company_name'     => $config->get('company_name', ''),
            'company_nif'      => $config->get('company_nif', ''),
            'company_address'  => $config->get('company_address', ''),
            'company_city'     => $config->get('company_city', ''),
            'company_province' => $config->get('company_province', ''),
            'company_zip'      => $config->get('company_zip', ''),
            'company_phone'    => $config->get('company_phone', ''),
            'company_email'    => $config->get('company_email', ''),
            // Bloqueo
            'lock_timeout'     => $config->get('lock_timeout', '15'),
            'home_page_id'     => $config->get('home_page_id', 0),
            'maintenance_mode' => $config->get('maintenance_mode', 0),
            // Cookies
            'cookies_enabled'         => $config->get('cookies_enabled', 1),
            'cookies_banner_title_es' => $config->get('cookies_banner_title_es', 'Cookies'),
            'cookies_banner_title_en' => $config->get('cookies_banner_title_en', 'Cookies'),
            'cookies_banner_text_es'  => $config->get('cookies_banner_text_es', 'Este sitio web utiliza cookies para mejorar la experiencia del usuario.'),
            'cookies_banner_text_en'  => $config->get('cookies_banner_text_en', 'This website uses cookies to enhance the user experience.'),
            'cookies_policy_slug'     => $config->get('cookies_policy_slug', ''),
            'cookies_banner_text' => $config->get('cookies_banner_text', ''),
            'cookies_policy_url'  => $config->get('cookies_policy_url', ''),
            'cookies_head_scripts' => $config->get('cookies_head_scripts', ''),

        ]);
    }

    public function save()
    {
        $config = new ConfigModel();

        $keys = [
            'site_name'        => 'string',
            'site_language'    => 'string',
            'home_page_id'     => 'int',
            'contact_email'    => 'string',
            'site_meta_desc'   => 'string',
            'social_facebook'  => 'string',
            'social_instagram' => 'string',
            'social_x'         => 'string',
            'social_youtube'   => 'string',
            'company_name'     => 'string',
            'company_nif'      => 'string',
            'company_address'  => 'string',
            'company_city'     => 'string',
            'company_province' => 'string',
            'company_zip'      => 'string',
            'company_phone'    => 'string',
            'company_email'    => 'string',
            'lock_timeout'     => 'string',   // minutos de inactividad
            'maintenance_mode' => 'int',      // 0 o 1
            'cookies_enabled'         => 'int',
            'cookies_banner_title_es' => 'string',
            'cookies_banner_title_en' => 'string',
            'cookies_banner_text_es'  => 'string',
            'cookies_banner_text_en'  => 'string',
            'cookies_policy_slug'     => 'string',
            'cookies_banner_text'     => 'string',
            'cookies_policy_url'      => 'string',
            'cookies_head_scripts'    => 'string',
        ];

        foreach ($keys as $key => $type) {
            $config->saveValue($key, $this->request->getPost($key), $type);
        }

        return redirect()->to('/admin/settings')->with('success', 'Ajustes guardados.');
    }
}
