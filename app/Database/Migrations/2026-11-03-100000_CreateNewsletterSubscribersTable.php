<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewsletterSubscribersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 191],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'token'      => ['type' => 'VARCHAR', 'constraint' => 64],
            'active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'ip'         => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('token');
        $this->forge->addKey('active');
        $this->forge->createTable('newsletter_subscribers');
    }

    public function down()
    {
        $this->forge->dropTable('newsletter_subscribers');
    }
}
