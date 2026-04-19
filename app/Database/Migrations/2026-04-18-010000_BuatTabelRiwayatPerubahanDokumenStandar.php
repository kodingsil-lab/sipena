<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatTabelRiwayatPerubahanDokumenStandar extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('riwayat_perubahan_dokumen_standar')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'dokumen_standar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'standar_mutu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'changed_fields' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rasional' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'subjek_bertanggung_jawab' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'definisi_istilah' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'pernyataan_isi_standar' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'indikator_ketercapaian' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'strategi_pencapaian' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'dokumen_terkait' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'referensi' => [
                'type' => 'LONGTEXT',
                'null' => true,
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
        $this->forge->addKey('dokumen_standar_id');
        $this->forge->addKey('standar_mutu_id');
        $this->forge->addKey('updated_by');
        $this->forge->addForeignKey('dokumen_standar_id', 'dokumen_standar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('standar_mutu_id', 'standar_mutu', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('riwayat_perubahan_dokumen_standar', true);
    }

    public function down()
    {
        $this->forge->dropTable('riwayat_perubahan_dokumen_standar', true);
    }
}

