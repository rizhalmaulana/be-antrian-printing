<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use \Datetime;

class AntrianController extends ResourceController
{
    use ResponseTrait;
    protected $modelName = 'App\Models\Antrian';
    protected $format    = 'json';
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
            'data' =>  $this->model->findAll()
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
        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d');

        $array_where = array(
            'id_user' => $id, 
            'tgl_pesanan' => $date_now, 
            'status' => "Menunggu"
        );

        $data = [
            'status' => true,
            'code' => 200,
            'message' => 'Berhasil Mengambil Data Antrian',
            'data' => $this->model->where($array_where)->first()
        ];

        if ($data['data'] == null) {
            $response = [
                'status' => false,
                'code' => 400,
                'message' => 'Data Antrian Tidak Ditemukan.'
            ];
    
            return $this->respondCreated($response);
        }

        return $this->respond($data, 200);
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

    public function riwayat($id = null)
    {
        $data = [
            'status' => true,
            'code' => 200,
            'message' => 'success',
            'data' => $this->model->where('id_user', $id)->orderBy('id', 'DESC')->findAll()
        ];

        return $this->respond($data, 200);
    }
    /**
     * Check a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function check()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('antrian');
        $builder_user = $db->table('user');
        $builder_designer = $db->table('admin');
        $builder_booking = $db->table('waktu_booking');
        $builder_selesai = $db->table('waktu_selesai');

        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d');
        $time_now = date('H:i:s');

        $request_id = $this->request->getVar('id_user');
        $request_designer = $this->request->getVar('nama_designer');
        $request_layanan = $this->request->getVar('jenis_layanan');
        $request_jam_book = $this->request->getVar('jam_booking');
        $request_jam_selesai = $this->request->getVar('jam_selesai');
        $request_tgl = $this->request->getVar('tgl_pesanan');
        $request_number = $this->request->getVar('no_handphone');

        helper(['form']);
        $rules = $this->validate([
            'id_user'       => 'required',
            'nama_designer' => 'required|min_length[5]',
            'jenis_layanan' => 'required|min_length[5]',
            'jam_booking'   => 'required',
            'jam_selesai'   => 'required',
            'tgl_pesanan'   => 'required',
            'no_handphone'  => 'required|is_unique[antrian.no_handphone]|numeric|min_length[10]|max_length[13]',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        // Cek data di builder table custom
        $cek_user_available         = $builder_user->where('id', $request_id)->countAllResults();
        $cek_designer_available     = $builder_designer->where('username', $request_designer)->countAllResults();
        $cek_jam_booking_available  = $builder_booking->where('jam_booking', $request_jam_book)->countAllResults();
        $cek_jam_selesai_available  = $builder_selesai->where('jam_selesai', $request_jam_selesai)->countAllResults();

        $array_where_user_booking = array(
            'id_user' => $request_id, 
            'tgl_pesanan' => $request_tgl,
            'status' => "Menunggu"
        );

        $array_date_book = array(
            'tgl_pesanan' => $request_tgl, 
            'jam_booking' => $request_jam_book, 
            'jam_selesai' => $request_jam_selesai
        );

        // Cek data di builder table antrian 
        $cek_date_booking       = $builder->where($array_date_book)->countAllResults();
        $cek_user_booking       = $builder->where($array_where_user_booking)->countAllResults();
        $cek_designer_booking   = $builder->where('nama_designer', $request_designer)->countAllResults();
        $cek_waktu_booking      = $builder->where('jam_booking', $request_jam_book)->countAllResults();
        $cek_waktu_selesai      = $builder->where('jam_selesai', $request_jam_selesai)->countAllResults();

        $from_time      = strtotime($time_now); 
        $to_time_book   = strtotime($request_jam_book);
        $to_time_end    = strtotime($request_jam_selesai); 

        $diff_book  = ($to_time_book - $from_time) / 60;
        $diff_end   = ($to_time_end - $from_time) / 60;

        if ($cek_user_available > 0) {
            if ($cek_designer_available > 0) {
                if ($cek_date_booking > 0) {
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => 'Jadwal yang anda pesan sudah terisi, silahkan pilih opsi lain.'
                    ];
            
                    return $this->respond($response, 200);

                } else {
                    if ($cek_user_booking > 0) {
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Kamu sedang dalam antrian, silahkan selesaikan.'
                        ];
                
                        return $this->respond($response, 200);
                        
                    } else if ($cek_jam_booking_available < 0) { // Cek Jam Booking di table
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Waktu booking tidak tersedia.'
                        ];
                
                        return $this->respond($response, 200);

                    } else if ($cek_jam_selesai_available < 0) { // Cek Jam Selesai di table
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Waktu selesai tidak tersedia.'
                        ];
                
                        return $this->respond($response, 200);

                    } else if ($diff_book <= 60) { // Compare waktu booking kurang dari 60 menit
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Minimal booking antrian 1 jam dari waktu saat ini.'
                        ];
                
                        return $this->respond($response, 200);

                    }  else if ($diff_end >= 180) { // Waktu booking lebih dari 180 menit
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Maksimal booking antrian yaitu 3 jam pelayanan dari waktu saat ini.'
                        ];
                
                        return $this->respond($response, 200);

                    } else if (strtotime($request_jam_selesai) <= strtotime($time_now)) {
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Jam selesai sudah terlewat dari jam saat ini'
                        ];
                
                        return $this->respond($response, 200);

                    } else if (strtotime($request_jam_book) >= strtotime($request_jam_selesai)) {
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Jam booking melebihi atau sama dari jam selesai'
                        ];
                
                        return $this->respond($response, 200);

                    } else if ($cek_waktu_booking > 0) {
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Waktu booking sudah terisi, silahkan pilih opsi lain.'
                        ];
                
                        return $this->respond($response, 200);

                    } else if ($cek_waktu_selesai > 0) {
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Waktu selesai sudah terisi, silahkan pilih opsi lain.'
                        ];
                
                        return $this->respond($response, 200);

                    } else if ($cek_designer_booking > 1 ) {
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Jadwal designer sedang penuh, silahkan pilih opsi lain.'
                        ];
                
                        return $this->respond($response, 200);

                    } else {
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => 'Data antrian tersedia, silahkan konfirmasi untuk melanjutkan.'
                        ];
                
                        return $this->respond($response, 200);
                    }
                }
            } else {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => 'Designer tidak terdaftar, silahkan pilih opsi designer lain'
                ];
        
                return $this->respond($response, 200);

            }
        } else {
            $response = [
                'status' => false,
                'code' => 400,
                'message' => 'User tidak terdaftar.'
            ];
    
            return $this->respond($response, 200);
        }
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('antrian');

        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        $request_id = $this->request->getVar('id_user');
        $request_designer = $this->request->getVar('nama_designer');
        $request_layanan = $this->request->getVar('jenis_layanan');
        $request_jam_book = $this->request->getVar('jam_booking');
        $request_jam_selesai = $this->request->getVar('jam_selesai');
        $request_tgl = $this->request->getVar('tgl_pesanan');
        $request_number = $this->request->getVar('no_handphone');

        helper(['form']);
        $rules = $this->validate([
            'id_user'       => 'required',
            'nama_designer' => 'required|min_length[5]',
            'jenis_layanan' => 'required|min_length[5]',
            'jam_booking'   => 'required',
            'jam_selesai'   => 'required',
            'tgl_pesanan'   => 'required',
            'no_handphone'  => 'required|is_unique[antrian.no_handphone]|numeric|min_length[10]|max_length[13]',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }
        
        $this->model->insert([
            'id_user'       => esc($this->request->getVar('id_user')),
            'nama_designer' => esc($this->request->getVar('nama_designer')),
            'jenis_layanan' => esc($this->request->getVar('jenis_layanan')),
            'jam_booking'   => esc($this->request->getVar('jam_booking')),
            'jam_selesai'   => esc($this->request->getVar('jam_selesai')),
            'tgl_pesanan'   => esc($this->request->getVar('tgl_pesanan')),
            'no_handphone'  => esc($this->request->getVar('no_handphone')),
            'status'        => "Menunggu",
            'created_at'    => $date_now,
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Horee antrian kamu sudah masuk, Pantau terus yah antrianmu.'
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
            'id_user'       => 'required',
            'nama_designer' => 'required|min_length[5]',
            'jenis_layanan' => 'required|min_length[5]',
            'jam_booking'   => 'required',
            'jam_selesai'   => 'required',
            'tgl_pesanan'   => 'required',
            'no_handphone'  => 'required|is_unique[antrian.no_handphone]|numeric|min_length[10]|max_length[13]',
            'status'        => 'required',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->update($id, [
            'id_user'       => esc($this->request->getVar('id_user')),
            'nama_designer' => esc($this->request->getVar('nama_designer')),
            'jenis_layanan' => esc($this->request->getVar('jenis_layanan')),
            'jam_booking'   => esc($this->request->getVar('jam_booking')),
            'jam_selesai'   => esc($this->request->getVar('jam_selesai')),
            'tgl_pesanan'   => esc($this->request->getVar('tgl_pesanan')),
            'no_handphone'  => esc($this->request->getVar('no_handphone')),
            'status'        => esc($this->request->getVar('status')),
            'updated_at'    => $date_now,
        ]);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Data Antrian Berhasil Diubah'
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
        //
    }
}
