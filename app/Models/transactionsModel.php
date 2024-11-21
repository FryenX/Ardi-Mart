<?php

namespace App\Models;

use CodeIgniter\Model;

class transactionsModel extends Model
{
    protected $table      = 'transactions';
    protected $primaryKey = 'invoice';

    protected $allowedFields = [
        'invoice', 
        'date_time',
        'customer_id',
        'discount_percent',
        'discount_idr',
        'gross_total',
        'net_total'
    ];
}