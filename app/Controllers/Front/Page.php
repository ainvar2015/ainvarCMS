<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\PageModel;
use App\Models\ThemeModel;
use App\Models\ModuleModel;

class Page extends BaseController
{
    protected $helpers = ['url', 'html', 'menu', 'shortcode', 'i18n'];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->registerShortcodes();
    }

    private function getThemePath(): string
    {
        $themeModel = new ThemeModel();
        $active = $themeModel->where('active', 1)->first();
        return $active['slug'] ?? 'default';
    }

    private function getLanguages(): array
    {
        if (!class_exists(\App\Modules\I18n\Models\LanguageModel::class)) {
            return [];
        }

        try {
            $langModel = new \App\Modules\I18n\Models\LanguageModel();
            return $langModel->where('active', 1)->orderBy('sort_order', 'ASC')->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function home($lang = null)
    {
        $themePath   = $this->getThemePath();
        $configModel = new \App\Models\ConfigModel();
        $defaultLang = $configModel->get('site_language', 'es');
        $currentLang = $lang ?: \current_lang() ?: $defaultLang;

        // === Portada configurable (Settings → General) ===
        // home_page_id = 0  → vista home.php del tema (comportamiento antiguo)
        // home_page_id > 0  → esa página, traducida al idioma actual
        $homePageId = (int) $configModel->get('home_page_id', 0);

        if ($homePageId) {
            $page = (new PageModel())->find($homePageId);
            if ($page && $page['status'] === 'published') {
                return $this->renderPage($page, $currentLang);
            }
        }

        // === Fallback: vista estática del tema ===
        $pageModel  = new PageModel();
        $transModel = new \App\Models\PageTranslationModel();
        $page       = $pageModel->where('slug', 'inicio')->where('status', 'published')->first();

        if ($page && $currentLang !== $defaultLang) {
            $trans = $transModel->where('page_id', $page['id'])->where('lang', $currentLang)->first();
            if ($trans) {
                $page['title']      = $trans['title'];
                $page['content']    = $trans['content'];
                $page['meta_title'] = $trans['meta_title'] ?: $trans['title'];
                $page['meta_desc']  = $trans['meta_desc'];
            }
        }

        return view('front/themes/' . $themePath . '/home', [
            'title'        => $page['meta_title'] ?? ($page['title'] ?? 'Inicio'),
            'meta_desc'    => $page['meta_desc'] ?? null,
            'page'         => $page,
            'base_url'     => base_url(),
            'theme_url'    => base_url('themes/' . $themePath),
            'themePath'    => $themePath,
            'current_lang' => $currentLang,
            'default_lang' => $defaultLang,
            'languages'    => $this->getLanguages(),
        ]);
    }


    public function render($slug = null, $lang = null)
    {
        if (empty($slug)) {
            return $this->home($lang);
        }

        $pageModel   = new PageModel();
        $transModel  = new \App\Models\PageTranslationModel();
        $configModel = new \App\Models\ConfigModel();
        $defaultLang = $configModel->get('site_language', 'es');
        $currentLang = $lang ?: \current_lang() ?: $defaultLang;

        // 1) ¿Es el slug de una página canónica?
        $page = $pageModel->where('slug', $slug)->where('status', 'published')->first();

        // 2) Si no, ¿slug de una traducción en el idioma actual?
        if (!$page) {
            $trans = $transModel->where('slug', $slug)->where('lang', $currentLang)->first();
            if ($trans) {
                $page = $pageModel->where('id', $trans['page_id'])->where('status', 'published')->first();
            }
        }

        if (!$page) {
            return $this->page404($currentLang);
        }

        return $this->renderPage($page, $currentLang);
    }

    /**
     * Pinta una página (normal, módulo o plantilla) con su traducción,
     * shortcodes, slugs para el switcher, etc. Usado por render() y home().
     */
    private function renderPage(array $page, string $currentLang)
    {
        $transModel  = new \App\Models\PageTranslationModel();
        $themePath   = $this->getThemePath();
        $configModel = new \App\Models\ConfigModel();
        $defaultLang = $configModel->get('site_language', 'es');

        // Portada configurable: solo se sirve en la raíz de cada idioma.
        // Si llegan al slug de la página (/inicio, /en/home...), consolidamos con 301.
        $homePageId = (int) $configModel->get('home_page_id', 0);
        if ($homePageId && (int) $page['id'] === $homePageId) {
            $path     = trim((string) service('request')->getPath(), '/');
            $expected = $currentLang === $defaultLang ? '' : $currentLang;

            if ($path !== $expected) {
                return redirect()->to(base_url($expected))->setStatusCode(301);
            }
        }

        // Traducción con fallback por campo
        if ($currentLang !== $defaultLang) {
            $trans = $transModel->where('page_id', $page['id'])->where('lang', $currentLang)->first();
            if ($trans) {
                $page['title']      = $trans['title'];
                if (trim((string) $trans['content']) !== '') {
                    $page['content'] = $trans['content'];
                }
                $page['meta_title'] = $trans['meta_title'] ?: $trans['title'];
                $page['meta_desc']  = $trans['meta_desc'];
            }
        }

        // Páginas-módulo (contenido del módulo como contenido de la página)
        if ($page['type'] === 'module' && empty(trim((string) $page['content'])) && !empty($page['module_name'])) {
            $moduleActive = (new \App\Models\ModuleModel())
                ->where('slug', $page['module_name'])
                ->where('active', 1)
                ->first();

            if ($moduleActive) {
                $moduleName = ucfirst($page['module_name']);
                $ctrlClass  = "\\App\\Modules\\{$moduleName}\\Controllers\\Front\\{$moduleName}";
                if (class_exists($ctrlClass)) {
                    try {
                        $moduleCtrl = new $ctrlClass();
                        $moduleCtrl->initController(
                            \Config\Services::request(),
                            \Config\Services::response(),
                            \Config\Services::logger()
                        );

                        if (property_exists($moduleCtrl, 'embed')) {
                            $moduleCtrl->embed = true;
                        }

                        if (method_exists($moduleCtrl, 'index')) {
                            $page['content'] = $moduleCtrl->index();
                        }
                    } catch (\Throwable $e) {
                        log_message('error', '[renderPage] módulo ' . $page['module_name'] . ': ' . $e->getMessage());
                    }
                }
            }
        }

        // Mapa de slugs para el switcher
        $translationSlugs = [$defaultLang => $page['slug']];
        foreach ($transModel->where('page_id', $page['id'])->findAll() as $t) {
            $translationSlugs[$t['lang']] = $t['slug'];
        }

        // Plantillas
        $templateSlug      = null;
        $templateFieldsDef = [];
        $templateData      = [];

        if ($page['type'] === 'template' && !empty($page['template_id'])) {
            $templateModel = new \App\Models\TemplateModel();
            $template = $templateModel->find($page['template_id']);
            if ($template) {
                $templateSlug      = $template['slug'];
                $templateFieldsDef = json_decode($template['fields'] ?? '[]', true) ?: [];
            }
        }

        if ($page['type'] === 'template' && !empty($page['content'])) {
            $decoded = json_decode($page['content'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $templateData = $decoded;
            }
        }

        if ($page['type'] === 'normal' && !empty($page['content']) && is_string($page['content'])) {
            $page['content'] = do_shortcode($page['content']);
        }

        if ($page['type'] === 'template') {
            $page['content'] = '';
        }

        // Selección de vista
        $view = 'front/themes/' . $themePath . '/page';

        if ($page['type'] === 'template' && $templateSlug) {
            $templateView = 'front/themes/' . $themePath . '/templates/' . $templateSlug;
            if (is_file(APPPATH . 'Views/' . $templateView . '.php')) {
                $view = $templateView;
            } else {
                log_message('debug', '[renderPage] Vista de plantilla NO encontrada: ' . APPPATH . 'Views/' . $templateView . '.php');
            }
        }

        return view($view, [
            'title'             => $page['meta_title'] ?: $page['title'],
            'meta_desc'         => $page['meta_desc'],
            'page'              => $page,
            'base_url'          => base_url(),
            'theme_url'         => base_url('themes/' . $themePath),
            'themePath'         => $themePath,
            'template_slug'     => $templateSlug,
            'template_fields'   => $templateFieldsDef,
            'template_data'     => $templateData,
            'current_lang'      => $currentLang,
            'default_lang'      => $defaultLang,
            'languages'         => $this->getLanguages(),
            'translation_slugs' => $translationSlugs,
        ]);
    }

    private function page404($lang = null)
    {
        $this->response->setStatusCode(404);
        $themePath = $this->getThemePath();
        $configModel = new \App\Models\ConfigModel();
        $defaultLang = $configModel->get('site_language', 'es');
        $currentLang = $lang ?: \current_lang() ?: $defaultLang;

        return view('front/themes/' . $themePath . '/404', [
            'title'        => 'Página no encontrada',
            'base_url'     => base_url(),
            'theme_url'    => base_url('themes/' . $themePath),
            'themePath'    => $themePath,
            'current_lang' => $currentLang,
            'default_lang' => $defaultLang,
            'languages'    => $this->getLanguages(),
        ]);
    }

    public function submitForm()
    {
        $pageId   = (int) $this->request->getPost('page_id');
        $pageSlug = $this->request->getPost('page_slug');

        if (!$pageId || !$pageSlug) {
            return redirect()->back()->with('error', 'Datos inválidos.');
        }

        $pageModel = new PageModel();
        $page = $pageModel->find($pageId);

        if (!$page || $page['type'] !== 'template') {
            return redirect()->back()->with('error', 'Página no válida.');
        }

        $templateModel = new \App\Models\TemplateModel();
        $template = $templateModel->find($page['template_id']);
        if (!$template) {
            return redirect()->back()->with('error', 'Plantilla no encontrada.');
        }

        $fields = json_decode($template['fields'] ?? '[]', true) ?: [];
        $submitted = $this->request->getPost('template_fields') ?: [];

        $submissionModel = new \App\Models\FormSubmissionModel();
        $submissionModel->insert([
            'page_id'    => $pageId,
            'page_slug'  => $pageSlug,
            'data'       => json_encode($submitted, JSON_UNESCAPED_UNICODE),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent(),
        ]);

        $email = \Config\Services::email();
        $body = '<h2>Nuevo envío desde: ' . esc($page['title']) . '</h2>';
        $body .= '<p><strong>Página:</strong> ' . base_url($pageSlug) . '</p>';
        $body .= '<hr><table border="0" cellpadding="8" cellspacing="0" style="font-family:Arial,sans-serif;font-size:14px;">';
        foreach ($fields as $field) {
            $value = $submitted[$field['name']] ?? '(no proporcionado)';
            $body .= '<tr><td style="font-weight:bold;">' . esc($field['label']) . ':</td><td>' . nl2br(esc($value)) . '</td></tr>';
        }
        $body .= '</table>';

        $configModel = new \App\Models\ConfigModel();
        $toEmail = $configModel->get('contact_email', 'admin@ainvarcms.loc');

        $email->setFrom(config('Email')->fromEmail, config('Email')->fromName);
        $email->setTo($toEmail);
        $email->setSubject('Nuevo mensaje: ' . esc($page['title']));
        $email->setMessage($body);
        $email->send();

        return redirect()->to('/' . $pageSlug)->with('success', 'Mensaje enviado correctamente.');
    }

    private function registerShortcodes(): void
    {
        // [productos] - Lee del módulo Catalog si está activo, si no de páginas template
        add_shortcode('productos', function ($attrs, $content, $tag) {
            $moduleModel = new ModuleModel();
            $catalog = $moduleModel->where('slug', 'catalog')->where('active', 1)->first();

            $limit = (int) ($attrs['limite'] ?? $attrs['limit'] ?? 6);

            if ($catalog) {
                // Módulo Catalog activo
                $productModel = new \App\Modules\Catalog\Models\ProductModel();
                $categorySlug = $attrs['categoria'] ?? null;
                $categoryId = null;

                if ($categorySlug) {
                    $catModel = new \App\Modules\Catalog\Models\CategoryModel();
                    $cat = $catModel->where('slug', $categorySlug)->where('active', 1)->first();
                    if ($cat) $categoryId = $cat['id'];
                }

                $products = $productModel->getActive($categoryId, null, $limit);

                if (empty($products)) return '<div class="alert alert-info">No hay productos disponibles.</div>';

                $html = '<div class="row">';
                foreach ($products as $p) {
                    $html .= '<div class="col-md-6 col-lg-4 mb-4">';
                    $html .= '<div class="card h-100 shadow-sm">';
                    if ($p['image_main']) {
                        $html .= '<img src="' . base_url($p['image_main']) . '" class="card-img-top" style="height:200px;object-fit:cover;">';
                    } else {
                        $html .= '<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:200px;"><i class="bi bi-box-seam text-muted" style="font-size:3rem;"></i></div>';
                    }
                    $html .= '<div class="card-body">';
                    $html .= '<h5 class="card-title">' . esc($p['name']) . '</h5>';
                    $html .= '<p class="card-text text-muted small">' . esc($p['short_desc']) . '</p>';
                    $html .= '<p class="card-text"><span class="fw-bold text-primary fs-5">' . number_format($p['price'], 2, ',', '.') . ' €</span>';
                    if ($p['price_old'] && $p['price_old'] > $p['price']) {
                        $html .= ' <span class="text-muted text-decoration-line-through">' . number_format($p['price_old'], 2, ',', '.') . ' €</span>';
                    }
                    $html .= '</p>';
                    $html .= '<a href="' . base_url('catalogo/producto/' . $p['slug']) . '" class="btn btn-outline-primary btn-sm">Ver ficha</a>';
                    $html .= '</div></div></div>';
                }
                return $html . '</div>';
            }

            // Fallback: páginas tipo plantilla "ficha-producto"
            $pageModel = new PageModel();
            $templateModel = new \App\Models\TemplateModel();
            $template = $templateModel->where('slug', 'ficha-producto')->where('active', 1)->first();
            if (!$template) return '<div class="alert alert-warning">No existe catálogo.</div>';

            $productos = $pageModel->where('type', 'template')->where('template_id', $template['id'])
                ->where('status', 'published')->orderBy('sort_order', 'ASC')->limit($limit)->findAll();

            if (empty($productos)) return '<div class="alert alert-info">No hay productos.</div>';

            $html = '<div class="row">';
            foreach ($productos as $p) {
                $data = json_decode($p['content'], true) ?: [];
                $html .= '<div class="col-md-4 mb-4"><div class="card h-100">';
                if (!empty($data['imagen'])) $html .= '<img src="' . base_url($data['imagen']) . '" class="card-img-top" style="height:200px;object-fit:cover;">';
                $html .= '<div class="card-body"><h5>' . esc($p['title']) . '</h5>';
                $html .= '<p class="text-primary fw-bold">' . number_format((float)($data['precio'] ?? 0), 2, ',', '.') . ' €</p>';
                $html .= '<a href="' . base_url($p['slug']) . '" class="btn btn-sm btn-outline-primary">Ver</a>';
                $html .= '</div></div></div>';
            }
            return $html . '</div>';
        });

        // [contacto_form]
        add_shortcode('contacto_form', function ($attrs, $content, $tag) {
            $titulo = $attrs['titulo'] ?? 'Envíanos un mensaje';
            return view('front/shortcodes/contacto_form', ['titulo' => $titulo, 'base_url' => base_url()]);
        });

        // [galeria album="vacaciones" limite=8]
        add_shortcode('galeria', function ($attrs, $content, $tag) {
            $moduleModel = new \App\Models\ModuleModel();
            $gallery = $moduleModel->where('slug', 'gallery')->where('active', 1)->first();

            if (!$gallery) {
                return '<div class="alert alert-secondary"><i class="bi bi-images"></i> Módulo de galería no instalado.</div>';
            }

            $imageModel = new \App\Modules\Gallery\Models\ImageModel();
            $album = $attrs['album'] ?? null;
            $limit = (int) ($attrs['limite'] ?? $attrs['limit'] ?? 6);

            $images = $imageModel->getActive($album, $limit);

            if (empty($images)) {
                return '<div class="alert alert-info">No hay imágenes en la galería.</div>';
            }

            $html = '<div class="row g-3">';
            foreach ($images as $img) {
                $html .= '<div class="col-md-4 col-lg-3">';
                $html .= '<a href="' . base_url($img['file_path']) . '" class="d-block" data-fancybox="shortcode-gallery" data-caption="' . esc($img['title']) . '">';
                $html .= '<img src="' . base_url($img['file_path']) . '" class="img-fluid rounded" style="width:100%;height:200px;object-fit:cover;">';
                $html .= '</a></div>';
            }
            $html .= '</div>';

            // Cargar Fancybox solo si hay imágenes
            $html .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />';
            $html .= '<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>';
            $html .= '<script>Fancybox.bind("[data-fancybox]", {});</script>';

            return $html;
        });

        /**
         * [noticias limite="3"] — Últimas noticias en tarjetas
         */
        add_shortcode('noticias', function ($attrs, $content, $tag) {
            $limit    = max(1, (int) ($attrs['limite'] ?? 3));
            $articles = (new \App\Modules\News\Models\NewsModel())->getPublished(null, $limit);
            if (empty($articles)) return '';

            // URL base de la página-módulo en el idioma actual
            $page = (new \App\Models\PageModel())
                ->where('type', 'module')->where('module_name', 'news')->where('status', 'published')->first();
            $pb = $page['slug'] ?? 'noticias';
            $cur = current_lang();
            $basePath = ($cur !== default_lang() ? $cur . '/' : '') . $pb;

            ob_start(); ?>
            <div class="row g-4">
                <?php foreach ($articles as $a): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($a['image_main'])): ?>
                                <img src="<?= thumb_url($a['image_main']) ?>" class="card-img-top" style="height:200px;object-fit:cover;" alt="<?= esc(news_field($a, 'title')) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <small class="text-muted"><?= news_date($a['published_at']) ?></small>
                                <h3 class="h6 mt-1"><?= esc(news_field($a, 'title')) ?></h3>
                                <a href="<?= base_url($basePath . '/noticia/' . $a['slug']) ?>" class="btn btn-outline-primary btn-sm"><?= lang('News.read_more') ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
            return ob_get_clean();
        });

        add_shortcode('faq', function ($attrs, $content, $tag) {
            try {
                $items = (new \App\Modules\Faq\Models\FaqModel())->getActive();
            } catch (\Throwable $e) {
                return '';
            }
            if (empty($items)) return '';

            ob_start();
            echo view('App\Modules\Faq\Views\front\_accordion', ['items' => $items]);
            return ob_get_clean();
        });

        add_shortcode('formulario', function ($attrs, $content, $tag) {
            $slug = $attrs['slug'] ?? ($attrs['id'] ?? null);
            if (!$slug) return '<p class="text-danger">[formulario] necesita slug="..."</p>';

            $form = (new \App\Modules\Forms\Models\FormModel())->where('slug', $slug)->where('active', 1)->first();
            if (!$form) return '';

            $fields = (new \App\Modules\Forms\Models\FormFieldModel())->getByForm((int) $form['id']);

            $okKey  = 'form_success_' . $form['slug'];
            $errKey = 'form_error_' . $form['slug'];
            $success = session()->getFlashdata($okKey);
            $error   = session()->getFlashdata($errKey);
            $errors  = session()->getFlashdata('errors') ?: [];
            $old     = old();

            ob_start(); ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= esc($success) ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= esc($error) ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('forms/submit/' . $form['slug']) ?>" method="post" class="needs-validation" novalidate>
                        <?= csrf_field() ?>
                        <p class="text-muted small"><?= lang('Forms.required') ?></p>

                        <?php foreach ($fields as $f):
                            $val   = $old[$f['name']] ?? '';
                            $err   = $errors[$f['name']] ?? '';
                            $req   = $f['required'] ? 'required' : '';
                            $reqMk = $f['required'] ? ' <span class="text-danger">*</span>' : '';
                            $id    = 'ff_' . $f['name'];
                        ?>
                            <div class="mb-3">
                                <label for="<?= $id ?>" class="form-label"><?= esc($f['label']) ?><?= $reqMk ?></label>

                                <?php if ($f['type'] === 'textarea'): ?>
                                    <textarea name="<?= $f['name'] ?>" id="<?= $id ?>" class="form-control <?= $err ? 'is-invalid' : '' ?>" rows="4" <?= $req ?>><?= esc($val) ?></textarea>

                                <?php elseif ($f['type'] === 'select'):
                                    $opts = array_filter(array_map('trim', explode("\n", (string) $f['options']))); ?>
                                    <select name="<?= $f['name'] ?>" id="<?= $id ?>" class="form-select <?= $err ? 'is-invalid' : '' ?>" <?= $req ?>>
                                        <option value="">— Selecciona —</option>
                                        <?php foreach ($opts as $o): ?>
                                            <option value="<?= esc($o) ?>" <?= $val === $o ? 'selected' : '' ?>><?= esc($o) ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php elseif ($f['type'] === 'checkbox'): ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="<?= $f['name'] ?>" value="1" id="<?= $id ?>" class="form-check-input <?= $err ? 'is-invalid' : '' ?>" <?= $val ? 'checked' : '' ?> <?= $req ?>>
                                        <label class="form-check-label" for="<?= $id ?>"><?= esc($f['label']) ?></label>
                                    </div>

                                <?php else:
                                    $inputType = in_array($f['type'], ['email', 'number', 'date'], true) ? $f['type'] : 'text'; ?>
                                    <input type="<?= $inputType ?>" name="<?= $f['name'] ?>" id="<?= $id ?>"
                                        class="form-control <?= $err ? 'is-invalid' : '' ?>"
                                        value="<?= esc($val) ?>" <?= $req ?>>
                                <?php endif; ?>

                                <?php if ($err): ?><div class="invalid-feedback"><?= esc($err) ?></div><?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Honeypot anti-spam (oculto) -->
                        <div style="position:absolute;left:-9999px" aria-hidden="true">
                            <label>No rellenar<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> <?= lang('Forms.submit') ?></button>
                    </form>
                </div>
            </div>
<?php
            return ob_get_clean();
        });

        add_shortcode('faq', function ($atts) {
            $slug  = $atts['categoria'] ?? null;
            $limit = (int) ($atts['limite'] ?? 0);
            $faqs  = (new \App\Modules\Faq\Models\FaqModel())->getForFront($slug ?: null, $limit);
            return view('App\Modules\Faq\Views\_grid', ['faqs' => $faqs, 'agrupar' => empty($slug)]);
        });

        // [boton url="/ruta" clase="btn-primary"]Texto[/boton]
        add_shortcode('boton', function ($attrs, $content, $tag) {
            $url   = $attrs['url'] ?? '#';
            $class = $attrs['clase'] ?? $attrs['class'] ?? 'btn btn-primary';
            $texto = trim($content ?? 'Click aquí');
            return '<a href="' . esc($url) . '" class="' . esc($class) . '">' . esc($texto) . '</a>';
        });
    }
}
