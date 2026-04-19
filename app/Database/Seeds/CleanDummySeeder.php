<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CleanDummySeeder extends Seeder
{
    /**
     * Urutan truncate dibuat dari tabel turunan ke tabel induk.
     */
    private array $tables = [
        'user_penanggung_jawab_proses',
        'riwayat_perubahan_dokumen_standar',
        'dokumen_standar',
        'audit_mutu_internal',
        'pedoman_ppepp',
        'kebijakan_spmi',
        'kebijakan_mutu',
        'peraturan',
        'standar_mutu',
        'profil_institusi',
        'users',
    ];

    public function run()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->tables as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->table($table)->truncate();
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
