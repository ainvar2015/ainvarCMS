<?php $this->extend('admin/layout') ?>

<?php $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?php
$isEdit  = !empty($page);
$p       = $page ?? [];
$typeSel = old('type', $p['type'] ?? 'normal');

$defaultLang = default_lang();
$allLangs    = $languages_list ?? active_languages();
$otherLangs  = array_filter($allLangs, fn($l) => $l['code'] !== $defaultLang);
$defLangData = [];
foreach ($allLangs as $l) {
    if ($l['code'] === $defaultLang) {
        $defLangData = $l;
        break;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2"><?= $title ?></h1>
    <a href="<?= base_url('admin/pages') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Contenido de la página</h5>
                </div>
                <div class="card-body">

                    <!-- Tipo (independiente del idioma: siempre visible) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de página <span class="text-danger">*</span></label>
                        <select name="type" id="pageType" class="form-select">
                            <option value="normal" <?= $typeSel === 'normal' ? 'selected' : '' ?>>Normal (Editor HTML)</option>
                            <option value="template" <?= $typeSel === 'template' ? 'selected' : '' ?>>Basada en plantilla</option>
                            <option value="module" <?= $typeSel === 'module' ? 'selected' : '' ?>>Módulo</option>
                        </select>
                    </div>

                    <?php if (!empty($allLangs)): ?>
                        <!-- Pestañas de idiomas -->
                        <ul class="nav nav-tabs" role="tablist"
                            style="display:flex !important;flex-direction:row !important;flex-wrap:nowrap !important;overflow-x:auto;">
                            <li class="nav-item" style="flex-shrink:0;">
                                <a class="nav-link active" data-bs-toggle="tab" href="#lang-<?= $defaultLang ?>">
                                    <?= $defLangData['flag'] ?? '' ?> <?= esc($defLangData['name'] ?? strtoupper($defaultLang)) ?>
                                    <span class="badge bg-secondary ms-1">por defecto</span>
                                </a>
                            </li>
                            <?php foreach ($otherLangs as $l): ?>
                                <li class="nav-item" style="flex-shrink:0;">
                                    <a class="nav-link trans-tab-link" data-bs-toggle="tab" href="#lang-<?= $l['code'] ?>">
                                        <?= $l['flag'] ?? '' ?> <?= esc($l['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="tab-content mt-3">

                            <!-- ============ PESTAÑA: IDIOMA POR DEFECTO ============ -->
                            <div class="tab-pane fade show active" id="lang-<?= $defaultLang ?>">
                                <!-- Título -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="pageTitle" class="form-control form-control-lg"
                                        value="<?= old('title', $p['title'] ?? '') ?>" required>
                                </div>

                                <!-- Slug -->
                                <div class="mb-3">
                                    <label class="form-label">Slug (URL)</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-muted"><?= base_url() ?>/</span>
                                        <input type="text" name="slug" id="pageSlug" class="form-control"
                                            value="<?= old('slug', $p['slug'] ?? '') ?>"
                                            placeholder="se-genera-automaticamente">
                                    </div>
                                    <div class="form-text">Déjalo vacío para generarlo automáticamente desde el título.</div>
                                </div>

                                <!-- Editor HTML (solo normal) -->
                                <div id="blockNormal" class="type-block" style="<?= $typeSel !== 'normal' ? 'display:none' : '' ?>">
                                    <label class="form-label">Contenido HTML</label>
                                    <textarea name="content" id="summernote"><?= old('content', $p['content'] ?? '') ?></textarea>
                                </div>

                                <!-- Selector de plantilla -->
                                <div id="blockTemplate" class="type-block" style="<?= $typeSel !== 'template' ? 'display:none' : '' ?>">
                                    <label class="form-label fw-bold">Plantilla <span class="text-danger">*</span></label>
                                    <select name="template_id" class="form-select" id="templateSelect">
                                        <option value="">-- Selecciona una plantilla --</option>
                                        <?php foreach ($templates as $t): ?>
                                            <option value="<?= $t['id'] ?>" <?= (old('template_id', $p['template_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                                                <?= esc($t['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="templateFieldsContainer" class="mt-4"></div>
                                </div>

                                <!-- Selector de módulo -->
                                <div id="blockModule" class="type-block" style="<?= $typeSel !== 'module' ? 'display:none' : '' ?>">
                                    <label class="form-label fw-bold">Módulo <span class="text-danger">*</span></label>
                                    <select name="module_name" class="form-select">
                                        <option value="">-- Selecciona un módulo --</option>
                                        <?php foreach ($modules as $m): ?>
                                            <option value="<?= esc($m['slug']) ?>" <?= (old('module_name', $p['module_name'] ?? '') == $m['slug']) ? 'selected' : '' ?>>
                                                <?= esc($m['name']) ?> (v<?= esc($m['version']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                        <?php if (empty($modules)): ?>
                                            <option value="" disabled>No hay módulos activos</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- SEO del idioma por defecto -->
                                <hr class="my-4">
                                <h6 class="text-muted mb-3"><i class="bi bi-search"></i> SEO</h6>
                                <div class="mb-3">
                                    <label class="form-label">Meta título</label>
                                    <input type="text" name="meta_title" class="form-control" maxlength="255"
                                        value="<?= old('meta_title', $p['meta_title'] ?? '') ?>">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Meta descripción</label>
                                    <textarea name="meta_desc" class="form-control" rows="2" maxlength="500"><?= old('meta_desc', $p['meta_desc'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <!-- ============ PESTAÑAS: TRADUCCIONES ============ -->
                            <?php foreach ($otherLangs as $l):
                                $code = $l['code'];
                                $t    = $translations[$code] ?? [];
                            ?>
                                <div class="tab-pane fade" id="lang-<?= $code ?>">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Título (<?= strtoupper($code) ?>)</label>
                                        <input type="text" name="trans[<?= $code ?>][title]" class="form-control"
                                            value="<?= esc(old("trans.$code.title", $t['title'] ?? '')) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Slug (<?= strtoupper($code) ?>)</label>
                                        <input type="text" name="trans[<?= $code ?>][slug]" class="form-control"
                                            value="<?= esc(old("trans.$code.slug", $t['slug'] ?? '')) ?>"
                                            placeholder="Se genera desde el título">
                                    </div>

                                    <!-- Contenido traducido (solo páginas normales) -->
                                    <div class="trans-content-wrap" style="<?= $typeSel !== 'normal' ? 'display:none' : '' ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Contenido (<?= strtoupper($code) ?>)</label>
                                            <textarea name="trans[<?= $code ?>][content]" class="form-control summernote-trans" rows="8"><?= esc(old("trans.$code.content", $t['content'] ?? '')) ?></textarea>
                                        </div>
                                    </div>

                                    <?php if ($typeSel === 'template'): ?>
                                        <div class="alert alert-info small">
                                            <i class="bi bi-info-circle"></i> Página basada en plantilla: los datos de los campos son comunes a todos los idiomas; aquí solo se traduce título, slug y SEO.
                                        </div>
                                    <?php endif; ?>

                                    <h6 class="text-muted mb-3 mt-2"><i class="bi bi-search"></i> SEO (<?= strtoupper($code) ?>)</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Meta título</label>
                                            <input type="text" name="trans[<?= $code ?>][meta_title]" class="form-control"
                                                value="<?= esc(old("trans.$code.meta_title", $t['meta_title'] ?? '')) ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Meta descripción</label>
                                            <input type="text" name="trans[<?= $code ?>][meta_desc]" class="form-control"
                                                value="<?= esc(old("trans.$code.meta_desc", $t['meta_desc'] ?? '')) ?>">
                                        </div>
                                    </div>

                                    <?php if (empty($t)): ?>
                                        <div class="form-text text-muted">
                                            <i class="bi bi-exclamation-circle"></i>
                                            Sin traducción en <?= esc($l['name']) ?> — la página mostrará el contenido del idioma por defecto.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <!-- Publicación -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Publicación</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="status" class="form-select">
                            <option value="published" <?= (old('status', $p['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>>Publicada</option>
                            <option value="draft" <?= (old('status', $p['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Borrador</option>
                            <option value="hidden" <?= (old('status', $p['status'] ?? 'draft') === 'hidden') ? 'selected' : '' ?>>Oculta</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Orden</label>
                        <input type="number" name="sort_order" class="form-control" min="0"
                            value="<?= old('sort_order', $p['sort_order'] ?? 0) ?>">
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear página' ?>
                    </button>
                </div>
            </div>

            <!-- Menú -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-menu-button"></i> Menú</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="add_to_menu" id="addToMenu" value="1"
                            <?= old('add_to_menu', $menuItem ? 1 : 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="addToMenu">Añadir al menú de navegación</label>
                    </div>

                    <div id="menuOptions" style="<?= old('add_to_menu', $menuItem ? 1 : 0) ? '' : 'display:none' ?>">
                        <div class="mb-3">
                            <label class="form-label">Etiqueta del menú</label>
                            <input type="text" name="menu_label" class="form-control"
                                value="<?= old('menu_label', $menuItem['label'] ?? '') ?>"
                                placeholder="Igual que el título">
                            <?php
                            $defaultMenuLang = default_lang();
                            $menuTrans = $menu_translations ?? [];
                            ?>
                            <?php foreach (active_languages() as $l): ?>
                                <?php if ($l['code'] === $defaultMenuLang) continue; ?>
                                <div class="mb-2">
                                    <label class="form-label small">Etiqueta (<?= strtoupper($l['code']) ?>)</label>
                                    <input type="text" name="menu_label_trans[<?= $l['code'] ?>]" class="form-control form-control-sm"
                                        value="<?= esc(old("menu_label_trans.{$l['code']}", $menuTrans[$l['code']]['label'] ?? '')) ?>"
                                        placeholder="Etiqueta en <?= esc($l['name']) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen / Icono del menú</label>
                            <input type="file" name="menu_icon" class="form-control" accept="image/*">
                            <?php if ($isEdit):
                                $menuModel = new \App\Models\MenuModel();
                                $menuItem = $menuModel->where('page_id', $p['id'])->first();
                                if (!empty($menuItem['icon'])): ?>
                                    <div class="mt-2">
                                        <img src="<?= base_url($menuItem['icon']) ?>" height="40" class="img-thumbnail">
                                    </div>
                            <?php endif;
                            endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Menú padre</label>
                            <select name="menu_parent_id" class="form-select">
                                <option value="">-- Ninguno (raíz) --</option>
                                <?php foreach ($menus as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= old('menu_parent_id', $menuItem['parent_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                                        <?= esc($m['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Orden en menú</label>
                            <input type="number" name="menu_sort_order" class="form-control" min="0"
                                value="<?= old('menu_sort_order', $menuItem['sort_order'] ?? 0) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php if (!empty($revisions)): ?>
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <i class="bi bi-clock-history"></i> Historial de versiones
            <span class="text-muted small">(se guardan las últimas 20)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Título en esa versión</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($revisions as $rev): ?>
                            <tr>
                                <td class="text-nowrap"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></td>
                                <td><?= esc($rev['username'] ?? '—') ?></td>
                                <td><?= esc($rev['title']) ?></td>
                                <td class="text-end">
                                    <form action="<?= base_url('admin/pages/restore/' . $rev['id']) ?>" method="post" class="d-inline"
                                        onsubmit="return confirm('¿Restaurar esta versión? El estado actual quedará guardado en el historial.')">
                                        <?= csrf_field() ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-view-revision"
                                            data-url="<?= base_url('admin/pages/revision/' . $rev['id']) ?>">
                                            <i class="bi bi-eye"></i> Ver
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $this->endSection() ?>

<?php $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script src="<?= base_url('assets/admin/js/media-picker.js') ?>"></script>
<script>
    $(document).ready(function() {

        // Botón personalizado del toolbar
        const MediaButton = function(context) {
            const ui = $.summernote.ui;
            const button = ui.button({
                contents: '<i class="bi bi-images"></i>',
                tooltip: 'Biblioteca de medios',
                click: function() {
                    MediaPicker.open(function(url, alt) {
                        context.invoke('editor.insertImage', url, alt || '');
                    });
                },
            });
            return button.render();
        };

        $('#summernote').summernote({
            height: 300,
            lang: 'es-ES',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'media', 'picture']],
                ['view', ['fullscreen', 'codeview']],
            ],
            buttons: {
                media: MediaButton,
            },
            callbacks: {
                onImageUpload: function() {
                    MediaPicker.open(function(url, alt) {
                        $('#summernote').summernote('insertImage', url, alt || '');
                    });
                },
            },
        });

        // Summernote perezoso en las pestañas de traducción
        function initTransEditor(ta) {
            if (ta.dataset.snInit) return;
            ta.dataset.snInit = '1';
            $(ta).summernote({
                height: 250,
                lang: 'es-ES',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                ],
            });
        }
        document.querySelectorAll('a.trans-tab-link').forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', () => {
                const ta = document.querySelector(tabEl.getAttribute('href') + ' textarea.summernote-trans');
                if (ta) initTransEditor(ta);
            });
        });

        // Generar slug automático
        $('#pageTitle').on('blur', function() {
            const slugField = $('#pageSlug');
            if (slugField.val() === '') {
                let slug = $(this).val().toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .trim('-');
                slugField.val(slug);
            }
        });

        // Mostrar/ocultar bloques según tipo (incluidos los editores de traducción)
        $('#pageType').on('change', function() {
            $('.type-block').hide();
            const val = $(this).val();

            if (val === 'normal') {
                $('#blockNormal').show();
                $('#summernote').prop('disabled', false);
                $('.trans-content-wrap').show();
            } else {
                $('#summernote').prop('disabled', true);
                $('.trans-content-wrap').hide();
            }

            if (val === 'template') $('#blockTemplate').show();
            if (val === 'module') $('#blockModule').show();
        });

        const initialType = $('#pageType').val();
        if (initialType !== 'normal') {
            $('#summernote').prop('disabled', true);
            $('.trans-content-wrap').hide();
        }

        $('#addToMenu').on('change', function() {
            $('#menuOptions').toggle(this.checked);
        });
    });

    // ========== CAMPOS DINÁMICOS DE PLANTILLA ==========

    const templateSelect = document.getElementById('templateSelect');
    const fieldsContainer = document.getElementById('templateFieldsContainer');
    const savedTemplateData = <?= json_encode($template_data ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function loadTemplateFields(templateId, values = {}) {
        if (!templateId) {
            fieldsContainer.innerHTML = '';
            return;
        }

        fieldsContainer.innerHTML = '<div class="text-muted"><i class="bi bi-hourglass-split"></i> Cargando campos...</div>';

        fetch('<?= base_url('admin/pages/template-fields/') ?>' + templateId)
            .then(r => r.json())
            .then(data => {
                if (!data.fields || data.fields.length === 0) {
                    fieldsContainer.innerHTML = '<div class="alert alert-warning">Esta plantilla no tiene campos definidos.</div>';
                    return;
                }

                let html = '<div class="card border-primary"><div class="card-header bg-primary text-white"><i class="bi bi-input-cursor"></i> Campos de la plantilla</div><div class="card-body">';

                data.fields.forEach(field => {
                    const val = values[field.name] ?? '';
                    const id = 'tpl_field_' + field.name;

                    html += '<div class="mb-3">';
                    html += '<label class="form-label fw-bold">' + escapeHtml(field.label) + '</label>';

                    if (field.type === 'textarea') {
                        html += '<textarea name="template_fields[' + escapeHtml(field.name) + ']" class="form-control" rows="4">' + escapeHtml(val) + '</textarea>';
                    } else if (field.type === 'image') {
                        html += '<input type="text" name="template_fields[' + escapeHtml(field.name) + ']" id="' + id + '" class="form-control" value="' + escapeHtml(val) + '" placeholder="URL de la imagen o sube una">';
                        if (val) {
                            html += '<img src="' + escapeHtml(val) + '" height="60" class="mt-2 img-thumbnail">';
                        }
                    } else {
                        const inputType = (field.type === 'number' || field.type === 'email' || field.type === 'url') ? field.type : 'text';
                        html += '<input type="' + inputType + '" name="template_fields[' + escapeHtml(field.name) + ']" class="form-control" value="' + escapeHtml(val) + '">';
                    }

                    html += '</div>';
                });

                html += '</div></div>';
                fieldsContainer.innerHTML = html;
            })
            .catch(err => {
                fieldsContainer.innerHTML = '<div class="alert alert-danger">Error cargando campos: ' + err.message + '</div>';
            });
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    templateSelect?.addEventListener('change', function() {
        loadTemplateFields(this.value);
    });

    if (document.getElementById('pageType')?.value === 'template' && templateSelect?.value) {
        loadTemplateFields(templateSelect.value, savedTemplateData);
    }
</script>

<!-- Modal: comparar versión -->
<div class="modal fade" id="revisionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Diferencias con el estado actual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="revisionModalBody">
                <div class="text-center text-muted py-4">Cargando...</div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let revModal = null;
        document.querySelectorAll('.btn-view-revision').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!revModal) {
                    revModal = new bootstrap.Modal(document.getElementById('revisionModal'));
                }
                const body = document.getElementById('revisionModalBody');
                body.innerHTML = '<div class="text-center text-muted py-4">Cargando...</div>';
                revModal.show();
                try {
                    const res = await fetch(btn.dataset.url);
                    body.innerHTML = await res.text();
                } catch (e) {
                    body.innerHTML = '<div class="alert alert-danger">Error al cargar la comparación.</div>';
                }
            });
        });
    });
</script>
<?php $this->endSection() ?>