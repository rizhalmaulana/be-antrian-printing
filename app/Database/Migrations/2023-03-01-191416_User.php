<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User extends Migration
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
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'jenis_kelamin' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'tempat_lahir' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'tanggal_lahir' => [
                'type'       => 'DATE',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '300',
            ],
            'status' => [
                'type'          => 'INT',
                'constraint'    => 2,
            ],
            'phone_user' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'email_user' => [
                'type'       => 'VARCHAR',
                'constraint' => '80',
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
        $this->forge->createTable('user');
    }
    
    public function down()
    {
        $this->forge->dropTable('user');
    }
}