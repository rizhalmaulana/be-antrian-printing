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
    
    /**
    * Create a new resource object, from "posted" parameters
    *
    * @return mixed
    */
    public function create()
    {
        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        helper(['form']);
        $rules = $this->validate([
            'username'      => 'required|min_length[5]|max_length[10]',
            'nama_lengkap'  => 'required|max_length[35]',
            'alamat'        => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir'  => 'required',
            'tanggal_lahir' => 'required',
            'password'      => 'required|min_length[8]',
            'conf_password' => 'matches[password]',
            'status'        => 'required',
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

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data User Berhasil Ditambahkan'
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
            'alamat'        => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir'  => 'required',
            'tanggal_lahir' => 'required',
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