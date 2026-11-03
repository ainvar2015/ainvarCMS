<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla bloqueada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #2c3e50, #4ca1af); min-height: 100vh; display: flex; align-items: center; }
        .lock-card { max-width: 380px; width: 100%; margin: auto; }
        .pin-dot { width: 16px; height: 16px; border-radius: 50%; border: 2px solid #6c757d; display: inline-block; }
        .pin-dot.filled { background: #0d6efd; border-color: #0d6efd; }
        .pin-btn { font-size: 1.4rem; font-weight: 600; }
    </style>
</head>
<body>
<div class="lock-card">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4 text-center">
            <div class="mb-3">
                <i class="bi bi-person-circle" style="font-size:4rem;color:#6c757d;"></i>
            </div>
            <h5 class="mb-1">Sesión bloqueada</h5>
            <p class="text-muted mb-3"><?= esc($username ?? '') ?></p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <!-- Indicador de PIN -->
            <div class="mb-3">
                <span class="pin-dot" id="dot0"></span>
                <span class="pin-dot" id="dot1"></span>
                <span class="pin-dot" id="dot2"></span>
                <span class="pin-dot" id="dot3"></span>
            </div>

            <form id="pinForm" action="<?= base_url('admin/unlock') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="pin" id="pinInput" maxlength="4">
            </form>

            <!-- Teclado numérico -->
            <div class="d-grid gap-2" style="grid-template-columns:repeat(3,1fr);">
                <?php foreach ([1,2,3,4,5,6,7,8,9] as $n): ?>
                    <button type="button" class="btn btn-outline-secondary pin-btn" data-digit="<?= $n ?>"><?= $n ?></button>
                <?php endforeach; ?>
                <button type="button" class="btn btn-outline-danger pin-btn" id="pinClear"><i class="bi bi-x-lg"></i></button>
                <button type="button" class="btn btn-outline-secondary pin-btn" data-digit="0">0</button>
                <button type="submit" form="pinForm" class="btn btn-primary pin-btn" id="pinOk" disabled><i class="bi bi-check-lg"></i></button>
            </div>

            <a href="<?= base_url('logout') ?>" class="d-block mt-3 small text-muted">
                Cerrar sesión e identificarse de nuevo
            </a>
        </div>
    </div>
</div>

<script>
    const input = document.getElementById('pinInput');
    const okBtn = document.getElementById('pinOk');
    const dots  = [0, 1, 2, 3].map(i => document.getElementById('dot' + i));

    function refresh() {
        dots.forEach((d, i) => d.classList.toggle('filled', i < input.value.length));
        okBtn.disabled = input.value.length !== 4;
        if (input.value.length === 4) {
            setTimeout(() => document.getElementById('pinForm').submit(), 150);
        }
    }

    function addDigit(digit) {
        if (input.value.length < 4) {
            input.value += digit;
            refresh();
        }
    }

    function removeDigit() {
        input.value = input.value.slice(0, -1);
        refresh();
    }

    // Teclado en pantalla
    document.querySelectorAll('[data-digit]').forEach(btn => {
        btn.addEventListener('click', () => addDigit(btn.dataset.digit));
    });
    document.getElementById('pinClear').addEventListener('click', removeDigit);

    // Teclado físico
    document.addEventListener('keydown', (e) => {
        if (e.key >= '0' && e.key <= '9') {
            addDigit(e.key);
            e.preventDefault();
        } else if (e.key === 'Backspace' || e.key === 'Delete') {
            removeDigit();
            e.preventDefault();
        } else if (e.key === 'Enter' && input.value.length === 4) {
            document.getElementById('pinForm').submit();
        }
    });
</script>
</body>
</html>