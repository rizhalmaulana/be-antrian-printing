<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class WaktuSelesaiController extends ResourceController
{
    use ResponseTrait;
    protected $modelName    = 'App\Models\WaktuSelesai';
    protected $format       = 'json';
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $db = \Config\Database::connect();

        $data = [
            'status' => true,
            'code' => 200,
            'message' => 'Berhasil Mengambil Data Waktu Selesai',
            'data' => $db->query("SELECT id, jam_selesai FROM waktu_selesai")->getResultArray()
        ];

        if ($data['data'] == null) {
            return $this->failNotFound('Data Waktu Selesai Tidak Ditemukan');
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
        //
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
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
            'jam_selesai'      => 'required|is_unique[waktu_selesai.jam_selesai]',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->insert([
            'jam_selesai'      => esc($this->request->getVar('jam_selesai')),
            'created_at'    => $date_now,
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Waktu Selesai Berhasil Ditambahkan'
        ];

        return $this->respondCreated($response);
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
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
            'jam_selesai'      => 'required|is_unique[waktu_selesai.jam_selesai]',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->update($id, [
            'jam_selesai'   => 'required|is_unique[waktu_selesai.jam_selesai]',
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data Waktu Selesai Berhasil Diubah'
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
            return $this->failNotFound('Data Waktu Selesai Tidak Ditemukan');
        }

        $this->model->delete($id);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data Waktu Selesai Berhasil Dihapus'
        ];

        return $this->respondDeleted($response);
    }
}
