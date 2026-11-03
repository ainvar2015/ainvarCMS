<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\PageModel;
use App\Models\PageTranslationModel;

class Search extends BaseController
{
    protected $helpers = ['url', 'html', 'menu', 'i18n', 'thumb'];

    public function index($lang = null)
    {
        $configModel = new \App\Models\ConfigModel();
        $defaultLang = $configModel->get('site_language', 'es');
        $currentLang = $lang ?: \current_lang() ?: $defaultLang;

        $q = trim((string) $this->request->getGet('q'));
        $q = mb_substr(strip_tags($q), 0, 100);

        $results = ['pages' => [], 'news' => [], 'products' => []];
        if (mb_strlen($q) >= 2) {
            $results['pages']    = $this->searchPages($q, $currentLang, $defaultLang);
            $results['news']     = $this->searchNews($q, $currentLang, $defaultLang);
            $results['products'] = $this->searchProducts($q, $currentLang, $defaultLang);
        }

        $themePath = (new \App\Models\ThemeModel())->where('active', 1)->first()['slug'] ?? 'default';

        return view('front/search', [
            'title'        => $currentLang === 'en' ? 'Search' : 'Búsqueda',
            'q'            => $q,
            'results'      => $results,
            'total'        => array_sum(array_map('count', $results)),
            'themePath'    => $themePath,
            'base_url'     => base_url(),
            'theme_url'    => base_url('themes/' . $themePath),
            'current_lang' => $currentLang,
            'default_lang' => $defaultLang,
            'languages'    => $this->getLanguages(),
        ]);
    }

    /** Trozo de texto alrededor de la coincidencia */
    private function excerpt(?string $text, string $q, int $len = 160): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));
        $pos  = mb_stripos($text, $q);
        if ($pos === false) return mb_substr($text, 0, $len) . (mb_strlen($text) > $len ? '…' : '');
        $start = max(0, $pos - (int)($len / 3));
        $out   = mb_substr($text, $start, $len);
        return ($start > 0 ? '…' : '') . $out . (mb_strlen($text) > $start + $len ? '…' : '');
    }

    private function prefix(string $lang, string $default): string
    {
        return $lang !== $default ? $lang . '/' : '';
    }

    private function searchPages(string $q, string $lang, string $default): array
    {
        $pageModel  = new PageModel();
        $transModel = new PageTranslationModel();
        $found = [];

        // Canónicas publicadas
        $rows = $pageModel->where('status', 'published')
            ->groupStart()->like('title', $q)->orLike('content', $q)->groupEnd()
            ->limit(20)->findAll();
        foreach ($rows as $p) $found[$p['id']] = $p;

        // Traducciones
        foreach ($transModel->groupStart()->like('title', $q)->orLike('content', $q)->groupEnd()->findAll() as $t) {
            if (!isset($found[$t['page_id']])) {
                $p = $pageModel->where('id', $t['page_id'])->where('status', 'published')->first();
                if ($p) $found[$p['id']] = $p;
            }
        }

        $out = [];
        foreach ($found as $p) {
            // Título y slug en el idioma actual
            $title = $p['title'];
            $slug  = $p['slug'];
            if ($lang !== $default) {
                $t = $transModel->where('page_id', $p['id'])->where('lang', $lang)->first();
                if ($t) {
                    if (!empty($t['title'])) $title = $t['title'];
                    if (!empty($t['slug']))  $slug  = $t['slug'];
                }
            }
            if ($slug === '' || $slug === '/') continue;

            $out[] = [
                'title'   => $title,
                'url'     => base_url($this->prefix($lang, $default) . $slug),
                'excerpt' => $this->excerpt($p['content'], $q),
            ];
        }
        return $out;
    }

    private function searchNews(string $q, string $lang, string $default): array
    {
        try {
            $newsModel  = new \App\Modules\News\Models\NewsModel();
            $transModel = new \App\Modules\News\Models\ArticleTranslationModel();

            $found = [];
            $rows = $newsModel->where('active', 1)
                ->where('published_at <=', date('Y-m-d H:i:s'))
                ->groupStart()->like('title', $q)->orLike('summary', $q)->orLike('content', $q)->groupEnd()
                ->limit(20)->findAll();
            foreach ($rows as $a) $found[$a['id']] = $a;

            foreach ($transModel->groupStart()->like('title', $q)->orLike('summary', $q)->orLike('content', $q)->groupEnd()->findAll() as $t) {
                if (!isset($found[$t['article_id']])) {
                    $a = $newsModel->where('id', $t['article_id'])->where('active', 1)
                        ->where('published_at <=', date('Y-m-d H:i:s'))->first();
                    if ($a) $found[$a['id']] = $a;
                }
            }

            // Base de la página-módulo de noticias (slug traducido)
            $page = (new PageModel())->where('type', 'module')->where('module_name', 'news')->where('status', 'published')->first();
            $pb = $page['slug'] ?? 'noticias';
            if ($lang !== $default) {
                $t = (new PageTranslationModel())->where('page_id', $page['id'])->where('lang', $lang)->first();
                if ($t && !empty($t['slug'])) $pb = $t['slug'];
            }

            $out = [];
            foreach ($found as $a) {
                $out[] = [
                    'title'   => news_field($a, 'title'),
                    'url'     => base_url($this->prefix($lang, $default) . $pb . '/noticia/' . $a['slug']),
                    'excerpt' => $this->excerpt(news_field($a, 'summary') ?: news_field($a, 'content'), $q),
                    'date'    => $a['published_at'],
                ];
            }
            return $out;
        } catch (\Throwable $e) {
            return [];   // módulo no instalado
        }
    }

    private function searchProducts(string $q, string $lang, string $default): array
    {
        try {
            $model = new \App\Modules\Catalog\Models\ProductModel();
            $rows  = $model->where('active', 1)
                ->groupStart()->like('name', $q)->orLike('short_desc', $q)->orLike('description', $q)->groupEnd()
                ->limit(20)->findAll();

            $page = (new PageModel())->where('type', 'module')->where('module_name', 'catalog')->where('status', 'published')->first();
            $pb = $page['slug'] ?? 'catalogo';
            if ($lang !== $default) {
                $t = (new PageTranslationModel())->where('page_id', $page['id'])->where('lang', $lang)->first();
                if ($t && !empty($t['slug'])) $pb = $t['slug'];
            }

            $out = [];
            foreach ($rows as $p) {
                $out[] = [
                    'title'   => $p['name'],
                    'url'     => base_url($this->prefix($lang, $default) . $pb . '/producto/' . $p['slug']),
                    'excerpt' => $this->excerpt(($p['short_desc'] ?: $p['description']) ?? '', $q),
                    'price'   => $p['price'],
                ];
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getLanguages(): array
    {
        try {
            return (new \App\Modules\I18n\Models\LanguageModel())->where('active', 1)->orderBy('sort_order', 'ASC')->findAll();
        } catch (\Exception $e) {
            return [];
        }
    }
}