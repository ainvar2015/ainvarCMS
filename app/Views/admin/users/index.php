<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Usuarios</h1>
    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nuevo usuario
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form class="mb-3" method="get" action="<?= base_url('admin/users') ?>">
            <div class="input-group" style="max-width:400px;">
                <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o email"
                    value="<?= esc($search ?? '') ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <?php if (!empty($u['avatar'])): ?>
                                    <img src="<?= base_url($u['avatar']) ?>" class="rounded-circle me-1"
                                        style="width:28px;height:28px;object-fit:cover;">
                                <?php endif; ?>
                                <?= esc($u['username']) ?>
                            </td>
                            <td><?= esc($u['email']) ?></td>
                            <td>
                                <?php
                                $roleMeta = [
                                    'superadmin' => ['label' => 'Superadmin', 'color' => 'danger'],
                                    'admin'      => ['label' => 'Admin',      'color' => 'primary'],
                                    'editor'     => ['label' => 'Editor',     'color' => 'secondary'],
                                ];
                                $role = $u['role'] ?? 'editor';
                                $meta = $roleMeta[$role] ?? ['label' => ucfirst($role), 'color' => 'dark'];
                                ?>
                                <span class="badge bg-<?= $meta['color'] ?>"><?= esc($meta['label']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $u['active'] ? 'success' : 'danger' ?>">
                                    <?= $u['active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= base_url('admin/users/toggle/' . $u['id']) ?>" class="btn btn-sm btn-outline-warning"
                                    onclick="return confirm('¿Cambiar el estado de este usuario?')">
                                    <i class="bi bi-power"></i>
                                </a>
                                <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Eliminar este usuario? No se puede deshacer.')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay usuarios</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager->getPageCount() > 1): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->only(['q'])->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $this->endSection() ?>