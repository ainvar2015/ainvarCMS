<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAgendaTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'summary'      => ['type' => 'TEXT', 'null' => true],
            'content'      => ['type' => 'LONGTEXT', 'null' => true],
            'image_main'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'location'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'start_at'     => ['type' => 'DATETIME'],
            'end_at'       => ['type' => 'DATETIME', 'null' => true],
            'external_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'featured'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'active'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'meta_title'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_desc'    => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('slug');
        $this->forge->addKey('start_at');
        $this->forge->createTable('agenda_events');
    }

    public function down()
    {
        $this->forge->dropTable('agenda_events');
    }
}
