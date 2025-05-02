<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class transactionsDataModel extends Model
{
    protected $table = "transactions";
    protected $column_order = array(null, 'invoice', 'date_time', 'customer', 'discount_percent', 'discount_idr', 'gross_total', 'net_total', 'payment_amount', 'payment_change', 'payment_method',null);
    protected $column_search = array('invoice', 'date_time', 'customers.name');
    protected $order = array('date_time' => 'desc');
    protected $request;
    protected $db;
    protected $dt;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
    }

    public function _get_datatables_query($startDate, $endDate)
    {
        $this->dt = $this->db->table($this->table)
            ->select('transactions.*, customers.name as customer')
            ->join('customers', 'customers.id = transactions.customer_id');

        if ($startDate && $endDate) {
            $this->dt->where("DATE(date_time) >=", $startDate);
            $this->dt->where("DATE(date_time) <=", $endDate);
        }

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


    public function get_datatables($startDate, $endDate)
    {
        $this->_get_datatables_query($startDate, $endDate);

        if ($this->request->getPost('length') != -1) {
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        }

        return $this->dt->get()->getResult();
    }

    public function count_filtered($startDate, $endDate)
    {
        $this->_get_datatables_query($startDate, $endDate);
        return $this->dt->countAllResults();
    }

    public function count_all($startDate, $endDate)
    {
        $builder = $this->db->table($this->table)
            ->select('transactions.*, customers.name as customer')
            ->join('customers', 'customers.id = transactions.customer_id');

        if ($startDate && $endDate) {
            $builder->where("DATE(date_time) >=", $startDate);
            $builder->where("DATE(date_time) <=", $endDate);
        }

        return $builder->countAllResults();
    }
}
