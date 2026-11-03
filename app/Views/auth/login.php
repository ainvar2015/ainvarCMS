<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - CMS</title>
    <script>
        // Respeta la preferencia elegida en el admin (misma clave, mismo origen) o el tema del SO
        (function() {
            try {
                var t = localStorage.getItem('admin-theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }

        .login-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-card .logo i {
            font-size: 48px;
            color: #667eea;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            color: #667eea;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            color: #fff;
        }

        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
        }

        /* === Modo oscuro === */
        [data-bs-theme="dark"] body {
            background: linear-gradient(135deg, #2b3350 0%, #3a2b4d 100%);
        }

        [data-bs-theme="dark"] .login-card {
            background: #212529;
            color: #dee2e6;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        }

        [data-bs-theme="dark"] .login-card .logo i {
            color: #8ea2ff;
        }

        [data-bs-theme="dark"] .form-floating>.form-control {
            background: #2b3035;
            border-color: #495057;
            color: #dee2e6;
        }

        [data-bs-theme="dark"] .form-floating>label {
            color: #adb5bd;
        }

        [data-bs-theme="dark"] .form-floating>.form-control:focus~label,
        [data-bs-theme="dark"] .form-floating>.form-control:not(:placeholder-shown)~label {
            color: #8ea2ff;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="logo">
            <i class="bi bi-shield-lock"></i>
            <h4 class="mt-2 mb-0">CMS Admin</h4>
            <small class="text-muted">Inicia sesión para continuar</small>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('authenticate') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-floating mb-3">
                <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                    id="username" name="username" placeholder="Usuario"
                    value="<?= old('username') ?>" autofocus>
                <label for="username"><i class="bi bi-person"></i> Usuario o Email</label>
                <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback"><?= $errors['username'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                    id="password" name="password" placeholder="Contraseña">
                <label for="password"><i class="bi bi-key"></i> Contraseña</label>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">¿Olvidaste tu contraseña? Contacta al administrador.</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>