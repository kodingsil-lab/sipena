<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class FullDummySeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('id_ID');
        $this->call('CleanDummySeeder');

        $this->seedUsers($faker);
        $this->seedProfilInstitusi($faker);
        $standarIds = $this->seedStandarMutu($faker);
        $this->seedDokumenStandar($faker, $standarIds);
        $this->seedPeraturan($faker);
        $this->seedKebijakanMutu($faker);
        $this->seedKebijakanSpmi($faker);
        $this->seedPedomanPpepp($faker);
        $this->seedAuditMutuInternal($faker);
    }

    private function seedUsers($faker): void
    {
        $now = date('Y-m-d H:i:s');
        $rows = [];

        // User admin default agar mudah login setelah seeding.
        $rows[] = [
            'nama'       => 'Admin SIPENA',
            'email'      => 'admin@sipena.test',
            'username'   => 'admin',
            'password'   => password_hash('password123', PASSWORD_BCRYPT),
            'role'       => 'admin',
            'ttd_digital'=> null,
            'is_active'  => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $roles = ['dosen', 'admin', 'kepala_lpm', 'dosen', 'dosen'];
        for ($i = 2; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'nama'       => 'Dummy User ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'email'      => 'dummy.user' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '@sipena.test',
                'username'   => 'dummy_user_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'password'   => password_hash('password123', PASSWORD_BCRYPT),
                'role'       => $roles[$i % count($roles)],
                'ttd_digital'=> null,
                'is_active'  => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        $this->db->table('users')->insertBatch($rows);

        $userIds = array_column(
            $this->db->table('users')->select('id')->orderBy('id', 'ASC')->get()->getResultArray(),
            'id'
        );

        $prosesList = ['Perumusan', 'Pemeriksaan', 'Persetujuan', 'Pengesahan', 'Pengendalian'];
        $mapRows = [];
        foreach ($userIds as $idx => $userId) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $mapRows[] = [
                'user_id'    => (int) $userId,
                'proses'     => $prosesList[$idx % count($prosesList)],
                'is_active'  => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        $this->db->table('user_penanggung_jawab_proses')->insertBatch($mapRows);
    }

    private function seedProfilInstitusi($faker): void
    {
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'nama_institusi'            => 'Universitas Dummy Mutu ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'singkatan_institusi'       => 'UDM' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'visi'                      => 'Menjadi institusi unggul dalam budaya mutu berkelanjutan.',
                'misi'                      => '<ol><li>Meningkatkan kualitas akademik.</li><li>Menguatkan tata kelola.</li><li>Mendorong inovasi.</li></ol>',
                'tujuan'                    => '<ol><li>Standar mutu terimplementasi.</li><li>Dokumen tertib.</li></ol>',
                'sasaran'                   => '<ol><li>Kepatuhan standar meningkat.</li><li>Evaluasi rutin berjalan.</li></ol>',
                'alamat'                    => 'Jl. Dummy Kampus No. ' . $i . ', Kota Mutu',
                'logo'                      => null,
                'created_at'                => $time,
                'updated_at'                => $time,
            ];
        }

        $this->db->table('profil_institusi')->insertBatch($rows);
    }

    private function seedStandarMutu($faker): array
    {
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'kode_standar'      => 'STD-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'nama_standar'      => 'Standar Mutu Dummy ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'deskripsi'         => 'Deskripsi standar mutu dummy ke-' . $i . ' untuk kebutuhan pengujian modul.',
                'status_publikasi'  => $this->statusByIndex($i),
                'created_at'        => $time,
                'updated_at'        => $time,
            ];
        }

        $this->db->table('standar_mutu')->insertBatch($rows);

        return array_column(
            $this->db->table('standar_mutu')->select('id')->orderBy('id', 'ASC')->get()->getResultArray(),
            'id'
        );
    }

    private function seedDokumenStandar($faker, array $standarIds): void
    {
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-10 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'standar_mutu_id'           => (int) $standarIds[($i - 1) % count($standarIds)],
                'kode_dokumen'              => 'DOC-STD-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'tanggal_dokumen'           => $faker->date('Y-m-d'),
                'revisi'                    => 'R' . ($i % 5),
                'halaman'                   => (string) $faker->numberBetween(10, 120),
                'rasional'                  => '<p>Rasional dokumen standar dummy ' . $i . '.</p>',
                'subjek_bertanggung_jawab'  => '<ul><li>LPM</li><li>Program Studi</li></ul>',
                'definisi_istilah'          => '<p>Definisi istilah mutu untuk dokumen dummy ' . $i . '.</p>',
                'pernyataan_isi_standar'    => '<ol><li>Pernyataan isi standar 1.</li><li>Pernyataan isi standar 2.</li></ol>',
                'indikator_ketercapaian'    => '<ol><li>Indikator capaian 1.</li><li>Indikator capaian 2.</li></ol>',
                'strategi_pencapaian'       => '<p>Strategi pencapaian standar melalui evaluasi berkala.</p>',
                'dokumen_terkait'           => '<p>Dokumen terkait: SOP, panduan, formulir.</p>',
                'referensi'                 => '<p>Referensi: SN-Dikti, kebijakan internal.</p>',
                'status_publikasi'          => $this->statusByIndex($i),
                'created_at'                => $time,
                'updated_at'                => $time,
            ];
        }

        $this->db->table('dokumen_standar')->insertBatch($rows);
    }

    private function seedPeraturan($faker): void
    {
        $kategoriList = ['Landasan Hukum', 'Peraturan Dikti', 'Peraturan Rektor'];
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'kategori'          => $kategoriList[($i - 1) % count($kategoriList)],
                'judul'             => 'Peraturan Dummy ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'nomor_dokumen'     => 'PRT/' . str_pad((string) $i, 3, '0', STR_PAD_LEFT) . '/SPMI',
                'tahun'             => (string) (2020 + ($i % 7)),
                'deskripsi'         => 'Deskripsi peraturan dummy untuk pengujian tampilan dan fitur.',
                'file_pdf'          => 'dummy_peraturan_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi'  => $this->statusByIndex($i),
                'created_at'        => $time,
                'updated_at'        => $time,
            ];
        }

        $this->db->table('peraturan')->insertBatch($rows);
    }

    private function seedKebijakanMutu($faker): void
    {
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'judul'             => 'Kebijakan Mutu Dummy ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'nomor_dokumen'     => 'KM/' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'tahun'             => (string) (2020 + ($i % 7)),
                'deskripsi'         => 'Dokumen kebijakan mutu dummy untuk kebutuhan pengujian.',
                'file_pdf'          => 'dummy_kebijakan_mutu_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi'  => $this->statusByIndex($i),
                'created_at'        => $time,
                'updated_at'        => $time,
            ];
        }

        $this->db->table('kebijakan_mutu')->insertBatch($rows);
    }

    private function seedKebijakanSpmi($faker): void
    {
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'judul'             => 'Kebijakan SPMI Dummy ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'nomor_dokumen'     => 'KSPMI/' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'tahun'             => (string) (2020 + ($i % 7)),
                'deskripsi'         => 'Dokumen kebijakan SPMI dummy untuk kebutuhan pengujian.',
                'file_pdf'          => 'dummy_kebijakan_spmi_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi'  => $this->statusByIndex($i),
                'created_at'        => $time,
                'updated_at'        => $time,
            ];
        }

        $this->db->table('kebijakan_spmi')->insertBatch($rows);
    }

    private function seedPedomanPpepp($faker): void
    {
        $jenis = ['Dokumen PPEPP', 'SOP', 'Formulir'];
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'jenis_dokumen'     => $jenis[($i - 1) % count($jenis)],
                'judul'             => 'Pedoman PPEPP Dummy ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'nomor_dokumen'     => 'PPEPP/' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'tahun'             => (string) (2020 + ($i % 7)),
                'deskripsi'         => 'Dokumen pedoman PPEPP dummy untuk kebutuhan pengujian.',
                'file_pdf'          => 'dummy_pedoman_ppepp_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi'  => $this->statusByIndex($i),
                'created_at'        => $time,
                'updated_at'        => $time,
            ];
        }

        $this->db->table('pedoman_ppepp')->insertBatch($rows);
    }

    private function seedAuditMutuInternal($faker): void
    {
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'judul'             => 'Audit Mutu Internal Dummy ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'nomor_dokumen'     => 'AMI/' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'tahun'             => (string) (2020 + ($i % 7)),
                'deskripsi'         => 'Dokumen AMI dummy untuk simulasi data sistem.',
                'file_pdf'          => 'dummy_ami_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi'  => $this->statusByIndex($i),
                'created_at'        => $time,
                'updated_at'        => $time,
            ];
        }

        $this->db->table('audit_mutu_internal')->insertBatch($rows);
    }

    private function statusByIndex(int $index): string
    {
        return $index % 3 === 0 ? 'draft' : 'publish';
    }
}
