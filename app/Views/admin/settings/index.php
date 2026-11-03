<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Configuración</h1>
</div>


<form action="<?= base_url('admin/settings/save') ?>" method="post">
    <?= csrf_field() ?>

    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="pill" href="#tab-general">General</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#tab-company">Empresa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#tab-seo">SEO</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#tab-social">Redes sociales</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#tab-cookies">Cookies</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- GENERAL -->
        <div class="tab-pane fade show active" id="tab-general">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del sitio</label>
                        <input type="text" name="site_name" class="form-control" value="<?= esc($site_name) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Idioma por defecto</label>
                        <input type="text" name="site_language" class="form-control" value="<?= esc($site_language) ?>" maxlength="2">
                        <div class="form-text">Código ISO de 2 letras (es, en, fr...). Debe existir en los idiomas activos.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Página de portada</label>
                        <select name="home_page_id" class="form-select">
                            <option value="0" <?= (int) old('home_page_id', $home_page_id ?? 0) === 0 ? 'selected' : '' ?>>
                                — Portada del tema (home.php) —
                            </option>
                            <?php foreach ((new \App\Models\PageModel())->where('status', 'published')->orderBy('title', 'ASC')->findAll() as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (int) old('home_page_id', $home_page_id ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['title']) ?> (<?= esc($p['slug']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            La misma página se usa como portada en todos los idiomas; cada idioma muestra su traducción.
                        </div>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input type="checkbox" class="form-check-input" id="maintenanceMode" name="maintenance_mode" value="1"
                            <?= (int) old('maintenance_mode', $maintenance_mode ?? 0) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="maintenanceMode">Modo mantenimiento</label>
                        <div class="form-text">
                            Los visitantes verán una página de "en mantenimiento" (503). Los administradores ven el sitio con normalidad.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email de contacto</label>
                        <input type="email" name="contact_email" class="form-control" value="<?= esc($contact_email) ?>">
                        <div class="form-text">Aquí llegarán los mensajes de los formularios del front.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bloqueo por inactividad (minutos)</label>
                        <input type="number" name="lock_timeout" class="form-control" value="<?= esc($lock_timeout ?? '15') ?>" min="1" max="120">
                        <div class="form-text">Tras este tiempo sin actividad, la pantalla se bloquea y pedirá el PIN.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EMPRESA -->
        <div class="tab-pane fade" id="tab-company">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Denominación social</label>
                            <input type="text" name="company_name" class="form-control" value="<?= esc($company_name) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">NIF</label>
                            <input type="text" name="company_nif" class="form-control" value="<?= esc($company_nif) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="company_address" class="form-control" value="<?= esc($company_address) ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Población</label>
                            <input type="text" name="company_city" class="form-control" value="<?= esc($company_city) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Provincia</label>
                            <input type="text" name="company_province" class="form-control" value="<?= esc($company_province) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Código postal</label>
                            <input type="text" name="company_zip" class="form-control" value="<?= esc($company_zip) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="company_phone" class="form-control" value="<?= esc($company_phone) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email de la empresa</label>
                            <input type="email" name="company_email" class="form-control" value="<?= esc($company_email) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO -->
        <div class="tab-pane fade" id="tab-seo">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Meta description por defecto</label>
                        <textarea name="site_meta_desc" class="form-control" rows="3" maxlength="160"><?= esc($site_meta_desc) ?></textarea>
                        <div class="form-text">Máximo 160 caracteres. Se usa cuando una página no define la suya.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SOCIAL -->
        <div class="tab-pane fade" id="tab-social">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Facebook</label>
                        <input type="url" name="social_facebook" class="form-control" value="<?= esc($social_facebook) ?>" placeholder="https://facebook.com/tuweb">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Instagram</label>
                        <input type="url" name="social_instagram" class="form-control" value="<?= esc($social_instagram) ?>" placeholder="https://instagram.com/tuweb">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bluesky</label>
                        <input type="url" name="social_bluesky" class="form-control" value="<?= esc($social_bluesky) ?>" placeholder="https://bsky.app/profile/tuweb">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">X / Twitter</label>
                        <input type="url" name="social_x" class="form-control" value="<?= esc($social_x) ?>" placeholder="https://x.com/tuweb">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">YouTube</label>
                        <input type="url" name="social_youtube" class="form-control" value="<?= esc($social_youtube) ?>" placeholder="https://youtube.com/@tuweb">
                    </div>
                    <div class="form-text">Estos enlaces los puedes mostrar en el footer del tema leyéndolos con <code>ConfigModel</code>.</div>
                </div>
            </div>
        </div>

        <!-- COOKIES / GDPR -->
        <div class="tab-pane fade" id="tab-cookies">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3 form-check form-switch">
                        <input type="hidden" name="cookies_enabled" value="0">
                        <input type="checkbox" class="form-check-input" id="cookiesEnabled" name="cookies_enabled" value="1"
                            <?= (int) ($cookies_enabled ?? 0) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="cookiesEnabled">Banner de cookies activo</label>
                        <div class="form-text">Se muestra a los visitantes que aún no han respondido. Sin activar, el gestor no interviene en el front.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Título del banner (ES)</label>
                            <input type="text" name="cookies_banner_title_es" class="form-control" value="<?= esc($cookies_banner_title_es ?? 'Usamos cookies') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Título del banner (EN)</label>
                            <input type="text" name="cookies_banner_title_en" class="form-control" value="<?= esc($cookies_banner_title_en ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Texto del banner (ES)</label>
                            <textarea name="cookies_banner_text_es" class="form-control" rows="3"><?= esc($cookies_banner_text_es ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Texto del banner (EN)</label>
                            <textarea name="cookies_banner_text_en" class="form-control" rows="3"><?= esc($cookies_banner_text_en ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Slug de la página de política de cookies</label>
                        <input type="text" name="cookies_policy_slug" class="form-control" value="<?= esc($cookies_policy_slug ?? '') ?>" placeholder="politica-de-cookies">
                        <div class="form-text">Debe ser una página <b>publicada</b> del CMS; aparece como enlace «Más información» en el banner. Créala antes de activar el banner.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Scripts de terceros (head)</label>
                        <textarea name="cookies_head_scripts" class="form-control font-monospace" rows="5"><?= old('cookies_head_scripts', $cookies_head_scripts ?? '') ?></textarea>
                        <div class="form-text">
                            Pégalo <strong>apagado</strong>: <code>&lt;script type="text/plain" data-cookieconsent="analitica"&gt;...&lt;/script&gt;</code>.
                            Categorías: <code>analitica</code>, <code>marketing</code>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">
        <i class="bi bi-check-lg"></i> Guardar ajustes
    </button>
</form>
<?php $this->endSection() ?>