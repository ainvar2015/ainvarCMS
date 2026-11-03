<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Copias de seguridad</h1>
    <form action="<?= base_url('admin/backups/create') ?>" method="post"
        onsubmit="return confirm('¿Crear copia ahora? Puede tardar unos segundos.')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-archive"></i> Crear copia ahora
        </button>
    </form>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php
        function humanSize(int $bytes): string
        {
            if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
            if ($bytes >= 1024)   return round($bytes / 1024, 1) . ' KB';
            return $bytes . ' B';
        }
        ?>

        <?php if (empty($backups)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size:3rem;"></i>
                <p class="mt-2">No hay copias de seguridad todavía.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Archivo</th>
                            <th>Tamaño</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $b): ?>
                            <tr>
                                <td><i class="bi bi-file-zip me-1"></i> <?= esc($b['file']) ?></td>
                                <td><?= humanSize($b['size']) ?></td>
                                <td><?= date('d/m/Y H:i:s', $b['date']) ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="openRestore('<?= esc($b['file']) ?>')">Restaurar</button>
                                    <a href="<?= base_url('admin/backups/download/' . $b['file']) ?>"
                                        class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-download"></i> Descargar
                                    </a>
                                    <form action="<?= base_url('admin/backups/delete/' . $b['file']) ?>" method="post" class="d-inline"
                                        onsubmit="return confirm('¿Eliminar esta copia de seguridad?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restaurar copia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="restoreSummary"></div>
                <div id="restoreProgress" class="d-none mt-3">
                    <div class="progress">
                        <div id="restoreBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div>
                    </div>
                    <small id="restoreLabel" class="text-muted"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="restoreConfirm" class="btn btn-danger">Restaurar ahora</button>
            </div>
        </div>
    </div>
</div>

<script>
    let restoreFile = null;

    async function openRestore(file) {
        restoreFile = file;
        const res = await fetch('<?= base_url('admin/backups/restore-info') ?>/' + file);
        const info = await res.json();

        if (!info.ok) {
            alert(info.error);
            return;
        }

        const mods = Object.keys(info.modules || {});
        const missing = info.missing_modules || [];
        const extra = info.extra_modules || [];

        let modHtml = `<li>Módulos en la copia: ${mods.length ? mods.join(', ') : 'ninguno'}</li>`;
        if (missing.length) {
            modHtml += `<li class="text-success"><strong>Se restaurarán (faltan en la web):</strong> ${missing.join(', ')}</li>`;
        }
        if (extra.length) {
            modHtml += `<li class="text-warning"><strong>En la web pero no en la copia:</strong> ${extra.join(', ')} — al restaurar la BD quedarán archivos sin registrar (podrás borrarlos desde Módulos).</li>`;
        }

        document.getElementById('restoreSummary').innerHTML = `
        <p>Se restaurará la copia <strong>${info.stamp}</strong>:</p>
        <ul>
            <li>Base de datos: ${info.has_db ? 'sí' : '<strong class="text-danger">no (¡esta copia no contiene BD!)</strong>'}</li>
            <li>Archivos de uploads: ${info.uploads}</li>
            ${modHtml}
        </ul>
        <p class="text-warning small"><i class="bi bi-exclamation-triangle"></i>
        Los datos actuales se sobrescribirán. Los archivos nuevos que no estén en la copia no se borrarán.</p>`;

        document.getElementById('restoreProgress').classList.add('d-none');
        document.getElementById('restoreConfirm').disabled = false;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('restoreModal')).show();
    }

    document.getElementById('restoreConfirm').addEventListener('click', async function() {
        this.disabled = true;
        const bar = document.getElementById('restoreBar');
        const label = document.getElementById('restoreLabel');
        document.getElementById('restoreProgress').classList.remove('d-none');

        let lastKey = '',
            repeats = 0;

        while (true) {
            let data;
            try {
                const res = await fetch('<?= base_url('admin/backups/restore-step') ?>/' + restoreFile, {
                    method: 'POST'
                });
                data = await res.json();
            } catch (e) {
                label.textContent = 'Error de comunicación con el servidor: ' + e.message;
                this.disabled = false;
                return;
            }

            if (!data.ok) {
                alert('Error: ' + data.error);
                this.disabled = false;
                return;
            }

            const st = data.state;
            const key = st.phase + '|' + st.uploads + '|' + st.modules + '|' + st.db;
            if (key === lastKey) {
                if (++repeats > 20) {
                    label.textContent = 'El proceso no avanza (fase: ' + st.phase + '). Revisa writable/logs y la tabla de módulos.';
                    this.disabled = false;
                    return;
                }
            } else {
                repeats = 0;
                lastKey = key;
            }

            label.textContent = st.label || 'Procesando...';

            if (st.phase === 'finished') {
                bar.style.width = '100%';
                label.textContent = 'Restauración completada.';
                setTimeout(() => location.reload(), 1500);
                return;
            }

            await new Promise(r => setTimeout(r, 50));
        }
    });
</script>
<?php $this->endSection() ?>