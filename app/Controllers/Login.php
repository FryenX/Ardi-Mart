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

    public function credential()
    {
        return view('login/credentialAuth');
    }

    public function verifyCredential()
    {
        $credential = $this->request->getPost('credential');
        $row = $this->users
            ->where('username', $credential)
            ->orWhere('email', $credential)
            ->get()
            ->getRowArray();

        if (empty($row)) {
            $msg = [
                'error' => [
                    'errorCredential' => 'Failed can\'t find your credential in our system'
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

        $expiration = 60 * 60 * 24 * 30;
        $this->response->setCookie('password', $newPassword, $expiration, '', '');

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
            $reset_token = bin2hex(random_bytes(32));
            if (!empty($row)) {
                $this->users->update($row['uuid'], [
                    'reset_password_token' => $reset_token,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $to = $email;
                $subject = 'Reset Password Link';
                $token = $reset_token;
                $message = '<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 8px;">' .
                    '<h2 style="color: #1a73e8; text-align: center;">Password Reset Request</h2>' .
                    '<p>Dear ' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . ',</p>' .
                    '<p>We received a request to reset the password associated with your account. If you made this request, please click the button below to reset your password:</p>' .
                    '<div style="text-align: center; margin: 20px 0;">' .
                    '<a href="' . base_url() . 'login/resetPassword/' . $token . '" style="background-color: #1a73e8; color: #fff; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Reset Your Password</a>' .
                    '</div>' .
                    '<p>If you did not request this password reset, please ignore this email. The link will expire in 24 hours for your security.</p>' .
                    '<p>For any concerns or if you need further assistance, feel free to contact our support team at <a href="mailto:ardiwidana.gg@gmail.com" style="color: #1a73e8; text-decoration: none;">ardiwidana.gg@gmail.com</a>.</p>' .
                    '<hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">' .
                    '<p style="font-size: 0.9em; color: #666;">This email was sent by Ardi-Mart. Please do not reply to this email as it is not monitored.</p>' .
                    '<p style="font-size: 0.9em; color: #666;">&copy; ' . date('Y') . ' Ardi-Mart. All rights reserved.</p>' .
                    '</div>';


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
                    $msg = [
                        'error' => [
                            'errorEmail' => 'Failed to send email. Please try again later.'
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

    public function resetPassword($token)
    {
        if (!empty($token)) {
            $row = $this->users->where('reset_password_token', $token)->get()->getRowArray();
            if (!empty($row)) {
                if ($this->expired($row['updated_at'])) {
                    $data = [
                        'token' => $row['reset_password_token'],
                        'uuid' => $row['uuid']
                    ];
                } else {
                    $this->users->update($row['uuid'], [
                        'reset_password_token' => NULL,
                        'updated_at' => Time::now(),
                    ]);
                    $data = ['error' => 'Reset password link was expired.'];
                }
            }
        }

        return view('login/resetPassword', $data);
    }

    public function newPassword()
    {
        $newPassword = $this->request->getPost('newPassword');
        $uuid = $this->request->getPost('uuid');
        $user = $this->users->where('uuid', $uuid)->get()->getRowArray();

        $validation = \Config\Services::validation();

        $doValid = $this->validate([
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
            if ($user) {
                $this->users->update($uuid, [
                    'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                    'reset_password_token' => NULL,
                    'updated_at' => Time::now(),
                ]);
            }

            $msg = [
                'success' => 'Password Is Successfully Changed'
            ];
        }
        
        $expiration = 60 * 60 * 24 * 30;
        $this->response->setCookie('password', $newPassword, $expiration, '', '');

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

    public function expired($time)
    {
        $update_time = strtotime($time);
        $current_time = time();
        $timeDiff = $current_time - $update_time;

        if ($timeDiff < 300) {
            return true;
        } else {
            return false;
        }
    }
}
