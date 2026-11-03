<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePageTranslations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'page_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'lang'       => ['type' => 'VARCHAR', 'constraint' => 5],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 191],
            'content'    => ['type' => 'LONGTEXT', 'null' => true],
            'meta_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_desc'  => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['page_id', 'lang']);
        $this->forge->addUniqueKey(['lang', 'slug']);
        $this->forge->addForeignKey('page_id', 'pages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('page_translations');
    }

    public function down()
    {
        $this->forge->dropTable('page_translations');
    }
}