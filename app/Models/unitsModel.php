<?php

namespace App\Models;

use CodeIgniter\Model;

class unitsModel extends Model
{
    protected $table      = 'units';
    protected $primaryKey = 'id';

    protected $allowedFields = ['id', 'name'];
}