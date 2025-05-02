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
        $one_day = $current_date;

        $new_transactions = $this->db->table('transactions')
            ->selectCount('invoice')
            ->where('DATE(date_time) =', $one_day)
            ->get()->getRow();
        $profit = $this->db->table('transactions_detail')
            ->select('SUM(transactions.net_total - (transactions_detail.purchase_price * transactions_detail.qty)) AS profit')
            ->join('transactions', 'transactions_detail.invoice = transactions.invoice')
            ->where('DATE(transactions.date_time) >=', $one_day)
            ->where('DATE(transactions.date_time) <=', $current_date)
            ->get()
            ->getRow();

        $data_user = $this->db->table('users')->selectCount('id')->get()->getRow();

        $product_data = $this->db->table('products')->selectCount('barcode')->get()->getRow();

        $data = [
            'new_transactions' => $new_transactions->invoice,
            'profit' => $profit->profit,
            'data_user' => $data_user->id,
            'product_data' => $product_data->barcode
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

    public function fetchSalesData()
    {
        if ($this->request->isAJAX()) {
            $year = $this->request->getPost('year');

            $query = $this->db->query("SELECT DATE_FORMAT(date_time, '%M') AS month, DATE_FORMAT(date_time, '%Y') AS year, COUNT(invoice) AS transactions
            FROM transactions WHERE DATE_FORMAT(date_time, '%Y') = '$year' GROUP BY DATE_FORMAT(date_time, '%M') ORDER BY date_time ASC")
                ->getResult();

            $data =  [
                'chart' => $query
            ];

            $msg = [
                'data' => view('templates/salesChart', $data)
            ];

            echo json_encode($msg);
        }
    }

    public function fetchProductsData()
    {
        if ($this->request->isAJAX()) {
            $year = $this->request->getPost('year');

            $query = $this->db->query("
                SELECT 
                    products.name AS name, 
                    transactions_detail.barcode, 
                    DATE_FORMAT(transactions.date_time, '%Y') AS year, 
                    SUM(transactions_detail.qty) AS qty 
                FROM 
                    transactions_detail 
                JOIN 
                    transactions 
                    ON transactions_detail.invoice = transactions.invoice 
                JOIN 
                    products 
                    ON transactions_detail.barcode = products.barcode 
                WHERE 
                    DATE_FORMAT(transactions.date_time, '%Y') = '$year' 
                GROUP BY 
                    transactions_detail.barcode, DATE_FORMAT(transactions.date_time, '%Y') 
                ORDER BY 
                    transactions.date_time ASC;
            ")->getResult();


            $data =  [
                'chart' => $query
            ];

            $msg = [
                'data' => view('templates/productsChart', $data)
            ];

            echo json_encode($msg);
        }
    }
}
