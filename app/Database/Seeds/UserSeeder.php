<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;
use CodeIgniter\I18n\Time;

class UserSeeder extends Seeder
{
    public function run()
    {
        $uuid = Uuid::uuid4()->toString();

        $data = [
            [
                'uuid'       =>  $uuid,
                'name'       => 'Ardi',
                'level_id'   => '1',
                'username'   => 'ardiwidana',
                'email'      => 'ardiwidana@gmail.com',
                'password'   => password_hash('ardi2405', PASSWORD_BCRYPT),
                'image'      => '',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
