<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMediaTables extends Migration
{
    public function up()
    {
        // Carpetas
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'parent_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent_id', 'media_folders', 'id', 'SET_NULL', 'CASCADE');
        $this->forge->createTable('media_folders');

        // Archivos
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'folder_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'file_path'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'mime_type'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'size'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'width'         => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
            'height'        => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'alt_text'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'uploaded_by'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('folder_id', 'media_folders', 'id', 'SET_NULL', 'CASCADE');
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'SET_NULL', 'CASCADE');
        $this->forge->createTable('media');
    }

    public function down()
    {
        $this->forge->dropTable('media');
        $this->forge->dropTable('media_folders');
    }
}