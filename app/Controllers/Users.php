<?php

namespace App\Controllers;

use App\Models\usersModel;
use App\Models\levelsModel;
use Ramsey\Uuid\Uuid;
use CodeIgniter\I18n\Time;

class Users extends BaseController
{

    protected $users;
    protected $levels;

    public function __construct()
    {
        $this->users = new usersModel();
        $this->levels = new levelsModel();
        $this->db = db_connect();
    }
    public function index()
    {

        $searchBtn = $this->request->getPost('searchUserBtn');
        if (isset($searchBtn)) {
            $search = $this->request->getPost('searchUser');
            session()->set('searchUser', $search);
            redirect()->to('/users');
        } else {
            $search = session()->get('searchUser');
        }

        $data_user  = $search ? $this->users->searchData($search) : $this->users->select('users.id AS user_id, levels.id AS level_id, users.*, levels.*')->join('levels', 'levels.id=users.level_id')->orderBy('users.level_id', 'ASC');

        $pagenumber = $this->request->getVar('page_users') ? $this->request->getVar('page_users') : 1;
        $data = [
            'query' => $data_user->paginate(10, 'users'),
            'pager_user' => $data_user->pager,
            'pagenumber' => $pagenumber,
            'search' => $search
        ];
        return view('users/data', $data);
    }

    public function add()
    {
        return view('users/addForm');
    }
    public function fetchDataLevels()
    {
        if ($this->request->isAJAX()) {
            $dataLevels = $this->levels->findAll();

            $levelsValue = "<option value='' selected> --- Choose --- </option>";

            foreach ($dataLevels as $row) :
                $levelsValue .= '<option value="' . $row['id'] . '">' . $row['info'] . '</option>';
            endforeach;

            $msg = [
                'data' => $levelsValue
            ];
            echo json_encode($msg);
        }
    }

    public function saveData()
    {
        $uuid = Uuid::uuid4()->toString();

        if ($this->request->isAJAX()) {
            $name     = $this->request->getPost('name');
            $username = $this->request->getPost('username');
            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $level    = $this->request->getPost('level');

            $validation = \Config\Services::validation();

            $doValid = $this->validate([
                'name' => [
                    'label'  => 'Name',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => '{field} Can\'t be Empty'
                    ]
                ],
                'username' => [
                    'label'  => 'Username',
                    'rules'  => 'required|is_unique[users.username]',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                        'is_unique' => '{field} Already Existed'
                    ]
                ],
                'email' => [
                    'label'  => 'Email',
                    'rules'  => 'required|is_unique[users.email]|valid_email',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                        'is_unique' => '{field} Already Existed'
                    ]
                ],
                'password' => [
                    'label' => 'Password',
                    'rules' => 'required|min_length[8]|max_length[20]|regex_match[/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/]',
                    'errors' => [
                        'required' => 'The {field} field is required.',
                        'min_length' => 'Password must be at least 8 characters long.',
                        'max_length' => 'Password cannot exceed 20 characters.',
                        'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                    ]
                ],
                'password_confirm' => [
                    'label' => 'Confirm Password',
                    'rules' => 'required|matches[password]',
                    'errors' => [
                        'required' => '{field} Can\'t be Empty',
                        'matches' => 'Password confirmation does not match the password.'
                    ]
                ],
                'level' => [
                    'label' => 'Level',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} Can\'t be Empty',
                    ]
                ],
                'upload_image' => [
                    'label' => 'Image',
                    'rules' => 'mime_in[image,image/png,image/jpeg]|ext_in[image,png,jpg]|is_image[image]',
                ]
            ]);

            if (!$doValid) {
                $msg = [
                    'error' => [
                        'errorName' => $validation->getError('name'),
                        'errorUserName' => $validation->getError('username'),
                        'errorEmail' => $validation->getError('email'),
                        'errorPassword' => $validation->getError('password'),
                        'errorPasswordConfirm' => $validation->getError('password_confirm'),
                        'errorLevel' => $validation->getError('level'),
                        'errorUploadImage' => $validation->getError('upload_image')
                    ]
                ];
            } else {
                $file_upload = $_FILES['image']['name'];

                if ($file_upload != NULL) {
                    $image_name = "$username-$name";
                    $image_file = $this->request->getFile('image');
                    $image_file->move('assets/upload/users/', $image_name . '.' . $image_file->getExtension());

                    $path_image = '/assets/upload/users/' . $image_file->getName();
                } else {
                    $path_image = '';
                }

                $this->users->insert([
                    'uuid' => $uuid,
                    'name' => $name,
                    'level_id' => $level,
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'image' => $path_image,
                    'created_at' => Time::now(),
                    'updated_at' => Time::now(),
                ]);

                $msg = ['success' => 'User Successfully Added'];
            }

            echo json_encode($msg);
        }
    }

    public function edit($uuid)
    {
        $row = $this->users->where('uuid', $uuid)->first();

        if ($row) {
            $data = [
                'id'       => $row['id'],
                'uuid'     => $row['uuid'],
                'name'     => $row['name'],
                'level_id' => $row['level_id'],
                'data_level' => $this->levels->findAll(),
                'username' => $row['username'],
                'email'    => $row['email'],
                'image'    => $row['image'],
            ];
            return view('users/editForm', $data);
        } else {
            exit('No Data Found 404');
        }
    }

    public function delete()
    {
        if ($this->request->isAJAX()) {
            $uuid = $this->request->getVar('uuid');
            $rowDataUsers = $this->users->find($uuid);

            if ($rowDataUsers && !empty($rowDataUsers['image'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . $rowDataUsers['image'];

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $this->users->delete($uuid);

            $msg = [
                'success' => 'User Deleted Successfully'
            ];

            echo json_encode($msg);
        }
    }

    public function updateData()
    {
        if ($this->request->isAJAX()) {

            $uuid     = $this->request->getVar('uuid');
            $name     = $this->request->getVar('name');
            $username = $this->request->getVar('username');
            $email    = $this->request->getVar('email');
            $level    = $this->request->getVar('level');

            $validation = \Config\Services::validation();

            $doValid = $this->validate([
                'name' => [
                    'label'  => 'Name',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => '{field} Can\'t be Empty'
                    ]
                ],
                'username' => [
                    'label'  => 'Username',
                    'rules'  => 'required|is_unique[users.username,uuid,' . $uuid . ']',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                        'is_unique'  => '{field} Already Existed',
                    ]
                ],
                'email' => [
                    'label'  => 'Email',
                    'rules'  => 'required|valid_email|is_unique[users.email,uuid,' . $uuid . ']',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                        'is_unique'  => '{field} Already Existed',
                    ]
                ],
                'level' => [
                    'label' => 'Level',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} Can\'t be Empty',
                    ],
                ],
                'upload_image' => [
                    'label' => 'Image',
                    'rules' => 'mime_in[image,image/png,image/jpeg]|ext_in[image,png,jpg]|is_image[image]',
                ]
            ]);

            if (!$doValid) {
                $msg = [
                    'error' => [
                        'errorName' => $validation->getError('name'),
                        'errorUserName' => $validation->getError('username'),
                        'errorEmail' => $validation->getError('email'),
                        'errorLevel' => $validation->getError('level'),
                        'errorUploadImage' => $validation->getError('upload_image')
                    ]
                ];
            } else {
                $uuid = $this->request->getVar('uuid');
                $file_upload = $_FILES['image']['name'];

                $rowDataUsers = $this->users->find($uuid);

                if ($file_upload != NULL) {
                    if ($rowDataUsers && !empty($rowDataUsers['image'])) {
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . $rowDataUsers['image'];

                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                    $image_name = "$username-$name";
                    $image_file = $this->request->getFile('image');
                    $image_file->move('assets/upload/users/', $image_name . '.' . $image_file->getExtension());

                    $path_image = '/assets/upload/users/' . $image_file->getName();
                } else {
                    $path_image = $rowDataUsers['image'];
                }

                $this->users->update($uuid, [
                    'name' => $name,
                    'level_id' => $level,
                    'username' => $username,
                    'email' => $email,
                    'image' => $path_image,
                    'updated_at' => Time::now(),
                ]);

                $currentId = session()->get('uuid');
                if ($uuid == $currentId) {
                    $data = $this->users->join('levels', 'levels.id = level_id')->where('uuid', $uuid)->get()->getRowArray();
                    if ($data) {
                        session()->set([
                            'name'        => $data['name'],
                            'level_info'  => $data['info'],
                            'username'    => $data['username'],
                            'image'       => $data['image'],
                        ]);
                    }
                }

                $msg = ['success' => 'User Successfully Edited'];
            }

            echo json_encode($msg);
        }
    }

    public function changePassword($uuid)
    {
        $row = $this->users->where('uuid', $uuid)->first();

        if ($row) {
            $data = [
                'uuid'      => $row['uuid'],
                'password'  => $row['password'],
            ];
            return view('users/changePassword', $data);
        } else {
            exit('No Data Found 404');
        }
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

    public function profile($uuid)
    {
        $row = $this->users->join('levels', 'levels.id = users.level_id')->where('uuid', $uuid)->first();

        if ($row) {
            $data = [
                'uuid'     => $row['uuid'],
                'name'     => $row['name'],
                'level'    => $row['info'],
                'username' => $row['username'],
                'email'    => $row['email'],
                'image'    => $row['image'],
            ];
            return view('users/profile', $data);
        } else {
            exit('No Data Found 404');
        }
    }

    public function editProfile($uuid)
    {
        $row = $this->users->join('levels', 'levels.id = users.level_id')->where('uuid', $uuid)->first();
        $rowLevel = $this->levels->where('levels.id', $row['level_id'])->first();

        if ($row) {
            $data = [
                'uuid'          => $row['uuid'],
                'name'          => $row['name'],
                'level_id'      => $row['level_id'],
                'data_level'    => $rowLevel['info'],
                'username'      => $row['username'],
                'email'         => $row['email'],
                'image'         => $row['image'],
            ];
            return view('users/editProfile', $data);
        } else {
            exit('No Data Found 404');
        }
    }

    public function saveUserData()
    {
        if ($this->request->isAJAX()) {

            $uuid     = $this->request->getVar('uuid');
            $name     = $this->request->getVar('name');
            $username = $this->request->getVar('username');
            $email    = $this->request->getVar('email');

            $validation = \Config\Services::validation();

            $doValid = $this->validate([
                'name' => [
                    'label'  => 'Name',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => '{field} Can\'t be Empty'
                    ]
                ],
                'username' => [
                    'label'  => 'Username',
                    'rules'  => 'required|is_unique[users.username,uuid,' . $uuid . ']',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                        'is_unique'  => '{field} Already Existed',
                    ]
                ],
                'email' => [
                    'label'  => 'Email',
                    'rules'  => 'required|valid_email|is_unique[users.email,uuid,' . $uuid . ']',
                    'errors' => [
                        'required'  => '{field} Can\'t be empty',
                        'is_unique'  => '{field} Already Existed',
                    ]
                ],
                'upload_image' => [
                    'label' => 'Image',
                    'rules' => 'mime_in[image,image/png,image/jpeg]|ext_in[image,png,jpg]|is_image[image]',
                ]
            ]);

            if (!$doValid) {
                $msg = [
                    'error' => [
                        'errorName' => $validation->getError('name'),
                        'errorUserName' => $validation->getError('username'),
                        'errorEmail' => $validation->getError('email'),
                        'errorUploadImage' => $validation->getError('upload_image')
                    ]
                ];
            } else {
                $uuid = $this->request->getVar('uuid');
                $file_upload = $_FILES['image']['name'];

                $rowDataUsers = $this->users->find($uuid);

                if ($file_upload != NULL) {
                    if ($rowDataUsers && !empty($rowDataUsers['image'])) {
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . $rowDataUsers['image'];

                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                    $image_name = "$username-$name";
                    $image_file = $this->request->getFile('image');
                    $image_file->move('assets/upload/users/', $image_name . '.' . $image_file->getExtension());

                    $path_image = '/assets/upload/users/' . $image_file->getName();
                } else {
                    $path_image = $rowDataUsers['image'];
                }

                $this->users->update($uuid, [
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'image' => $path_image,
                    'updated_at' => Time::now(),
                ]);

                $data = $this->users->where('uuid', $uuid)->get()->getRowArray();
                if ($data) {
                    session()->set([
                        'name'        => $data['name'],
                        'username'    => $data['username'],
                        'image'       => $data['image'],
                    ]);
                }

                $msg = ['success' => 'User Successfully Edited'];
            }

            echo json_encode($msg);
        }
    }
}
