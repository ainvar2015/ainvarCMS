<?php $this->extend('front/themes/' . ($themePath ?? 'default') . '/layout') ?>

<?php $this->section('content') ?>
<div class="container py-5">

    <!-- Migas de pan -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('catalogo') ?>">Catálogo</a></li>
            <?php if (!empty($product['category_slug'])): ?>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('catalogo/categoria/' . $product['category_slug']) ?>">
                        <?= esc($product['category_name'] ?? 'Categoría') ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Imagen -->
        <div class="col-lg-6">
            <?php if (!empty($product['image_main'])): ?>
                <img src="<?= base_url($product['image_main']) ?>"
                     alt="<?= esc($product['name']) ?>"
                     class="img-fluid rounded shadow-sm w-100"
                     style="max-height:480px;object-fit:cover;">
            <?php else: ?>
                <div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm"
                     style="height:480px;">
                    <i class="bi bi-box-seam text-muted" style="font-size:6rem;"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Datos -->
        <div class="col-lg-6">
            <?php if (!empty($product['category_name'])): ?>
                <span class="badge bg-secondary mb-2"><?= esc($product['category_name']) ?></span>
            <?php endif; ?>

            <h1 class="display-6 fw-bold mb-3"><?= esc($product['name']) ?></h1>

            <p class="fs-3 mb-3">
                <span class="fw-bold text-primary"><?= number_format((float) $product['price'], 2, ',', '.') ?> €</span>
                <?php if (!empty($product['price_old']) && $product['price_old'] > $product['price']): ?>
                    <span class="text-muted text-decoration-line-through fs-5">
                        <?= number_format((float) $product['price_old'], 2, ',', '.') ?> €
                    </span>
                    <span class="badge bg-danger ms-2">
                        -<?= round(100 - ($product['price'] / $product['price_old'] * 100)) ?>%
                    </span>
                <?php endif; ?>
            </p>

            <?php if (!empty($product['short_desc'])): ?>
                <p class="lead text-muted"><?= esc($product['short_desc']) ?></p>
            <?php endif; ?>

            <?php if (!empty($product['description'])): ?>
                <div class="content my-4">
                    <?= nl2br(esc($product['description'])) ?>
                </div>
            <?php endif; ?>

            <!-- Fila de metadatos opcionales -->
            <div class="d-flex gap-4 text-muted small mb-4">
                <?php if (!empty($product['sku'])): ?>
                    <span><i class="bi bi-upc"></i> Ref: <?= esc($product['sku']) ?></span>
                <?php endif; ?>
                <?php if (isset($product['stock'])): ?>
                    <span>
                        <?php if ($product['stock'] > 0): ?>
                            <i class="bi bi-check-circle text-success"></i> En stock
                        <?php else: ?>
                            <i class="bi bi-x-circle text-danger"></i> Agotado
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>

            <a href="<?= base_url('catalogo') ?>" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Volver al catálogo
            </a>
        </div>
    </div>
</div>
<?php $this->endSection() ?>