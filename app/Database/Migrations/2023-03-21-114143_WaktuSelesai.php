<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WaktuSelesai extends Migration
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
            'jam_selesai' => [
                'type'       => 'TIME',
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
        $this->forge->createTable('waktu_selesai');
    }
    
    public function down()
    {
        $this->forge->dropTable('waktu_selesai');
    }
}
