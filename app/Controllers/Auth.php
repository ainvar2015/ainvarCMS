<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function login()
    {
        // Si ya está logueado, redirigir al admin
        if (session()->get('logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('auth/login', [
            'title'      => 'Iniciar sesión',
            'validation' => $this->validator,
        ]);
    }

    public function authenticate()
    {
        $login    = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if (!$login || !$password) {
            return redirect()->back()->withInput()->with('error', 'Introduce usuario y contraseña.');
        }

        $maxAttempts = 5;   // intentos antes del bloqueo
        $lockMinutes = 15;  // duración del bloqueo

        $userModel = new \App\Models\UserModel();
        $user = $userModel->groupStart()
            ->where('username', $login)
            ->orWhere('email', $login)
            ->groupEnd()
            ->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Usuario o contraseña incorrectos.');
        }

        // Cuenta bloqueada temporalmente
        if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $mins = ceil((strtotime($user['locked_until']) - time()) / 60);
            return redirect()->back()->withInput()
                ->with('error', "Cuenta bloqueada por intentos fallidos. Inténtalo de nuevo en {$mins} minuto(s).");
        }

        // Contraseña incorrecta
        if (!password_verify($password, $user['password'])) {
            $attempts = (int) $user['failed_attempts'] + 1;

            if ($attempts >= $maxAttempts) {
                $userModel->update($user['id'], [
                    'failed_attempts' => 0,
                    'locked_until'    => date('Y-m-d H:i:s', time() + $lockMinutes * 60),
                ]);
                return redirect()->back()->withInput()
                    ->with('error', "Demasiados intentos fallidos. Cuenta bloqueada durante {$lockMinutes} minutos.");
            }

            $userModel->update($user['id'], ['failed_attempts' => $attempts]);
            $left = $maxAttempts - $attempts;
            return redirect()->back()->withInput()
                ->with('error', "Usuario o contraseña incorrectos. Te quedan {$left} intento(s).");
        }

        // Usuario desactivado
        if (!$user['active']) {
            return redirect()->back()->withInput()->with('error', 'Usuario desactivado. Contacta con el administrador.');
        }

        // Login correcto: resetear contadores y crear sesión
        $userModel->update($user['id'], ['failed_attempts' => 0, 'locked_until' => null]);

        $session = session();
        $session->regenerate();  // evita fijación de sesión

        $session->set([
            'logged_in'     => true,
            'user_id'       => $user['id'],
            'username'      => $user['username'],
            'role'          => $user['role'],
            'lock_timeout'  => (int) (new \App\Models\ConfigModel())->get('lock_timeout', 15),
            'last_activity' => time(),
        ]);

        return redirect()->to('/admin');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Sesión cerrada correctamente.');
    }
}
