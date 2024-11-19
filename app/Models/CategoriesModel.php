<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriesModel extends Model
{
    protected $table      = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'name'];

    public function searchData($search)
    {
        return $this->table('categories')->like('name', $search);
    }
}
