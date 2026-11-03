<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>

<?php
$isEdit = !empty($template);
$t      = $template ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2"><?= $title ?></h1>
    <a href="<?= base_url('admin/templates') ?>" class="btn btn-outline-secondary">
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

<form action="<?= $action ?>" method="post" class="needs-validation" novalidate>
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Datos básicos -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Datos de la plantilla</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="tplName" class="form-control"
                               value="<?= old('name', $t['name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="tplSlug" class="form-control"
                               value="<?= old('slug', $t['slug'] ?? '') ?>" required>
                        <div class="form-text">Identificador único. Ej: contacto, ficha-producto</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="2"><?= old('description', $t['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Constructor de campos -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Campos dinámicos</h5>
                    <button type="button" class="btn btn-sm btn-success" onclick="addField()">
                        <i class="bi bi-plus-lg"></i> Añadir campo
                    </button>
                </div>
                <div class="card-body">
                    <div id="fieldsContainer">
                        <?php if (!empty($fields)): ?>
                            <?php foreach ($fields as $i => $f): ?>
                                <div class="field-row border rounded p-3 mb-3 bg-light">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">Nombre (key)</label>
                                            <input type="text" name="field_name[]" class="form-control form-control-sm"
                                                   value="<?= esc($f['name']) ?>" placeholder="ej: email" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small text-muted">Etiqueta visible</label>
                                            <input type="text" name="field_label[]" class="form-control form-control-sm"
                                                   value="<?= esc($f['label']) ?>" placeholder="ej: Correo electrónico" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">Tipo</label>
                                            <select name="field_type[]" class="form-select form-select-sm">
                                                <option value="text"     <?= $f['type'] === 'text' ? 'selected' : '' ?>>Texto</option>
                                                <option value="email"    <?= $f['type'] === 'email' ? 'selected' : '' ?>>Email</option>
                                                <option value="textarea" <?= $f['type'] === 'textarea' ? 'selected' : '' ?>>Textarea</option>
                                                <option value="number"   <?= $f['type'] === 'number' ? 'selected' : '' ?>>Número</option>
                                                <option value="url"      <?= $f['type'] === 'url' ? 'selected' : '' ?>>URL</option>
                                                <option value="image"    <?= $f['type'] === 'image' ? 'selected' : '' ?>>Imagen</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeField(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small mt-2">
                        <i class="bi bi-info-circle"></i> Estos campos aparecerán en el formulario de edición de páginas que usen esta plantilla.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Opciones</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="active" id="tplActive" value="1"
                            <?= old('active', $t['active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="tplActive">Plantilla activa</label>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear plantilla' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php $this->endSection() ?>

<?php $this->section('js') ?>
<script>
// Generar slug automático
document.getElementById('tplName')?.addEventListener('blur', function() {
    const slugField = document.getElementById('tplSlug');
    if (slugField.value === '') {
        slugField.value = this.value.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .trim('-');
    }
});

// Plantilla HTML para un campo nuevo
function getFieldTemplate() {
    return `
    <div class="field-row border rounded p-3 mb-3 bg-light">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Nombre (key)</label>
                <input type="text" name="field_name[]" class="form-control form-control-sm" placeholder="ej: telefono" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Etiqueta visible</label>
                <input type="text" name="field_label[]" class="form-control form-control-sm" placeholder="ej: Teléfono" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Tipo</label>
                <select name="field_type[]" class="form-select form-select-sm">
                    <option value="text">Texto</option>
                    <option value="email">Email</option>
                    <option value="textarea">Textarea</option>
                    <option value="number">Número</option>
                    <option value="url">URL</option>
                    <option value="image">Imagen</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeField(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>`;
}

function addField() {
    const container = document.getElementById('fieldsContainer');
    const wrapper = document.createElement('div');
    wrapper.innerHTML = getFieldTemplate();
    container.appendChild(wrapper.firstElementChild);
}

function removeField(btn) {
    btn.closest('.field-row').remove();
}
</script>
<?php $this->endSection() ?>