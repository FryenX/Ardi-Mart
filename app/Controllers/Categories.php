<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CategoriesModel;

class Categories extends BaseController
{
    protected $categories;
    public function __construct()
    {
        $this->categories = new CategoriesModel();
    }
    public function index()
    {
        $searchBtn = $this->request->getPost('btn_category');
        if (isset($searchBtn)) {
            $search = $this->request->getPost('search_category');
            session()->set('search_category', $search);
            redirect()->to('/categories');
        } else {
            $search = session()->get('search_category');
        }

        $data_categories = $search ? $this->categories->searchData($search) : $this->categories;

        $pagenumber = $this->request->getVar('page_categories') ? $this->request->getVar('page_categories') : 1;
        $data = [
            'query' => $data_categories->paginate(10, 'categories'),
            'pager_categories' => $this->categories->pager,
            'pagenumber' => $pagenumber,
            'search' => $search
        ];
        return view('categories/data', $data);
    }

    function add()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'action' => $this->request->getPost('action')
            ];
            $msg = [
                'data' => view('categories/modalAddForm', $data)
            ];
            echo json_encode($msg);
        } else {
            exit('Error 404 no page found');
        }
    }

    public function saveData()
    {
        if ($this->request->isAJAX()) {
            $name = $this->request->getVar('name');

            $this->categories->insert([
                'name' => $name
            ]);

            $msg = [
                'success' => 'Category added successfully'
            ];
            echo json_encode($msg);
        }
    }

    public function delete()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->categories->delete($id);

            $msg = [
                'success' => 'Category deleted successfully'
            ];
            echo json_encode($msg);
        }
    }

    function edit()
    {
        if ($this->request->isAJAX()) {
            $id =  $this->request->getVar('id');

            $getCategory = $this->categories->find($id);
            $data = [
                'id' => $id,
                'name' => $getCategory['name']
            ];

            $msg = [
                'data' => view('categories/modalEditForm', $data)
            ];
            echo json_encode($msg);
        }
    }

    function update()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');
            $name = $this->request->getVar('name');

            $this->categories->update($id, [
                'name' => $name
            ]);

            $msg = [
                'success' => 'Category edited successfully'
            ];
            echo json_encode($msg);
        }
    }
}
