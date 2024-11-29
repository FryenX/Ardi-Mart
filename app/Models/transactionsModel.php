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

    public function getMonthlySalesData($year)
    {
        return $this
            ->select("MONTH(date_time) as month, COUNT(invoice) as total_sales")
            ->where('YEAR(date_time)', $year)
            ->groupBy('MONTH(date_time)')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResult();
    }
}
