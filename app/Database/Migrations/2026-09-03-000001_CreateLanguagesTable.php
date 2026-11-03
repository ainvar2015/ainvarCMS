<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLanguagesTable extends Migration
{
    public function up()
    {
        // Tabla de idiomas
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'code'        => ['type' => 'VARCHAR', 'constraint' => 5, 'unique' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'flag'        => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'is_default'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('languages');

        // Añadir columna lang a pages
        $this->forge->addColumn('pages', [
            'lang' => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => 'es', 'after' => 'id'],
        ]);

        // Añadir columna lang a menus
        $this->forge->addColumn('menus', [
            'lang' => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => 'es', 'after' => 'id'],
        ]);

        // Insertar español por defecto
        $this->db->table('languages')->insert([
            'code'       => 'es',
            'name'       => 'Español',
            'flag'       => '🇪🇸',
            'is_default' => 1,
            'active'     => 1,
            'sort_order' => 0,
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('languages');
        $this->forge->dropColumn('pages', 'lang');
        $this->forge->dropColumn('menus', 'lang');
    }
}