<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WaktuSelesaiSeeder extends Seeder
{
    public function run()
    {
        $data_jam_selesai = [
            [
				'jam_selesai' => '09:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'jam_selesai' => '10:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'jam_selesai' => '11:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '12:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '14:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '15:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '16:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '17:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '19:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '20:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '21:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'jam_selesai' => '22:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach($data_jam_selesai as $jam_selesai) {
            // insert semua data ke tabel
            $this->db->table('waktu_selesai')->insert($jam_selesai);
        }
    }
}
