<?php

namespace App\Controllers;

use App\Models\productsDataModel;
use Config\Services;

class Transactions extends BaseController
{
    public function index()
    {
        return view('transactions/index');
    }

    public function input()
    {
        $data = [
            'invoice' => $this->createInvoice()
        ];
        return view('transactions/input', $data);
    }

    public function createInvoice()
    {
        $date = $this->request->getPost('date');
        $query = $this->db->query("SELECT MAX(invoice) AS noInvoice FROM transactions WHERE DATE_FORMAT(date_time, '%Y-%m-%d %H:%i:%s') = '$date'");
        $result = $query->getRowArray();
        $data = $result['noInvoice'];

        $lastNum = substr($data, -4);

        $nextNum = intval($lastNum) + 1;
        $invoice = 'T' . date('dmy') . sprintf('%05s', $nextNum);
        return $invoice;
    }

    public function dataDetail()
    {
        $noInvoice = $this->request->getPost('noInvoice');

        $tempTransactions = $this->db->table('temp_transactions');
        $query = $tempTransactions->select('temp_transactions.id AS id, temp_transactions.barcode AS barcode, 
        products.name AS product, temp_transactions.sell_price AS sell_price, temp_transactions.qty AS qty, temp_transactions.subtotal AS sub_total')
            ->join('products', 'temp_transactions.barcode = products.barcode')->where('temp_transactions.invoice', $noInvoice)
            ->orderBy('temp_transactions.id', 'ASC');

        $data = [
            'dataDetail' => $query->get()
        ];

        $msg = [
            'data' => view('transactions/viewDetail', $data)
        ];

        echo json_encode($msg);
    }

    public function viewProductData()
    {
        if ($this->request->isAJAX()) 
        {
            $keyword = $this->request->getPost('keyword');
            $data = [
                'keyword' => $keyword
            ];
            $msg = [
                'modal' => view('transactions/searchProductModal', $data)
            ];

            echo json_encode($msg);
        }
    }

    public function productDataList()
    {
        if ($this->request->isAJAX()) {
            $keyword = $this->request->getPost('keyword');
            $request = Services::request();
            $productData = new productsDataModel($request);
            if($request->getMethod(true) == 'POST') {
                $lists = $productData->get_datatables($keyword);
                $data = [];
                $num = $request->getPost("start");
                foreach ($lists as $list) {
                    $num++;
                    $row = [];
                    $row[] = $num;
                    $row[] = $list->barcode;
                    $row[] = $list->name;
                    $row[] = $list->category_name;
                    $row[] = number_format($list->stocks, 0, ",", ".");
                    $row[] = number_format($list->sell_price, 0, ",", ".");
                    $row[] = "<button type=\"button\" class=\"btn btn-primary\" onclick=\"selectItem('" . $list->barcode . "','" . $list->name . "')\">Select</button>";
                    $data[] = $row;
                }
                $msg =  [
                    "draw" => $request->getPost('draw'),
                    "recordsTotal" => $productData->count_all($keyword),
                    "recordsFiltered" => $productData->count_filtered($keyword),
                    "data" => $data
                ];
                echo json_encode($msg);
            }
        }
    }

    public function saveTemp()
    {
        if($this->request->isAJAX())
        {
            $barcode = $this->request->getVar('barcode');
            $name = $this->request->getVar('name');
            $qty = $this->request->getVar('qty');
            $invoice = $this->request->getVar('invoice');

            $query = $this->db->table('products')->like('barcode', $barcode)->orLike('name', $barcode)->get();

            $Data = $query->getNumRows();

            if($Data > 1 ){
                $msg = [
                    'Data' => 'Many'
                ];
            } else {
                $msg = [
                    'Data' => 'One'
                ];
            }
            echo json_encode($msg);
        }
    }
}
