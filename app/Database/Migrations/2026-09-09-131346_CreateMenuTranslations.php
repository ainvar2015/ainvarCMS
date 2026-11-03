<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuTranslations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'menu_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'lang'       => ['type' => 'VARCHAR', 'constraint' => 5],
            'label'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['menu_id', 'lang']);
        // Ajusta 'menus' al nombre real de la tabla de tu MenuModel
        $this->forge->addForeignKey('menu_id', 'menus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('menu_translations');
    }

    public function down()
    {
        $this->forge->dropTable('menu_translations');
    }
}