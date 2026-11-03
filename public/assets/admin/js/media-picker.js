/**
 * MediaPicker.open(function(url, alt, meta) { ... })
 * Devuelve la imagen seleccionada de la biblioteca de medios.
 */
window.MediaPicker = (function () {
    let callback = null;
    let currentFolder = null;

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
    function setCsrf(t) {
        if (t) document.querySelector('meta[name="csrf-token"]').setAttribute('content', t);
    }

    function ensureModal() {
        if (document.getElementById('mediaPickerModal')) return;
        const div = document.createElement('div');
        div.innerHTML = `
        <div class="modal fade" id="mediaPickerModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Biblioteca de medios</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div id="mpBreadcrumb" class="breadcrumb mb-0"></div>
                            <label class="btn btn-sm btn-primary mb-0">
                                Subir <input type="file" id="mpUpload" multiple accept="image/*" class="d-none">
                            </label>
                        </div>
                        <div id="mpFolders" class="mb-3"></div>
                        <div id="mpGrid" class="row g-2" style="max-height:50vh;overflow-y:auto;"></div>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.appendChild(div.firstElementChild);

        document.getElementById('mpUpload').addEventListener('change', async e => {
            const fd = new FormData();
            for (const f of e.target.files) fd.append('files[]', f);
            fd.append('csrf_test_name', csrf());          // coincide con tu tokenName, correcto
            fd.append('folder_id', currentFolder || '');  // raíz = '', sin sorpresas de URL

            try {
                const res = await fetch(baseUrl + 'admin/media/upload', {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) {
                    console.error('[media-picker] HTTP', res.status, await res.text());
                    return;
                }
                const data = await res.json();
                setCsrf(data.csrf);
                if (data.errors && data.errors.length) alert('Errores: ' + data.errors.join('\n'));
                load(currentFolder);
            } catch (err) {
                console.error('[media-picker]', err);
                alert('Error al subir. Mira la consola.');
            } finally {
                e.target.value = '';   // permite volver a elegir los mismos archivos
            }
        });
    }

    const baseUrl = document.querySelector('base')?.href || '/';

    async function load(folder) {
        currentFolder = folder;
        const res = await fetch(baseUrl + 'admin/media/api/' + (folder || ''));
        const data = await res.json();
        setCsrf(data.csrf);

        // Breadcrumb
        const bc = document.getElementById('mpBreadcrumb');
        bc.innerHTML = `<a href="#" class="breadcrumb-item" data-folder="">Medios</a>` +
            `<span class="breadcrumb-item active">Carpeta actual</span>`;

        // Carpetas
        document.getElementById('mpFolders').innerHTML = data.folders.map(f =>
            `<a href="#" class="btn btn-sm btn-outline-warning me-1" data-folder="${f.id}">
                <i class="bi bi-folder-fill"></i> ${f.name}</a>`).join('');

        // Imágenes
        document.getElementById('mpGrid').innerHTML = data.media.map(m => `
            <div class="col-4 col-md-2">
                <img src="${m.url}" title="${m.alt || ''}" data-url="${m.url}" data-alt="${m.alt || ''}" data-id="${m.id}"
                     class="img-fluid rounded mp-pick" style="height:100px;width:100%;object-fit:cover;cursor:pointer;">
            </div>`).join('') || '<div class="col-12 text-muted text-center py-4">Carpeta vacía</div>';

        document.querySelectorAll('#mediaPickerModal [data-folder]').forEach(el =>
            el.addEventListener('click', e => { e.preventDefault(); load(el.dataset.folder); }));
        document.querySelectorAll('.mp-pick').forEach(img =>
            img.addEventListener('click', () => {
                bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal')).hide();
                if (callback) callback(img.dataset.url, img.dataset.alt, { id: img.dataset.id, url: img.dataset.url });
            }));
    }

    return {
        open: function (cb) {
            callback = cb;
            ensureModal();
            load(null);
            new bootstrap.Modal(document.getElementById('mediaPickerModal')).show();
        },
    };
})();