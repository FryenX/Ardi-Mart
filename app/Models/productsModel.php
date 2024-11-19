<?php

namespace App\Models;

use CodeIgniter\Model;

class productsModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'barcode';
    protected $allowedFields = [
        'barcode', 
        'name',
        'unit_id',
        'category_id',
        'stocks',
        'purchase_price',
        'sell_price',
        'image',
    ];

    public function searchData($search)
    {
        return $this->table('products')
        ->select(' products.*, categories.name AS category_name, units.name AS unit_name')
        ->join('units', 'units.id=products.unit_id')
        ->join('categories', 'categories.id=products.category_id')
        ->like('products.barcode', $search)
        ->orLike('products.name', $search)
        ->orLike('categories.name', $search)
        ->orLike('units.name', $search);
    }
}
