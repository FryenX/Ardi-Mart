<?php

namespace App\Controllers;

use App\Models\usersModel;
use App\Models\transactionsModel;

class Main extends BaseController
{
    protected $users;
    protected $transactions;
    public function __construct()
    {
        $this->users = new usersModel();
        $this->transactions = new transactionsModel();
    }
    public function index()
    {
        $current_date = date('Y-m-d');
        $one_month = date('Y-m-d', strtotime('-30 Days'));

        $new_transactions = $this->db->table('transactions')
            ->selectCount('invoice')
            ->where('DATE(date_time) >=', $one_month)
            ->where('DATE(date_time) <=', $current_date)
            ->get()->getRow();
        $profit = $this->db->table('transactions_detail')
            ->select('SUM(transactions.net_total - (transactions_detail.purchase_price * transactions_detail.qty)) AS profit')
            ->join('transactions', 'transactions_detail.invoice = transactions.invoice')
            ->where('DATE(transactions.date_time) >=', $one_month)
            ->where('DATE(transactions.date_time) <=', $current_date)
            ->get()
            ->getRow();

        $data_user = $this->db->table('users')->selectCount('id')->get()->getRow();

        $product_data = $this->db->table('products')->selectCount('barcode')->get()->getRow();

        $data = [
            'new_transactions' => $new_transactions->invoice,
            'profit' => $profit->profit,
            'data_user' => $data_user->id,
            'product_data' => $product_data->barcode,
        ];
        return view('templates/home', $data);
    }

    public function fetchSaleYears()
    {
        if ($this->request->isAJAX()) {
            $yearData = $this->transactions
                ->select('YEAR(date_time) as year')
                ->groupBy('YEAR(date_time)')
                ->orderBy('year', 'DESC')
                ->get()
                ->getResult();

            $year = '';
            foreach ($yearData as $row) {
                $year .= '<option value="' . $row->year . '">' . $row->year . '</option>';
            }

            $msg = [
                'year' => $year
            ];
            echo json_encode($msg);
        }
    }

    public function fetchSalesData($year)
    {
        if ($this->request->isAJAX()) {
            $fetchSaleData = $this->transactions->getMonthlySalesData($year);

            $months = [];
            $salesCounts = [];

            foreach ($fetchSaleData as $data) {
                $months[] = date("F", mktime(0, 0, 0, $data->month, 10));
                $salesCounts[] = $data->total_sales;
            }
            $msg = [
                'months' => $months,
                'sales' => $salesCounts
            ];  
            echo json_encode($msg);
        }
    }
}
