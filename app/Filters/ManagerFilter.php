<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ManagerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userLevel = session()->get('level_info');

        if ($userLevel !== 'Admin') {
            if (current_url() === base_url('/')) {

                return redirect()->to('/restricted');
            }

            return redirect()->to('/restricted');
        }

        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
