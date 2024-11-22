<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TransactionsDetail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
                'null'           => false
            ],
            'invoice' => [
                'type'            => 'CHAR',
                'constraint'     => '20',
                'null'            => false
            ],
            'barcode'    => [
                'type'           => 'CHAR',
                'constraint'     => '50',
                'null'           => false
            ],
            'purchase_price'     => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
            'sell_price'         => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
            'qty'                => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
            'sub_total'           => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('invoice', 'transactions', 'invoice', 'CASCADE');
        $this->forge->addForeignKey('barcode', 'products', 'barcode', 'CASCADE');
        $this->forge->createTable('transactions_detail');
    }

    public function down()
    {
        $this->forge->dropTable('transactions_detail');
    }
}
