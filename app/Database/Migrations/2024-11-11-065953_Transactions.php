<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transactions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'invoice' => [
                'type'            => 'VARCHAR',
                'constraint'      => '50',
                'null'            => false
            ],
            'date' => [
                'type'            => 'DATE',
                'null'            => false
            ],
            'customer_id' => [
                'type'           => 'INT',
                'null'           => false,
            ],
            'discount_percent' => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
            'discount_idr' => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
            'gross_total' => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
            'net_total' => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
        ]);
        $this->forge->addPrimaryKey('invoice');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE');
        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}
