<?php

namespace App\Models;

use CodeIgniter\Model;

class levelsModel extends Model
{
    protected $table      = 'levels';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'info'];

}