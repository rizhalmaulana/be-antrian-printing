<?php

namespace App\Models;

use CodeIgniter\Model;

class FlaggingAntrian extends Model
{
    protected $table            = 'flaggingantrians';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['id_antrian', 'total_jam_booking', 'jam_reminder', 'time_schedule', 'status_flagging'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
