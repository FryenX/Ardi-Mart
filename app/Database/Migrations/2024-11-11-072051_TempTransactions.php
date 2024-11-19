<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TempTransactions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'    => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
                'null'           => false
            ],
            'transaction_invoice' => [
                'type'            => 'VARCHAR',
                'constraint'      => '50',
                'null'            => false
            ],
            'barcode' => [
                'type'           => 'CHAR',
                'constraint'     => '100',
                'null'           => false,
            ],
            'purchase_price'   => [
                'type'       => 'DOUBLE',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false
            ],
            'sell_price'   => [
                'type'       => 'DOUBLE',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false
            ],
            'qty'          => [
                'type'       => 'DOUBLE',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false
            ],
            'subtotal'     => [
                'type'       => 'DOUBLE',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('temp_transactions');
    }

    public function down()
    {
        $this->forge->dropTable('temp_transactions');
    }
}
