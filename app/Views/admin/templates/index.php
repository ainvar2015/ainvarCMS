<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Plantillas</h1>
    <a href="<?= base_url('admin/templates/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nueva Plantilla
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nombre</th>
                        <th>Slug</th>
                        <th>Campos</th>
                        <th>Estado</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($templates)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay plantillas creadas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($templates as $t):
                            $fields = json_decode($t['fields'] ?? '[]', true) ?: [];
                        ?>
                            <tr>
                                <td class="ps-3">
                                    <strong><?= esc($t['name']) ?></strong>
                                    <?php if ($t['description']): ?>
                                        <br><small class="text-muted"><?= esc($t['description']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= esc($t['slug']) ?></code></td>
                                <td>
                                    <?php if (empty($fields)): ?>
                                        <span class="text-muted">Sin campos</span>
                                    <?php else: ?>
                                        <?php foreach ($fields as $f): ?>
                                            <span class="badge bg-light text-dark border">
                                                <?= esc($f['label']) ?> <small class="text-muted">(<?= $f['type'] ?>)</small>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($t['active']): ?>
                                        <span class="badge bg-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?= base_url('admin/templates/edit/' . $t['id']) ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-post="<?= base_url('admin/templates/delete/' . $t['id']) ?>"
                                        data-confirm="¿Eliminar la plantilla «<?= esc($t['name']) ?>»?">
                                        <i class="bi bi-trash"></i>
                                    </button>
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