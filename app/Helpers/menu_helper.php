<?php

if (!function_exists('render_menu')) {
    function render_menu(?int $parentId = null, ?string $lang = null): string
    {
        $lang = $lang ?: current_lang();   // ← idioma actual si no se indica

        $menuModel = new \App\Models\MenuModel();
        $items = $menuModel->getMenuTree($parentId, $lang);

        // Si getMenuTree filtra por idioma y no hay ítems en este, caer al menú completo
        if (empty($items) && $lang !== default_lang()) {
            $items = $menuModel->getMenuTree($parentId, null);
        }

        if (empty($items)) return '';

        $html = '<ul class="navbar-nav ms-auto">';
        foreach ($items as $item) {
            $html .= render_menu_item($item, $lang);
        }
        $html .= '</ul>';
        return $html;
    }
}

if (!function_exists('render_menu_item')) {
    function render_menu_item(array $item, ?string $lang = null, bool $inDropdown = false): string
    {
        $lang    = $lang ?: current_lang();
        $default = default_lang();
        $hasChildren = !empty($item['children']);

        // Cadena de fallback de etiqueta:
        // 1) menu_translations (etiqueta de menú traducida, si existe)
        // 2) page_translations.title (título de la página en ese idioma)
        // 3) menus.label (etiqueta base = título canónico)
        $menuLabel = null;
        if ($lang !== $default && !empty($item['id'])) {
            static $labelCache = [];
            $mid = (int) $item['id'];
            if (!array_key_exists($mid, $labelCache)) {
                $t = (new \App\Models\MenuTranslationModel())
                    ->where('menu_id', $mid)
                    ->where('lang', $lang)
                    ->first();
                $labelCache[$mid] = $t['label'] ?? null;
            }
            $menuLabel = $labelCache[$mid];
        }

        if ($menuLabel) {
            $item['label'] = $menuLabel;
        } elseif ($lang !== $default && !empty($item['page_id'])) {
            // Sin etiqueta de menú traducida → caer al título de la página en ese idioma
            static $pageTitleCache = [];
            $pid = (int) $item['page_id'];
            if (!array_key_exists($pid, $pageTitleCache)) {
                $t = (new \App\Models\PageTranslationModel())
                    ->where('page_id', $pid)
                    ->where('lang', $lang)
                    ->first();
                $pageTitleCache[$pid] = $t['title'] ?? null;
            }
            if ($pageTitleCache[$pid]) {
                $item['label'] = $pageTitleCache[$pid];
            }
        }

        // Resolver la ruta del ítem
        $url = $item['url'] ?? $item['page_slug'] ?? '';

        if (!empty($item['page_id']) && $lang !== $default && !str_starts_with((string) $url, 'http')) {
            static $transSlugs = [];
            $pid = (int) $item['page_id'];
            if (!array_key_exists($pid, $transSlugs)) {
                $t = (new \App\Models\PageTranslationModel())
                    ->where('page_id', $pid)
                    ->where('lang', $lang)
                    ->first();
                $transSlugs[$pid] = $t['slug'] ?? null;
            }
            if ($transSlugs[$pid]) {
                $url = $transSlugs[$pid];
            }
        }

        if ($url === '' && !empty($item['page_id'])) {
            $page = (new \App\Models\PageModel())->find($item['page_id']);
            $url  = $page['slug'] ?? '';
        }

        if ($url && !str_starts_with($url, 'http')) {
            $prefix = ($lang !== $default) ? $lang . '/' : '';
            $url    = base_url($prefix . ltrim($url, '/'));
        } elseif (empty($url)) {
            $url = localized_url();
        }

        // Detección de activo: comparar rutas SIN prefijo de idioma
        $currentUri = trim(service('uri')->getPath(), '/');
        $itemPath   = trim((string) parse_url($url, PHP_URL_PATH), '/');

        $stripLang = function (string $path): string {
            $seg = explode('/', $path);
            foreach (active_languages() as $l) {
                if (($seg[0] ?? '') === $l['code']) {
                    array_shift($seg);
                    break;
                }
            }
            return implode('/', $seg);
        };

        $active = ($itemPath !== '' && $stripLang($currentUri) === $stripLang($itemPath)) ? 'active' : '';

        $label = esc($item['label']);
        if (!empty($item['icon'])) {
            $label = '<img src="' . base_url($item['icon']) . '" height="20" class="me-1"> ' . $label;
        }

        // ← CLASES DISTINTAS DENTRO DEL DROPDOWN: dropdown-item, no nav-link
        $linkClass = $inDropdown ? 'dropdown-item' : 'nav-link';

        if ($hasChildren) {
            $html  = '<li class="nav-item dropdown">';
            $html .= '<a class="nav-link dropdown-toggle ' . $active . '" href="#" data-bs-toggle="dropdown">' . $label . '</a>';
            $html .= '<ul class="dropdown-menu">';
            foreach ($item['children'] as $child) {
                $html .= render_menu_item($child, $lang, true);   // ← true = dentro del dropdown
            }
            $html .= '</ul></li>';
        } else {
            $target = $item['target'] ?? '_self';
            $html   = '<li' . ($inDropdown ? '' : ' class="nav-item"') . '>';
            $html  .= '<a class="' . $linkClass . ' ' . $active . '" href="' . $url . '" target="' . $target . '">' . $label . '</a>';
            $html  .= '</li>';
        }

        return $html;
    }
}
