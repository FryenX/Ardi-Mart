<?php

namespace App\Controllers;

class Templates extends BaseController
{
    public function index(): string
    {
        return view('templates/home');
    }
}
