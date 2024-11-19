<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'    => [
                'type'           => 'INT',
                'auto_increment' => true,
                'null'           => false,
            ],
            'uuid'  => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => false
            ],
            'name'  => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => false
            ],
            'level_id' => [
                'type'           => 'INT',
                'null'           => false
            ],
            'username' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => false
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => false
            ],
            'password' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => false
            ],
            'image'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true
            ],
            'created_at'    => [
                'type'           => 'DATETIME',
                'null'           => false
            ],
            'updated_at'    => [
                'type'           => 'DATETIME',
                'null'           => false
            ],
            'reset_password_token'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('level_id', 'levels', 'id', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
