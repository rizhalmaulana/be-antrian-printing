<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Admin extends Migration
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
            'email_admin' => [
                'type'       => 'VARCHAR',
                'constraint' => '80',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '300',
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
        $this->forge->createTable('admin');
    }
    
    public function down()
    {
        $this->forge->dropTable('admin');
    }
}
