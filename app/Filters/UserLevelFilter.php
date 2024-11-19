<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class UserLevelFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userLevel = session()->get('level_info');

        if ($userLevel !== 'Admin' && $userLevel !== 'Manager') {
            return redirect()->to('/restricted');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
