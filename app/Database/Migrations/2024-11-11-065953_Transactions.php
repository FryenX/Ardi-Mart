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
            'date_time' => [
                'type'            => 'DATETIME',
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
            'payment_amount' => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
            'payment_change' => [
                'type'           => 'DOUBLE',
                'constraint'     => '12,2',
                'default'        => 0.00,
                'null'           => false
            ],
            'payment_method' => [
                'type'           => 'ENUM',
                'constraint'     => ['Cash', 'Midtrans'],
                'default'        => 'Cash',
                'null'           => false
            ],
            'order_id' => [
                'type'           => 'CHAR',
                'constraint'     => '20',
                'null'           => true
            ],
            'payment_type' => [
                'type'           => 'VARCHAR',
                'constraint'     => '50',
                'null'           => true
            ],
            'va_number' => [
                'type'           => 'VARCHAR',
                'constraint'     => '40',
                'null'           => true
            ],
            'bank' => [
                'type'           => 'VARCHAR',
                'constraint'     => '40',
                'null'           => true
            ],
            'status' => [
                'type'           => 'VARCHAR',
                'constraint'     => '40',
                'null'           => true
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
