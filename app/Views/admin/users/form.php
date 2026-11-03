<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2"><?= $user ? 'Editar usuario' : 'Nuevo usuario' ?></h1>
    <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session('errors') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= $user ? base_url('admin/users/update/' . $user['id']) : base_url('admin/users/store') ?>"
    method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-cuenta-btn" data-bs-toggle="tab" data-bs-target="#tab-cuenta"
                type="button" role="tab">
                <i class="bi bi-person-badge"></i> Cuenta
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-perfil-btn" data-bs-toggle="tab" data-bs-target="#tab-perfil"
                type="button" role="tab">
                <i class="bi bi-person-vcard"></i> Perfil
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ============ PESTAÑA: CUENTA ============ -->
        <div class="tab-pane fade show active" id="tab-cuenta" role="tabpanel">
            <div class="row g-2">
                <div class="col-lg-6">
                    <!-- Foto de perfil -->
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-primary text-start border-bottom pb-2 mb-3">
                                <i class="bi bi-image"></i> Foto de Perfil
                            </h6>
                            <div class="mb-2">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?= base_url($user['avatar']) ?>" alt="Avatar"
                                        class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                                <?php else: ?>
                                    <i class="bi bi-person-circle text-muted" style="font-size:7rem;"></i>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                            <label for="avatarInput" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-upload"></i> Cambiar foto
                            </label>
                            <div class="form-text">Formatos: JPG, PNG, GIF. Máx: 2MB</div>
                        </div>
                    </div>
                    <div class="row g-2 pt-2">
                        <div class="col-lg-6">
                            <!-- PIN de desbloqueo -->
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <?php if (session()->getFlashdata('new_pin')): ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <strong>Nuevo PIN: <?= esc(session()->getFlashdata('new_pin')) ?></strong>
                                            — Anótalo ahora, solo se muestra esta vez.
                                        </div>
                                    <?php endif; ?>
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="bi bi-grid-3x3-gap"></i> PIN de Desbloqueo Rápido
                                    </h6>
                                    <p class="text-muted small">
                                        Usa un PIN de 4 dígitos para desbloquear la pantalla más rápidamente.
                                    </p>

                                    <?php if ($user): ?>
                                        <p>
                                            PIN actual:
                                            <?php if (!empty($user['pin'])): ?>
                                                <span class="badge bg-success">Establecido</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sin PIN (se aceptará la contraseña)</span>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">PIN (4 dígitos)</label>
                                        <div class="input-group">
                                            <input type="password" name="pin" id="pinField" class="form-control"
                                                maxlength="4" inputmode="numeric" pattern="\d{4}"
                                                autocomplete="off">
                                            <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('pinField', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            <?= $user ? 'Deja vacío para no cambiarlo.' : 'Opcional: puedes dejarlo vacío y establecerlo después.' ?>
                                            4 dígitos numéricos.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Confirmar PIN</label>
                                        <input type="password" name="pin_confirm" class="form-control"
                                            maxlength="4" inputmode="numeric" autocomplete="off">
                                    </div>

                                    <?php if ($user): ?>
                                        <button type="submit" form="formGeneratePin" class="btn btn-outline-warning"
                                            onclick="return confirm('¿Generar un nuevo PIN? El anterior dejará de funcionar.')">
                                            <i class="bi bi-arrow-repeat"></i> Generar nuevo PIN
                                        </button>
                                    <?php endif; ?>
                                </div>

                            </div>

                        </div>
                        <!-- Preferencias de Privacidad -->
                        <div class="col-lg-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-shield-lock"></i> Preferencias de Privacidad
                                    </h6>

                                    <div class="form-check form-switch mb-2">
                                        <input type="checkbox" name="show_email" class="form-check-input" value="1" id="chkShowEmail"
                                            <?= old('show_email', $user['show_email'] ?? 0) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="chkShowEmail">Mostrar email públicamente</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input type="checkbox" name="show_name" class="form-check-input" value="1" id="chkShowName"
                                            <?= old('show_name', $user['show_name'] ?? 0) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="chkShowName">Mostrar nombre real públicamente</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="allow_contact" class="form-check-input" value="1" id="chkAllowContact"
                                            <?= old('allow_contact', $user['allow_contact'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="chkAllowContact">Permitir contacto</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <!-- Información de Cuenta -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-key"></i> Acceso
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Login <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="username" class="form-control"
                                        value="<?= esc(old('username', $user['username'] ?? '')) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control"
                                        value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Contraseña <?= $user ? '<span class="text-muted fw-normal">(vacía = no cambiar)</span>' : '<span class="text-danger">*</span>' ?>
                                </label>
                                <div class="input-group">
                                    <input type="password" name="password" id="pwdField" class="form-control"
                                        <?= $user ? '' : 'required' ?> minlength="8" autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('pwdField', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <?php if (!$user): ?>
                                    <div class="form-text">Mínimo 8 caracteres.</div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Confirmar contraseña <?= $user ? '<span class="text-muted fw-normal">(vacía = no cambiar)</span>' : '<span class="text-danger">*</span>' ?></label>
                                <input type="password" name="password_confirm" class="form-control"
                                    <?= $user ? '' : 'required' ?> minlength="8" autocomplete="new-password">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nivel de Acceso <span class="text-danger">*</span></label>
                                <?php $role = old('role', $user['role'] ?? 'editor'); ?>
                                <select name="role" class="form-select">
                                    <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor</option>
                                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    <option value="superadmin" <?= $role === 'superadmin' ? 'selected' : '' ?>>Superadministrador</option>
                                </select>
                            </div>

                            <div class="form-check form-switch">
                                <input type="checkbox" name="active" class="form-check-input" value="1" id="chkActive"
                                    <?= old('active', $user['active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="chkActive">Usuario Activo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ PESTAÑA: PERFIL ============ -->
        <div class="tab-pane fade" id="tab-perfil" role="tabpanel">
            <div class="row g-2">
                <div class="col-lg-6">
                    <!-- Información Personal -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-person-vcard"></i> Información Personal
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombre</label>
                                    <input type="text" name="first_name" class="form-control"
                                        value="<?= esc(old('first_name', $user['first_name'] ?? '')) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Apellidos</label>
                                    <input type="text" name="last_name" class="form-control"
                                        value="<?= esc(old('last_name', $user['last_name'] ?? '')) ?>">
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone" class="form-control"
                                        value="<?= esc(old('phone', $user['phone'] ?? '')) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <!-- Dirección -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-geo-alt"></i> Dirección
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Dirección</label>
                                <input type="text" name="address" class="form-control"
                                    value="<?= esc(old('address', $user['address'] ?? '')) ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Población</label>
                                    <input type="text" name="city" class="form-control"
                                        value="<?= esc(old('city', $user['city'] ?? '')) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Provincia</label>
                                    <input type="text" name="province" class="form-control"
                                        value="<?= esc(old('province', $user['province'] ?? '')) ?>">
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Código Postal</label>
                                <input type="text" name="zip" class="form-control"
                                    value="<?= esc(old('zip', $user['zip'] ?? '')) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <!-- Descripción -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-chat-left-text"></i> Descripción
                            </h6>
                            <textarea name="bio" class="form-control" rows="4"
                                placeholder="Descripción del usuario..."><?= esc(old('bio', $user['bio'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Guardar usuario
        </button>
    </div>
</form>

<?php if ($user): ?>
    <!-- Formulario independiente para generar PIN (fuera del form principal) -->
    <form id="formGeneratePin" action="<?= base_url('admin/users/generate-pin/' . $user['id']) ?>" method="post">
        <?= csrf_field() ?>
    </form>
<?php endif; ?>

<script>
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    }
</script>
<?php $this->endSection() ?>