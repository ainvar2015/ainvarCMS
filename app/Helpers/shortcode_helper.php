<?php

// Registro global de shortcodes
if (!isset($GLOBALS['shortcodes'])) {
    $GLOBALS['shortcodes'] = [];
}

/**
 * Registra un shortcode
 */
if (!function_exists('add_shortcode')) {
    function add_shortcode(string $tag, callable $callback): void
    {
        $GLOBALS['shortcodes'][$tag] = $callback;
    }
}

/**
 * Procesa todos los shortcodes en un texto
 */
if (!function_exists('do_shortcode')) {
    function do_shortcode(?string $content): string
    {
        if (empty($content)) return '';

        $pattern = '/\[(\w+)([^\]]*)\](?:([^\[]*)\[\/\1\])?/s';

        return preg_replace_callback($pattern, function ($matches) {
            $tag     = $matches[1];
            $attrStr = $matches[2] ?? '';
            $content = $matches[3] ?? null;

            if (!isset($GLOBALS['shortcodes'][$tag])) {
                return $matches[0]; // No registrado, devolver tal cual
            }

            $attrs = _parse_shortcode_attrs($attrStr);
            $callback = $GLOBALS['shortcodes'][$tag];

            return (string) $callback($attrs, $content, $tag);
        }, $content);
    }
}

/**
 * Parsea atributos de shortcode: foo="bar" baz='qux' num=3
 */
if (!function_exists('_parse_shortcode_attrs')) {
    function _parse_shortcode_attrs(string $str): array
    {
        $attrs = [];
        $pattern = '/(\w+)\s*=\s*(["\'])(.*?)\2|\s*(\w+)(?=\s|$)/s';
        preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            if (!empty($m[4])) {
                $attrs[$m[4]] = true; // atributo sin valor (flag)
            } elseif (!empty($m[1])) {
                $attrs[$m[1]] = $m[3];
            }
        }
        return $attrs;
    }
}