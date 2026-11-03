<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class Users extends BaseController
{
    /** Jerarquía: un rol solo puede gestionar roles de nivel inferior */
    private const ROLE_LEVEL = [
        'superadmin' => 3,
        'admin'      => 2,
        'editor'     => 1,
    ];

    public function index()
    {
        $model  = new UserModel();
        $search = trim((string) $this->request->getGet('q'));

        if ($search !== '') {
            $model->groupStart()
                ->like('username', $search)
                ->orLike('email', $search)
                ->orLike('first_name', $search)
                ->orLike('last_name', $search)
                ->groupEnd();
        }

        return $this->render('users/index', [
            'title'  => 'Usuarios',
            'users'  => $model->orderBy('id', 'ASC')->paginate(20),
            'pager'  => $model->pager,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return $this->render('users/form', [
            'title' => 'Nuevo usuario',
            'user'  => null,
        ]);
    }

    public function store()
    {
        $assignable = implode(',', $this->assignableRoles());

        if (!$this->validate([
            'username'         => 'required|min_length[3]|is_unique[users.username]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'role'             => "required|in_list[{$assignable}]",
            'pin'              => 'permit_empty|numeric|exact_length[4]',
            'pin_confirm'      => 'matches[pin]',
            'avatar'           => 'permit_empty|uploaded[avatar]|max_size[avatar,2048]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/gif]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $avatarPath = $this->saveAvatar();

        $result = (new UserModel())->insert([
            'username'      => $this->request->getPost('username'),
            'first_name'    => $this->request->getPost('first_name'),
            'last_name'     => $this->request->getPost('last_name'),
            'email'         => $this->request->getPost('email'),
            'password'      => $this->request->getPost('password'), // hashea el modelo
            'role'          => $this->request->getPost('role'),
            'active'        => $this->request->getPost('active') ? 1 : 0,
            'phone'         => $this->request->getPost('phone') ?: null,
            'address'       => $this->request->getPost('address') ?: null,
            'city'          => $this->request->getPost('city') ?: null,
            'province'      => $this->request->getPost('province') ?: null,
            'zip'           => $this->request->getPost('zip') ?: null,
            'bio'           => $this->request->getPost('bio') ?: null,
            'avatar'        => $avatarPath,
            'show_email'    => $this->request->getPost('show_email') ? 1 : 0,
            'show_name'     => $this->request->getPost('show_name') ? 1 : 0,
            'allow_contact' => $this->request->getPost('allow_contact') ? 1 : 0,
            'pin'           => $this->request->getPost('pin') ?: null, // hashea el modelo
        ]);

        if ($result === false) {
            $this->removeAvatarFile($avatarPath);
            return redirect()->back()->withInput()->with('error', 'No se pudo crear el usuario.');
        }

        return redirect()->to('/admin/users')->with('success', 'Usuario creado.');
    }

    public function edit($id)
    {
        $model = new UserModel();
        $user  = $model->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado.');
        }

        $self = $this->isSelf($user);
        if (!$self && !$this->canManage($user)) {
            return redirect()->to('/admin/users')->with('error', 'No tienes permiso para editar a ese usuario.');
        }

        return $this->render('users/form', [
            'title' => 'Editar usuario',
            'user'  => $user,
        ]);
    }

    public function update($id)
    {
        $model = new UserModel();
        $user  = $model->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado.');
        }

        $self = $this->isSelf($user);
        if (!$self && !$this->canManage($user)) {
            return redirect()->to('/admin/users')->with('error', 'No tienes permiso para editar a ese usuario.');
        }

        $assignable = implode(',', $this->assignableRoles());

        $rules = [
            'username'         => "required|min_length[3]|is_unique[users.username,id,{$id}]",
            'email'            => "required|valid_email|is_unique[users.email,id,{$id}]",
            'password'         => 'permit_empty|min_length[8]',
            'password_confirm' => 'matches[password]',
            'pin'              => 'permit_empty|numeric|exact_length[4]',
            'pin_confirm'      => 'matches[pin]',
            'avatar'           => 'permit_empty|uploaded[avatar]|max_size[avatar,2048]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/gif]',
        ];
        if (!$self) {
            $rules['role'] = "required|in_list[{$assignable}]";
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username'      => $this->request->getPost('username'),
            'first_name'    => $this->request->getPost('first_name'),
            'last_name'     => $this->request->getPost('last_name'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone') ?: null,
            'address'       => $this->request->getPost('address') ?: null,
            'city'          => $this->request->getPost('city') ?: null,
            'province'      => $this->request->getPost('province') ?: null,
            'zip'           => $this->request->getPost('zip') ?: null,
            'bio'           => $this->request->getPost('bio') ?: null,
            'show_email'    => $this->request->getPost('show_email') ? 1 : 0,
            'show_name'     => $this->request->getPost('show_name') ? 1 : 0,
            'allow_contact' => $this->request->getPost('allow_contact') ? 1 : 0,
        ];

        // Rol y "activo" no se tocan en la propia ficha
        if (!$self) {
            $data['role']   = $this->request->getPost('role');
            $data['active'] = $this->request->getPost('active') ? 1 : 0;
        }

        $avatarPath = $this->saveAvatar();
        if ($avatarPath) {
            $data['avatar'] = $avatarPath;
        }

        $pwd = $this->request->getPost('password');
        if (!empty($pwd)) {
            $data['password']        = $pwd; // hashea el modelo
            $data['failed_attempts'] = 0;
            $data['locked_until']    = null;
        }

        $newPin = $this->request->getPost('pin');
        if (!empty($newPin)) {
            $data['pin'] = $newPin; // hashea el modelo
        }

        if ($model->update($id, $data) === false) {
            $this->removeAvatarFile($avatarPath);
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar el usuario.');
        }

        // El avatar antiguo se borra solo si el update funcionó
        if ($avatarPath && !empty($user['avatar'])) {
            $this->removeAvatarFile($user['avatar']);
        }

        return redirect()->to('/admin/users')->with('success', 'Usuario actualizado.');
    }

    public function toggle($id)
    {
        $model = new UserModel();
        $user  = $model->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado.');
        }
        if ($this->isSelf($user)) {
            return redirect()->to('/admin/users')->with('error', 'No puedes desactivarte a ti mismo.');
        }
        if (!$this->canManage($user)) {
            return redirect()->to('/admin/users')->with('error', 'No tienes permiso para gestionar a ese usuario.');
        }
        if ($user['active'] && $this->isLastActiveOfRole($model, $user)) {
            return redirect()->to('/admin/users')->with('error', 'No puedes desactivar al último "' . $user['role'] . '" activo.');
        }

        $model->update($id, ['active' => $user['active'] ? 0 : 1]);

        return redirect()->to('/admin/users')->with('success', 'Estado actualizado.');
    }

    public function delete($id)
    {
        $model = new UserModel();
        $user  = $model->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado.');
        }
        if ($this->isSelf($user)) {
            return redirect()->to('/admin/users')->with('error', 'No puedes borrar tu propio usuario.');
        }
        if (!$this->canManage($user)) {
            return redirect()->to('/admin/users')->with('error', 'No tienes permiso para gestionar a ese usuario.');
        }
        if ($this->isLastActiveOfRole($model, $user)) {
            return redirect()->to('/admin/users')->with('error', 'No puedes borrar al último "' . $user['role'] . '" activo.');
        }

        $model->delete($id);
        $this->removeAvatarFile($user['avatar'] ?? null);

        return redirect()->to('/admin/users')->with('success', 'Usuario eliminado.');
    }

    public function generatePin($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/admin/users');
        }

        $model = new UserModel();
        $user  = $model->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado.');
        }
        if (!$this->canManage($user)) {
            return redirect()->to('/admin/users')->with('error', 'No tienes permiso para gestionar a ese usuario.');
        }

        $pin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $model->update($id, ['pin' => $pin]); // hashea el modelo

        return redirect()->to('/admin/users/edit/' . $id)->with('new_pin', $pin);
    }

    /* ------------------------------------------------------------------
     * Privados
     * ----------------------------------------------------------------*/

    private function roleLevel(?string $role): int
    {
        return self::ROLE_LEVEL[$role] ?? 0;
    }

    private function isSelf(array $user): bool
    {
        return (int) $user['id'] === (int) $this->currentUserId();
    }

    /** Uno mismo siempre; para otros, el rol del actor debe ser superior */
    private function canManage(array $user): bool
    {
        if ($this->isSelf($user)) {
            return true;
        }
        return $this->roleLevel($this->currentRole()) > $this->roleLevel($user['role']);
    }

    /** Roles que el actor puede asignar: solo inferiores al suyo */
    private function assignableRoles(): array
    {
        $level = $this->roleLevel($this->currentRole());
        return array_keys(array_filter(self::ROLE_LEVEL, static fn($l) => $l < $level));
    }

    private function isLastActiveOfRole(UserModel $model, array $user): bool
    {
        return $model->where('role', $user['role'])
            ->where('active', 1)
            ->countAllResults() <= 1;
    }

    private function saveAvatar(): ?string
    {
        $avatar = $this->request->getFile('avatar');
        if (!$avatar || !$avatar->isValid() || $avatar->hasMoved()) {
            return null;
        }

        $newName = $avatar->getRandomName();
        $avatar->move(FCPATH . 'uploads/avatars', $newName);

        return 'uploads/avatars/' . $newName;
    }

    private function removeAvatarFile(?string $avatar): void
    {
        // El prefijo evita borrar rutar arbitrarias si la BD se altera
        if (!empty($avatar) && strpos($avatar, 'uploads/avatars/') === 0 && is_file(FCPATH . $avatar)) {
            unlink(FCPATH . $avatar);
        }
    }

    /** AJUSTA estas dos a tu sistema de autenticación */
    private function currentUserId()
    {
        return session()->get('user_id');
    }

    private function currentRole(): string
    {
        return session()->get('role') ?? 'editor';
    }
}
