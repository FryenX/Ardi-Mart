<?php

namespace App\Controllers;

use App\Models\productsModalDataModel;
use App\Models\transactionsDataModel;
use App\Models\transactionsModel;
use Config\Services;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\Printer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

\Midtrans\Config::$serverKey = 'SB-Mid-server-MfLAGbgCiHH5HmV1LJQIL4BR';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

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
        if ($this->request->isAJAX()) {
            $session = session();
            $uuid = $session->get('uuid');
            $userId = substr($uuid, -3);
            $date = date('Y-m-d');

            $query = $this->db->query("SELECT MAX(invoice) AS noInvoice FROM transactions WHERE DATE_FORMAT(date_time, '%Y-%m-%d') = '$date'");
            $result = $query->getRowArray();
            $data = $result['noInvoice'];

            $lastNum = substr($data, -4);

            $nextNum = intval($lastNum) + 1;

            $formattedNextNum = sprintf('%04s', $nextNum);

            $formattedDate = date('dmy', strtotime($date));

            $invoice = 'T' . $formattedDate . $userId . $formattedNextNum;

            $msg = ['invoice' => $invoice];
            echo json_encode($msg);
        }
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
            $productData = new productsModalDataModel($request);
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

    public function checkCode()
    {
        if ($this->request->isAJAX()) {
            $barcode = $this->request->getVar('barcode');

            $query = $this->db->table('products')->like('barcode', $barcode)->orLike('name', $barcode)->get();
            $Data = $query->getNumRows();

            if ($Data > 1) {
                $msg = [
                    'Data' => 'Many'
                ];
            } else if ($Data == 1) {
                $row = $query->getRowArray();
                $msg = [
                    'success' => 'Success',
                    'data' => [
                        'barcode' => $row['barcode'],
                        'product' => $row['name']
                    ]
                ];
            }

            echo json_encode($msg);
        }
    }

    public function saveTemp()
    {
        if ($this->request->isAJAX()) {
            $barcode = $this->request->getVar('barcode');
            $name = $this->request->getVar('product');
            $qty = $this->request->getVar('qty');
            $invoice = $this->request->getVar('invoice');

            if (!empty($barcode)) {
                $query = $this->db->table('products')->where('barcode', $barcode)->where('name', $name)->get();
                $Data = $query->getNumRows();

                if ($Data == 1) {
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
                        $insert = [
                            'invoice' => $invoice,
                            'barcode' => $row['barcode'],
                            'purchase_price' => $row['purchase_price'],
                            'sell_price' => $row['sell_price'],
                            'qty' => $qty,
                            'subtotal' => floatval($row['sell_price']) * $qty
                        ];
                        $temp_transactions->insert($insert);

                        $this->db->table('products')
                            ->where('barcode', $barcode)
                            ->set('stocks', 'stocks - ' . intval($qty), false)
                            ->update();

                        $msg = ['success' => 'Success'];
                    }
                } else {
                    $msg = ['error' => 'Sorry No Such Product in Our Directory'];
                }
            } else {
                $msg = ['error' => 'Please input a product first'];
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

            // Get the transaction first
            $row = $temp_transactions->where('id', $id)->get()->getRowArray();

            if ($row) {
                $barcode = $row['barcode'];
                $qty = $row['qty'];

                // Restore stock before deletion
                $this->db->table('products')
                    ->where('barcode', $barcode)
                    ->set('stocks', 'stocks + ' . intval($qty), false)
                    ->update();

                // Now delete the temp transaction
                $query = $temp_transactions->delete(['id' => $id]);

                if ($query) {
                    $msg = [
                        'success' => 'Item Deleted Successfully'
                    ];
                    echo json_encode($msg);
                }
            }
        }
    }

    public function cancelTransaction()
    {
        if ($this->request->isAJAX()) {
            $invoice = $this->request->getPost('invoice');
            $temp_transactions = $this->db->table('temp_transactions');

            // Get all items related to the invoice
            $items = $temp_transactions->where('invoice', $invoice)->get()->getResultArray();

            if (!empty($items)) {
                foreach ($items as $item) {
                    $barcode = $item['barcode'];
                    $qty = $item['qty'];

                    // Restore stock
                    $this->db->table('products')
                        ->where('barcode', $barcode)
                        ->set('stocks', 'stocks + ' . intval($qty), false)
                        ->update();
                }

                // Now delete all temp transactions with this invoice
                $temp_transactions->where('invoice', $invoice)->delete();

                $msg = ['success' => 'Transaction cancelled and stock restored'];
            } else {
                $msg = ['error' => 'No items found for this invoice'];
            }

            echo json_encode($msg);
        }
    }


    public function payment()
    {
        if ($this->request->isAJAX()) {
            $invoice = $this->request->getPost('invoice');
            $invoiceDate = $this->request->getPost('invoiceDate');
            $customer = $this->request->getPost('customer');
            $temp_transactions = $this->db->table('temp_transactions');
            $query = $temp_transactions->getWhere(['invoice' => $invoice]);
            $queryTotal = $temp_transactions->select('SUM(subtotal) AS net_total')->where('invoice', $invoice)->get();
            $rowTotal = $queryTotal->getRowArray();
            if ($query->getNumRows() > 0) {
                $data = [
                    'invoice' => $invoice,
                    'customer' => $customer,
                    'net_total' => $rowTotal['net_total'],
                    'invoiceDate' => $invoiceDate
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

    public function paymentTransfer()
    {
        if ($this->request->isAJAX()) {
            $invoice = $this->request->getPost('invoice');
            $customer = $this->request->getPost('customer');
            $invoiceDate = $this->request->getPost('invoiceDate');
            $temp_transactions = $this->db->table('temp_transactions');
            $query = $temp_transactions->getWhere(['invoice' => $invoice]);
            $queryTotal = $temp_transactions->select('SUM(subtotal) AS gross_total')->where('invoice', $invoice)->get();
            $rowTotal = $queryTotal->getRowArray();
            $gross_total = floatval($rowTotal['gross_total'] ?? 0);

            $disc_percent = floatval($this->request->getPost('disc_percent') ?? 0);
            $disc_idr = floatval(str_replace(",", "", $this->request->getPost('disc_idr') ?? 0));

            $net_total = $gross_total - ($gross_total * $disc_percent / 100) - $disc_idr;
            if ($query->getNumRows() > 0) {
                $tempData = [];
                foreach ($query->getResultArray() as $product):
                    $barcode = $product['barcode'];
                    $productRow = $this->db->table('products')->getWhere(['barcode' => $barcode])->getRowArray();
                    $name = $productRow ? $productRow['name'] : 'Unknown';

                    $tempData[] = [
                        'id'       => $barcode,
                        'price'    => $product['sell_price'],
                        'quantity' => (int) $product['qty'],
                        'name'     => $name
                    ];
                endforeach;

                $tempData[] = [
                    'id' => 'DISCOUNT',
                    'price' => -1 * ($gross_total - $net_total),
                    'quantity' => 1,
                    'name' => 'Discount'
                ];

                $params = array(
                    'transaction_details' => array(
                        'order_id' => rand(),
                        'gross_amount' => $net_total,
                    ),
                    'enabled_payments' => array(
                        'credit_card',
                        'bank_transfer',
                        'gopay',
                        'qris',
                        'shopeepay',
                        'dana'
                    ),
                    'item_details'        => $tempData
                );

                $msg = [
                    'invoice' => $invoice,
                    'invoiceDate' => $invoiceDate,
                    'customer' => $customer,
                    'gross_total' => $gross_total,
                    'net_total' => $net_total,
                    'snapToken' => \Midtrans\Snap::getSnapToken($params)
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
            $invoiceDate = $this->request->getPost('invoiceDate') ?? date('Y-m-d H:i:s');
            $gross_total = $this->request->getPost('gross_total');
            $net_total = str_replace(",", "", $this->request->getPost('net_total'));
            $disc_percent = str_replace(",", "", $this->request->getPost('disc_percent'));
            $disc_idr = str_replace(",", "", $this->request->getPost('disc_idr'));
            $payment = $this->request->getPost('payment'); 
            $cash = str_replace(",", "", $this->request->getPost('cash_amount'));
            $transfer = str_replace(",", "", $this->request->getPost('transfer') ?? $net_total);
            $change = str_replace(",", "", $this->request->getPost('change'));

            $order_id = $this->request->getPost('order_id') ?? '-';
            $payment_type = $this->request->getPost('payment_type') ?? 'cash';
            $transaction_status = $this->request->getPost('transaction_status') ?? 'Success';
            $va_number = $this->request->getPost('va_number') ?? '-';
            $bank = $this->request->getPost('bank') ?? '-';

            $finalPayment = ($payment === 'cash') ? $cash : $net_total;
            $finalMethod = ($payment === 'cash') ? 'Cash' : 'Transfer';

            $transactions = $this->db->table('transactions');
            $temp_transactions = $this->db->table('temp_transactions');
            $detail_transactions = $this->db->table('transactions_detail');

            $insertTransactionsData = [
                'invoice' => $invoice,
                'date_time' => $invoiceDate,
                'customer_id' => $customer,
                'discount_percent' => $disc_percent,
                'discount_idr' => $disc_idr,
                'gross_total' => $gross_total,
                'net_total' => $net_total,
                'payment_amount' => $finalPayment,
                'payment_change' => $change,
                'payment_method' => $finalMethod,
                'order_id' => $order_id,
                'payment_type' => $payment_type,
                'va_number' => $va_number,
                'bank' => $bank,
                'status' => $transaction_status
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
            $lebar_kolom_1 = 32;

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

        function buatBaris2Kolom($kolom1, $kolom2)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 16;
            $lebar_kolom_2 = 14;

            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);

            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);

            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array));

            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();

            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {

                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ", STR_PAD_LEFT);

                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n", $hasilBaris) . "\n";
        }


        function buatBaris3Kolom($kolom1, $kolom2, $kolom3)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 11;
            $lebar_kolom_2 = 8;
            $lebar_kolom_3 = 11;

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
        $connector = new WindowsPrintConnector("RONGTA RPP02 Series Printer");
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

        $printer->text(buatBaris1Kolom("--------------------------------"));

        $query_transactions_detail = $transactions_detail
            ->select('products.name as product, qty, units.name as unit, transactions_detail.sell_price AS sell_price, transactions_detail.sub_total AS sub_total')
            ->join('products', 'transactions_detail.barcode = products.barcode')
            ->join('units', 'units.id = products.unit_id')
            ->where('transactions_detail.invoice', $invoice)->get();

        $gross_total = 0;

        foreach ($query_transactions_detail->getResultArray() as $data) {
            $printer->text(buatBaris1Kolom("$data[product]"));
            $printer->text(buatBaris3Kolom(
                number_format($data['qty'], 0) . ' ' . $data['unit'],
                number_format($data['sell_price'], 0),
                number_format($data['sub_total'], 0)
            ));

            $gross_total += $data['sub_total'];
        }

        $printer->text(buatBaris1Kolom("--------------------------------"));
        $printer->text(buatBaris2Kolom("Gross Total: ", number_format($gross_total, 0)));
        $printer->text(buatBaris2Kolom("Discount (%): ", number_format($row_transactions['discount_percent'], 0)));
        $printer->text(buatBaris2Kolom("Discount (IDR): ", number_format($row_transactions['discount_idr'], 0)));
        $printer->text(buatBaris2Kolom("Net Total: ", number_format($row_transactions['net_total'], 0)));
        $printer->text(buatBaris2Kolom("Gross Total: ", number_format($row_transactions['payment_amount'], 0)));
        $printer->text(buatBaris2Kolom("Gross Total: ", number_format($row_transactions['payment_change'], 0)));
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
        if ($this->request->isAJAX()) {
            $request = Services::request();
            $transactionData = new transactionsDataModel($request);

            $startDate = $this->request->getPost('startDate');
            $endDate = $this->request->getPost('endDate');

            if ($request->getMethod(true) == 'POST') {
                $lists = $transactionData->get_datatables($startDate, $endDate);
                $data = [];
                $num = $request->getPost("start");

                foreach ($lists as $list) {
                    $paymentLabel = '';
                    switch (strtolower($list->payment_method)) {
                        case 'cash':
                            $paymentLabel = '<span class="badge badge-success">Cash</span>';
                            break;
                        case 'transfer':
                            $paymentLabel = '<span class="badge badge-warning">Transfer</span>';
                            break;
                        default:
                            $paymentLabel = '<span class="badge badge-secondary">' . ucfirst($list->payment_method) . '</span>';
                            break;
                    }

                    $num++;
                    $row = [];
                    $row[] = $num;
                    $row[] = $list->invoice;
                    $row[] = $list->date_time;
                    $row[] = $list->customer;
                    $row[] = $list->discount_percent . ' %';
                    $row[] = '<div style="text-align: right;">Rp. ' . number_format($list->discount_idr, 0, ",", ".") . '</div>';
                    $row[] = '<div style="text-align: right;">Rp. ' . number_format($list->gross_total, 0, ",", ".") . '</div>';
                    $row[] = '<div style="text-align: right;">Rp. ' . number_format($list->net_total, 0, ",", ".") . '</div>';
                    $row[] = '<div style="text-align: right;">Rp. ' . number_format($list->payment_amount, 0, ",", ".") . '</div>';
                    $row[] = '<div style="text-align: right;">Rp. ' . number_format($list->payment_change, 0, ",", ".") . '</div>';
                    $row[] = $paymentLabel;
                    $row[] = "<button type=\"button\" class=\"btn btn-danger\" onclick=\"deleteData('" . $list->invoice . "')\">Delete</button>";
                    $data[] = $row;
                }

                $output = [
                    "draw" => $request->getPost('draw'),
                    "recordsTotal" => $transactionData->count_all($startDate, $endDate),
                    "recordsFiltered" => $transactionData->count_filtered($startDate, $endDate),
                    "data" => $data
                ];

                echo json_encode($output);
            }
        }
    }

    public function delete()
    {
        if ($this->request->isAJAX()) {
            $msg = [];

            try {
                $invoice = $this->request->getPost('invoice');

                // Fetch all related detail transactions
                $detail_data = $this->db->table('transactions_detail')->where('invoice', $invoice);
                $details = $detail_data->get()->getResultArray();

                if (!empty($details)) {
                    foreach ($details as $row) {
                        if (!empty($row['barcode']) && isset($row['qty'])) {
                            $this->db->table('products')
                                ->where('barcode', $row['barcode'])
                                ->set('stocks', 'stocks + ' . intval($row['qty']), false)
                                ->update();
                        }
                    }
                }

                // Delete detail and main transaction
                $delete_detail = $this->db->table('transactions_detail')->where('invoice', $invoice)->delete();

                if ($delete_detail) {
                    $this->db->table('transactions')->where('invoice', $invoice)->delete();

                    $msg = [
                        'success' => 'Transactions deleted successfully'
                    ];
                } else {
                    $msg = [
                        'error' => 'Failed to delete transaction details'
                    ];
                }
            } catch (\Throwable $e) {
                $msg = [
                    'error' => 'Exception: ' . $e->getMessage()
                ];
            }

            echo json_encode($msg);
        }
    }


    public function exportToCSV()
    {
        $transactionData = new transactionsModel();
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $query = $transactionData->select('transactions.*, customers.name as customer')
            ->join('customers', 'customers.id = transactions.customer_id');

        if ($startDate && $endDate) {
            $query->where("DATE(date_time) >=", $startDate)
                ->where("DATE(date_time) <=", $endDate);
        }

        $lists = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'No',
            'Invoice',
            'Date Time',
            'Customer',
            'Discount Percent',
            'Discount IDR',
            'Gross Total',
            'Net Total',
            'Payment',
            'Change'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        $rowIndex = 2;
        $num = 1;
        foreach ($lists as $list) {
            $sheet->setCellValue('A' . $rowIndex, $num++);
            $sheet->setCellValue('B' . $rowIndex, $list['invoice']);
            $sheet->setCellValue('C' . $rowIndex, $list['date_time']);
            $sheet->setCellValue('D' . $rowIndex, $list['customer']);
            $sheet->setCellValue('E' . $rowIndex, $list['discount_percent']);
            $sheet->setCellValue('F' . $rowIndex, $list['discount_idr']);
            $sheet->setCellValue('G' . $rowIndex, $list['gross_total']);
            $sheet->setCellValue('H' . $rowIndex, $list['net_total']);
            $sheet->setCellValue('I' . $rowIndex, $list['payment_amount']);
            $sheet->setCellValue('J' . $rowIndex, $list['payment_change']);
            $rowIndex++;
        }

        $filename = 'transactions_data_' . ($startDate ?: 'all') . '_to_' . ($endDate ?: 'all') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Csv($spreadsheet);
        $writer->save('php://output');

        exit();
    }
}
