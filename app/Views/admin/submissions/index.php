<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Envíos de formularios</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Página</th>
                        <th>Datos</th>
                        <th>IP</th>
                        <th>Fecha</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No hay envíos todavía.</td></tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $s): ?>
                            <tr class="<?= empty($s['read']) ? 'table-warning' : '' ?>">
                                <td class="ps-3">
                                    <strong><?= esc($s['page_slug']) ?></strong>
                                    <?php if (empty($s['read'])): ?>
                                        <span class="badge bg-danger">Nuevo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php foreach ($s['data_decoded'] as $k => $v): ?>
                                        <small class="d-block">
                                            <strong><?= esc($k) ?>:</strong> <?= esc($v) ?>
                                        </small>
                                    <?php endforeach; ?>
                                </td>
                                <td><small class="text-muted"><?= esc($s['ip_address']) ?></small></td>
                                <td><small><?= $s['created_at'] ?></small></td>
                                <td class="text-end pe-3">
                                    <?php if (empty($s['read'])): ?>
                                        <a href="<?= base_url('admin/submissions/read/' . $s['id']) ?>" class="btn btn-sm btn-outline-success" title="Marcar leído">
                                            <i class="bi bi-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('admin/submissions/delete/' . $s['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $this->endSection() ?>