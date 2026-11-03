<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCookieConsents extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'token'      => ['type' => 'VARCHAR', 'constraint' => 64],
            'necessary'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'analytics'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'marketing'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'language'   => ['type' => 'VARCHAR', 'constraint' => 10],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 250, 'default' => ''],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('token');
        $this->forge->createTable('cookie_consents');
    }

    public function down()
    {
        $this->forge->dropTable('cookie_consents');
    }
}