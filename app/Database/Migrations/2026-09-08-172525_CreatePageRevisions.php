<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePageRevisions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'page_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'content'    => ['type' => 'TEXT', 'null' => true],
            'meta_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_desc'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('page_id');
        $this->forge->addForeignKey('page_id', 'pages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET_NULL', 'CASCADE');
        $this->forge->createTable('page_revisions');
    }

    public function down()
    {
        $this->forge->dropTable('page_revisions');
    }
}