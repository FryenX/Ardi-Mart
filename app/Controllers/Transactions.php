<?php

namespace App\Controllers;

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
}
