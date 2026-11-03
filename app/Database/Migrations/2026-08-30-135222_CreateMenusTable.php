<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenusTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'page_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'label'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'url'         => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true], // Para URLs externas
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],  // Imagen/icono del menú
            'parent_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'sort_order'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'target'      => ['type' => 'ENUM', 'constraint' => ['_self','_blank'], 'default' => '_self'],
            'active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('menus');
    }

    public function down()
    {
        $this->forge->dropTable('menus');
    }
}