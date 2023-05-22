<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use \Datetime;
use Pusher\Pusher;
use GuzzleHttp\Client;

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

        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        $builder = $db->table('antrian');
        $builder_schedule = $db->table('time_schedule');
        
        $request_id = $this->request->getVar('id_user');
        $request_designer = $this->request->getVar('nama_designer');
        $request_layanan = $this->request->getVar('jenis_layanan');
        $request_jam_book = $this->request->getVar('jam_booking');
        $request_jam_selesai = $this->request->getVar('jam_selesai');
        $request_tgl = $this->request->getVar('tgl_pesanan');
        $request_number = $this->request->getVar('no_handphone');
        $time_schedule = [];
        
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
        
        $difference = $this->differenceInHours($request_jam_book, $request_jam_selesai);

        if ($difference == 1) {
            $first = ["id" => 1, "time" => 300000];
            $second = ["id" => 2, "time" => 150000];

            $arr_time = array($first, $second);

        } else if ($difference == 2) {
            $first = ["id" => 1, "time" => 300000];
            $second = ["id" => 2, "time" => 300000];
            $third = ["id" => 3, "time" => 300000];
            $four = ["id" => 4, "time" => 150000];

            $arr_time = array($first, $second, $third, $four);

        } else {
            $first = ["id" => 1, "time" => 300000];
            $second = ["id" => 2, "time" => 300000];
            $third = ["id" => 3, "time" => 300000];
            $four = ["id" => 4, "time" => 150000];
            $five = ["id" => 5, "time" => 300000];
            $six = ["id" => 6, "time" => 150000];

            $arr_time = array($first, $second, $third, $four, $five, $six);
        }

        $result = $this->model->insert([
            'id_user'       => esc($request_id),
            'nama_designer' => esc($request_designer),
            'jenis_layanan' => esc($request_layanan),
            'jam_booking'   => esc($request_jam_book),
            'jam_selesai'   => esc($request_jam_selesai),
            'tgl_pesanan'   => esc($request_tgl),
            'no_handphone'  => esc($request_number),
            'status'        => "Menunggu",
            'created_at'    => $date_now,
            'updated_at'    => $date_now,
        ]);

        if ($result) {

            $result_push = $this->send_push_notification("Horee antrian kamu sudah masuk", "Pantau antrian kamu disini, jangan sampai kelewat yah.");

            $response = [
                'status' => true,
                'code' => 200,
                'message' => 'Horee antrian kamu sudah masuk, Pantau terus yah antrianmu.',
                'time_schedule' => $arr_time,
            ];
            
            return $this->respondCreated($response);
            
        } else {
            $response = [
                'status' => false,
                'code' => 400,
                'message' => 'Maaf antrian kamu gagal ditambahkan, yuk coba lagi!'
            ];
            
            return $this->respond($response, 200);
        }
    }

    function differenceInHours($startdate,$enddate){
        $starttimestamp = strtotime($startdate);
        $endtimestamp = strtotime($enddate);

        $difference = abs($endtimestamp - $starttimestamp) / 3600;
        return $difference;
    }

    public function send_push_notification($title, $message) {
        // URL FCM endpoint
        $url = 'https://fcm.googleapis.com/fcm/send';

        // Data payload untuk notifikasi
        $data = [
            'title' => $title,
            'message' => $message,
            // Tambahan data payload lainnya jika diperlukan
        ];

        // Target device token
        $deviceToken = 'dmgW7KUsRvutBOF6w2CzD0:APA91bGSWjLdJ1xhqfk23Vv36gyOD06SIaXf7LUs1KitlD2RzAYscP9UISUQ137GGd6RNrRl7NHmbZLwUsPqnz2N-CuvLz08xyWywbw82J0MAMMH_mOzkN2lZUnrh11dZoafxkSE6UNS';

        // Header authorization key
        $authorizationKey = 'AAAAkhtk95E:APA91bEUyQ9pj_fYdXy_FsWO8QN6weFZB78SKWDlC3EF4mtO1qCWPl6Ol7A8gZOrHrhva_7DMsPssgXI7k2aFgLGzpfUhYJ3z9MP-axkYXA3I82LYtq_lHydsUYvOhQuuqyvoAoT5rkt';

        // Buat HTTP client menggunakan library Guzzle
        $client = new Client();

        // Kirim request ke FCM endpoint
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'key=' . $authorizationKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'to' => $deviceToken,
                'data' => $data,
                'priority' => 'high'
            ]
        ]);

        // Tampilkan hasil response dari FCM endpoint
        return $response->getBody();
    }

    // Sends Push notification for Android users
	public function sendPush($to, $title, $body, $icon, $url) {
        $postdata = json_encode(
            [
                'notification' => 
                    [
                        'title' => $title,
                        'body' => $body,
                        'icon' => $icon,
                        'click_action' => $url
                    ]
                ,
                'to' => $to
            ]
        );
    
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/json'."\r\n"
                            .'Authorization: key='.FCM_AUTH_KEY."\r\n",
                'content' => $postdata
            )
        );
    
        $context  = stream_context_create($opts);
    
        $result = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
        if($result) {
            return json_decode($result);
        } else return false;
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