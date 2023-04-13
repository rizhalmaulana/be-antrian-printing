<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Layanan extends Migration
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
            'nama_layanan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'status' => [
                'type'          => 'INT',
                'constraint'    => 2,
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
        $this->forge->createTable('layanan');
    }
    
    public function down()
    {
        $this->forge->dropTable('layanan');
    }
}
