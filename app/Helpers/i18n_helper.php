<?php

if (!function_exists('active_languages')) {
    // Idiomas activos, cacheados en la petición
    function active_languages(): array
    {
        static $langs = null;
        if ($langs === null) {
            try {
                $langs = (new \App\Modules\I18n\Models\LanguageModel())
                    ->where('active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
            } catch (\Throwable $e) {
                $langs = [['code' => 'es', 'name' => 'Español', 'flag' => '🇪🇸']];
            }
        }
        return $langs;
    }
}

if (!function_exists('default_lang')) {
    function default_lang(): string
    {
        try {
            return (new \App\Models\ConfigModel())->get('site_language', 'es');
        } catch (\Throwable $e) {
            return 'es';
        }
    }
}

if (!function_exists('current_lang')) {
    function current_lang(): string
    {
        $seg = service('uri')->getSegment(1);
        foreach (active_languages() as $l) {
            if ($l['code'] === $seg) {
                return $seg;
            }
        }
        return default_lang();
    }
}

// URL con el prefijo del idioma ACTUAL: localized_url('quienes-somos') → /en/quienes-somos (o sin prefijo si es el default)
if (!function_exists('localized_url')) {
    function localized_url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        $lang = current_lang();
        if ($lang === default_lang()) {
            return base_url($path);
        }
        return base_url($lang . '/' . $path);
    }
}

// Mantiene la ruta actual cambiando solo el idioma (para URLs no traducibles: catálogo, galería…)
if (!function_exists('lang_url')) {
    function lang_url(string $lang): string
    {
        $path     = trim(service('uri')->getPath(), '/');
        $segments = explode('/', $path);

        // Quitar prefijo de idioma actual si existe
        foreach (active_languages() as $l) {
            if (($segments[0] ?? '') === $l['code']) {
                array_shift($segments);
                break;
            }
        }

        $newPath = implode('/', $segments);
        if ($lang === default_lang()) {
            return base_url($newPath);
        }
        return base_url($lang . '/' . $newPath);
    }

    if (!function_exists('album_title')) {
        /** Nombre visible de un álbum en el idioma actual (fallback al nombre canónico) */
        function album_title(string $album): string
        {
            $lang = current_lang();
            if ($lang === default_lang()) return $album;

            static $cache = [];
            if (!array_key_exists($album, $cache)) {
                $t = (new \App\Modules\Gallery\Models\AlbumTranslationModel())
                    ->where('album', $album)->where('lang', $lang)->first();
                $cache[$album] = $t['title'] ?? null;
            }
            return $cache[$album] ?: $album;
        }
    }

    if (!function_exists('category_name')) {
        /** Nombre visible de una categoría (array con id/name o id) en el idioma actual */
        function category_name($category): string
        {
            if (is_numeric($category)) {
                $category = (new \App\Modules\Catalog\Models\CategoryModel())->find($category);
            }
            if (!$category) return '';

            $lang = current_lang();
            if ($lang === default_lang()) return $category['name'];

            static $cache = [];
            $id = (int) $category['id'];
            if (!array_key_exists($id, $cache)) {
                $t = (new \App\Modules\Catalog\Models\CategoryTranslationModel())
                    ->where('category_id', $id)->where('lang', $lang)->first();
                $cache[$id] = $t['name'] ?? null;
            }
            return $cache[$id] ?: $category['name'];
        }
    }

    if (!function_exists('news_field')) {
        /** Campo de artículo (title|summary|content) traducido con fallback por campo al canónico */
        function news_field(array $article, string $field): string
        {
            $lang = current_lang();
            if ($lang === default_lang()) return (string) ($article[$field] ?? '');

            static $cache = [];
            $id = (int) $article['id'];
            if (!array_key_exists($id, $cache)) {
                $t = (new \App\Modules\News\Models\ArticleTranslationModel())
                    ->where('article_id', $id)->where('lang', $lang)->first();
                $cache[$id] = $t ?? [];
            }
            $val = trim((string) ($cache[$id][$field] ?? ''));
            return $val !== '' ? $val : (string) ($article[$field] ?? '');
        }
    }

    if (!function_exists('news_category_name')) {
        /** Nombre de categoría de noticias traducido (array o id), fallback al canónico */
        function news_category_name($category): string
        {
            if (is_numeric($category)) {
                $category = (new \App\Modules\News\Models\NewsCategoryModel())->find($category);
            }
            if (!$category) return '';

            $lang = current_lang();
            if ($lang === default_lang()) return $category['name'];

            static $cache = [];
            $id = (int) $category['id'];
            if (!array_key_exists($id, $cache)) {
                $t = (new \App\Modules\News\Models\NewsCategoryTranslationModel())
                    ->where('category_id', $id)->where('lang', $lang)->first();
                $cache[$id] = $t['name'] ?? null;
            }
            return $cache[$id] ?: $category['name'];
        }
    }

    if (!function_exists('news_date')) {
        /** Fecha de publicación localizada (larga) con fallback a d/m/Y */
        function news_date(?string $datetime): string
        {
            if (!$datetime) return '';
            $ts = strtotime($datetime);
            if (class_exists('\IntlDateFormatter')) {
                $fmt = new \IntlDateFormatter(current_lang(), \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
                return (string) $fmt->format($ts);
            }
            return date('d/m/Y', $ts);
        }
    }

    if (!function_exists('slugify')) {
        /**
         * Slug URL-seguro: translitera acentos y limpia caracteres no permitidos.
         * Basado en el generateSlug() de Pages, unificado para todo el CMS.
         */
        function slugify(string $text): string
        {
            $text = trim(mb_strtolower($text));

            // 1) Transliteración: é→e, ñ→n, ü→u…
            if (class_exists('\Transliterator')) {
                // intl (preferido; WAMP lo trae activado)
                $text = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')->transliterate($text);
            } elseif (function_exists('iconv')) {
                $text = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
                // iconv en Windows puede dejar apóstrofos ('e) — los quitamos
                $text = str_replace(["'", '"', '`', '^', '~'], '', $text);
            } else {
                // Último recurso: mapa manual español
                $text = strtr($text, [
                    'á' => 'a',
                    'é' => 'e',
                    'í' => 'i',
                    'ó' => 'o',
                    'ú' => 'u',
                    'ü' => 'u',
                    'ñ' => 'n',
                    'ç' => 'c',
                    'à' => 'a',
                    'è' => 'e',
                    'ì' => 'i',
                    'ò' => 'o',
                    'ù' => 'u',
                ]);
            }

            // 2) Limpieza (tu regex de Pages)
            $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
            $text = preg_replace('/[\s-]+/', '-', $text);
            $text = trim($text, '-');

            return $text !== '' ? $text : 'item';
        }
    }
    /** FAQ: traducción de campos con fallback al canónico
     * (para usar en front-end, no en admin)
     */
    if (!function_exists('faq_field')) {
        /** Campo de pregunta FAQ (question|answer) traducido con fallback por campo */
        function faq_field(array $item, string $field): string
        {
            $lang = current_lang();
            if ($lang === default_lang()) return (string) ($item[$field] ?? '');

            static $cache = [];
            $id = (int) $item['id'];
            if (!array_key_exists($id, $cache)) {
                $t = (new \App\Modules\Faq\Models\FaqTranslationModel())
                    ->where('item_id', $id)->where('lang', $lang)->first();
                $cache[$id] = $t ?? [];
            }
            $val = trim((string) ($cache[$id][$field] ?? ''));
            return $val !== '' ? $val : (string) ($item[$field] ?? '');
        }
    }
}
