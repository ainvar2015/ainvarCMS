<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCookieConsentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'categories' => ['type' => 'VARCHAR', 'constraint' => 255],   // JSON: ["analitica","marketing"]
            'ip'         => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cookie_consents');
    }

    public function down()
    {
        $this->forge->dropTable('cookie_consents');
    }
}