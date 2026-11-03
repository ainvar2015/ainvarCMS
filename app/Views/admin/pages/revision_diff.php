<div>
    <div class="mb-3">
        <strong>Versión del <?= date('d/m/Y H:i', strtotime($revision['created_at'])) ?></strong>
        por <?= esc($user['username'] ?? '—') ?>
        · Título: <?= esc($revision['title']) ?>
    </div>

    <div class="border rounded p-3" style="max-height:60vh;overflow-y:auto;white-space:pre-wrap;font-size:.9rem;">
        <?php if (empty($diff)): ?>
            <span class="text-muted">(sin contenido)</span>
        <?php endif; ?>
        <?php foreach ($diff as $op): ?>
            <?php if ($op['op'] === 'eq'): ?>
                <span><?= esc($op['text']) ?> </span>
            <?php elseif ($op['op'] === 'del'): ?>
                <span class="diff-del"><?= esc($op['text']) ?> </span>
            <?php else: ?>
                <span class="diff-ins"><?= esc($op['text']) ?> </span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="mt-2 small text-muted">
        Comparado con el estado actual ·
        <span class="diff-ins px-1 rounded">verde</span> = añadido desde esa versión ·
        <span class="diff-del px-1 rounded">rojo</span> = eliminado desde esa versión
    </div>
</div>