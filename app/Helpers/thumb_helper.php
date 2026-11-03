<?php

if (!function_exists('thumb_url')) {
    /**
     * URL de la miniatura de una imagen; cae al original si no existe.
     * Uso en vistas: <img src="<?= thumb_url($img['file_path']) ?>">
     */
    function thumb_url(?string $filePath): string
    {
        if (empty($filePath)) {
            return '';
        }

        $rel = ltrim(str_replace('\\', '/', $filePath), '/');
        $thumbRel = \App\Libraries\ImageThumb::thumbRel($rel);

        if ($thumbRel && is_file(FCPATH . $thumbRel)) {
            return base_url($thumbRel);
        }

        return base_url($rel);
    }
}