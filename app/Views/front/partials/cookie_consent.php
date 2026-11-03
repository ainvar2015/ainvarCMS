<?php
helper('cookies');
$configModel = new \App\Models\ConfigModel();
$lang       = function_exists('current_lang') ? current_lang() : 'es';
$enabled    = (int) $configModel->get('cookies_enabled', 0);
$showBanner = $enabled && !has_consent();

$title      = $configModel->get("cookies_banner_title_$lang") ?: $configModel->get('cookies_banner_title', 'Cookies');
$text       = $configModel->get("cookies_banner_text_$lang")  ?: $configModel->get('cookies_banner_text', '');
$policySlug = $configModel->get('cookies_policy_slug', '');
?>

<?php if ($showBanner): ?>
    <!-- Banner: solo cuando no hay consentimiento previo -->
    <div id="cookieBanner" class="cookie-banner card shadow-lg border-0 rounded-0 fixed-bottom mb-0" role="dialog" aria-live="polite" aria-label="<?= esc($title) ?>">
        <div class="card-body container py-3">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <strong><?= esc($title) ?></strong>
                    <p class="mb-0 small"><?= esc($text) ?>
                        <?php if ($policySlug): ?>
                            <a href="<?= base_url($policySlug) ?>"><?= lang('Cookies.policy_link') ?></a>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="cookieConfigure"><?= lang('Cookies.configure') ?></button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="cookieReject"><?= lang('Cookies.reject') ?></button>
                    <button type="button" class="btn btn-sm btn-primary" id="cookieAccept"><?= lang('Cookies.accept') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de preferencias: SIEMPRE disponible (retirar consentimiento) -->
<div class="modal fade" id="cookieModal" tabindex="-1" aria-labelledby="cookieModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cookieModalLabel"><?= lang('Cookies.modal_title') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" checked disabled id="swNecessary">
                    <label class="form-check-label" for="swNecessary"><?= lang('Cookies.necessary') ?></label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="swAnalytics">
                    <label class="form-check-label" for="swAnalytics"><?= lang('Cookies.analytics') ?></label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="swMarketing">
                    <label class="form-check-label" for="swMarketing"><?= lang('Cookies.marketing') ?></label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="cookieSaveChoice"><?= lang('Cookies.save_choice') ?></button>
                <button type="button" class="btn btn-primary" id="cookieAcceptAll"><?= lang('Cookies.accept_all') ?></button>
            </div>
        </div>
    </div>
</div>

<style>
    .cookie-banner {
        z-index: 1080;
    }
</style>

<script>
    window.CC_BASE = '<?= base_url() ?>';
    window.CC_CSRF = '<?= csrf_hash() ?>';
</script>
<script src="<?= base_url('assets/js/cookie-consent.js') ?>"></script>