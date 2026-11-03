<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username'   => 'admin',
            'email'      => 'admin@cms.local',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'superadmin',
            'active'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('users')->insert($data);
    }
}