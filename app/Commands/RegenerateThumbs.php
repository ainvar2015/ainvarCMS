<?php

namespace App\Commands;

use App\Libraries\ImageThumb;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RegenerateThumbs extends BaseCommand
{
    protected $group       = 'Media';
    protected $name        = 'thumbs:regenerate';
    protected $description = 'Genera miniaturas para todas las imágenes de uploads/ que no la tengan';

    public function run(array $params)
    {
        if (!extension_loaded('gd')) {
            CLI::error('La extensión GD no está activa en este PHP.');
            return EXIT_ERROR;
        }

        $force = array_key_exists('force', $params);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(FCPATH . 'uploads', \FilesystemIterator::SKIP_DOTS)
        );

        $gen = 0; $skip = 0; $fail = 0;

        foreach ($iterator as $file) {
            if (!$file->isFile()) continue;

            $rel = str_replace('\\', '/', substr($file->getPathname(), strlen(FCPATH)));

            // No procesar la propia carpeta de thumbs
            if (str_starts_with($rel, 'uploads/thumbs/')) continue;

            if (!str_starts_with((string) mime_content_type($file->getPathname()), 'image/')) continue;

            $thumbRel = ImageThumb::thumbRel($rel);
            if (!$thumbRel) { $skip++; continue; }

            if (!$force && is_file(FCPATH . $thumbRel)) { $skip++; continue; }

            ImageThumb::generate($file->getPathname(), $force) ? $gen++ : $fail++;
        }

        CLI::write("Miniaturas generadas: {$gen} · Ya existían: {$skip} · Fallos: {$fail}");

        return EXIT_SUCCESS;
    }
}