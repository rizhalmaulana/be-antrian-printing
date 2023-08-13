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

    public function dashboard()
    {
        $db = \Config\Database::connect();
        $antrian = $db->table('antrian');

        $cek_status_batal         = $antrian->where('status', 'Batal')->countAllResults();
        $cek_status_berjalan      = $antrian->where('status', 'Menunggu')->countAllResults();
        $cek_status_berhasil      = $antrian->where('status', 'Berhasil')->countAllResults();

        $total_semua_antrian      = $cek_status_batal + $cek_status_berhasil + $cek_status_berjalan;

        $data = [
            'title'             => "Admin | Antrian Printing",
            'total_batal'       => $cek_status_batal,
            'total_berjalan'    => $cek_status_berjalan,
            'total_berhasil'    => $cek_status_berhasil,
            'total_semua'       => $total_semua_antrian
        ];

        echo view('component/headeradmin', $data);
        echo view('admin/index', $data);
        echo view('component/footeradmin');
    }

    public function muser()
    {
        $db = \Config\Database::connect();
        $user = $db->table('user');
        
        $query   = $user->get();

        $data = [
            'title' => "Master User | Antrian Printing",
            'data_user' => $query
        ];

        echo view('component/headeradmin', $data);
        echo view('admin/masteruser', $data);
        echo view('component/footeradmin');
    }

    public function mantrian()
    {
        $db = \Config\Database::connect();

        $antrian = $db->table('antrian');
        $designer = session()->get("username");

        $antrian->select('antrian.id, antrian.id_user, antrian.nama_designer, antrian.jenis_layanan, antrian.jam_booking, antrian.jam_selesai, antrian.tgl_pesanan, antrian.status as status_antrian, antrian.no_handphone, 
        user.username, user.email_user, user.nama_lengkap, user.status');

        $antrian->join('user', 'user.id = antrian.id_user');
        $antrian->where('nama_designer', $designer);

        $query = $antrian->get()->getResult();

        $data = [
            'title' => "Master Antrian | Antrian Printing",
            'data_antrian' => $query
        ];

        echo view('component/headeradmin', $data);
        echo view('admin/masterantrian', $data);
        echo view('component/footeradmin');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();

        $err = 'Berhasil Logout, Terima Kasih!';
        session()->setFlashdata( 'success', $err );

        return redirect()->to('/login');
    }

    public function profil()
    {
        $db = \Config\Database::connect();
        $admin = $db->table('admin');

        $id = session()->get("user_id");
        
        $admin->select('*');
        $admin->where('id', $id);
        $query = $admin->get()->getResult();

        $data = [
            'title' => "Profil | Antrian Printing",
            'admin' => $query
        ];

        echo view('component/headeradmin', $data);
        echo view('admin/profil', $data);
        echo view('component/footeradmin');
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

    public function edit($id = null)
    {
        $db = \Config\Database::connect();
        $user = $db->table('user');

        $userbyid = $user->where('id', $id)->get();

        $data = [
            'title'         => "Edit User | Antrian Printing",
            'data_user_id'  => $userbyid
        ];

        echo view('component/headeradmin', $data);
        echo view('admin/edituser', $data);
        echo view('component/footeradmin');
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

    public function updateuser($id = null)
    {
        $db = \Config\Database::connect();
        $user = $db->table('user');

        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        helper(['form']);
        $rules = $this->validate([
            'username'      => 'required|min_length[5]|max_length[10]',
            'nama_lengkap'  => 'required|max_length[35]',
            'tempat_lahir'  => 'required',
            'tanggal_lahir' => 'required',
            'email_user'    => 'required|valid_email',
            'phone_user'    => 'required',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $data = [
            'username'      => esc($this->request->getVar('username')),
            'nama_lengkap'  => esc($this->request->getVar('nama_lengkap')),
            'tempat_lahir'  => esc($this->request->getVar('tempat_lahir')),
            'tanggal_lahir' => esc($this->request->getVar('tanggal_lahir')),
            'email_user'    => esc($this->request->getVar('email_user')),
            'phone_user'    => esc($this->request->getVar('phone_user')),
            'updated_at'    => $date_now
        ];

        $user->where('id', $id);
        $updateuser = $user->update($data);

        if ($updateuser > 0) {
            $msg = 'Berhasil Update Data User!';
            session()->setFlashdata( 'success', $msg );

            return redirect()->to( '/dashboard/master-user' );
        } else {
            $msg = 'Gagal Update Data User!';
            session()->setFlashdata( 'success', $msg );

            return redirect()->to( '/dashdashboard/master-user' );
        }
    }

    public function updateadmin($id = null)
    {
        $db = \Config\Database::connect();
        $admin = $db->table('admin');

        date_default_timezone_set('Asia/Jakarta');
        $date_now = date('Y-m-d H:i:s');

        helper(['form']);
        $rules = $this->validate([
            'username'      => 'required|min_length[5]|max_length[10]',
            'nama_lengkap'  => 'required|max_length[35]',
            'email_admin'    => 'required|valid_email',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $data = [
            'username'      => esc($this->request->getVar('username')),
            'nama_lengkap'  => esc($this->request->getVar('nama_lengkap')),
            'email_admin'    => esc($this->request->getVar('email_admin')),
            'updated_at'    => $date_now
        ];

        $admin->where('id', $id);
        $updateadmin = $admin->update($data);

        if ($updateadmin > 0) {
            $msg = 'Berhasil Update Data Admin!';
            session()->setFlashdata( 'success', $msg );

            return redirect()->to('/dashboard/profil');
        } else {
            $err = 'Gagal Update Data Admin!';
            session()->setFlashdata( 'error', $err );

            return redirect()->to('/dashboard/profil');
        }
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