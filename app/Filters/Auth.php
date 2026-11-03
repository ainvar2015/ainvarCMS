<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    // Rutas exentas del chequeo de inactividad
    private array $exempt = ['admin/lock', 'admin/unlock'];

    public function before(RequestInterface $request, $arguments = null)
    {
        helper('auth');
        
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Rol requerido con jerarquía
        if (!empty($arguments) && role_level($session->get('role')) < role_level($arguments[0])) {
            return redirect()->to('/admin')->with('error', 'No tienes permisos para acceder a esa sección.');
        }

        // === BLOQUEO POR INACTIVIDAD ===
        $path = trim($request->getUri()->getPath(), '/');
        if (!in_array($path, $this->exempt)) {
            $last      = (int) $session->get('last_activity');
            $timeout   = (int) (new \App\Models\ConfigModel())->get('lock_timeout', 15) * 60;
            if ($last && (time() - $last) > $timeout) {
                $session->set('lock_return', current_url());
                return redirect()->to('/admin/lock');
            }
        }
        $session->set('last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
