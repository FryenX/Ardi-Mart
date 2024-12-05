<?php

namespace App\Controllers;


use App\Models\productsDataModel;
use Config\Services;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\Printer;

class Transactions extends BaseController
{
    public function index()
    {
        return view('transactions/index');
    }

    public function input()
    {
        return view('transactions/input');
    }

    public function createInvoice()
    {
        $session = session();
        $uuid = $session->get('uuid');
        $userId = substr($uuid, -3);
        $date = $this->request->getPost('date');

        $query = $this->db->query("
        SELECT MAX(invoice) AS noInvoice 
        FROM transactions 
        WHERE DATE_FORMAT(date_time, '%Y-%m-%d') = '$date'");
        $result = $query->getRowArray();
        $data = $result['noInvoice'];

        if ($data) {
            $lastNum = substr($data, -8, 4);
            $nextNum = intval($lastNum) + 1;
        } else {
            $nextNum = 1;
        }

        $formattedDate = date('dmy', strtotime($date));
        $invoice = 'T' . $formattedDate . sprintf('%04s', $nextNum) . 'U' . sprintf('%03s', $userId);
        $msg = ['invoice' => $invoice];
        echo json_encode($msg);
    }


    public function dataDetail()
    {
        $noInvoice = $this->request->getPost('noInvoice');

        $tempTransactions = $this->db->table('temp_transactions');
        $query = $tempTransactions->select('temp_transactions.id AS id, temp_transactions.barcode AS barcode, 
        products.name AS product, temp_transactions.sell_price AS sell_price, temp_transactions.qty AS qty, temp_transactions.subtotal AS sub_total')
            ->join('products', 'temp_transactions.barcode = products.barcode')->like('temp_transactions.invoice', $noInvoice)
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
        if ($this->request->isAJAX()) {
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
            if ($request->getMethod(true) == 'POST') {
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
        if ($this->request->isAJAX()) {
            $barcode = $this->request->getVar('barcode');
            $name = $this->request->getVar('name');
            $qty = $this->request->getVar('qty');
            $invoice = $this->request->getVar('invoice');

            if (strlen($name) > 0) {
                $query = $this->db->table('products')->where('barcode', $barcode)->where('name', $name)->get();
            } else {
                $query = $this->db->table('products')->like('barcode', $barcode)->orLike('name', $barcode)->get();
            }

            $Data = $query->getNumRows();

            if ($Data > 1) {
                $msg = [
                    'Data' => 'Many'
                ];
            } else if ($Data == 1) {
                $temp_transactions = $this->db->table('temp_transactions');
                $row = $query->getRowArray();
                $stock = $row['stocks'];
                if (intval($stock) == 0) {
                    $msg = [
                        'error' => 'Stock isn\'t available'
                    ];
                } else if ($qty > intval($stock)) {
                    $msg = [
                        'error' => 'Stock isn\'t enough'
                    ];
                } else {
                    $insert =  [
                        'invoice' => $invoice,
                        'barcode' => $row['barcode'],
                        'purchase_price' => $row['purchase_price'],
                        'sell_price' => $row['sell_price'],
                        'qty' => $qty,
                        'subtotal' => floatval($row['sell_price']) * $qty
                    ];
                    $temp_transactions->insert($insert);

                    $msg = ['success' => 'Success'];
                }
            } else {
                $msg = ['error' => 'Sorry No Such Product in Our Directory'];
            }
            echo json_encode($msg);
        }
    }

    public function sumTotal()
    {
        if ($this->request->isAJAX()) {
            $invoice = $this->request->getPost('invoice');
            $temp_transactions = $this->db->table('temp_transactions');
            $query = $temp_transactions->select('SUM(subtotal) AS total')->where('invoice', $invoice)->get();
            $row = $query->getRowArray();

            $msg = [
                'total' => number_format($row['total'], '0', ",", ".")
            ];
            echo json_encode($msg);
        }
    }

    public function deleteItem()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');
            $temp_transactions = $this->db->table('temp_transactions');
            $query = $temp_transactions->delete(['id' => $id]);
            if ($query) {
                $msg = [
                    'success' => 'Item Deleted Successfully'
                ];
                echo json_encode($msg);
            }
        }
    }

    public function cancelTransaction()
    {
        if ($this->request->isAJAX()) {
            $invoice = $this->request->getPost('invoice');
            $temp_transactions = $this->db->table('temp_transactions');
            $delete = $temp_transactions->where('invoice', $invoice)->delete();

            if ($delete) {
                $msg = [
                    'success' => 'Success'
                ];
            }

            echo json_encode($msg);
        }
    }

    public function payment()
    {
        if ($this->request->isAJAX()) {
            $invoice = $this->request->getPost('invoice');
            $invoiceDate = $this->request->getPost('datetime');
            $customer = $this->request->getPost('customer');
            $temp_transactions = $this->db->table('temp_transactions');
            $query = $temp_transactions->getWhere(['invoice' => $invoice]);
            $queryTotal = $temp_transactions->select('SUM(subtotal) AS net_total')->where('invoice', $invoice)->get();
            $rowTotal = $queryTotal->getRowArray();
            if ($query->getNumRows() > 0) {
                $data = [
                    'invoice' => $invoice,
                    'customer' => $customer,
                    'net_total' => $rowTotal['net_total']
                ];

                $msg = [
                    'data' => view('transactions/modalPayment', $data)
                ];
            } else {
                $msg = [
                    'error' => 'No Item Yet'
                ];
            }
            echo json_encode($msg);
        }
    }

    public function saveData()
    {
        if ($this->request->isAJAX()) {
            $invoice = $this->request->getPost('invoice');
            $customer = $this->request->getPost('customer');
            $gross_total = $this->request->getPost('gross_total');
            $net_total = str_replace(",", "", $this->request->getPost('net_total'));
            $disc_percent = str_replace(",", "", $this->request->getPost('disc_percent'));
            $disc_idr = str_replace(",", "", $this->request->getPost('disc_idr'));
            $payment = str_replace(",", "", $this->request->getPost('payment'));
            $change = str_replace(",", "", $this->request->getPost('change'));

            $transactions = $this->db->table('transactions');
            $temp_transactions = $this->db->table('temp_transactions');
            $detail_transactions = $this->db->table('transactions_detail');

            $insertTransactionsData = [
                'invoice' => $invoice,
                'date_time' => date('Y-m-d H:i:s'),
                'customer_id' => $customer,
                'discount_percent' => $disc_percent,
                'discount_idr' => $disc_idr,
                'gross_total' => $gross_total,
                'net_total' => $net_total,
                'payment_amount' => $payment,
                'payment_change' => $change,
            ];

            $transactions->insert($insertTransactionsData);

            $fetchTempData = $temp_transactions->getWhere(['invoice' => $invoice]);
            $fieldDetailTransactions = [];

            foreach ($fetchTempData->getResultArray() as $row) {
                $fieldDetailTransactions[] = [
                    'invoice' => $row['invoice'],
                    'barcode' => $row['barcode'],
                    'purchase_price' => $row['purchase_price'],
                    'sell_price' => $row['sell_price'],
                    'qty' => $row['qty'],
                    'sub_total' => $row['subtotal'],
                ];
            }

            if (!empty($fieldDetailTransactions)) {
                $detail_transactions->insertBatch($fieldDetailTransactions);
            }

            $temp_transactions->where('invoice', $invoice)->delete();

            $msg = [
                'success' => 'Success',
                'invoice' => $invoice
            ];

            echo json_encode($msg);
        }
    }

    public function printInvoice()
    {
        function buatBaris1Kolom($kolom1)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 30;

            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);

            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);

            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = count($kolom1Array);

            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();

            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {

                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");

                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n", $hasilBaris) . "\n";
        }

        function buatBaris3Kolom($kolom1, $kolom2, $kolom3)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 8;
            $lebar_kolom_2 = 6;
            $lebar_kolom_3 = 8;

            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);

            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
            $kolom3Array = explode("\n", $kolom3);

            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array));

            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();

            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {

                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                // memberikan rata kanan pada kolom 3 dan 4 karena akan kita gunakan untuk harga dan total harga
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ", STR_PAD_LEFT);

                $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);

                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n", $hasilBaris) . "\n";

        }

        $profile = CapabilityProfile::load("simple");
        $connector = new WindowsPrintConnector("printer_kasir");
        $printer = new Printer($connector, $profile);

        $invoice = $this->request->getPost('invoice');
        $transactions = $this->db->table('transactions');
        $transactions_detail = $this->db->table('transactions_detail');

        $query_transactions = $transactions->getWhere(['invoice' => $invoice]);
        $row_transactions = $query_transactions->getRowArray();

        $printer->initialize();
        $printer->selectPrintMode(Printer::MODE_FONT_A);
        $printer->text(buatBaris1Kolom("Ardi Mart"));
        $printer->text(buatBaris1Kolom("Tabanan, Telp 085738754536"));
        $printer->text(buatBaris1Kolom("Invoice: $invoice"));
        $printer->text(buatBaris1Kolom("Date: $row_transactions[date_time]"));

        $printer->text(buatBaris1Kolom("------------------------------"));

        $query_transactions_detail = $transactions_detail
            ->select('products.name as product, qty, units.name as unit, transactions_detail.sell_price AS sell_price, transactions_detail.sub_total AS sub_total')
            ->join('products', 'transactions_detail.barcode = products.barcode')
            ->join('units', 'units.id = products.unit_id')
            ->where('transactions_detail.invoice', $invoice)->get();

        $net_total = 0;

        foreach ($query_transactions_detail->getResultArray() as $data) {
            $printer->text(buatBaris1Kolom("$data[product]"));
            $printer->text(buatBaris3Kolom(
                number_format($data['qty'], 0) . ' ' . $data['unit'],
                number_format($data['sell_price'], 0),
                number_format($data['sub_total'], 0)
            ));

            $net_total += $data['sub_total'];
        }

        $printer->text(buatBaris1Kolom("------------------------------"));
        $printer->text(buatBaris3Kolom("", "TOTAL: ", number_format($net_total, 0)));
        $printer->text("\n");
        $printer->text("Thanks For Your Visit");

        $printer->feed(4);
        $printer->cut();
        echo "Invoice Printed Successfully";
        $printer->close();
    }

    public function data()
    {
        return view('transactions/data');
    }

    public function showTransactionsData()
    {
        
    }
}
