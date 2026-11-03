<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['key' => 'site_name',    'value' => 'CMS Ainvar', 'type' => 'string'],
            ['key' => 'contact_email','value' => 'admin@ainvarcms.loc', 'type' => 'string'],
        ];
        $this->db->table('settings')->insertBatch($data);
    }
}