<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class Lockscreen extends BaseController
{
    public function index()
    {
        // URL de retorno: solo rutas locales (evita open redirect)
        $return = $this->request->getGet('return');
        if ($return && str_starts_with($return, '/')) {
            session()->set('lock_return', $return);
        }

        return $this->response->setBody(view('admin/lockscreen/index', [
            'username' => session()->get('username'),
        ]));
    }

    public function unlock()
    {
        $pin = $this->request->getPost('pin');
        $session = session();
        $user = (new UserModel())->find($session->get('user_id'));

        if (!$user) {
            $session->destroy();
            return redirect()->to('/login');
        }

        // Verificar PIN; si no tiene, aceptar contraseña como alternativa
        $ok = false;
        if (!empty($user['pin']) && password_verify($pin, $user['pin'])) {
            $ok = true;
        } elseif (empty($user['pin']) && password_verify($pin, $user['password'])) {
            $ok = true;
        }

        if (!$ok) {
            return redirect()->to('/admin/lock')->with('error', 'PIN incorrecto.');
        }

        $session->set('last_activity', time());
        $return = $session->get('lock_return') ?: '/admin';
        $session->remove('lock_return');

        return redirect()->to($return);
    }
}
