<?php $this->extend('front/themes/' . ($themePath ?? 'default') . '/layout') ?>

<?php $this->section('content') ?>
<?php $en = ($current_lang ?? 'es') === 'en'; ?>
<div class="container py-5">

    <h1 class="mb-4"><?= $en ? 'Search' : 'Búsqueda' ?></h1>

    <form action="<?= localized_url('buscar') ?>" method="get" class="mb-5" role="search">
        <div class="input-group input-group-lg" style="max-width:640px;">
            <input type="search" name="q" class="form-control" minlength="2"
                placeholder="<?= $en ? 'Search the site…' : 'Buscar en el sitio…' ?>"
                value="<?= esc($q) ?>" autofocus>
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <?php if (mb_strlen($q) < 2): ?>
        <div class="alert alert-info"><?= $en ? 'Type at least 2 characters.' : 'Escribe al menos 2 caracteres.' ?></div>
    <?php elseif ($total === 0): ?>
        <div class="alert alert-warning">
            <?= $en ? 'No results for' : 'No se han encontrado resultados para' ?> <strong>“<?= esc($q) ?>”</strong>.
        </div>
    <?php else: ?>
        <p class="text-muted mb-4"><?= $total ?> <?= $en ? 'results for' : 'resultados para' ?> <strong>“<?= esc($q) ?>”</strong></p>

        <?php
        $groups = [
            'pages'    => ['label' => $en ? 'Pages'    : 'Páginas',    'icon' => 'bi-file-earmark-text'],
            'news'     => ['label' => $en ? 'News'     : 'Noticias',   'icon' => 'bi-newspaper'],
            'products' => ['label' => $en ? 'Products' : 'Productos',  'icon' => 'bi-box-seam'],
        ];
        foreach ($groups as $key => $g):
            if (empty($results[$key])) continue;
        ?>
            <h2 class="h5 mt-4 mb-3"><i class="bi <?= $g['icon'] ?> me-1"></i> <?= $g['label'] ?> (<?= count($results[$key]) ?>)</h2>
            <div class="list-group shadow-sm mb-4">
                <?php foreach ($results[$key] as $r): ?>
                    <a href="<?= $r['url'] ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h3 class="h6 mb-1"><?= esc($r['title']) ?></h3>
                            <?php if (!empty($r['date'])): ?><small class="text-muted"><?= news_date($r['date']) ?></small><?php endif; ?>
                            <?php if (isset($r['price'])): ?><small class="text-primary fw-bold"><?= number_format((float) $r['price'], 2, ',', '.') ?> €</small><?php endif; ?>
                        </div>
                        <?php if (!empty($r['excerpt'])): ?>
                            <p class="mb-0 small text-muted"><?= esc($r['excerpt']) ?></p>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
<?php $this->endSection() ?>