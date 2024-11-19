<?php

namespace App\Controllers;
use App\Models\levelsModel;

class Levels extends BaseController
{   
    protected $levels;

    public function __construct()
    {
        $this->levels = new levelsModel();
    }
    public function index(): string
    {   

        $data = $this->levels;

        $pagenumber = $this->request->getVar('levels') ? $this->request->getVar('page_levels') : 1;
        $data = [
            'levels' => $this->levels->paginate(10, 'levels'),
            'pager_levels' => $this->levels->pager,
            'pagenumber' => $pagenumber,
        ];
        return view('levels/data', $data);
    }

    function add() {
        if($this->request->isAJAX()) {
            $data = [
                'action' => $this->request->getPost('action')
            ];
            $msg = [
                'data' => view('levels/modalAddForm', $data)
            ];
            echo json_encode($msg);
        }
        else
        {
            exit('Error 404 no page found');
        }
    }

    public function saveData() 
    {
        if($this->request->isAJAX()) {
            $info = $this->request->getVar('info');

        $this->levels->insert([
            'info' => $info
        ]);

        $msg = [
            'success' => 'Level added successfully'
        ];
        echo json_encode($msg);
        }
    }

    public function delete()
    {
        if($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->levels->delete($id);

            $msg = [
                'success' => 'Level deleted successfully'
            ];
            echo json_encode($msg);
        }
    }

    function edit()
    {
        if ($this->request->isAJAX()) {
            $id =  $this->request->getVar('id');

            $getLevel = $this->levels->find($id);
            $data = [
                'id' => $id,
                'info' => $getLevel['info']
            ];

            $msg = [
                'data' => view('levels/modalEditForm', $data)
            ];
            echo json_encode($msg);
        }
    }

    function update()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');
            $info = $this->request->getVar('info');

            $this->levels->update($id, [
                'info' => $info
            ]);

            $msg = [
                'success' => 'Level edited successfully'
            ];
            echo json_encode($msg);
        }
    }

}
