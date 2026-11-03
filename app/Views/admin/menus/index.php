<?php $this->extend('admin/layout') ?>

<?php $this->section('css') ?>
<style>
    .menu-tree,
    .menu-tree ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .menu-tree ul {
        padding-left: 35px;
        min-height: 10px;
        border-left: 2px dashed #dee2e6;
        margin-left: 10px;
    }

    .menu-item {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px 14px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: move;
        transition: all .2s;
    }

    .menu-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-color: #adb5bd;
    }

    .menu-item .handle {
        color: #6c757d;
        margin-right: 12px;
        font-size: 1.1rem;
        cursor: grab;
    }

    .menu-item .handle:active {
        cursor: grabbing;
    }

    .menu-item.sortable-ghost {
        opacity: 0.4;
        background: #e9ecef;
    }

    .menu-item.sortable-drag {
        opacity: 0.95;
        background: #fff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .menu-info {
        flex: 1;
        min-width: 0;
    }

    .menu-info small {
        color: #6c757d;
    }

    .menu-actions {
        white-space: nowrap;
        margin-left: 10px;
    }
</style>
<?php $this->endSection() ?>

<?php $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Menús de navegación</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#menuModal" onclick="resetForm()">
        <i class="bi bi-plus-lg"></i> Añadir item
    </button>
</div>

<?php if (empty($tree)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No hay items en el menú. Crea el primero.
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-muted small mb-3">
                <i class="bi bi-info-circle"></i> Arrastra los items para reordenar o anidar. Pulsa <strong>Guardar orden</strong> para aplicar cambios.
            </p>
            <ul class="menu-tree" id="menuTree">
                <?= $tree ?>
            </ul>
            <div class="text-end mt-3">
                <button type="button" id="saveOrderBtn" class="btn btn-success" style="display:none;">
                    <i class="bi bi-check-lg"></i> Guardar orden
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal -->
<div class="modal fade" id="menuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('admin/menus/save') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="menuId">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Etiqueta <span class="text-danger">*</span></label>
                        <input type="text" name="label" id="menuLabel" class="form-control" required>
                    </div>
                    <?php foreach (active_languages() as $l): if ($l['code'] === default_lang()) continue; ?>
                        <div class="mb-2">
                            <label class="form-label small">Etiqueta (<?= strtoupper($l['code']) ?>)</label>
                            <input type="text" name="label_trans[<?= $l['code'] ?>]" id="labelTrans_<?= $l['code'] ?>" class="form-control form-control-sm">
                        </div>
                    <?php endforeach; ?>

                    <div class="mb-3">
                        <label class="form-label">Enlazar a página</label>
                        <select name="page_id" id="menuPageId" class="form-select">
                            <option value="">-- Ninguna (URL manual) --</option>
                            <?php foreach ($pages as $pg): ?>
                                <option value="<?= $pg['id'] ?>"><?= esc($pg['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL manual</label>
                        <input type="text" name="url" id="menuUrl" class="form-control" placeholder="https://... o /contacto">
                        <div class="form-text">Si seleccionas una página arriba, déjalo vacío.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Menú padre</label>
                        <select name="parent_id" id="menuParentId" class="form-select">
                            <option value="">-- Ninguno (raíz) --</option>
                            <?= (new \App\Models\MenuModel())->getOptions($menuItem['id'] ?? null) ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Icono / Imagen</label>
                        <input type="file" name="icon" class="form-control" accept="image/*">
                        <div id="currentIcon" class="mt-2" style="display:none;">
                            <img src="" height="40" class="img-thumbnail">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="remove_icon" value="1" id="removeIcon">
                                <label class="form-check-label" for="removeIcon">Eliminar icono actual</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Target</label>
                        <select name="target" id="menuTarget" class="form-select">
                            <option value="_self">Misma ventana</option>
                            <option value="_blank">Nueva ventana</option>
                        </select>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" id="menuActive" value="1" checked>
                        <label class="form-check-label" for="menuActive">Activo</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->endSection() ?>

<?php $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    const MENU_LABEL_TRANS = <?= json_encode($menuTranslations ?? []) ?>;

    function resetForm() {
        document.querySelector('#menuModal form').reset();
        document.getElementById('menuId').value = '';
        document.getElementById('modalTitle').textContent = 'Nuevo item';
        document.getElementById('currentIcon').style.display = 'none';
        document.getElementById('menuActive').checked = true;
        document.querySelectorAll('[id^="labelTrans_"]').forEach(i => i.value = '');
    }

    function editMenu(btn) {
        resetForm();
        document.getElementById('modalTitle').textContent = 'Editar item';
        document.getElementById('menuId').value = btn.dataset.id;
        document.getElementById('menuLabel').value = btn.dataset.label;
        document.getElementById('menuUrl').value = btn.dataset.url || '';
        document.getElementById('menuPageId').value = btn.dataset.page_id || '';
        document.getElementById('menuParentId').value = btn.dataset.parent_id || '';
        document.getElementById('menuTarget').value = btn.dataset.target || '_self';
        document.getElementById('menuActive').checked = btn.dataset.active == '1';
        const tr = MENU_LABEL_TRANS[btn.dataset.id] || {};
        document.querySelectorAll('[id^="labelTrans_"]').forEach(i => {
            const code = i.id.replace('labelTrans_', '');
            i.value = tr[code] || '';
        });

        if (btn.dataset.icon) {
            document.getElementById('currentIcon').style.display = 'block';
            document.getElementById('currentIcon').querySelector('img').src = '<?= base_url() ?>/' + btn.dataset.icon;
        }

        new bootstrap.Modal(document.getElementById('menuModal')).show();
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => editMenu(btn));
    });

    function initSortable() {
        document.querySelectorAll('.menu-tree, .menu-tree ul').forEach(ul => {
            new Sortable(ul, {
                group: 'nested',
                animation: 150,
                handle: '.handle',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: function() {
                    document.getElementById('saveOrderBtn').style.display = 'inline-block';
                }
            });
        });
    }
    initSortable();

    document.getElementById('saveOrderBtn').addEventListener('click', function() {
        const order = [];

        function parseList(ul, parentId) {
            Array.from(ul.children).forEach((li, index) => {
                const id = li.getAttribute('data-id');
                if (!id) return;
                order.push({
                    id: id,
                    parent_id: parentId,
                    sort_order: index
                });
                const nested = li.querySelector('ul');
                if (nested) parseList(nested, id);
            });
        }

        parseList(document.getElementById('menuTree'), null);

        const btn = this;
        btn.disabled = true;

        fetch('<?= base_url('admin/menus/reorder') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'order=' + encodeURIComponent(JSON.stringify(order)) + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                if (data.status === 'success') {
                    btn.style.display = 'none';
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Guardado';
                    setTimeout(() => btn.innerHTML = '<i class="bi bi-check-lg"></i> Guardar orden', 1500);
                } else {
                    alert('Error: ' + (data.message || 'No se pudo guardar'));
                }
            })
            .catch(err => {
                btn.disabled = false;
                alert('Error de red: ' + err.message);
            });
    });
</script>
<?php $this->endSection() ?>