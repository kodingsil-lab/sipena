<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HapusKolomLembagaPenjaminanMutu extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('profil_institusi')
            && $this->db->fieldExists('lembaga_penjaminan_mutu', 'profil_institusi')
        ) {
            $this->forge->dropColumn('profil_institusi', 'lembaga_penjaminan_mutu');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('profil_institusi')
            && ! $this->db->fieldExists('lembaga_penjaminan_mutu', 'profil_institusi')
        ) {
            $this->forge->addColumn('profil_institusi', [
                'lembaga_penjaminan_mutu' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'singkatan_institusi',
                ],
            ]);
        }
    }
}

