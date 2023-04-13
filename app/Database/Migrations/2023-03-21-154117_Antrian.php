<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Antrian extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'           => 'INT',
                'constraint'     => 5,
            ],
            'nama_designer' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'jenis_layanan' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'jam_booking' => [
                'type'       => 'TIME',
            ],
            'jam_selesai' => [
                'type'       => 'TIME',
            ],
            'tgl_pesanan' => [
                'type'       => 'DATE',
            ],
            'no_handphone' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'status' => [
                'type'          => 'VARCHAR',
                'constraint'    => '100',
            ],
            'created_at' => [
                'type'      => 'DATETIME',
                'null'      => true,
            ],
            'updated_at' => [
                'type'      => 'DATETIME',
                'null'      => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('antrian');
    }
    
    public function down()
    {
        $this->forge->dropTable('antrian');
    }
}
