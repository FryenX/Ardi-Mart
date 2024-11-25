<?php

namespace App\Controllers;

use App\Models\usersModel;

class Main extends BaseController
{
    public function __construct()
    {
        $this->users = new usersModel();
    }
    public function index()
    {
        $date = date('Y-m-d');
        $data_user = $this->db->table('users')->selectCount('id')->get()->getRow();
        $new_transactions = $this->db->table('transactions')->selectCount('invoice')->like('date_time', $date)->get()->getRow();
        $data = [
            'data_user' => $data_user->id,
            'new_transactions' => $new_transactions->invoice,
        ];
        return view('templates/home', $data);
    }
}
