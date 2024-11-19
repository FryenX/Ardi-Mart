<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'info' => 'Admin'
            ],
            [
                'info' => 'Manager'
            ],
            [
                'info' => 'Cashier'
            ],
            [
                'info' => 'Supplier'
            ]
        ];

        $this->db->table('levels')->insertBatch($data);
    }
}
