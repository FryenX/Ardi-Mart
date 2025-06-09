<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;
use CodeIgniter\I18n\Time;

class UserSeeder extends Seeder
{
    public function run()
    {
        $uuidAdmin = Uuid::uuid4()->toString();
        $uuidManager = Uuid::uuid4()->toString();
        $uuidCashier = Uuid::uuid4()->toString();

        $data = [
            [
                'uuid'       => $uuidAdmin,
                'name'       => 'Admin',
                'level_id'   => '1',
                'username'   => 'admin',
                'email'      => 'admin@gmail.com',
                'password'   => password_hash('admin', PASSWORD_BCRYPT),
                'image'      => '',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'uuid'       => $uuidManager,
                'name'       => 'Manager',
                'level_id'   => '2',
                'username'   => 'manager',
                'email'      => 'manager@gmail.com',
                'password'   => password_hash('manager', PASSWORD_BCRYPT),
                'image'      => '',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'uuid'       => $uuidCashier,
                'name'       => 'Cashier',
                'level_id'   => '3',
                'username'   => 'cashier',
                'email'      => 'cashier@gmail.com',
                'password'   => password_hash('cashier', PASSWORD_BCRYPT),
                'image'      => '',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
