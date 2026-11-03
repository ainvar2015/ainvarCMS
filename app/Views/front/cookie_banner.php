<?php
helper('cookies');
$configModel = new \App\Models\ConfigModel();
$lang        = function_exists('current_lang') ? current_lang() : 'es';
$enabled     = (int) $configModel->get('cookies_enabled', 0);
$showBanner  = $enabled && ! has_consent();

$title       = $configModel->get("cookies_banner_title_$lang") ?: $configModel->get('cookies_banner_title', 'Cookies');
$text        = $configModel->get("cookies_banner_text_$lang")  ?: $configModel->get('cookies_banner_text', '');
$policySlug  = $configModel->get('cookies_policy_slug', '');

$en          = ($lang === 'en');
$policyUrl   = $policySlug !== ''
    ? base_url($lang === 'es' ? $policySlug : $lang . '/' . $policySlug)
    : '';
?>

<?php if ($showBanner): ?>

    <!-- Inyectado antes de </body> por el filtro CookieBanner. Vanilla JS (sin depender de jQuery en el front). -->
    <div id="cookieBanner" class="fixed-bottom bg-dark text-white shadow-lg" style="z-index:1070;">
        <div class="container py-3">
            <?php if (!empty($title)): ?>
                <strong class="d-block mb-1"><?= esc($title) ?></strong>
            <?php endif; ?>
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                <div class="small flex-grow-1"><?= nl2br(esc($text)) ?>
                    <?php if (!empty($policyUrl)): ?>
                        <a href="<?= esc($policyUrl) ?>" class="text-warning text-nowrap"><?= $en ? 'More information' : 'Más información' ?></a>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#cookiePrefsModal">
                        <?= $en ? 'Configure' : 'Configurar' ?>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light" id="cookieReject">
                        <?= $en ? 'Reject' : 'Rechazar' ?>
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" id="cookieAcceptAll">
                        <?= $en ? 'Accept all' : 'Aceptar todo' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de preferencias -->
<div class="modal fade" id="cookiePrefsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $en ? 'Cookie preferences' : 'Preferencias de cookies' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" checked disabled id="ccNec">
                    <label class="form-check-label" for="ccNec">
                        <strong><?= $en ? 'Necessary' : 'Necesarias' ?></strong><br>
                        <small class="text-muted"><?= $en ? 'Required for the site to work. Always on.' : 'Imprescindibles para que el sitio funcione. Siempre activas.' ?></small>
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="ccAna">
                    <label class="form-check-label" for="ccAna">
                        <strong><?= $en ? 'Analytics' : 'Analítica' ?></strong><br>
                        <small class="text-muted"><?= $en ? 'Anonymous usage statistics.' : 'Estadísticas de uso anónimas.' ?></small>
                    </label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ccMkt">
                    <label class="form-check-label" for="ccMkt">
                        <strong><?= $en ? 'Marketing' : 'Marketing' ?></strong><br>
                        <small class="text-muted"><?= $en ? 'Personalization and campaigns.' : 'Personalización y campañas.' ?></small>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cookieSavePrefs"><?= $en ? 'Save preferences' : 'Guardar preferencias' ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const BANNER = document.getElementById('cookieBanner');
        const CSRF = {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        };

        // Lee el consentimiento almacenado (cookie 1 año)
        function getConsent() {
            const m = document.cookie.match(/(?:^|;\s*)cookie_consent=([^;]+)/);
            if (!m) return null;
            try {
                return JSON.parse(decodeURIComponent(m[1]));
            } catch (e) {
                return null;
            }
        }

        // Activa los scripts "apagados" de una categoría
        function activate(category) {
            document.querySelectorAll('script[type="text/plain"][data-cookieconsent="' + category + '"]').forEach(function(s) {
                if (s.dataset.activated) return;
                const n = document.createElement('script');
                for (const a of s.attributes) {
                    if (a.name !== 'type') n.setAttribute(a.name, a.value);
                }
                n.text = s.text;
                s.dataset.activated = '1';
                s.replaceWith(n);
            });
        }

        // Al cargar: si ya hay consentimiento, activar lo aceptado
        const consent = getConsent();
        if (Array.isArray(consent)) consent.forEach(activate);

        function hideBanner() {
            if (BANNER) BANNER.remove();
        }

        function save(categories) {
            const fd = new FormData();
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            categories.forEach(c => fd.append('categories[]', c));
            fetch('<?= base_url('cookies/consent') ?>', {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(() => {
                    categories.forEach(activate);
                    hideBanner();
                    const m = document.getElementById('cookiePrefsModal');
                    if (m) bootstrap.Modal.getOrCreateInstance(m).hide();
                })
                .catch(e => console.error('[cookies] error al guardar:', e));
        }

        // Al abrir el modal, reflejar el consentimiento actual en los switches
        const prefsModal = document.getElementById('cookiePrefsModal');
        if (prefsModal) {
            prefsModal.addEventListener('show.bs.modal', () => {
                const c = getConsent();
                const cats = Array.isArray(c) ? c : [];
                const ana = document.getElementById('ccAna');
                const mkt = document.getElementById('ccMkt');
                if (ana) ana.checked = cats.includes('analytics');
                if (mkt) mkt.checked = cats.includes('marketing');
            });
        }

        function on(id, fn) {
            const el = document.getElementById(id);
            if (el) el.addEventListener('click', fn);
        }

        on('cookieAcceptAll', () => save(['analytics', 'marketing']));
        on('cookieReject', () => save([]));
        on('cookieSavePrefs', () => {
            const cats = [];
            const ana = document.getElementById('ccAna');
            const mkt = document.getElementById('ccMkt');
            if (ana && ana.checked) cats.push('analytics');
            if (mkt && mkt.checked) cats.push('marketing');
            save(cats);
        });

        // Exponer getConsent para poder consultarlo desde consola u otros scripts
        window.getConsent = getConsent;
    })();
</script>