<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2 mb-0">Dashboard</h1>
    <div class="btn-toolbar">
        <a href="<?= base_url('admin/pages/create') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Nueva página
        </a>
    </div>
</div>

<!-- Tarjetas de estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Páginas publicadas</h6>
                        <p class="card-text display-6 mb-0"><?= $published_pages ?></p>
                        <small class="text-white-50"><?= $draft_pages ?> en borrador</small>
                    </div>
                    <i class="bi bi-file-earmark-text" style="font-size:2.5rem;opacity:.4;"></i>
                </div>
            </div>
            <a href="<?= base_url('admin/pages') ?>" class="card-footer text-white-50 small text-decoration-none">
                Ver todas <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Envíos sin leer</h6>
                        <p class="card-text display-6 mb-0"><?= $unread_submissions ?></p>
                        <small class="text-white-50">formulario de contacto</small>
                    </div>
                    <i class="bi bi-envelope" style="font-size:2.5rem;opacity:.4;"></i>
                </div>
            </div>
            <a href="<?= base_url('admin/submissions') ?>" class="card-footer text-white-50 small text-decoration-none">
                Ver envíos <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Módulos activos</h6>
                        <p class="card-text display-6 mb-0"><?= $total_modules ?></p>
                        <small class="text-white-50">
                            <?php
                            $mods = array_column($active_modules ?? [], 'name');
                            echo esc(implode(', ', array_slice($mods, 0, 2)));
                            if (count($mods) > 2) echo '…';
                            ?>
                        </small>
                    </div>
                    <i class="bi bi-box-seam" style="font-size:2.5rem;opacity:.4;"></i>
                </div>
            </div>
            <a href="<?= base_url('admin/modules') ?>" class="card-footer text-white-50 small text-decoration-none">
                Gestionar <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Tema activo</h6>
                        <p class="card-text h4 mb-0"><?= esc($active_theme['name'] ?? 'Ninguno') ?></p>
                        <small class="text-white-50">
                            <?php if ($total_products !== null): ?>Productos: <?= $total_products ?> · <?php endif; ?>
                        <?php if ($total_images !== null): ?>Imágenes: <?= $total_images ?><?php endif; ?>
                        </small>
                    </div>
                    <i class="bi bi-palette" style="font-size:2.5rem;opacity:.4;"></i>
                </div>
            </div>
            <a href="<?= base_url('admin/themes') ?>" class="card-footer text-white-50 small text-decoration-none">
                Cambiar tema <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
<!-- Gráfico de envíos por semana -->
<?php if (!empty($chart_labels)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-bar-chart"></i> Envíos por semana (últimas 12 semanas)
                </div>
                <div class="card-body">
                    <canvas id="chartSubmissions" height="90"></canvas>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="row g-4">
    <!-- Últimos envíos -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-envelope"></i> Últimos envíos</span>
                <a href="<?= base_url('admin/submissions') ?>" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent_submissions)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size:2rem;"></i>
                        <p class="mb-0 mt-2">No hay envíos todavía</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>De</th>
                                    <th>Página</th>
                                    <th>Fecha</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_submissions as $sub):
                                    $data  = json_decode($sub['data'] ?? '{}', true) ?: [];
                                    $from  = $data['nombre'] ?? $data['name'] ?? $data['email'] ?? '—';
                                    $unread = empty($sub['read']);
                                ?>
                                    <tr class="<?= $unread ? 'table-warning' : '' ?>">
                                        <td>
                                            <?= esc($from) ?>
                                            <?php if ($unread): ?>
                                                <span class="badge bg-warning text-dark ms-1">nuevo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="text-muted small">/<?= esc($sub['page_slug']) ?></span></td>
                                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($sub['created_at'])) ?></td>
                                        <td class="text-end">
                                            <a href="<?= base_url('admin/submissions') ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Últimas páginas -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text"></i> Últimas páginas</span>
                <a href="<?= base_url('admin/pages') ?>" class="btn btn-sm btn-outline-secondary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($recent_pages as $p): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span><?= esc($p['title']) ?></span>
                                <?php if (($p['status'] ?? '') === 'draft'): ?>
                                    <span class="badge bg-secondary ms-1">borrador</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url('admin/pages/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($recent_pages)): ?>
                        <li class="list-group-item text-muted text-center py-3">No hay páginas creadas</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="card">
            <div class="card-header"><i class="bi bi-lightning"></i> Accesos rápidos</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/pages/create') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-plus-lg"></i> Crear página
                    </a>
                    <a href="<?= base_url('admin/menus') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-list"></i> Gestionar menús
                    </a>
                    <a href="<?= base_url('admin/settings') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-gear"></i> Ajustes del sitio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>

<?php $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('chartSubmissions');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: 'Envíos',
                    data: <?= json_encode($chart_data) ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.6)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (c) => c.parsed.y + (c.parsed.y === 1 ? ' envío' : ' envíos')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
</script>
<?php $this->endSection() ?>