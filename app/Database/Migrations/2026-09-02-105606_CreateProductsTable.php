<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 191],
            'sku'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'description'    => ['type' => 'TEXT', 'null' => true],
            'short_desc'     => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'price'          => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'price_old'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'stock'          => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'image_main'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'images_gallery' => ['type' => 'JSON', 'null' => true],
            'featured'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'active'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'meta_title'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_desc'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('category_id');
        $this->forge->addKey('active');
        $this->forge->addKey('featured');
        $this->forge->createTable('catalog_products');
    }

    public function down()
    {
        $this->forge->dropTable('catalog_products');
    }
}