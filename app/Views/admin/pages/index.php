<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Páginas</h1>
    <a href="<?= base_url('admin/pages/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nueva Página
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Título</th>
                        <th>Slug</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Orden</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay páginas creadas todavía.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pages as $p): ?>
                            <tr>
                                <td class="ps-3">
                                    <strong><?= esc($p['title']) ?></strong>
                                    <?php if ($p['meta_title']): ?>
                                        <br><small class="text-muted"><?= esc($p['meta_title']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= esc($p['slug']) ?></code></td>
                                <td>
                                    <?php
                                    $badgeType = match ($p['type']) {
                                        'normal'   => 'bg-secondary',
                                        'template' => 'bg-info',
                                        'module'   => 'bg-warning text-dark',
                                        default    => 'bg-light text-dark',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeType ?>"><?= ucfirst($p['type']) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $badgeStatus = match ($p['status']) {
                                        'published' => 'bg-success',
                                        'draft'     => 'bg-warning text-dark',
                                        'hidden'    => 'bg-dark',
                                        default     => 'bg-light text-dark',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeStatus ?>"><?= ucfirst($p['status']) ?></span>
                                </td>
                                <td><?= $p['sort_order'] ?></td>
                                <td class="text-end pe-3">
                                    <a href="<?= base_url($p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= base_url('admin/pages/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-post="<?= base_url('admin/pages/delete/' . $p['id']) ?>"
                                        data-confirm="¿Eliminar la página «<?= esc($p['title']) ?>»?">
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