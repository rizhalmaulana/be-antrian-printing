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
        $db = \Config\Database::connect();
        
        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d');
        
        $array_where = array(
            'id_user' => $id, 
            'tgl_pesanan' => $date_now, 
            'status' => "Menunggu"
        );

        $find_id_antrian = $this->model->where($array_where)->find();

        $array_where_flagging = array(
            'id_antrian' => $find_id_antrian[0]['id'],
        );

        $builder = $db->table('flagging_antrian');
        $builder->select('*');
        $builder->orderBy('id_antrian','DESC');
        $builder->limit(1);
        
        if ($builder->countAllResults() > 0) {
            $query = $builder->get();
            $result = $query->getResult(); // Result as objects eg;
            $time = json_encode($result[0]);
        }
        
        $data = [
            'status' => true,
            'code' => 200,
            'message' => 'Berhasil Mengambil Data Antrian',
            'data' => $this->model->where($array_where)->first(),
            'time' => json_decode($time)
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
            'no_handphone'  => 'required|numeric|min_length[10]|max_length[13]',
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
            'jam_selesai' => $request_jam_selesai,
            'status' => "Menunggu"
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
        $diff_end   = ($to_time_end - $to_time_book) / 60;
        
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
                        
                    } else if ($diff_book < 30) { // Compare waktu booking kurang dari 60 menit
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Minimal booking antrian 30 menit dari waktu saat ini.'
                        ];
                        
                        return $this->respond($response, 200);
                        
                    } else if ($diff_book > 120) { // Compare waktu booking kurang dari 60 menit
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Maksimal booking antrian 120 menit dari waktu saat ini.'
                        ];
                        
                        return $this->respond($response, 200);
                        
                    } else if ($diff_end >= 120) {
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Maksimal booking antrian yaitu 2 jam pelayanan dari waktu saat ini.'
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
                        
                    } 
                    else if ($cek_waktu_booking > 0) {
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
        $time_now = date('H:i:s');

        $builder = $db->table('antrian');
        $builder_flagging = $db->table('flagging_antrian');
        
        $arr_time = 0;

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
            'no_handphone'  => 'required|numeric|min_length[10]|max_length[13]',
        ]);
        
        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];
            
            return $this->failValidationErrors($response);
        }
        
        $difference = $this->differenceInHours($time_now, $request_jam_book);
        $round_difference = round($difference, 0, PHP_ROUND_HALF_UP);

        if ($round_difference == 1) {
            $arr_time = 1800000;

        } else if ($round_difference == 2) {
            $arr_time = 3600000;

        } else if ($round_difference < 1) {
            $response = [
                'status' => false,
                'code' => 400,
                'message' => 'Minimal booking antrian yaitu 30 menit pelayanan dari waktu saat ini.'
            ];
            
            return $this->respond($response, 200);
        } else if ($round_difference > 2) {
            $response = [
                'status' => false,
                'code' => 400,
                'message' => 'Maksimal booking antrian yaitu 2 jam pelayanan dari waktu saat ini.'
            ];
            
            return $this->respond($response, 200);
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
        $insert_id = $db->insertID();

        if ($result) {

            $adjustTimeSchedule = $this->addingScheduleTime($request_jam_book, $arr_time);

            $result_flagging = $builder_flagging->insert([
                'id_antrian'        => esc($insert_id),
                'total_jam_booking' => esc($difference),            // 1 - 2 Jam
                'jam_reminder'      => esc($adjustTimeSchedule),    // 14:30
                'time_schedule'     => esc($arr_time),              // 30 Menit
                'status_flagging'   => esc(1)
            ]);

            if ($result_flagging) {
                $divideTime = ($arr_time/1000);

                if (($divideTime == 3600)) {
                    $messageTime = ($divideTime/3600);
                    $result_push = $this->send_push_notification("Notifikasi Antrian", "Kamu punya waktu ". $messageTime ." Jam untuk konfirmasi, Pantau terus antrian kamu disini.");

                } else {
                    $messageTime = ($divideTime%3600) / 60;
                    $result_push = $this->send_push_notification("Notifikasi Antrian", "Kamu punya waktu ". $messageTime ." Menit untuk konfirmasi, Pantau terus antrian kamu disini.");

                }

                if ($result_push != null) {
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => 'Horee antrian kamu sudah masuk, Pantau terus yah antrianmu.',
                    ];
                    
                    return $this->respondCreated($response);   
                } else {
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => 'Horee antrian kamu sudah masuk, Terjadi masalah pada notifikasi.',
                    ];
                    
                    return $this->respondCreated($response);
                }
            } else {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => 'Maaf antrian kamu gagal ditambahkan, yuk coba lagi!'
                ];
                
                return $this->respond($response, 200);
            }
            
        } else {
            $response = [
                'status' => false,
                'code' => 400,
                'message' => 'Maaf antrian kamu gagal ditambahkan, yuk coba lagi!'
            ];
            
            return $this->respond($response, 200);
        }
    }

    function differenceInHours($startdate, $enddate) {
        $starttimestamp = strtotime($startdate);
        $endtimestamp = strtotime($enddate);

        $difference = abs($endtimestamp - $starttimestamp) / 3600;

        return $difference;
    }

    function addingScheduleTime($requestBook, $timeSchedule) {
        $newData = date("H:i:s", strtotime($requestBook. ' +'. $timeSchedule .' minutes'));
        return $newData;
    }

    // For Mthod in android
    public function notification() {
        $title = $this->request->getVar('title');
        $message = $this->request->getVar('message');
        
        helper(['form']);
        $rules = $this->validate([
            'title' => 'required|max_length[50]',
            'message' => 'required|max_length[150]',
        ]);
        
        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];
            
            return $this->failValidationErrors($response);
        }

        $deviceToken = 'dmgW7KUsRvutBOF6w2CzD0:APA91bGSWjLdJ1xhqfk23Vv36gyOD06SIaXf7LUs1KitlD2RzAYscP9UISUQ137GGd6RNrRl7NHmbZLwUsPqnz2N-CuvLz08xyWywbw82J0MAMMH_mOzkN2lZUnrh11dZoafxkSE6UNS';
        $authorizationKey = 'AAAAkhtk95E:APA91bEUyQ9pj_fYdXy_FsWO8QN6weFZB78SKWDlC3EF4mtO1qCWPl6Ol7A8gZOrHrhva_7DMsPssgXI7k2aFgLGzpfUhYJ3z9MP-axkYXA3I82LYtq_lHydsUYvOhQuuqyvoAoT5rkt';

        $data = [
            'title' => $title,
            'message' => $message,
            'image' => "",
            'action' => "",
            'action_destination' => ""
        ];

        $headers = [
            'Authorization' => 'key='.$authorizationKey,
            'Content-Type'  => 'application/json',
        ];

        $fields = [
            'to' => "/topics/all_user",
            'content-available' => true,
            'priority' => 'high',
            'data' => $data,
        ];

        $fields = json_encode($fields);

        $client = new Client();

        try{
            $request = $client->post("https://fcm.googleapis.com/fcm/send", [
                'headers' => $headers,
                "body" => $fields,
            ]);

            $response = $request->getStatusCode();

            if ($response === 200) {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'data' => $request->getBody(),
                    'message' => $message
                ];
                
                return $this->respond($response, 200);
                // return $request->getBody();
            } else {
                return 'Failed to send FCM notification!';
            }
        } catch (Exception $e){
            return $e;
        }
    }

    public function send_push_notification($title, $message) {
        $deviceToken = 'dmgW7KUsRvutBOF6w2CzD0:APA91bGSWjLdJ1xhqfk23Vv36gyOD06SIaXf7LUs1KitlD2RzAYscP9UISUQ137GGd6RNrRl7NHmbZLwUsPqnz2N-CuvLz08xyWywbw82J0MAMMH_mOzkN2lZUnrh11dZoafxkSE6UNS';
        $authorizationKey = 'AAAAkhtk95E:APA91bEUyQ9pj_fYdXy_FsWO8QN6weFZB78SKWDlC3EF4mtO1qCWPl6Ol7A8gZOrHrhva_7DMsPssgXI7k2aFgLGzpfUhYJ3z9MP-axkYXA3I82LYtq_lHydsUYvOhQuuqyvoAoT5rkt';

        $data = [
            'title' => $title,
            'message' => $message,
            'image' => "",
            'action' => "",
            'action_destination' => ""
        ];

        $headers = [
            'Authorization' => 'key='.$authorizationKey,
            'Content-Type'  => 'application/json',
        ];

        $fields = [
            'to' => "/topics/all_user",
            'content-available' => true,
            'priority' => 'high',
            'data' => $data,
        ];

        $fields = json_encode($fields);

        $client = new Client();

        try{
            $request = $client->post("https://fcm.googleapis.com/fcm/send", [
                'headers' => $headers,
                "body" => $fields,
            ]);

            $response = $request->getStatusCode();

            if ($response === 200) {
                return $request->getBody();
            } else {
                return 'Failed to send FCM notification!';
            }
        } catch (Exception $e){
            return $e;
        }
    }

    /**
     * Edit untuk Web edit antrian
    */
    public function edit($id = null)
    {
        //
    }
    
    /**
     * Update untuk API cancel antrian
    */
    public function update($id = null)
    {
        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        $item_antrian = $this->model->where('id', $id)
                        ->where('status', "Menunggu")
                        ->first();

        if ($item_antrian != null) {
            $result_update = $this->model->update($id, [
                'status'        => esc("Batal"),
                'updated_at'    => $date_now,
            ]);

            if ($result_update > 0) {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => 'Antrian Berhasil Dibatalkan'
                ];
                
                return $this->respond($response, 200); 
            } else {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => 'Antrian Gagal Dibatalkan'
                ];
                
                return $this->respond($response, 200);
            }
        }
    }

    /**
    * Return a new resource object, with default properties
    *
    * @return mixed
    */
    public function verifikasi($id = null)
    {   
        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        $item_antrian = $this->model->where('id', $id)
                        ->where('status', "Menunggu")
                        ->first();
        
        if ($item_antrian != null) {
            $result_update = $this->model->update($id, [
                'status'        => esc("Berhasil"),
                'updated_at'    => $date_now,
            ]);

            if ($result_update > 0) {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => 'Antrian Berhasil Diverifikasi'
                ];
                
                return $this->respond($response, 200); 
            } else {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => 'Antrian Gagal Diverifikasi'
                ];
                
                return $this->respond($response, 200);
            }
        }
    }
    
    /**
    * Delete the designated resource object from the model
    *
    * @return mixed
    */
    public function delete($id = null)
    {
        
    }
}