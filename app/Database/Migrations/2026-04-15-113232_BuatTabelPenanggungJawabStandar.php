<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatTabelPenanggungJawabStandar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'urutan_proses' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1,
            ],
            'proses' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'role_proses' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'ttd_digital' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('penanggung_jawab_standar');
    }

    public function down()
    {
        $this->forge->dropTable('penanggung_jawab_standar');
    }
}