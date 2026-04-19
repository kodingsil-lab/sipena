<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahJenisKategoriKeStandarMutu extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('standar_mutu')) {
            return;
        }

        if (! $this->db->fieldExists('jenis_standar_id', 'standar_mutu')) {
            $this->forge->addColumn('standar_mutu', [
                'jenis_standar_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'deskripsi',
                ],
            ]);
            $this->db->query('ALTER TABLE standar_mutu ADD INDEX idx_standar_mutu_jenis_standar_id (jenis_standar_id)');
        }

        if (! $this->db->fieldExists('kategori_standar_id', 'standar_mutu')) {
            $this->forge->addColumn('standar_mutu', [
                'kategori_standar_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'jenis_standar_id',
                ],
            ]);
            $this->db->query('ALTER TABLE standar_mutu ADD INDEX idx_standar_mutu_kategori_standar_id (kategori_standar_id)');
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('standar_mutu')) {
            return;
        }

        if ($this->db->fieldExists('kategori_standar_id', 'standar_mutu')) {
            $this->forge->dropColumn('standar_mutu', 'kategori_standar_id');
        }

        if ($this->db->fieldExists('jenis_standar_id', 'standar_mutu')) {
            $this->forge->dropColumn('standar_mutu', 'jenis_standar_id');
        }
    }
}
