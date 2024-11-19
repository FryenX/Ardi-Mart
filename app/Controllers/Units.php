<?php

namespace App\Controllers;

use App\Models\unitsModel;
use Config\Services;
use App\Models\unitsDataModel;

class Units extends BaseController
{
    protected $units;

    public function index()
    {
        return view('units/data');
    }

    public function __construct()
    {
        $this->units = new unitsModel();
    }

    public function showUnitData()
    {
        if ($this->request->isAJAX()) {
            $request = Services::request();
            $unitData = new unitsDataModel($request);
            if ($request->getMethod(true) == 'POST') {
                $lists = $unitData->get_datatables();
                $data = [];
                $no = $request->getPost("start");
                foreach ($lists as $list) {
                    $no++;

                    $deleteBtn = "<button type=\"button\" class=\"btn btn-sm btn-danger\" onclick=\"deleteItem('" . $list->id . "','" . $list->name . "')\"><i class=\"fa fa-trash-alt\"></i></button>";
                    $editBtn = "<button type=\"button\" class=\"btn btn-sm btn-success\" onclick=\"formEdit('" . $list->id . "')\"><i class=\"fa fa-edit\"></i></button>";

                    $row = [];
                    $row[] = $no;
                    $row[] = $list->name;
                    $row[] = $editBtn . ' ' . $deleteBtn;
                    $data[] = $row;
                }
                $output = [
                    "draw" => $request->getPost('draw'),
                    "recordsTotal" => $unitData->count_all(),
                    "recordsFiltered" => $unitData->count_filtered(),
                    "data" => $data
                ];
                echo json_encode($output);
            }
        }
    }

    function add()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'action' => $this->request->getPost('action')
            ];
            $msg = [
                'data' => view('units/modalAddForm', $data)
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

            $this->units->insert([
                'name' => $name
            ]);

            $msg = [
                'success' => 'Unit Added Successfully'
            ];
            echo json_encode($msg);
        }
    }

    public function delete()
    {
        if($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->units->delete($id);

            $msg = [
                'success' => 'Unit deleted successfully'
            ];
            echo json_encode($msg);
        }
    }

    function edit()
    {
        if ($this->request->isAJAX()) {
            $id =  $this->request->getVar('id');

            $getUnit = $this->units->find($id);
            $data = [
                'id' => $id,
                'name' => $getUnit['name']
            ];

            $msg = [
                'data' => view('units/modalEditForm', $data)
            ];
            echo json_encode($msg);
        }
    }

    function update()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');
            $name = $this->request->getVar('name');

            $this->units->update($id, [
                'name' => $name
            ]);

            $msg = [
                'success' => 'unit edited successfully'
            ];
            echo json_encode($msg);
        }
    }
}
