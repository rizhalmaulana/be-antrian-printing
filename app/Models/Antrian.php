<?php

namespace App\Models;

use CodeIgniter\Model;

class Antrian extends Model
{
    protected $table            = 'antrian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['id_user', 'nama_designer', 'jenis_layanan', 'jam_booking', 'jam_selesai', 'tgl_pesanan', 'no_handphone', 'status', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
