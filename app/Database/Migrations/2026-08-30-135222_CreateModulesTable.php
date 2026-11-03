<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'version'     => ['type' => 'VARCHAR', 'constraint' => 20],
            'description' => ['type' => 'TEXT', 'null' => true],
            'namespace'   => ['type' => 'VARCHAR', 'constraint' => 100], // Ej: App\Modules\Gallery
            'path'        => ['type' => 'VARCHAR', 'constraint' => 255], // Ruta física
            'has_admin'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'has_front'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'installed_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('modules');
    }

    public function down()
    {
        $this->forge->dropTable('modules');
    }
}