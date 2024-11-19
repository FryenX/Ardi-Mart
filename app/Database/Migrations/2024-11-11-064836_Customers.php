<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Customers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
                'null'           => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
            ],
            'address' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => false
            ],
            'phone'   => [
                'type'           => 'CHAR',
                'constraint'     => '20',
                'null'           => false,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers');
    }

    public function down()
    {
        $this->forge->dropTable('customers');
    }
}