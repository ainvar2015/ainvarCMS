<?php

if (!function_exists('consent_allowed')) {
    /**
     * ¿El visitante consintió la categoría dada?
     * necesarias siempre true. Lee la cookie 'ainvar_consent' (JSON).
     */
    function consent_allowed(string $category): bool
    {
        if ($category === 'necessary' || $category === 'necesarias') {
            return true;
        }
        if (empty($_COOKIE['cookie_consent'])) {
            return false;
        }
        $data = json_decode($_COOKIE['cookie_consent'], true);
        return !empty($data[$category]);
    }
}

if (!function_exists('has_consent')) {
    function has_consent(): bool
    {
        return !empty($_COOKIE['cookie_consent']);
    }
}