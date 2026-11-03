<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2">Medios</h1>
    <form action="<?= base_url('admin/media/sync-gallery') ?>" method="post" class="d-inline"
        onsubmit="return confirm('¿Importar a la biblioteca las imágenes de Gallery que falten?')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-repeat"></i> Sincronizar con Gallery
        </button>
    </form>
    <button type="button" id="btnUpload" class="btn btn-primary" disabled>
        <i class="bi bi-upload"></i> <span id="btnUploadText">Subir imágenes</span>
    </button>
</div>

<!-- Zona de selección y arrastre -->
<div id="dropZone" class="border rounded p-4 text-center text-muted mb-4"
    style="border-style:dashed !important;cursor:pointer;min-height:180px;">
    <i class="bi bi-cloud-arrow-up" style="font-size:2rem;"></i>
    <p class="mb-1 mt-2">Arrastra imágenes aquí o haz clic para seleccionarlas</p>
    <button type="button" class="btn btn-outline-primary btn-sm" id="btnSelect">Seleccionar archivos</button>
    <span id="upStatus" class="ms-2 text-primary"></span>
    <div id="previewList" class="row row-cols-3 row-cols-md-6 g-2 mt-3 text-start"></div>
</div>
<input type="file" id="fileInput" multiple accept="image/*"
    style="position:absolute;left:-9999px;top:0;width:1px;height:1px;opacity:0;">
<!-- Migas de pan -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/media') ?>">Medios</a></li>
        <?php foreach ($breadcrumb as $b): ?>
            <li class="breadcrumb-item">
                <a href="<?= base_url('admin/media/folder/' . $b['id']) ?>"><?= esc($b['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>

<!-- Nueva carpeta -->
<form action="<?= base_url('admin/media/folders/create') ?>" method="post" class="d-flex gap-2 mb-4" style="max-width:420px;">
    <?= csrf_field() ?>
    <input type="hidden" name="parent_id" value="<?= $folder['id'] ?? '' ?>">
    <input type="text" name="name" class="form-control" placeholder="Nombre de la carpeta" required>
    <button class="btn btn-outline-primary"><i class="bi bi-folder-plus"></i> Crear</button>
</form>

<!-- Carpetas -->
<div class="row g-2 mb-4">
    <?php foreach ($folders as $f): ?>
        <div class="col-auto">
            <div class="card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-2">
                    <a href="<?= base_url('admin/media/folder/' . $f['id']) ?>" class="text-decoration-none">
                        <i class="bi bi-folder-fill text-warning"></i> <?= esc($f['name']) ?>
                    </a>
                    <form action="<?= base_url('admin/media/folders/delete/' . $f['id']) ?>" method="post" class="d-inline"
                        onsubmit="return confirm('¿Eliminar esta carpeta? Debe estar vacía.')">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-x"></i></button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Grid de imágenes -->
<div class="row g-3" id="mediaGrid">
    <?php foreach ($media as $m): ?>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card h-100 media-item" style="cursor:pointer;" data-id="<?= $m['id'] ?>"
                data-url="<?= base_url($m['file_path']) ?>" data-title="<?= esc($m['title'] ?? '') ?>"
                data-alt="<?= esc($m['alt_text'] ?? '') ?>" data-folder="<?= $m['folder_id'] ?? '' ?>">
                <img src="<?= thumb_url($m['file_path']) ?>" class="card-img-top"
                    style="height:120px;object-fit:cover;" alt="<?= esc($m['alt_text'] ?? '') ?>">
                <div class="card-body p-2">
                    <div class="small text-truncate"><?= esc($m['original_name']) ?></div>
                    <div class="text-muted" style="font-size:.75rem;">
                        <?= round($m['size'] / 1024) ?> KB<?= $m['width'] ? " · {$m['width']}×{$m['height']}" : '' ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($folders) && empty($media)): ?>
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-images" style="font-size:3rem;"></i>
            <p class="mt-2">No hay nada aquí todavía. Sube tu primera imagen.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de edición -->
<div class="modal fade" id="mediaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="mediaForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img id="modalImg" src="" class="img-fluid rounded" alt="">
                            <div class="input-group mt-2">
                                <input type="text" id="modalUrl" class="form-control form-control-sm" readonly>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('modalUrl').value)">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Título</label>
                                <input type="text" name="title" id="modalTitle" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Texto alternativo (alt)</label>
                                <input type="text" name="alt_text" id="modalAlt" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Carpeta</label>
                                <select name="folder_id" id="modalFolder" class="form-select">
                                    <option value="">— Raíz —</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="modalDelete" class="btn btn-outline-danger me-auto">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const FOLDER_ID = <?= json_encode($folder['id'] ?? null) ?>;
        const FOLDERS = <?= json_encode(array_merge(
                            [['id' => '', 'name' => '— Raíz —']],
                            array_map(fn($f) => ['id' => $f['id'], 'name' => $f['name']], $folders)
                        )) ?>;

        const MAX_SIZE = 5 * 1024 * 1024; // debe coincidir con el límite del controlador
        let pendingFiles = [];
        let fileSeq = 0;
        let uploading = false;
        let mediaModal = null;

        // === CSRF ===
        function csrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (!meta) {
                alert('Falta la meta tag csrf-token en el <head> del layout admin.');
                return null;
            }
            return meta.getAttribute('content');
        }

        function storeCsrf(data) {
            if (data && data.csrf) {
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf);
            }
        }

        // === Selección y previews ===
        function addFiles(fileList) {
            const skipped = [];
            for (const f of fileList) {
                if (!f.type.startsWith('image/')) {
                    skipped.push(f.name + ' (no es imagen)');
                    continue;
                }
                if (f.size > MAX_SIZE) {
                    skipped.push(f.name + ' (supera 5MB)');
                    continue;
                }
                f._uid = ++fileSeq;
                pendingFiles.push(f);
            }
            if (skipped.length) alert('Descartados:\n' + skipped.join('\n'));
            renderPreviews();
        }

        function cardEl(uid) {
            return document.querySelector('#previewList [data-uid="' + uid + '"]');
        }

        function renderPreviews() {
            const container = document.getElementById('previewList');
            container.innerHTML = '';

            pendingFiles.forEach((f) => {
                const url = URL.createObjectURL(f);
                const div = document.createElement('div');
                div.className = 'col';
                div.dataset.uid = f._uid;
                div.innerHTML =
                    '<div class="position-relative">' +
                    '  <img src="' + url + '" class="img-fluid rounded" style="height:90px;width:100%;object-fit:cover;">' +
                    '  <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1" data-uid="' + f._uid + '">&times;</button>' +
                    '  <div class="small text-truncate">' + f.name + '</div>' +
                    '  <div class="progress" style="height:6px;"><div class="progress-bar" style="width:0%"></div></div>' +
                    '  <div class="up-label small text-muted"></div>' +
                    '</div>';
                container.appendChild(div);
            });

            container.querySelectorAll('button[data-uid]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (uploading) return;
                    pendingFiles = pendingFiles.filter(x => x._uid != btn.dataset.uid);
                    renderPreviews();
                });
            });

            updateUploadBtn();
        }

        function updateUploadBtn() {
            const btn = document.getElementById('btnUpload');
            btn.disabled = uploading || pendingFiles.length === 0;
            document.getElementById('btnUploadText').textContent =
                pendingFiles.length ? 'Subir ' + pendingFiles.length + ' imagen(es)' : 'Subir imágenes';
        }

        // Clic en zona o botón → abrir selector
        document.getElementById('dropZone').addEventListener('click', () => {
            if (!uploading) document.getElementById('fileInput').click();
        });
        document.getElementById('btnSelect').addEventListener('click', (e) => {
            e.stopPropagation();
            if (!uploading) document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function() {
            if (this.files.length) addFiles(this.files);
            this.value = '';
        });

        // === Arrastre ===
        const dz = document.getElementById('dropZone');
        ['dragenter', 'dragover'].forEach(evt =>
            dz.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.add('bg-light');
            })
        );
        ['dragleave', 'drop'].forEach(evt =>
            dz.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.remove('bg-light');
            })
        );
        dz.addEventListener('drop', e => {
            if (e.dataTransfer.files.length) addFiles(e.dataTransfer.files);
        });

        // === Subida archivo a archivo (secuencial: cada POST regenera el CSRF) ===
        function uploadOne(file) {
            return new Promise((resolve) => {
                const card = cardEl(file._uid);
                const bar = card.querySelector('.progress-bar');
                const label = card.querySelector('.up-label');

                const fd = new FormData();
                fd.append('files[]', file);
                fd.append('folder_id', FOLDER_ID || '');
                fd.append('<?= csrf_token() ?>', csrfToken());

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= base_url('admin/media/upload') ?>');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const pct = Math.round((e.loaded / e.total) * 100);
                        bar.style.width = pct + '%';
                        label.textContent = pct + '%';
                    }
                });

                xhr.addEventListener('load', () => {
                    let data = null;
                    try {
                        data = JSON.parse(xhr.responseText);
                    } catch (e) {}
                    storeCsrf(data);

                    if (xhr.status >= 200 && xhr.status < 300 && data && data.uploaded && data.uploaded.length) {
                        bar.classList.add('bg-success');
                        label.textContent = 'OK';
                        resolve({
                            ok: true,
                            media: data.uploaded[0]
                        });
                    } else {
                        const msg = (data && data.errors && data.errors.join(' ')) || ('Error HTTP ' + xhr.status);
                        bar.classList.add('bg-danger');
                        label.textContent = 'Error';
                        card.title = msg;
                        resolve({
                            ok: false
                        });
                    }
                });
                xhr.addEventListener('error', () => {
                    bar.classList.add('bg-danger');
                    label.textContent = 'Error de red';
                    resolve({
                        ok: false
                    });
                });

                xhr.send(fd);
            });
        }

        function addMediaCard(m) {
            document.getElementById('mediaVacio')?.remove();
            const col = document.createElement('div');
            col.className = 'col-6 col-md-3 col-lg-2';

            const card = document.createElement('div');
            card.className = 'card h-100 media-item';
            card.style.cursor = 'pointer';
            card.dataset.id = m.id;
            card.dataset.url = m.url;
            card.dataset.title = '';
            card.dataset.alt = '';
            card.dataset.folder = m.folder_id ?? '';

            const img = document.createElement('img');
            img.src = m.thumb;
            img.className = 'card-img-top';
            img.style.cssText = 'height:120px;object-fit:cover;';
            img.alt = '';

            const body = document.createElement('div');
            body.className = 'card-body p-2';
            const name = document.createElement('div');
            name.className = 'small text-truncate';
            name.textContent = m.original_name;
            const meta = document.createElement('div');
            meta.className = 'text-muted';
            meta.style.fontSize = '.75rem';
            meta.textContent = Math.round(m.size / 1024) + ' KB' + (m.width ? ' · ' + m.width + '×' + m.height : '');
            body.append(name, meta);
            card.append(img, body);
            col.appendChild(card);

            document.getElementById('mediaGrid').prepend(col);
        }

        document.getElementById('btnUpload').addEventListener('click', async function() {
            if (uploading || !pendingFiles.length) return;
            if (!csrfToken()) return;

            uploading = true;
            this.disabled = true;
            const status = document.getElementById('upStatus');
            const queue = [...pendingFiles];

            for (let i = 0; i < queue.length; i++) {
                const f = queue[i];
                status.textContent = 'Subiendo ' + (i + 1) + '/' + queue.length + ': ' + f.name;
                const r = await uploadOne(f);
                if (r.ok) {
                    addMediaCard(r.media);
                    pendingFiles = pendingFiles.filter(x => x._uid !== f._uid);
                }
                // si falla, el archivo se queda en pendingFiles para reintento
            }

            status.textContent = '';
            uploading = false;
            renderPreviews();
        });

        // === Modal de edición (delegación: funciona también con cards nuevas) ===
        function openMediaModal(item) {
            document.getElementById('mediaForm').action = '<?= base_url('admin/media/update') ?>/' + item.dataset.id;
            document.getElementById('modalImg').src = item.dataset.url;
            document.getElementById('modalUrl').value = item.dataset.url;
            document.getElementById('modalTitle').value = item.dataset.title;
            document.getElementById('modalAlt').value = item.dataset.alt;
            document.getElementById('modalDelete').dataset.url = '<?= base_url('admin/media/delete') ?>/' + item.dataset.id;

            const sel = document.getElementById('modalFolder');
            sel.innerHTML = '';
            FOLDERS.forEach(f => {
                const opt = new Option(f.name, f.id);
                if (String(f.id) === String(item.dataset.folder)) opt.selected = true;
                sel.add(opt);
            });

            if (!mediaModal) {
                mediaModal = new bootstrap.Modal(document.getElementById('mediaModal'));
            }
            mediaModal.show();
        }

        document.getElementById('mediaGrid').addEventListener('click', function(e) {
            const item = e.target.closest('.media-item');
            if (item) openMediaModal(item);
        });

        document.getElementById('modalDelete').addEventListener('click', async function() {
            if (!confirm('¿Eliminar esta imagen? Se borrará también del servidor y su miniatura.')) return;

            const fd = new FormData();
            fd.append('<?= csrf_token() ?>', csrfToken());

            try {
                const res = await fetch(this.dataset.url, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json().catch(() => null);
                storeCsrf(data);
                location.reload();
            } catch (err) {
                alert('Error: ' + err.message);
            }
        });

    });
</script>
<?php $this->endSection() ?>