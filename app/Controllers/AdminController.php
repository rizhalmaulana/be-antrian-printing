<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class AdminController extends ResourceController
{
    use ResponseTrait;
    protected $modelName = 'App\Models\Admin';
    protected $format    = 'json';
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        return view('admin/index');
    }

    public function designer()
    {
        $db = \Config\Database::connect();

        $data = [
            'status' => true,
            'code' => 200,
            'message' => 'Berhasil Mengambil Data Designer',
            'data' => $db->query("SELECT id, username FROM admin")->getResultArray()
        ];

        if ($data['data'] == null) {
            return $this->failNotFound('Data Designer Tidak Ditemukan');
        }

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
            'email_admin'    => 'required|valid_email|is_unique[admin.email_admin]',
            'password'      => 'required|min_length[8]',
            'conf_password' => 'matches[password]',
            'status'        => 'required',
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
            'email_admin'    => esc($this->request->getVar('email_admin')),
            'password'      => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'status'        => 1,
            'created_at'    => $date_now,
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data Admin Berhasil Ditambahkan'
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
            'email_admin'    => 'required|valid_email|is_unique[admin.email_admin]',
            'password'      => 'required|min_length[8]',
            'status'        => 'required',
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
            'email_admin'    => esc($this->request->getVar('email_admin')),
            'password'      => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'status'        => 1,
            'created_at'    => $date_now,
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data Admin Berhasil Diubah'
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
            return $this->failNotFound('Data Admin Tidak Ditemukan');
        }

        $this->model->delete($id);

        $response = [
            'status' => 200,
            'message' => 'Data Admin Berhasil Dihapus'
        ];

        return $this->respondDeleted($response);
    }
}
