<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class productsModalDataModel extends Model
{
    protected $table = 'products';
    protected $column_order = array(null, 'barcode', 'name', 'category_name', 'stocks', 'sell_price', null);
    protected $column_search = array('barcode', 'products.name');
    protected $order = array('name' => 'DESC');
    protected $request;
    protected $db;
    protected $dt;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
    }

    private function _get_datatables_query($keyword)
    {
        if (strlen($keyword) == 0 ) {
            $this->dt = $this->db->table($this->table)
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id');
        } else {
            $this->dt = $this->db->table($this->table)
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->like('barcode', $keyword)->orLike('products.name', $keyword);
        }
        
        $i = 0;
        foreach ($this->column_search as $item) {
            if ($this->request->getPost('search')['value']) {
                if ($i === 0) {
                    $this->dt->groupStart();
                    $this->dt->like($item, $this->request->getPost('search')['value']);
                } else {
                    $this->dt->orLike($item, $this->request->getPost('search')['value']);
                }
                if (count($this->column_search) - 1 == $i) {
                    $this->dt->groupEnd();
                }
            }
            $i++;
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

    public function get_datatables($keyword)
    {
        $this->_get_datatables_query($keyword);

        if ($this->request->getPost('length') != -1) {
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        }

        $query = $this->dt->get();
        return $query->getResult();
    }

    public function count_filtered($keyword)
    {
        $this->_get_datatables_query($keyword);
        return $this->dt->countAllResults();
    }

    public function count_all($keyword)
    {
        if (strlen($keyword) == 0 ) {
            $tbl_storage = $this->db->table($this->table)
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id');
        } else {
            $tbl_storage = $this->db->table($this->table)
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->like('barcode', $keyword)->orLike('products.name', $keyword);
        }
        return $tbl_storage->countAllResults();
    }
}
