<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FlaggingAntrian extends Migration
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
            'id_antrian' => [
                'type'           => 'INT',
                'constraint'     => 5,
            ],
            'total_jam_booking' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'jam_reminder' => [
                'type'           => 'TIME',
            ],
            'time_schedule' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'status_flagging' => [
                'type'           => 'INT',
                'constraint'     => 5,
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
        $this->forge->createTable('flagging_antrian');
    }

    public function down()
    {
        $this->forge->dropTable('flagging_antrian');
    }
}
