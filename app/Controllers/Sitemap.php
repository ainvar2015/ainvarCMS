<?php

namespace App\Controllers;

use App\Models\PageModel;
use App\Models\PageTranslationModel;
use App\Models\ModuleModel;
use App\Models\ConfigModel;
use App\Modules\I18n\Models\LanguageModel;

class Sitemap extends BaseController
{
    private string $defaultLang;
    private array $languages;   // idiomas activos

    public function __construct()
    {
        $this->defaultLang = (new ConfigModel())->get('site_language', 'es');
        $this->languages   = (new LanguageModel())->where('active', 1)->orderBy('sort_order', 'ASC')->findAll();
    }

    /** Alternativas hreflang para una ruta dada en el idioma por defecto (mismo slug en todos) */
    private function alternates(string $defaultPath): array
    {
        $alts = [];
        foreach ($this->languages as $l) {
            $code = $l['code'];
            $path = $code === $this->defaultLang ? $defaultPath : $code . '/' . $defaultPath;
            $alts[] = ['hreflang' => $code, 'href' => base_url($path)];
        }
        $alts[] = ['hreflang' => 'x-default', 'href' => base_url($defaultPath)];
        return $alts;
    }

    /** Alternativas hreflang para una página con slugs traducidos */
    private function pageAlternates(array $page): array
    {
        $slugs = [$this->defaultLang => $page['slug']];
        foreach ((new PageTranslationModel())->where('page_id', $page['id'])->findAll() as $t) {
            $slugs[$t['lang']] = $t['slug'];
        }

        $alts = [];
        foreach ($slugs as $code => $slug) {
            $path = $code === $this->defaultLang ? $slug : $code . '/' . $slug;
            $alts[] = ['hreflang' => $code, 'href' => base_url($path)];
        }
        $alts[] = ['hreflang' => 'x-default', 'href' => base_url($page['slug'])];
        return $alts;
    }

    /** Slugs de la página-módulo en cada idioma: [lang => slug] */
    private function modulePageSlugs(string $moduleName): array
    {
        $page = (new PageModel())
            ->where('type', 'module')
            ->where('module_name', $moduleName)
            ->where('status', 'published')
            ->first();

        if (!$page) {
            return [$this->defaultLang => $moduleName]; // fallback: el nombre del módulo
        }

        $slugs = [$this->defaultLang => $page['slug']];
        foreach ((new PageTranslationModel())->where('page_id', $page['id'])->findAll() as $t) {
            $slugs[$t['lang']] = $t['slug'];
        }
        return $slugs;
    }

    /** URL de un idioma concreto a partir de un array [lang => slug] */
    private function langUrlFromSlugs(array $slugs, string $code): string
    {
        $slug = $slugs[$code] ?? $slugs[$this->defaultLang];
        return base_url($code === $this->defaultLang ? $slug : $code . '/' . $slug);
    }

    public function index()
    {
        log_message('debug', '[sitemap] index ejecutado');
        $today = date('Y-m-d');
        $urls  = [];

        $homePageId = (int) (new ConfigModel())->get('home_page_id', 0);

        // Home
        $urls[] = [
            'loc'        => base_url(),
            'alternates' => $this->alternates(''),
            'lastmod'    => $today,
            'changefreq' => 'weekly',
            'priority'   => '1.0',
        ];

        // Páginas publicadas
        $pages = (new PageModel())->where('status', 'published')->findAll();
        foreach ($pages as $p) {
            // La portada configurada ya sale como Home: no duplicar
            if ($homePageId && (int) $p['id'] === $homePageId) {
                continue;
            }
            // Las páginas-módulo las añade su propia sección (catálogo/galería): no duplicar
            if ($p['type'] === 'module') {
                continue;
            }
            if ($p['slug'] === '' || $p['slug'] === '/') {
                continue;
            }
            $urls[] = [
                'loc'        => base_url($p['slug']),
                'alternates' => $this->pageAlternates($p),
                'lastmod'    => !empty($p['updated_at']) ? date('Y-m-d', strtotime($p['updated_at'])) : $today,
                'changefreq' => 'monthly',
                'priority'   => '0.8',
            ];
        }

        // === CATÁLOGO (solo si el módulo está activo) ===
        try {
            $catalog = (new ModuleModel())->where('slug', 'catalog')->where('active', 1)->first();
            if ($catalog) {
                $catSlugs = $this->modulePageSlugs('catalog');

                $urls[] = [
                    'loc'        => $this->langUrlFromSlugs($catSlugs, $this->defaultLang),
                    'alternates' => array_map(
                        fn($l) => ['hreflang' => $l['code'], 'href' => $this->langUrlFromSlugs($catSlugs, $l['code'])],
                        $this->languages
                    ),
                    'lastmod'    => $today,
                    'changefreq' => 'weekly',
                    'priority'   => '0.9',
                ];

                // Categorías
                $cats = (new \App\Modules\Catalog\Models\CategoryModel())
                    ->where('active', 1)->findAll();
                foreach ($cats as $c) {
                    $path = $catSlugs[$this->defaultLang] . '/categoria/' . $c['slug'];
                    $urls[] = [
                        'loc'        => base_url($path),
                        'alternates' => $this->alternates($path),
                        'lastmod'    => $today,
                        'changefreq' => 'weekly',
                        'priority'   => '0.7',
                    ];
                }

                // Productos activos
                $products = (new \App\Modules\Catalog\Models\ProductModel())->getActive();
                foreach ($products as $prod) {
                    $lastmod = !empty($prod['updated_at']) ? date('Y-m-d', strtotime($prod['updated_at']))
                        : (!empty($prod['created_at']) ? date('Y-m-d', strtotime($prod['created_at'])) : $today);
                    $path = $catSlugs[$this->defaultLang] . '/producto/' . $prod['slug'];
                    $urls[] = [
                        'loc'        => base_url($path),
                        'alternates' => $this->alternates($path),
                        'lastmod'    => $lastmod,
                        'changefreq' => 'weekly',
                        'priority'   => '0.8',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Módulo no instalado o error: se omite del sitemap
        }

        // === GALERÍA (solo si el módulo está activo) ===
        try {
            $gallery = (new ModuleModel())->where('slug', 'gallery')->where('active', 1)->first();
            if ($gallery) {
                $galSlugs = $this->modulePageSlugs('gallery');

                $urls[] = [
                    'loc'        => $this->langUrlFromSlugs($galSlugs, $this->defaultLang),
                    'alternates' => array_map(
                        fn($l) => ['hreflang' => $l['code'], 'href' => $this->langUrlFromSlugs($galSlugs, $l['code'])],
                        $this->languages
                    ),
                    'lastmod'    => $today,
                    'changefreq' => 'monthly',
                    'priority'   => '0.8',
                ];

                $albums = (new \App\Modules\Gallery\Models\ImageModel())->getAlbums();
                foreach ($albums as $alb) {
                    $albumName = is_array($alb) ? ($alb['album'] ?? $alb) : $alb;
                    if (!$albumName) continue;

                    $path = $galSlugs[$this->defaultLang] . '/album/' . urlencode($albumName);
                    $urls[] = [
                        'loc'        => base_url($path),
                        'alternates' => $this->alternates($path),
                        'lastmod'    => $today,
                        'changefreq' => 'monthly',
                        'priority'   => '0.6',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Se omite
        }

        return $this->response
            ->setContentType('application/xml', 'utf-8')
            ->setBody('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . view('sitemap_xml', ['urls' => $urls]));
    }
}
