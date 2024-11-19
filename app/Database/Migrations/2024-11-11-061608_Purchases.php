<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Purchases extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'invoice' => [
                'type'            => 'CHAR',
                'constraint'      => '20',
                'null'            => false
            ],
            'date' => [
                'type'            => 'DATE',
                'null'            => false
            ],
            'payment' => [
                'type'            => 'ENUM',
                'constraint'      => ['Cash', 'Card'],
                'default'         => 'Cash'
            ],
            'supplier_id' => [
                'type'            => 'INT',
                'null'            => false
            ],
            'discount_percent' => [
                'type'            => 'DOUBLE',
                'constraint'      => '11,2',
                'default'         => 0.00,
                'null'            => false
            ],
            'discount_idr' => [
                'type'            => 'DOUBLE',
                'constraint'      => '11,2',
                'default'         => 0.00,
                'null'            => false
            ],
            'gross_total' => [
                'type'            => 'DOUBLE',
                'constraint'      => '11,2',
                'default'         => 0.00,
                'null'            => false
            ],
            'net_total' => [
                'type'            => 'DOUBLE',
                'constraint'      => '11,2',
                'default'         => 0.00,
                'null'            => false
            ],
        ]);
        $this->forge->addPrimaryKey('invoice');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE');
        $this->forge->createTable('purchases');
    }

    public function down()
    {
        $this->forge->dropTable('purchases');
    }
}
