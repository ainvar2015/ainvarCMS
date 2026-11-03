<?php

namespace App\Libraries;

/**
 * Generador de miniaturas con GD.
 * Convención: el thumb de "uploads/media/foto.jpg" se guarda como
 * "uploads/thumbs/foto_<hash8>.jpg" donde hash8 = substr(md5(ruta_relativa), 0, 8).
 * Evita colisiones entre archivos con el mismo nombre en carpetas distintas.
 */
class ImageThumb
{
    public const MAX_WIDTH = 400;
    public const QUALITY   = 82;

    /**
     * Genera (o devuelve la existente) la miniatura de una imagen.
     *
     * @param string $sourceFullPath Ruta absoluta del original
     * @param bool   $force          Regenerar aunque ya exista
     * @return string|null Ruta relativa del thumb, o null si no aplica/falla
     */
    public static function generate(string $sourceFullPath, bool $force = false): ?string
    {
        if (!extension_loaded('gd') || !is_file($sourceFullPath)) {
            return null;
        }

        $rel  = str_replace('\\', '/', substr($sourceFullPath, strlen(FCPATH)));
        $mime = (string) mime_content_type($sourceFullPath);
        [$w, $h] = @getimagesize($sourceFullPath) ?: [0, 0];

        if (!$w || !$h || !str_starts_with($mime, 'image/')) {
            return null;
        }

        // No agrandar: si ya es pequeña, el original ya sirve de miniatura
        if ($w <= self::MAX_WIDTH) {
            return null;
        }

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        if (!isset($extMap[$mime])) {
            return null;
        }

        $thumbRel  = self::thumbRel($rel);
        $thumbFull = FCPATH . $thumbRel;

        if (is_file($thumbFull) && !$force) {
            return $thumbRel;
        }

        $src = match ($mime) {
            'image/png'  => @imagecreatefrompng($sourceFullPath),
            'image/webp' => @imagecreatefromwebp($sourceFullPath),
            'image/gif'  => @imagecreatefromgif($sourceFullPath),
            default      => @imagecreatefromjpeg($sourceFullPath),
        };
        if (!$src) {
            return null;
        }

        // Orientación EXIF: las fotos de móvil salen giradas si se ignora
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $orientation = (@exif_read_data($sourceFullPath))['Orientation'] ?? null;
            if ($orientation == 6) {
                $src = imagerotate($src, -90, 0);
                [$w, $h] = [$h, $w];
            } elseif ($orientation == 8) {
                $src = imagerotate($src, 90, 0);
                [$w, $h] = [$h, $w];
            } elseif ($orientation == 3) {
                $src = imagerotate($src, 180, 0);
            }
        }

        $newW = self::MAX_WIDTH;
        $newH = max(1, (int) round($h * (self::MAX_WIDTH / $w)));

        $dst = imagecreatetruecolor($newW, $newH);

        // Conservar transparencia en PNG/WebP/GIF
        if ($mime !== 'image/jpeg') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        $thumbDir = dirname($thumbFull);
        if (!is_dir($thumbDir) && !mkdir($thumbDir, 0755, true)) {
            imagedestroy($src); imagedestroy($dst);
            return null;
        }

        $ok = match ($mime) {
            'image/png'  => imagepng($dst, $thumbFull, 6),
            'image/webp' => imagewebp($dst, $thumbFull, self::QUALITY),
            'image/gif'  => imagegif($dst, $thumbFull),
            default      => imagejpeg($dst, $thumbFull, self::QUALITY),
        };

        imagedestroy($src);
        imagedestroy($dst);

        return ($ok && is_file($thumbFull)) ? $thumbRel : null;
    }

    /** Ruta relativa del thumb correspondiente a una ruta relativa de imagen */
    public static function thumbRel(string $filePath): ?string
    {
        $rel = ltrim(str_replace('\\', '/', $filePath), '/');
        $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;

        if (!in_array($ext, ['jpg', 'png', 'webp', 'gif'])) {
            return null;
        }

        return 'uploads/thumbs/' . pathinfo($rel, PATHINFO_FILENAME) . '_' . substr(md5($rel), 0, 8) . '.' . $ext;
    }
}