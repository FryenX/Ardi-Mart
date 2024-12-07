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

        if (!empty($username) && !empty($password)) {
            return redirect()->to('login/rememberMe');
        }
        return view('login/index');
    }

    public function rememberMe()
    {
        $username = $this->request->getCookie('username');
        $password = $this->request->getCookie('password');

        $auth = $this->users->login($username, $password);
        if ($auth) {
            session()->set([
                'isLoggedIn'  => true,
                'justLoggedIn'=> true,
                'uuid'        => $auth['uuid'],
                'name'        => $auth['name'],
                'level_info'  => $auth['info'],
                'username'    => $auth['username'],
                'image'       => $auth['image'],
            ]);
        }
        $msg = ['success' => 'Login Successful'];
        echo json_encode($msg);
        return redirect()->to('/');
    }

    public function auth()
    {
        if ($this->request->isAJAX()) {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');
            $rememberMe = $this->request->getPost('rememberMe');

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
                        'isLoggedIn'  => true,
                        'justLoggedIn'=> true,
                        'uuid'        => $auth['uuid'],
                        'name'        => $auth['name'],
                        'level_info'  => $auth['info'],
                        'username'    => $auth['username'],
                        'image'       => $auth['image'],
                    ]);

                    if ($rememberMe == '1') {
                        $expiration = 60 * 60 * 24 * 30;
                        $this->response->setCookie('username', $username, $expiration, '', '');
                        $this->response->setCookie('password', $password, $expiration, '', '');
                    }


                    $msg = ['success' => 'Login Successful'];
                } else {
                    $msg = ['failed' => 'Login Credential Doesn\'t Match'];
                }
            }

            echo json_encode($msg);
        }
    }

    public function isLoggedIn()
    {
        $justLoggedIn = session()->get('justLoggedIn');
        $msg = '';
        if($justLoggedIn == True)
        {
            session()->remove('justLoggedIn');
            $msg = [
                'login' => 'Welcome to Ardi Mart'
            ];
        }
        echo json_encode($msg);
    }

    public function forgetPassword()
    {
        return view('login/forgetPassword');
    }

    public function logout()
    {
        session()->destroy();
        // return redirect()->to('/login');
        return redirect()->deleteCookie('username')->deleteCookie('password')->to('/login');
    }
}
