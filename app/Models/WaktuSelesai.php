<?php

namespace App\Models;

use CodeIgniter\Model;

class WaktuSelesai extends Model
{
    protected $table            = 'waktu_selesai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['jam_selesai','created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
