<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormSubmissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'page_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'page_slug'   => ['type' => 'VARCHAR', 'constraint' => 191],
            'data'        => ['type' => 'JSON'],
            'ip_address'  => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'read'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('page_id');
        $this->forge->createTable('form_submissions');
    }

    public function down()
    {
        $this->forge->dropTable('form_submissions');
    }
}