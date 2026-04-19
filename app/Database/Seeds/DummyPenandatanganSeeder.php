<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DummyPenandatanganSeeder extends Seeder
{
    public function run()
    {
        // Hash password dummy dengan bcrypt
        $password = password_hash('password123', PASSWORD_BCRYPT);

        // Hapus mapping lama
        $this->db->table('user_penanggung_jawab_proses')->truncate();

        // Insert 4 dummy users untuk proses lainnya (samping Konradus yang sudah ada)
        $users = [
            [
                'nama'        => 'Dr. Pemeriksapan',
                'email'       => 'pemeriksaan123@sipena.test',
                'username'    => 'pemeriksaan123',
                'password'    => $password,
                'role'        => 'dosen',
                'ttd_digital' => null,
                'is_active'   => 1,
            ],
            [
                'nama'        => 'Prof. Persetujuan Mutu',
                'email'       => 'persetujuan123@sipena.test',
                'username'    => 'persetujuan123',
                'password'    => $password,
                'role'        => 'dosen',
                'ttd_digital' => null,
                'is_active'   => 1,
            ],
            [
                'nama'        => 'Rektor Pengesahan',
                'email'       => 'pengesahan123@sipena.test',
                'username'    => 'pengesahan123',
                'password'    => $password,
                'role'        => 'admin',
                'ttd_digital' => null,
                'is_active'   => 1,
            ],
            [
                'nama'        => 'Kaprodi Pengendalian',
                'email'       => 'pengendalian123@sipena.test',
                'username'    => 'pengendalian123',
                'password'    => $password,
                'role'        => 'dosen',
                'ttd_digital' => null,
                'is_active'   => 1,
            ],
        ];

        // Delete users lama jika ada
        $this->db->table('users')->whereIn('username', ['pemeriksaan123', 'persetujuan123', 'pengesahan123', 'pengendalian123'])->delete();

        // Insert dummy users
        $this->db->table('users')->insertBatch($users);

        // Create mapping untuk semua 5 proses
        $mappings = [
            ['user_id' => 2, 'proses' => 'Perumusan', 'is_active' => 1],     // Konradus (id=2 yang sudah ada)
            ['user_id' => 3, 'proses' => 'Pemeriksaan', 'is_active' => 1],   // User baru id=3
            ['user_id' => 4, 'proses' => 'Persetujuan', 'is_active' => 1],   // User baru id=4
            ['user_id' => 5, 'proses' => 'Pengesahan', 'is_active' => 1],    // User baru id=5
            ['user_id' => 6, 'proses' => 'Pengendalian', 'is_active' => 1],  // User baru id=6
        ];

        $this->db->table('user_penanggung_jawab_proses')->insertBatch($mappings);
    }
}
