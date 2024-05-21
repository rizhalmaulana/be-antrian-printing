<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
		$admin_data = [
			[
				'username' => 'danielhardiansyah',
				'nama_lengkap'  => 'Daniel Hardiansyah',
				'email_admin' => 'danielhardi@gmail.com',
                'password' => password_hash('daniel123', PASSWORD_DEFAULT),
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'username' => 'dedensubagja',
				'nama_lengkap'  => 'Deden Subagja',
				'email_admin' => 'dedensubagja@gmail.com',
                'password' => password_hash('deden123', PASSWORD_DEFAULT),
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'username' => 'donisalman',
				'nama_lengkap'  => 'Doni Salman',
				'email_admin' => 'donisalman@gmail.com',
                'password' => password_hash('doni123', PASSWORD_DEFAULT),
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'username' => 'renitacahya',
				'nama_lengkap'  => 'Renita Cahya',
				'email_admin' => 'renitacahya@gmail.com',
                'password' => password_hash('renita123', PASSWORD_DEFAULT),
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
				'username' => 'salmahidayanti',
				'nama_lengkap'  => 'Salma Hidayanti',
				'email_admin' => 'salmahidayanti@gmail.com',
                'password' => password_hash('salma123', PASSWORD_DEFAULT),
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
		];

		foreach($admin_data as $data){
			// insert semua data ke tabel
			$this->db->table('admin')->insert($data);
		}
    }
}
