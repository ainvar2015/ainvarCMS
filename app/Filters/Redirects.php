<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Redirects implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Solo GET/HEAD (una redirección de un POST no tiene sentido)
        if (! in_array($request->getMethod(), ['get', 'head'], true)) {
            return null;
        }

        $path = trim($request->getUri()->getPath(), '/');

        // Nada que hacer en el panel, auth, ni en la raíz
        if ($path === '') return null;
        $first = explode('/', $path)[0];
        if (in_array($first, ['admin', 'login', 'authenticate', 'logout'], true)) return null;

        // Tabla presente y redirecciones activas
        try {
            $redirects = (new \App\Modules\Redirects\Models\RedirectModel())->getActive();
        } catch (\Throwable $e) {
            return null;   // tabla aún no migrada, etc.
        }

        foreach ($redirects as $r) {
            $from = trim($r['from_path'], '/');

            if (strcasecmp($from, $path) === 0) {
                (new \App\Modules\Redirects\Models\RedirectModel())->incrementHits((int) $r['id']);

                $to = trim($r['to_path']);
                $url = str_starts_with($to, 'http://') || str_starts_with($to, 'https://')
                    ? $to
                    : base_url(ltrim($to, '/'));

                return redirect()->to($url)->setStatusCode((int) $r['code']);
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}