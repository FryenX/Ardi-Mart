<?php

namespace App\Controllers;

class Restricted extends BaseController
{
    public function index(): string
    {
        return view('errors/html/error_403');
    }
}
