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
            'transaction_invoice' => [
                'type'            => 'CHAR',
                'constraint'     => '20',
                'null'            => false
            ],
            'product_barcode'    => [
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
            'subtotal'           => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('transaction_invoice', 'transactions', 'invoice', 'CASCADE');
        $this->forge->addForeignKey('product_barcode', 'products', 'barcode', 'CASCADE');
        $this->forge->createTable('transactions_detail');
    }

    public function down()
    {
        $this->forge->dropTable('transactions_detail');
    }
}
