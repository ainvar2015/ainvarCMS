<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'        => 'Default',
            'slug'        => 'default',
            'version'     => '1.0.0',
            'description' => 'Tema por defecto del CMS',
            'path'        => 'default',
            'is_default'  => 1,
            'active'      => 1,
        ];
        $this->db->table('themes')->insert($data);
    }
}