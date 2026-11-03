<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Maintenance implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        log_message('debug', '[maint] path=[' . trim($request->getUri()->getPath(), '/') . '] mode=' . (int) (new \App\Models\ConfigModel())->get('maintenance_mode', 0));
        if (!(int) (new \App\Models\ConfigModel())->get('maintenance_mode', 0)) {
            return null; // modo desactivado: nada que hacer
        }

        $path  = trim($request->getUri()->getPath(), '/');
        $first = explode('/', $path)[0];

        // Rutas que SIEMPRE pasan: sin esto no podrías entrar al admin a desactivar el modo,
        // ni autenticarte (POST a /authenticate), ni cerrar sesión.
        if (in_array($first, ['admin', 'login', 'authenticate', 'logout'], true)) {
            return null;
        }

        // Admins y superadmins ven el front normal mientras trabajan (misma jerarquía que Auth)
        $session = session();
        if ($session->get('logged_in')) {
            $levels = ['editor' => 1, 'admin' => 2, 'superadmin' => 3];
            if (($levels[$session->get('role')] ?? 0) >= $levels['admin']) {
                return null;
            }
        }

        // Resto del mundo: 503
        $response = service('response');
        $response->setStatusCode(503);
        $response->setHeader('Retry-After', '3600');
        $response->setBody(view('front/maintenance'));

        return $response;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}