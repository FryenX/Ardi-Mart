<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class transactionsDataModel extends Model
{
    protected $table = "transactions";
    protected $column_order = array(null, 'invoice', 'date_time', 'customer', 'discount_percent', 'discount_idr', 'gross_total', 'net_total', 'payment_amount', 'payment_change', null); // Columns for DataTables
    protected $column_search = array('invoice', 'date_time', 'customers.name'); // Include customers.name for search
    protected $order = array('date_time' => 'desc');
    protected $request;
    protected $db;
    protected $dt;

    // Constructor
    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
    }

    // Private function to handle the query building
    public function _get_datatables_query($date)
    {
        // Start building the base query
        $this->dt = $this->db->table($this->table)
            ->select('transactions.*, customers.name as customer')  // Select customer field as well
            ->join('customers', 'customers.id = transactions.customer_id');

        // Filter by date if provided
        if ($date) {
            $this->dt->where('DATE(date_time)', $date); // Filter by the provided date
        }

        // Apply search filter if search value exists
        $searchValue = $this->request->getPost('search')['value'];
        if ($searchValue) {
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->dt->groupStart();
                    $this->dt->like($item, $searchValue);
                } else {
                    $this->dt->orLike($item, $searchValue);
                }
                if (count($this->column_search) - 1 == $i) {
                    $this->dt->groupEnd();
                }
                $i++;
            }
        }

        // Order functionality
        if ($this->request->getPost('order')) {
            $this->dt->orderBy(
                $this->column_order[$this->request->getPost('order')[0]['column']],
                $this->request->getPost('order')[0]['dir']
            );
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->dt->orderBy(key($order), $order[key($order)]);
        }
    }


    // Public function to fetch data for the datatables
    public function get_datatables($date)
    {
        $this->_get_datatables_query($date);

        if ($this->request->getPost('length') != -1) {
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        }

        $query = $this->dt->get();
        return $query->getResult();
    }

    // Function to count filtered records
    public function count_filtered($date)
    {
        $this->_get_datatables_query($date);
        return $this->dt->countAllResults();
    }

    // Function to count all records
    public function count_all($date)
    {
        $query = $this->db->table($this->table)
            ->select('transactions.*, customers.name as customer')
            ->join('customers', 'customers.id = transactions.customer_id');

        // Apply date filter if provided
        if ($date) {
            $query->where('DATE(date_time)', $date);
        }

        return $query->countAllResults();
    }
}
