<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class WaktuBookingController extends ResourceController
{
    use ResponseTrait;
    protected $modelName    = 'App\Models\WaktuBooking';
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
            'message' => 'Berhasil Mengambil Data Waktu Booking',
            'data' => $db->query("SELECT id, jam_booking FROM waktu_booking")->getResultArray()
        ];

        if ($data['data'] == null) {
            return $this->failNotFound('Data Waktu Booking Tidak Ditemukan');
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
            'jam_booking'      => 'required|is_unique[waktu_booking.jam_booking]',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->insert([
            'jam_booking'      => esc($this->request->getVar('jam_booking')),
            'created_at'    => $date_now,
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Waktu Booking Berhasil Ditambahkan'
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
            'jam_booking'      => 'required|is_unique[waktu_booking.jam_booking]',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->update($id, [
            'jam_booking'   => 'required|is_unique[waktu_booking.jam_booking]',
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data Waktu Booking Berhasil Diubah'
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
            return $this->failNotFound('Data Waktu Booking Tidak Ditemukan');
        }

        $this->model->delete($id);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data Waktu Booking Berhasil Dihapus'
        ];

        return $this->respondDeleted($response);
    }
}