<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatTabelPenugasanPenandatanganStandar extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('penugasan_penandatangan_standar')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'standar_mutu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'proses' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addKey('standar_mutu_id');
        $this->forge->addKey('user_id');
        $this->forge->addUniqueKey(['standar_mutu_id', 'proses'], 'uniq_standar_proses');
        $this->forge->createTable('penugasan_penandatangan_standar', true);
    }

    public function down()
    {
        $this->forge->dropTable('penugasan_penandatangan_standar', true);
    }
}
