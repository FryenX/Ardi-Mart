<?php

namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\I18n\Time;

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
                'justLoggedIn' => true,
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
                    'rules'  => 'required|is_not_unique[users.username]',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                        'is_not_unique' => 'Username doesn\'t exist.'
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
                        'justLoggedIn' => true,
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
                    $msg = ['failed' => 'Password is Incorrect'];
                }
            }

            echo json_encode($msg);
        }
    }

    public function username()
    {
        return view('login/usernameAuth');
    }

    public function verifyUsername()
    {
        $username = $this->request->getPost('username');
        $row = $this->users->where('username', $username)->get()->getRowArray();
        $validation = \Config\Services::validation();

        $doValid = $this->validate([
            'username' => [
                'label'  => 'Username',
                'rules'  => 'required|is_not_unique[users.username]',
                'errors' => [
                    'required' => '{field} Can\'t be Empty',
                    'is_not_unique' => 'Can\'t find {field} in the system'
                ]
            ]
        ]);

        if (!$doValid) {
            $msg = [
                'error' => [
                    'errorUsername' => $validation->getError('username')
                ]
            ];
        } else {
            $msg = [
                'success' => 'success',
                'data' => [
                    'uuid' => $row['uuid']
                ]
            ];
        }
        echo json_encode($msg);
    }

    public function changePassword($uuid)
    {
        $row = $this->users->where('uuid', $uuid)->get()->getRowArray();
        $data = [
            'uuid' => $row['uuid']
        ];

        return view('login/changePassword', $data);
    }
    public function updatePassword()
    {
        $oldPassword = $this->request->getPost('oldPassword');
        $newPassword = $this->request->getPost('newPassword');
        $uuid = $this->request->getPost('uuid');
        $user = $this->users->where('uuid', $uuid)->get()->getRowArray();

        $validation = \Config\Services::validation();

        $doValid = $this->validate([
            'oldPassword' => [
                'label'  => 'Old Password',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} Can\'t be Empty'
                ]
            ],
            'newPassword' => [
                'label'  => 'New Password',
                'rules' => 'required|min_length[8]|max_length[20]|regex_match[/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/]',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'min_length' => 'Password must be at least 8 characters long.',
                    'max_length' => 'Password cannot exceed 20 characters.',
                    'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                ],
            ],
            'confirmPassword' => [
                'label'  => 'Confirm Password',
                'rules'  => 'required|matches[newPassword]',
                'errors' => [
                    'matches' => 'Password doesn\'t match',
                    'required' => '{field} Can\'t be Empty'
                ]
            ]
        ]);

        if (!$doValid) {
            $msg = [
                'error' => [
                    'errorOldPassword' => $validation->getError('oldPassword'),
                    'errorNewPassword' => $validation->getError('newPassword'),
                    'errorConfirmPassword' => $validation->getError('confirmPassword')
                ]
            ];
        } else {
            if (!password_verify($oldPassword, $user['password'])) {
                $msg = [
                    'failed' => 'The Old Password is incorrect'
                ];
            } else {
                if ($user) {
                    $this->users->update($uuid, [
                        'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                        'updated_at' => Time::now(),
                    ]);
                }

                $msg = [
                    'success' => 'Password Is Successfully Changed'
                ];
            }
        }

        echo json_encode($msg);
    }

    public function isLoggedIn()
    {
        $justLoggedIn = session()->get('justLoggedIn');
        $msg = '';
        if ($justLoggedIn == True) {
            session()->remove('justLoggedIn');
            $msg = [
                'login' => 'Welcome to Ardi Mart'
            ];
        }
        echo json_encode($msg);
    }

    public function emailAuth()
    {
        return view('login/emailAuth');
    }

    public function verifyEmail()
    {
        $email = $this->request->getPost('email');
        $row = $this->users->where('email', $email)->get()->getRowArray();
        $validation = \Config\Services::validation();

        $doValid = $this->validate([
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => '{field} Can\'t be Empty',
                    'valid_email' => '{field} is not valid',
                ]
            ]
        ]);

        if (!$doValid) {
            $msg = [
                'error' => [
                    'errorEmail' => $validation->getError('email')
                ]
            ];
        } else {
            if (!empty($row)) {
                $this->users->update($row['uuid'], [
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $to = $email;
                $subject = 'Reset Password Link';
                $token = $row['uuid'];
                $message = 'Hi ' . $row['name'] . '<br/><br/>'
                    . 'Your Reset Password Token has been received. Please click '
                    . 'the link below to reset your password. <br/><br/>'
                    . '<a href="' . base_url() . 'login/resetPassword/' . $token . '">Click Me</a>';

                $emailService = \Config\Services::email();
                $emailService->setTo($to);
                $emailService->setFrom('ardiwidana.gg@gmail.com', 'Ardi-Mart');
                $emailService->setSubject($subject);
                $emailService->setMessage($message);
                
                if ($emailService->send()) {
                    $msg = [
                        'success' => 'Email sent successfully!'
                    ];
                } else {
                    log_message('error', 'Email sending failed: ' . $emailService->printDebugger());
                    echo $emailService->printDebugger();
                    $msg = [
                        'error' => [
                            'errorEmail' => 'Failed to send email. Please try again.' . $emailService->printDebugger()
                        ]
                    ];
                }
            } else {
                $msg = [
                    'error' => [
                        'errorEmail' => 'Email not found in the system.'
                    ]
                ];
            }
        }

        echo json_encode($msg);
    }


    public function confirmEmail()
    {
        return view('login/confirmEmail');
    }

    public function logout()
    {
        session()->destroy();
        // return redirect()->to('/login');
        return redirect()->deleteCookie('username')->deleteCookie('password')->to('/login');
    }
}
