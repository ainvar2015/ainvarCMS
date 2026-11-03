<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRedirectsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'from_path'  => ['type' => 'VARCHAR', 'constraint' => 500],
            'to_path'    => ['type' => 'VARCHAR', 'constraint' => 500],
            'code'       => ['type' => 'SMALLINT', 'constraint' => 3, 'default' => 301],
            'hits'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('from_path');
        $this->forge->createTable('redirects');
    }

    public function down()
    {
        $this->forge->dropTable('redirects');
    }
}