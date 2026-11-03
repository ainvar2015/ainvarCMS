<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'          => ['type' => 'VARCHAR', 'constraint' => 191, 'unique' => true], // ← 191 en vez de 255
            'type'          => ['type' => 'ENUM', 'constraint' => ['normal','template','module'], 'default' => 'normal'],
            'content'       => ['type' => 'LONGTEXT', 'null' => true],
            'template_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'module_name'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'meta_title'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_desc'     => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'status'        => ['type' => 'ENUM', 'constraint' => ['published','draft','hidden'], 'default' => 'draft'],
            'sort_order'    => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        // $this->forge->addKey('slug'); // Índice normal, no unique aquí
        $this->forge->createTable('pages');
    }

    public function down()
    {
        $this->forge->dropTable('pages');
    }
}