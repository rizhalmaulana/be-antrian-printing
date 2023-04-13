<?php

namespace App\Models;

use CodeIgniter\Model;

class WaktuBooking extends Model
{
    protected $table            = 'waktu_booking';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['jam_booking','created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
