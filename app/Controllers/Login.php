<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Login extends BaseController
{
    protected $users;
    public function __construct()
    {
        $this->users = new UsersModel();
    }
    public function index()
    {
        $username = $this->request->getCookie('username');
        $password = $this->request->getCookie('password');

        if ($username && $password) {
            $auth = $this->users->login($username, $password);
            if ($auth) {
                session()->set([
                    'isLoggedIn' => true,
                    'uuid'        => $auth['uuid'],
                    'name'        => $auth['name'],
                    'username'    => $auth['username'],
                    'image'       => $auth['image'],
                ]);
                return redirect()->to(base_url());
            }
        }

        return view('login/index');
    }

    public function auth()
    {
        if ($this->request->isAJAX()) {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');
            $rememberMe = $this->request->getVar('rememberMe');

            $validation = \Config\Services::validation();

            $doValid = $this->validate([
                'username' => [
                    'label'  => 'Username',
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                    ]
                ],
                'password' => [
                    'label'  => 'Password',
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                    ]
                ],
            ]);

            if (!$doValid) {
                $msg = [
                    'error' => [
                        'errorUserName' => $validation->getError('username'),
                        'errorPassword' => $validation->getError('password'),
                    ]
                ];
            } else {
                $auth = $this->users->login($username, $password);
                if ($auth) {
                    session()->set([
                        'isLoggedIn' => true,
                        'uuid'        => $auth['uuid'],
                        'name'        => $auth['name'],
                        'level_info'  => $auth['info'],
                        'username'    => $auth['username'],
                        'image'       => $auth['image'],
                    ]);

                    if ($rememberMe == '1') {
                        $this->response->setCookie('username', $username, time() + 60 * 60 * 24 * 30, '/');
                        $this->response->setCookie('password',  $password, time() + 60 * 60 * 24 * 30, '/');
                    }

                    $msg = ['success' => 'Login Successful'];
                } else {
                    $msg = ['failed' => 'Login Credential Doesn\'t Match'];
                }
            }

            echo json_encode($msg);
        }
    }

    public function logout()
    {
        $this->response->deleteCookie('username', '/');
        $this->response->deleteCookie('password', '/');
        session()->destroy();

        return redirect()->to('/login');
    }
}
