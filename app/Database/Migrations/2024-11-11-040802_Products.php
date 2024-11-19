<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

use function PHPSTORM_META\type;

class Products extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'barcode' => [
                'type'           => 'CHAR',
                'constraint'     => '50',
                'null'           => false,
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => false
            ],
            'unit_id' => [
                'type'           => 'INT',
                'null'           => false
            ],
            'category_id' => [
                'type'           => 'INT',
                'null'           => false
            ],
            'stocks' => [
                'type'       => 'DOUBLE',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false
            ],
            'purchase_price' => [
                'type'       => 'DOUBLE',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false
            ],
            'sell_price' => [
                'type'       => 'DOUBLE',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false
            ],
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => '255'
            ]
        ]);
        $this->forge->addKey('barcode', true);
        $this->forge->addForeignKey('unit_id', 'units', 'id', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE');
        $this->forge->createTable('products');
    }

    public function down()
    {
        
    }
}
