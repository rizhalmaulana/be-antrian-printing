<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class UserController extends ResourceController
{
    use ResponseTrait;
    protected $modelName    = 'App\Models\User';
    protected $format       = 'json';
    
    /**
    * Return an array of resource objects, themselves in array format
    *
    * @return mixed
    */
    public function index()
    {
        $data = [
            'status' => true,
            'code' => 200,
            'message' => 'success',
            'data' =>  $this->model->orderBy('id', 'DESC')->findAll()
        ];
        
        return $this->respond($data, 200);
    }
    
    /**
    * Return the properties of a resource object
    *
    * @return mixed
    */
    public function show($id = null)
    {
        $data = [
            'status' => true,
            'code' => 200,
            'message' => 'success',
            'data' =>  $this->model->orderBy('id', 'DESC')->find($id)
        ];

        if ($data['data'] == null) {
            return $this->failNotFound('Data User Tidak Ditemukan');
        }
        
        return $this->respond($data, 200);
    }

    public function search()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('user');

        $phone = esc($this->request->getVar('phone_user'));
        $email = esc($this->request->getVar('email_user'));
        
        helper(['form']);
        $rules = $this->validate([
            'phone_user'    => 'required|is_unique[user.phone_user]|numeric|min_length[10]|max_length[13]',
            'email_user'    => 'required|valid_email|is_unique[user.email_user]',
        ]);

        if (!$rules) {
            $check_arr = array(
                'phone_user' => $phone,
                'email_user' => $email
            );

            $query   = $db->query("SELECT id, username, nama_lengkap, alamat, jenis_kelamin, tempat_lahir, tanggal_lahir, 
            status, phone_user, email_user FROM user WHERE phone_user = '$phone' OR email_user = '$email'");

            $results = $query->getResult();
            
            $data = [
                'status' => true,
                'code' => 400,
                'message' => 'No Handphone atau Email sudah terdaftar',
                'data' =>  $results
            ];
        } else {
            $data = [
                'status' => true,
                'code' => 200,
                'message' => 'Data User belum terdaftar'
            ];
        }
        
        return $this->respond($data, 200);
    }
    
    /**
    * Create a new resource object, from "posted" parameters
    *
    * @return mixed
    */
    public function create()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('user');
        
        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        helper(['form']);
        $rules = $this->validate([
            'username'      => 'required|min_length[5]|max_length[10]',
            'nama_lengkap'  => 'required|max_length[35]',
            'password'      => 'required|min_length[8]',
            'conf_password' => 'matches[password]',
            'phone_user'    => 'required|is_unique[user.phone_user]|numeric|min_length[10]|max_length[13]',
            'email_user'    => 'required|valid_email|is_unique[user.email_user]',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->insert([
            'username'      => esc($this->request->getVar('username')),
            'nama_lengkap'  => esc($this->request->getVar('nama_lengkap')),
            'alamat'        => esc($this->request->getVar('alamat')),
            'jenis_kelamin' => esc($this->request->getVar('jenis_kelamin')),
            'tempat_lahir'  => esc($this->request->getVar('tempat_lahir')),
            'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
            'password'      => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'status'        => 1,
            'phone_user'    => esc($this->request->getVar('phone_user')),
            'email_user'    => esc($this->request->getVar('email_user')),
            'created_at'    => $date_now,
            'updated_at'    => $date_now,
        ]);

        $insert_id = $db->insertID();
        $data_find = $this->model->where('id', $insert_id)->first();

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data User Berhasil Ditambahkan',
            'data' => $data_find
        ];

        return $this->respondCreated($response);
    }
    
    /**
    * Add or update a model resource, from "posted" properties
    *
    * @return mixed
    */
    public function update($id = null)
    {
        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        helper(['form']);
        $rules = $this->validate([
            'username'      => 'required|min_length[5]|max_length[10]',
            'nama_lengkap'  => 'required|max_length[35]',
            'password'      => 'required|min_length[8]',
            'phone_user'    => 'required|numeric|min_length[10]|max_length[13]',
            'email_user'    => 'required|valid_email',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->update($id, [
            'username'      => esc($this->request->getVar('username')),
            'nama_lengkap'  => esc($this->request->getVar('nama_lengkap')),
            'alamat'        => esc($this->request->getVar('alamat')),
            'jenis_kelamin' => esc($this->request->getVar('jenis_kelamin')),
            'tempat_lahir'  => esc($this->request->getVar('tempat_lahir')),
            'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
            'password'      => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'status'        => 1,
            'phone_user'    => esc($this->request->getVar('phone_user')),
            'email_user'    => esc($this->request->getVar('email_user')),
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data User Berhasil Diubah'
        ];

        return $this->respond($response, 200);
    }
    
    /**
    * Delete the designated resource object from the model
    *
    * @return mixed
    */
    public function delete($id = null)
    {
        $data = [
            'status' => true,
            'code' => 200,
            'message' => 'success',
            'data' =>  $this->model->orderBy('id', 'DESC')->find($id)
        ];

        if ($data['data'] == null) {
            return $this->failNotFound('Data User Tidak Ditemukan');
        }

        $this->model->delete($id);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data User Berhasil Dihapus'
        ];

        return $this->respondDeleted($response);
    }
}