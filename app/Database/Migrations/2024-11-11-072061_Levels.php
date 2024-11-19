<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Levels extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
                'null'           => false
            ],
            'info' => [
                'type'           => 'VARCHAR',
                'constraint'     => '20',
                'null'           => false
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('levels');
    }

    public function down()
    {
        $this->forge->dropTable('levels');
    }
}
