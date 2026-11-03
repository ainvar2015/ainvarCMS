<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNameTranslations extends Migration
{
    public function up()
    {
        // Álbumes de Gallery (clave = nombre canónico del álbum)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'album'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'lang'       => ['type' => 'VARCHAR', 'constraint' => 5],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['album', 'lang'], false, true);   // único por álbum+idioma
        $this->forge->createTable('gallery_album_translations');

        // Categorías de Catalog (clave = category_id)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'lang'        => ['type' => 'VARCHAR', 'constraint' => 5],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['category_id', 'lang'], false, true);
        $this->forge->createTable('catalog_category_translations');
    }

    public function down()
    {
        $this->forge->dropTable('gallery_album_translations');
        $this->forge->dropTable('catalog_category_translations');
    }
}