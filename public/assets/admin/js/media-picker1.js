/**
 * MediaPicker.open(function (url, alt) { ... })
 * Abre la biblioteca de medios y devuelve la imagen elegida.
 */
window.MediaPicker = (function () {
    const BASE      = window.ADMIN_URL || '/admin/';
    const CSRF_NAME = window.CSRF_NAME || 'csrf_test_name';
    let callback      = null;
    let currentFolder = null;

    function csrf()  { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }
    function setCsrf(t) { if (t) document.querySelector('meta[name="csrf-token"]').setAttribute('content', t); }

    function ensureModal() {
        if (document.getElementById('mediaPickerModal')) return;
        const div = document.createElement('div');
        div.innerHTML = `
        <div class="modal fade" id="mediaPickerModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-images"></i> Biblioteca de medios</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <nav id="mpBreadcrumb" class="breadcrumb mb-0"></nav>
                            <label class="btn btn-sm btn-primary mb-0">
                                <i class="bi bi-upload"></i> Subir
                                <input type="file" id="mpUpload" multiple accept="image/*" class="d-none">
                            </label>
                        </div>
                        <div id="mpFolders" class="mb-3"></div>
                        <div id="mpGrid" class="row g-2" style="max-height:55vh;overflow-y:auto;"></div>
                    </div>
                    <div class="modal-footer text-muted small">
                        Haz clic en una imagen para elegirla
                    </div>
                </div>
            </div>
        </div>`;
        document.body.appendChild(div.firstElementChild);

        document.getElementById('mpUpload').addEventListener('change', async function () {
            if (!this.files.length) return;
            const fd = new FormData();
            for (const f of this.files) fd.append('files[]', f);
            fd.append(CSRF_NAME, csrf());

            const res  = await fetch(BASE + 'media/upload/' + (currentFolder || ''), {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            setCsrf(data.csrf);
            load(currentFolder); // recargar el grid del picker
            this.value = '';
        });
    }

    async function load(folder) {
        currentFolder = folder;

        const res  = await fetch(BASE + 'media/api/' + (folder || ''));
        const data = await res.json();
        setCsrf(data.csrf);

        document.getElementById('mpBreadcrumb').innerHTML =
            '<a href="#" class="breadcrumb-item" data-folder="">Medios</a>' +
            (folder ? '<span class="breadcrumb-item active">Carpeta</span>' : '');

        document.getElementById('mpFolders').innerHTML = data.folders.map(f =>
            '<a href="#" class="btn btn-sm btn-outline-warning me-1 mb-1" data-folder="' + f.id + '">' +
            '<i class="bi bi-folder-fill"></i> ' + f.name + '</a>'
        ).join('');

        document.getElementById('mpGrid').innerHTML = data.media.length
            ? data.media.map(m =>
                '<div class="col-4 col-md-2">' +
                '<img src="' + m.url + '" title="' + (m.alt || '') + '" data-url="' + m.url + '" data-alt="' + (m.alt || '') + '"' +
                ' class="img-fluid rounded mp-pick" style="height:100px;width:100%;object-fit:cover;cursor:pointer;border:2px solid transparent;"' +
                ' onmouseover="this.style.borderColor=\'#0d6efd\'" onmouseout="this.style.borderColor=\'transparent\'">' +
                '</div>'
              ).join('')
            : '<div class="col-12 text-muted text-center py-4">Esta carpeta está vacía</div>';

        document.querySelectorAll('#mediaPickerModal [data-folder]').forEach(el =>
            el.addEventListener('click', e => { e.preventDefault(); load(el.dataset.folder); }));
        document.querySelectorAll('.mp-pick').forEach(img =>
            img.addEventListener('click', () => {
                bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal')).hide();
                if (callback) callback(img.dataset.url, img.dataset.alt);
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