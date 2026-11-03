<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThemesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'version'     => ['type' => 'VARCHAR', 'constraint' => 20],
            'description' => ['type' => 'TEXT', 'null' => true],
            'path'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'screenshot'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_default'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('themes');
    }

    public function down()
    {
        $this->forge->dropTable('themes');
    }
}