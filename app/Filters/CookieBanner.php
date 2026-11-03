<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CookieBanner implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (stripos($response->getHeaderLine('Content-Type'), 'text/html') === false) return;

        $path  = trim($request->getUri()->getPath(), '/');
        $first = explode('/', $path)[0];
        if (in_array($first, ['admin', 'login', 'authenticate', 'logout', 'cookies'], true)) return;
        if ($request->isAJAX()) return;

        try {
            $configModel = new \App\Models\ConfigModel();
            $headScripts = trim((string) $configModel->get('cookies_head_scripts', ''));
            $active      = (int) $configModel->get('cookies_enabled', 0);
        } catch (\Throwable $e) {
            return;
        }

        $body = $response->getBody();

        // Scripts "apagados" en <head>
        if ($headScripts !== '' && stripos($body, '</head>') !== false) {
            $body = str_ireplace('</head>', $headScripts . "\n</head>", $body);
        }

        // Banner
        $consent = $_COOKIE['cookie_consent'] ?? null;
        if (!$active || $consent !== null) {
            if ($body !== $response->getBody()) $response->setBody($body);
            return;
        }

        $en    = (\current_lang() ?: 'es') === 'en';
        $text  = trim((string) $configModel->get($en ? 'cookies_banner_text_en'  : 'cookies_banner_text_es', ''));
        $title = trim((string) $configModel->get($en ? 'cookies_banner_title_en' : 'cookies_banner_title_es', ''));

        if ($text === '') {
            if ($body !== $response->getBody()) $response->setBody($body);
            return;
        }

        $slug = trim((string) $configModel->get('cookies_policy_slug', ''));

        $bannerHtml = view('front/cookie_banner', [
            'title'     => $title,
            'text'      => $text,
            'policyUrl' => $slug !== '' ? localized_url($slug) : '',
            'en'        => $en,
        ]);

        if (stripos($body, '</body>') !== false) {
            $response->setBody(str_ireplace('</body>', $bannerHtml . '</body>', $body));
        }
    }
}
