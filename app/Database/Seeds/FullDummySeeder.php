<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class FullDummySeeder extends Seeder
{
    private const PROSES_PENANDATANGAN = ['perumusan', 'pemeriksaan', 'persetujuan', 'pengesahan', 'pengendalian'];

    public function run()
    {
        $faker = Factory::create('id_ID');
        $this->call('CleanDummySeeder');

        $masterMap = $this->seedMasterJenisKategori();

        $this->seedUsers($faker);
        $this->seedProfilInstitusi($faker);
        $standarRows = $this->seedStandarMutu($faker, $masterMap);
        $this->seedPenugasanPenandatanganStandar($faker, $standarRows);
        $this->seedDokumenStandar($faker, $standarRows);
        $this->seedPeraturan($faker);
        $this->seedKebijakanMutu($faker);
        $this->seedKebijakanSpmi($faker);
        $this->seedPedomanPpepp($faker);
        $this->seedAuditMutuInternal($faker);
    }

    private function seedMasterJenisKategori(): array
    {
        $result = [
            'jenis' => [],
            'kategori' => [],
        ];

        $now = date('Y-m-d H:i:s');

        if ($this->db->tableExists('master_jenis_standar')) {
            $jenisRows = [
                [
                    'nama_jenis' => 'Pendidikan',
                    'deskripsi' => 'Kelompok standar untuk menjamin mutu penyelenggaraan pendidikan.',
                ],
                [
                    'nama_jenis' => 'Penelitian',
                    'deskripsi' => 'Kelompok standar untuk menjamin mutu pelaksanaan penelitian.',
                ],
                [
                    'nama_jenis' => 'Pengabdian kepada Masyarakat',
                    'deskripsi' => 'Kelompok standar untuk menjamin mutu kegiatan pengabdian kepada masyarakat.',
                ],
            ];

            $table = $this->db->table('master_jenis_standar');
            foreach ($jenisRows as $row) {
                $existing = $table->where('nama_jenis', $row['nama_jenis'])->get()->getRowArray();
                $payload = [
                    'nama_jenis' => $row['nama_jenis'],
                    'deskripsi' => $row['deskripsi'],
                    'is_aktif' => 1,
                    'updated_at' => $now,
                ];

                if ($existing) {
                    $table->where('id', (int) $existing['id'])->update($payload);
                    $result['jenis'][$row['nama_jenis']] = (int) $existing['id'];
                    continue;
                }

                $payload['created_at'] = $now;
                $table->insert($payload);
                $result['jenis'][$row['nama_jenis']] = (int) $this->db->insertID();
            }
        }

        if ($this->db->tableExists('master_kategori_standar')) {
            $kategoriRows = [
                [
                    'nama_kategori' => 'Kompetensi dan Hasil',
                    'deskripsi' => 'Kategori standar terkait rumusan kompetensi lulusan dan hasil tridharma.',
                ],
                [
                    'nama_kategori' => 'Isi',
                    'deskripsi' => 'Kategori standar terkait substansi, muatan, dan ruang lingkup kegiatan.',
                ],
                [
                    'nama_kategori' => 'Proses',
                    'deskripsi' => 'Kategori standar terkait mekanisme pelaksanaan kegiatan.',
                ],
                [
                    'nama_kategori' => 'Penilaian',
                    'deskripsi' => 'Kategori standar terkait evaluasi dan pengukuran capaian.',
                ],
                [
                    'nama_kategori' => 'Sumber Daya Manusia',
                    'deskripsi' => 'Kategori standar terkait dosen, tenaga kependidikan, peneliti, dan pelaksana PKM.',
                ],
                [
                    'nama_kategori' => 'Sarana dan Prasarana',
                    'deskripsi' => 'Kategori standar terkait fasilitas pendukung pelaksanaan tridharma.',
                ],
                [
                    'nama_kategori' => 'Pengelolaan',
                    'deskripsi' => 'Kategori standar terkait tata kelola dan manajemen mutu.',
                ],
                [
                    'nama_kategori' => 'Pembiayaan dan Pendanaan',
                    'deskripsi' => 'Kategori standar terkait pendanaan kegiatan dan keberlanjutan program.',
                ],
            ];

            $table = $this->db->table('master_kategori_standar');
            foreach ($kategoriRows as $row) {
                $existing = $table->where('nama_kategori', $row['nama_kategori'])->get()->getRowArray();
                $payload = [
                    'nama_kategori' => $row['nama_kategori'],
                    'deskripsi' => $row['deskripsi'],
                    'is_aktif' => 1,
                    'updated_at' => $now,
                ];

                if ($existing) {
                    $table->where('id', (int) $existing['id'])->update($payload);
                    $result['kategori'][$row['nama_kategori']] = (int) $existing['id'];
                    continue;
                }

                $payload['created_at'] = $now;
                $table->insert($payload);
                $result['kategori'][$row['nama_kategori']] = (int) $this->db->insertID();
            }
        }

        return $result;
    }

    private function seedUsers($faker): void
    {
        $now = date('Y-m-d H:i:s');
        $rows = [];

        $users = [
            ['nama' => 'Admin SIPENA', 'email' => 'admin@sipena.test', 'username' => 'admin', 'role' => 'admin'],
            ['nama' => 'Nadia Rahmawati', 'email' => 'nadia.rahmawati@sipena.test', 'username' => 'nadia.rahmawati', 'role' => 'kepala_lpm'],
            ['nama' => 'Rizky Pratama', 'email' => 'rizky.pratama@sipena.test', 'username' => 'rizky.pratama', 'role' => 'dosen'],
            ['nama' => 'Sinta Wulandari', 'email' => 'sinta.wulandari@sipena.test', 'username' => 'sinta.wulandari', 'role' => 'dosen'],
            ['nama' => 'Dimas Saputra', 'email' => 'dimas.saputra@sipena.test', 'username' => 'dimas.saputra', 'role' => 'dosen'],
            ['nama' => 'Aulia Maulida', 'email' => 'aulia.maulida@sipena.test', 'username' => 'aulia.maulida', 'role' => 'admin'],
            ['nama' => 'Fajar Nugroho', 'email' => 'fajar.nugroho@sipena.test', 'username' => 'fajar.nugroho', 'role' => 'dosen'],
            ['nama' => 'Yuni Kartika', 'email' => 'yuni.kartika@sipena.test', 'username' => 'yuni.kartika', 'role' => 'dosen'],
            ['nama' => 'Bagas Firmansyah', 'email' => 'bagas.firmansyah@sipena.test', 'username' => 'bagas.firmansyah', 'role' => 'dosen'],
            ['nama' => 'Tia Anggraini', 'email' => 'tia.anggraini@sipena.test', 'username' => 'tia.anggraini', 'role' => 'dosen'],
            ['nama' => 'Ilham Kurniawan', 'email' => 'ilham.kurniawan@sipena.test', 'username' => 'ilham.kurniawan', 'role' => 'dosen'],
            ['nama' => 'Maya Putri Utami', 'email' => 'maya.utami@sipena.test', 'username' => 'maya.utami', 'role' => 'dosen'],
            ['nama' => 'Rina Oktaviani', 'email' => 'rina.oktaviani@sipena.test', 'username' => 'rina.oktaviani', 'role' => 'admin'],
            ['nama' => 'Andi Setiawan', 'email' => 'andi.setiawan@sipena.test', 'username' => 'andi.setiawan', 'role' => 'dosen'],
            ['nama' => 'Gita Permatasari', 'email' => 'gita.permatasari@sipena.test', 'username' => 'gita.permatasari', 'role' => 'dosen'],
            ['nama' => 'Arman Hidayat', 'email' => 'arman.hidayat@sipena.test', 'username' => 'arman.hidayat', 'role' => 'dosen'],
            ['nama' => 'Novi Yuliana', 'email' => 'novi.yuliana@sipena.test', 'username' => 'novi.yuliana', 'role' => 'dosen'],
            ['nama' => 'Fikri Ramadhan', 'email' => 'fikri.ramadhan@sipena.test', 'username' => 'fikri.ramadhan', 'role' => 'dosen'],
            ['nama' => 'Citra Lestari', 'email' => 'citra.lestari@sipena.test', 'username' => 'citra.lestari', 'role' => 'dosen'],
            ['nama' => 'Budi Santoso', 'email' => 'budi.santoso@sipena.test', 'username' => 'budi.santoso', 'role' => 'dosen'],
        ];

        foreach ($users as $index => $user) {
            $time = $index === 0 ? $now : $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'nama' => $user['nama'],
                'email' => $user['email'],
                'username' => $user['username'],
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'role' => $user['role'],
                'ttd_digital' => null,
                'is_active' => 1,
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
                'user_id' => (int) $userId,
                'proses' => $prosesList[$idx % count($prosesList)],
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        $this->db->table('user_penanggung_jawab_proses')->insertBatch($mapRows);
    }

    private function seedProfilInstitusi($faker): void
    {
        $time = $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s');
        $row = [
            'nama_institusi' => 'Universitas Bhakti Nusantara',
            'singkatan_institusi' => 'UBN',
            'visi' => 'Menjadi perguruan tinggi unggul berbasis inovasi, riset, dan pengabdian yang berdampak bagi masyarakat.',
            'misi' => '<ol><li>Menyelenggarakan pendidikan tinggi bermutu berbasis capaian pembelajaran.</li><li>Meningkatkan publikasi riset terapan yang bermanfaat.</li><li>Menguatkan pengabdian kepada masyarakat berbasis kebutuhan wilayah.</li></ol>',
            'tujuan' => '<ol><li>Meningkatkan ketercapaian standar mutu tridharma.</li><li>Memperkuat tata kelola berbasis data dan evaluasi berkelanjutan.</li></ol>',
            'sasaran' => '<ol><li>Akreditasi program studi meningkat.</li><li>Kinerja penelitian dan PKM dosen meningkat setiap tahun.</li><li>Kepuasan pemangku kepentingan internal dan eksternal meningkat.</li></ol>',
            'alamat' => 'Jl. Pendidikan No. 45, Kota Serang, Banten',
            'logo' => null,
            'created_at' => $time,
            'updated_at' => $time,
        ];

        $this->db->table('profil_institusi')->insert($row);
    }

    private function seedStandarMutu($faker, array $masterMap): array
    {
        $kategoriNames = [
            'Kompetensi dan Hasil',
            'Isi',
            'Proses',
            'Penilaian',
            'Sumber Daya Manusia',
            'Sarana dan Prasarana',
            'Pengelolaan',
            'Pembiayaan dan Pendanaan',
        ];

        $standarByJenis = [
            'Pendidikan' => [
                'Standar Kompetensi Lulusan (SKL)',
                'Standar Isi Pembelajaran',
                'Standar Proses Pembelajaran',
                'Standar Penilaian Pembelajaran',
                'Standar Dosen dan Tenaga Kependidikan',
                'Standar Sarana dan Prasarana Pembelajaran',
                'Standar Pengelolaan Pembelajaran',
                'Standar Pembiayaan Pembelajaran',
            ],
            'Penelitian' => [
                'Standar Hasil Penelitian',
                'Standar Isi Penelitian',
                'Standar Proses Penelitian',
                'Standar Penilaian Penelitian',
                'Standar Peneliti',
                'Standar Sarana dan Prasarana Penelitian',
                'Standar Pengelolaan Penelitian',
                'Standar Pendanaan dan Pembiayaan Penelitian',
            ],
            'Pengabdian kepada Masyarakat' => [
                'Standar Hasil PKM',
                'Standar Isi PKM',
                'Standar Proses PKM',
                'Standar Penilaian PKM',
                'Standar Pelaksana PKM',
                'Standar Sarana dan Prasarana PKM',
                'Standar Pengelolaan PKM',
                'Standar Pendanaan dan Pembiayaan PKM',
            ],
        ];

        $rows = [];
        $counter = 1;

        foreach ($standarByJenis as $jenisNama => $standarList) {
            $kodePrefix = $jenisNama === 'Pendidikan' ? 'A' : ($jenisNama === 'Penelitian' ? 'B' : 'C');

            foreach ($standarList as $index => $standarNama) {
                $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
                $kategoriNama = $kategoriNames[$index] ?? null;
                $rows[] = [
                    'kode_standar' => 'STD-' . $kodePrefix . '-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'nama_standar' => $standarNama,
                    'deskripsi' => 'Standar ini menjadi acuan mutu untuk memastikan proses perencanaan, pelaksanaan, evaluasi, dan peningkatan berjalan konsisten.',
                    'jenis_standar_id' => $masterMap['jenis'][$jenisNama] ?? null,
                    'kategori_standar_id' => $kategoriNama !== null ? ($masterMap['kategori'][$kategoriNama] ?? null) : null,
                    'status_publikasi' => $this->statusByIndex($counter),
                    'created_at' => $time,
                    'updated_at' => $time,
                ];
                $counter++;
            }
        }

        $this->db->table('standar_mutu')->insertBatch($rows);

        return $this->db->table('standar_mutu')
            ->select('id, kode_standar, nama_standar')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function seedDokumenStandar($faker, array $standarRows): void
    {
        $rows = [];
        foreach ($standarRows as $index => $standar) {
            $number = $index + 1;
            $time = $faker->dateTimeBetween('-10 months', 'now')->format('Y-m-d H:i:s');
            $namaStandar = trim((string) ($standar['nama_standar'] ?? 'Standar Mutu'));

            $rows[] = [
                'standar_mutu_id' => (int) $standar['id'],
                'kode_dokumen' => 'DOC/' . str_pad((string) $number, 3, '0', STR_PAD_LEFT) . '/SPMI/2026',
                'tanggal_dokumen' => $faker->date('Y-m-d'),
                'revisi' => 'R' . ($number % 4),
                'halaman' => (string) $faker->numberBetween(12, 80),
                'rasional' => '<p>' . $namaStandar . ' disusun untuk memastikan ketercapaian target mutu institusi secara terukur dan berkelanjutan.</p>',
                'subjek_bertanggung_jawab' => '<ul><li>Lembaga Penjaminan Mutu</li><li>Dekan/Ketua Program Studi</li><li>Unit Pendukung Akademik</li></ul>',
                'definisi_istilah' => '<p>Istilah yang digunakan merujuk pada SN Dikti, kebijakan internal, dan pedoman sistem penjaminan mutu internal perguruan tinggi.</p>',
                'pernyataan_isi_standar' => '<ol><li>Unit kerja melaksanakan standar sesuai dokumen mutu yang berlaku.</li><li>Pelaksanaan dievaluasi secara periodik untuk menjamin ketercapaian indikator.</li></ol>',
                'indikator_ketercapaian' => '<ol><li>Terdapat bukti pelaksanaan yang terdokumentasi dengan baik.</li><li>Capaian indikator memenuhi target minimal yang ditetapkan institusi.</li></ol>',
                'strategi_pencapaian' => '<p>Strategi pencapaian dilakukan melalui sosialisasi, pendampingan unit kerja, monitoring rutin, audit internal, dan tindak lanjut perbaikan.</p>',
                'dokumen_terkait' => '<p>Dokumen terkait meliputi kebijakan mutu, manual SPMI, SOP, formulir, serta berita acara evaluasi dan tindak lanjut.</p>',
                'referensi' => '<p>Permendikbudristek tentang SN Dikti, Statuta Perguruan Tinggi, dan dokumen kebijakan mutu internal.</p>',
                'status_publikasi' => $this->statusByIndex($number),
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        $this->db->table('dokumen_standar')->insertBatch($rows);
    }

    private function seedPenugasanPenandatanganStandar($faker, array $standarRows): void
    {
        if (! $this->db->tableExists('penugasan_penandatangan_standar')) {
            return;
        }

        $aktifUsers = $this->db->table('users')
            ->select('id')
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
        $aktifUserIds = array_map(
            static fn($id): int => (int) $id,
            array_column($aktifUsers, 'id')
        );

        if ($aktifUserIds === []) {
            return;
        }

        $rows = [];
        $userTotal = count($aktifUserIds);
        $today = new \DateTimeImmutable('today');

        foreach ($standarRows as $standarIndex => $standar) {
            $standarId = (int) ($standar['id'] ?? 0);
            if ($standarId <= 0) {
                continue;
            }

            foreach (self::PROSES_PENANDATANGAN as $prosesIndex => $proses) {
                $userId = $aktifUserIds[($standarIndex + $prosesIndex) % $userTotal];
                $tanggalTtd = $today
                    ->sub(new \DateInterval('P' . (20 + (($standarIndex + $prosesIndex) % 180)) . 'D'))
                    ->format('Y-m-d');
                $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');

                $rows[] = [
                    'standar_mutu_id' => $standarId,
                    'proses' => $proses,
                    'user_id' => $userId,
                    'tanggal_ttd' => $tanggalTtd,
                    'created_at' => $time,
                    'updated_at' => $time,
                ];
            }
        }

        if ($rows !== []) {
            $this->db->table('penugasan_penandatangan_standar')->insertBatch($rows);
        }
    }

    private function seedPeraturan($faker): void
    {
        $entries = [
            ['kategori' => 'Landasan Hukum', 'judul' => 'Undang-Undang Sistem Pendidikan Nasional', 'nomor' => 'UU-20-2003'],
            ['kategori' => 'Landasan Hukum', 'judul' => 'Undang-Undang Pendidikan Tinggi', 'nomor' => 'UU-12-2012'],
            ['kategori' => 'Peraturan Pemerintah', 'judul' => 'Standar Nasional Pendidikan', 'nomor' => 'PP-57-2021'],
            ['kategori' => 'Peraturan Menteri', 'judul' => 'Standar Nasional Pendidikan Tinggi', 'nomor' => 'Permendikbud-3-2020'],
            ['kategori' => 'Peraturan Menteri', 'judul' => 'Akreditasi Program Studi dan Perguruan Tinggi', 'nomor' => 'Permendikbud-5-2020'],
            ['kategori' => 'Peraturan Menteri', 'judul' => 'Merdeka Belajar Kampus Merdeka', 'nomor' => 'Permendikbud-3-2021'],
            ['kategori' => 'Peraturan BAN-PT', 'judul' => 'Instrumen Akreditasi Perguruan Tinggi', 'nomor' => 'IAPT-4-2024'],
            ['kategori' => 'Peraturan BAN-PT', 'judul' => 'Instrumen Akreditasi Program Studi', 'nomor' => 'IAPS-4-2024'],
            ['kategori' => 'Peraturan Internal', 'judul' => 'Statuta Universitas Bhakti Nusantara', 'nomor' => 'ST-UBN-2024'],
            ['kategori' => 'Peraturan Internal', 'judul' => 'Peraturan Akademik Universitas', 'nomor' => 'PA-UBN-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Kebijakan Implementasi SPMI', 'nomor' => 'PR-UBN-001-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Penyusunan Kurikulum Berbasis OBE', 'nomor' => 'PR-UBN-002-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Penyusunan RPS', 'nomor' => 'PR-UBN-003-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Monitoring dan Evaluasi Pembelajaran', 'nomor' => 'PR-UBN-004-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Penelitian Dosen', 'nomor' => 'PR-UBN-005-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Pengabdian kepada Masyarakat', 'nomor' => 'PR-UBN-006-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Audit Mutu Internal', 'nomor' => 'PR-UBN-007-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Rapat Tinjauan Manajemen', 'nomor' => 'PR-UBN-008-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Pengelolaan Dokumen Mutu', 'nomor' => 'PR-UBN-009-2025'],
            ['kategori' => 'Peraturan Rektor', 'judul' => 'Pedoman Tindak Lanjut Temuan Audit', 'nomor' => 'PR-UBN-010-2025'],
        ];

        $rows = [];
        foreach ($entries as $index => $entry) {
            $number = $index + 1;
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'kategori' => $entry['kategori'],
                'judul' => $entry['judul'],
                'nomor_dokumen' => $entry['nomor'],
                'tahun' => (string) (2023 + ($number % 3)),
                'deskripsi' => 'Dokumen peraturan sebagai rujukan resmi pelaksanaan sistem penjaminan mutu internal.',
                'file_pdf' => 'peraturan_spmi_' . str_pad((string) $number, 2, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi' => $this->statusByIndex($number),
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        $this->db->table('peraturan')->insertBatch($rows);
    }

    private function seedKebijakanMutu($faker): void
    {
        $titles = [
            'Kebijakan Mutu Universitas',
            'Kebijakan Penjaminan Mutu Akademik',
            'Kebijakan Penjaminan Mutu Non Akademik',
            'Kebijakan Capaian Pembelajaran Lulusan',
            'Kebijakan Pengembangan Kurikulum OBE',
            'Kebijakan Peningkatan Kualitas Pembelajaran',
            'Kebijakan Penjaminan Mutu Penelitian',
            'Kebijakan Penjaminan Mutu Pengabdian kepada Masyarakat',
            'Kebijakan Pengelolaan Dokumen Mutu',
            'Kebijakan Audit Mutu Internal',
            'Kebijakan Tindak Lanjut Hasil Audit',
            'Kebijakan Rapat Tinjauan Manajemen',
            'Kebijakan Peningkatan Kinerja Dosen',
            'Kebijakan Monitoring dan Evaluasi Pembelajaran',
            'Kebijakan Pengukuran Kepuasan Pemangku Kepentingan',
            'Kebijakan Manajemen Risiko Akademik',
            'Kebijakan Penguatan Budaya Mutu',
            'Kebijakan Digitalisasi Sistem Mutu',
            'Kebijakan Benchmarking Mutu Perguruan Tinggi',
            'Kebijakan Pengembangan Berkelanjutan SPMI',
        ];

        $rows = [];
        foreach ($titles as $index => $title) {
            $number = $index + 1;
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'judul' => $title,
                'nomor_dokumen' => 'KM/UBN/' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'tahun' => (string) (2023 + ($number % 3)),
                'deskripsi' => 'Dokumen kebijakan mutu sebagai arahan strategis implementasi SPMI di tingkat universitas.',
                'file_pdf' => 'kebijakan_mutu_' . str_pad((string) $number, 2, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi' => $this->statusByIndex($number),
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        $this->db->table('kebijakan_mutu')->insertBatch($rows);
    }

    private function seedKebijakanSpmi($faker): void
    {
        $titles = [
            'Kebijakan Sistem Penjaminan Mutu Internal',
            'Kebijakan PPEPP Universitas',
            'Kebijakan Penetapan Standar SPMI',
            'Kebijakan Pelaksanaan Standar SPMI',
            'Kebijakan Evaluasi Pelaksanaan Standar',
            'Kebijakan Pengendalian Pelaksanaan Standar',
            'Kebijakan Peningkatan Standar Berkelanjutan',
            'Kebijakan Integrasi SPMI dan SPME',
            'Kebijakan Pengelolaan Unit Penjaminan Mutu',
            'Kebijakan Audit Dokumen Mutu',
            'Kebijakan Monitoring Rencana Tindak Lanjut',
            'Kebijakan Manajemen Data Mutu',
            'Kebijakan Tata Kelola Proses Akademik',
            'Kebijakan Tata Kelola Penelitian',
            'Kebijakan Tata Kelola Pengabdian kepada Masyarakat',
            'Kebijakan Penjaminan Mutu Kerjasama',
            'Kebijakan Penguatan Kapasitas Auditor Internal',
            'Kebijakan Penguatan Kapasitas Gugus Mutu',
            'Kebijakan Pelaporan Kinerja Mutu Tahunan',
            'Kebijakan Evaluasi Efektivitas SPMI',
        ];

        $rows = [];
        foreach ($titles as $index => $title) {
            $number = $index + 1;
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'judul' => $title,
                'nomor_dokumen' => 'KSPMI/UBN/' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'tahun' => (string) (2023 + ($number % 3)),
                'deskripsi' => 'Dokumen kebijakan SPMI untuk memastikan siklus mutu berjalan efektif di seluruh unit kerja.',
                'file_pdf' => 'kebijakan_spmi_' . str_pad((string) $number, 2, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi' => $this->statusByIndex($number),
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        $this->db->table('kebijakan_spmi')->insertBatch($rows);
    }

    private function seedPedomanPpepp($faker): void
    {
        $dokumenPpepp = [
            'Dokumen Manual SPMI',
            'Dokumen Kebijakan SPMI',
            'Dokumen Standar SPMI',
            'Dokumen Formulir SPMI',
            'Dokumen Evaluasi Diri Program Studi',
            'Dokumen Rencana Mutu Tahunan',
            'Dokumen Peta Proses Mutu',
            'Dokumen Peta Risiko Mutu',
            'Dokumen Laporan Kinerja Mutu Semester',
            'Dokumen Laporan Capaian IKU dan IKT',
        ];

        $sopList = [
            'SOP Penetapan Standar Mutu SN Dikti',
            'SOP Perumusan Indikator Mutu dan Target Capaian',
            'SOP Penetapan Capaian Pembelajaran Lulusan (CPL)',
            'SOP Penyusunan dan Penetapan RPS Berbasis OBE',
            'SOP Pelaksanaan Pembelajaran Berbasis RPS',
            'SOP Pelaksanaan Penilaian Pembelajaran',
            'SOP Pelaksanaan Penelitian Dosen',
            'SOP Pelaksanaan Pengabdian kepada Masyarakat (PKM)',
            'SOP Pengelolaan Dokumen Mutu (Upload, Revisi, Arsip)',
            'SOP Monitoring dan Evaluasi Pembelajaran (Monev)',
            'SOP Evaluasi Kinerja Dosen (EDOM / Kinerja Tridharma)',
            'SOP Evaluasi Capaian CPL (OBE Assessment)',
            'SOP Pelaksanaan Audit Mutu Internal (AMI)',
            'SOP Pengelolaan Temuan Audit (Minor, Mayor, Observasi)',
            'SOP Penyusunan Rencana Tindak Lanjut (RTL)',
            'SOP Monitoring Tindak Lanjut Temuan Audit',
            'SOP Tinjauan Manajemen (RTM / Rapat Tinjauan Mutu)',
            'SOP Revisi dan Peningkatan Standar Mutu',
            'SOP Benchmarking Mutu Perguruan Tinggi',
            'SOP Pengembangan Budaya Mutu Berkelanjutan',
        ];

        $formulirList = [
            'Form Penetapan Standar Mutu',
            'Form Penetapan Indikator dan Target Mutu',
            'Form Penetapan CPL (Capaian Pembelajaran Lulusan)',
            'Form Validasi & Pengesahan RPS',
            'Form Monitoring Pelaksanaan Pembelajaran (Perkuliahan/RPS)',
            'Form Penilaian Hasil Belajar Mahasiswa',
            'Form Logbook Kegiatan Penelitian Dosen',
            'Form Logbook Kegiatan PKM',
            'Form Unggah & Kontrol Dokumen Mutu',
            'Form Evaluasi Pembelajaran (EDOM Mahasiswa)',
            'Form Evaluasi Kinerja Dosen (Tridharma)',
            'Form Evaluasi Capaian CPL (OBE Assessment)',
            'Form Instrumen Audit Mutu Internal (AMI Checklist)',
            'Form Laporan Temuan Audit (Minor/Major/Observasi)',
            'Form Rencana Tindak Lanjut (RTL)',
            'Form Monitoring Tindak Lanjut (Progress RTL)',
            'Form Berita Acara Rapat Tinjauan Manajemen (RTM)',
            'Form Usulan Revisi Standar Mutu',
            'Form Benchmarking Mutu Perguruan Tinggi',
            'Form Survei Kepuasan (Mahasiswa, Alumni, Pengguna Lulusan)',
        ];

        $rows = [];
        $counter = 1;

        foreach ($dokumenPpepp as $index => $judul) {
            $number = $index + 1;
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'jenis_dokumen' => 'Dokumen',
                'judul' => $judul,
                'nomor_dokumen' => 'DOC/SPMI/' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'tahun' => (string) (2023 + ($number % 3)),
                'deskripsi' => 'Dokumen pendukung implementasi siklus PPEPP pada unit akademik dan non-akademik.',
                'file_pdf' => 'dokumen_ppepp_' . str_pad((string) $number, 3, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi' => $this->statusByIndex($counter),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $counter++;
        }

        foreach ($sopList as $index => $judul) {
            $number = $index + 1;
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'jenis_dokumen' => 'SOP',
                'judul' => $judul,
                'nomor_dokumen' => 'SOP/SPMI/' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'tahun' => (string) (2024 + ($number % 2)),
                'deskripsi' => 'SOP operasional untuk memastikan proses mutu berjalan konsisten, terdokumentasi, dan terukur.',
                'file_pdf' => 'sop_spmi_' . str_pad((string) $number, 3, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi' => $this->statusByIndex($counter),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $counter++;
        }

        foreach ($formulirList as $index => $judul) {
            $number = $index + 1;
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'jenis_dokumen' => 'Formulir',
                'judul' => $judul,
                'nomor_dokumen' => 'FRM/SPMI/' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'tahun' => (string) (2024 + ($number % 2)),
                'deskripsi' => 'Formulir kendali mutu untuk pencatatan bukti pelaksanaan, evaluasi, dan tindak lanjut.',
                'file_pdf' => 'formulir_spmi_' . str_pad((string) $number, 3, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi' => $this->statusByIndex($counter),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $counter++;
        }

        $this->db->table('pedoman_ppepp')->insertBatch($rows);
    }

    private function seedAuditMutuInternal($faker): void
    {
        $titles = [
            'Program Audit Mutu Internal Tahunan',
            'Jadwal Audit Mutu Internal Semester Ganjil',
            'Jadwal Audit Mutu Internal Semester Genap',
            'Instrumen Audit Standar Pendidikan',
            'Instrumen Audit Standar Penelitian',
            'Instrumen Audit Standar PKM',
            'Laporan Hasil Audit Fakultas Teknik',
            'Laporan Hasil Audit Fakultas Ekonomi',
            'Laporan Hasil Audit Fakultas Keguruan',
            'Laporan Hasil Audit Pascasarjana',
            'Rekap Temuan Audit Kategori Minor',
            'Rekap Temuan Audit Kategori Mayor',
            'Rekap Observasi dan Peluang Peningkatan',
            'Analisis Akar Masalah Temuan Audit',
            'Rencana Tindak Lanjut Hasil AMI',
            'Monitoring Capaian Tindak Lanjut AMI',
            'Berita Acara Exit Meeting Audit',
            'Laporan Verifikasi Tindak Lanjut',
            'Evaluasi Efektivitas Audit Internal',
            'Laporan Penutupan Siklus AMI Tahunan',
        ];

        $rows = [];
        foreach ($titles as $index => $title) {
            $number = $index + 1;
            $time = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d H:i:s');
            $rows[] = [
                'judul' => $title,
                'nomor_dokumen' => 'AMI/UBN/' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'tahun' => (string) (2023 + ($number % 3)),
                'deskripsi' => 'Dokumen audit mutu internal untuk memastikan kepatuhan, efektivitas proses, dan perbaikan berkelanjutan.',
                'file_pdf' => 'audit_mutu_internal_' . str_pad((string) $number, 3, '0', STR_PAD_LEFT) . '.pdf',
                'status_publikasi' => $this->statusByIndex($number),
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        $this->db->table('audit_mutu_internal')->insertBatch($rows);
    }

    private function statusByIndex(int $index): string
    {
        return $index % 4 === 0 ? 'draft' : 'publish';
    }
}
