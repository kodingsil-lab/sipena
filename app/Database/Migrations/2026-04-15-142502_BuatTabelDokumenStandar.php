<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatTabelDokumenStandar extends Migration
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
            'standar_mutu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kode_dokumen' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tanggal_dokumen' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'revisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'halaman' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
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
            'status_publikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'draft',
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
        $this->forge->createTable('dokumen_standar');
    }

    public function down()
    {
        $this->forge->dropTable('dokumen_standar');
    }
}