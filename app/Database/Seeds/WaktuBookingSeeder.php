<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WaktuBookingSeeder extends Seeder
{
    public function run()
    {
        $data_jam_booking = [
            [
				'jam_booking' => '08:00 - 09:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'jam_booking' => '09:00 - 10:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'jam_booking' => '10:00 - 11:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '11:00 - 12:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '13:00 - 14:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '14:00 - 15:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '15:00 - 16:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '16:00 - 17:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '18:00 - 19:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '19:00 - 20:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '20:00 - 21:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_booking' => '21:00 - 22:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach($data_jam_booking as $jam_booking) {
            // insert semua data ke tabel
            $this->db->table('waktu_booking')->insert($jam_booking);
        }
    }
}
