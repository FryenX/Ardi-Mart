<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '0',
                'name' => '-',
                'address' => '-',
                'phone' => '-',
                'created_at' => '-',
                'updated_at' => '-',
            ],
        ];

        $this->db->table('customers')->insertBatch($data);
    }
}
