<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahTanggalTtdPadaPenugasanPenandatanganStandar extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('penugasan_penandatangan_standar')) {
            return;
        }

        if (! $this->db->fieldExists('tanggal_ttd', 'penugasan_penandatangan_standar')) {
            $this->forge->addColumn('penugasan_penandatangan_standar', [
                'tanggal_ttd' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'user_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if (
            $this->db->tableExists('penugasan_penandatangan_standar')
            && $this->db->fieldExists('tanggal_ttd', 'penugasan_penandatangan_standar')
        ) {
            $this->forge->dropColumn('penugasan_penandatangan_standar', 'tanggal_ttd');
        }
    }
}
