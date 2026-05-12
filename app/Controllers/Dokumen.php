<?php

namespace App\Controllers;

use App\Models\AuditMutuInternalModel;
use App\Models\DokumenStandarModel;
use App\Models\KebijakanMutuModel;
use App\Models\KebijakanSpmiModel;
use App\Models\KategoriStandarModel;
use App\Models\JenisStandarModel;
use App\Models\PenugasanPenandatanganStandarModel;
use App\Models\PedomanPpeppModel;
use App\Models\PeraturanModel;
use App\Models\RiwayatPerubahanDokumenStandarModel;
use App\Models\ProfilInstitusiModel;
use App\Models\StandarMutuModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Dokumen extends BaseController
{
    private const STATUS_OPTIONS = ['draft', 'publish'];
    private const PROSES_PENANDATANGAN = [
        'perumusan' => 'Perumusan',
        'pemeriksaan' => 'Pemeriksaan',
        'persetujuan' => 'Persetujuan',
        'pengesahan' => 'Pengesahan',
        'pengendalian' => 'Pengendalian',
    ];
    private const FIELD_RIWAYAT_DOKUMEN_STANDAR = [
        'rasional' => 'Rasional',
        'subjek_bertanggung_jawab' => 'Subjek / Pihak yang Bertanggung Jawab',
        'definisi_istilah' => 'Definisi Istilah',
        'pernyataan_isi_standar' => 'Pernyataan Isi Standar',
        'indikator_ketercapaian' => 'Indikator Ketercapaian',
        'strategi_pencapaian' => 'Strategi Pencapaian',
        'dokumen_terkait' => 'Dokumen Terkait',
        'referensi' => 'Referensi',
    ];

    private function resolvePerPage(): int
    {
        $allowed = [15, 25, 50];
        $requested = (int) $this->request->getGet('per_page');

        return in_array($requested, $allowed, true) ? $requested : 15;
    }

    private function statusFilterValue(string $status): string
    {
        return in_array($status, self::STATUS_OPTIONS, true) ? $status : '';
    }

    private function uploadPdf(string $folder, string $field = 'file_pdf', ?string $currentFile = null): ?string
    {
        $file = $this->request->getFile($field);

        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return $currentFile;
        }

        if (! $file->isValid()) {
            return $currentFile;
        }

        $directory = FCPATH . 'uploads/' . $folder;
        if (! is_dir($directory)) {
            if (! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                return $currentFile;
            }
        }

        $newName = $file->getRandomName();
        $file->move($directory, $newName);

        if (! empty($currentFile)) {
            $oldPath = $directory . '/' . $currentFile;
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        return $newName;
    }

    private function deletePdf(string $folder, ?string $filename): void
    {
        if (empty($filename)) {
            return;
        }

        $path = FCPATH . 'uploads/' . $folder . '/' . $filename;
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function sanitizeDokumenStandarHtml(?string $value): string
    {
        $allowedTags = '<p><br><ol><ul><li><strong><b><em><i><u><blockquote><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6>';
        $clean = trim((string) strip_tags((string) $value, $allowedTags));

        // Keep allowed formatting tags but strip all attributes/events (e.g. onerror, style).
        return preg_replace(
            '/<(\/?)(p|br|ol|ul|li|strong|b|em|i|u|blockquote|table|thead|tbody|tr|th|td|h[1-6])(?:\s+[^>]*)?>/i',
            '<$1$2>',
            $clean
        ) ?? '';
    }

    private function renderIndikatorKetercapaian(?string $rawValue): string
    {
        $raw = trim((string) $rawValue);
        if ($raw === '') {
            return '-';
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return $raw;
        }

        $items = [];
        foreach ($decoded as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $items[] = $item;
            }
        }

        if ($items === []) {
            return $raw;
        }

        $html = '<ol>';
        foreach ($items as $item) {
            $html .= '<li>' . esc($item) . '</li>';
        }
        $html .= '</ol>';

        return $html;
    }

    private function getPpeppRedirectUrl(string $jenisDokumen): string
    {
        return match ($jenisDokumen) {
            'SOP' => base_url('/pedoman-ppepp/sop'),
            'Formulir' => base_url('/pedoman-ppepp/formulir'),
            default => base_url('/pedoman-ppepp/dokumen'),
        };
    }

    private function getStandarMutuPrefixByJenis(string $namaJenis): string
    {
        $lower = strtolower(trim($namaJenis));
        if ($lower === 'pendidikan') {
            return 'P';
        }

        if ($lower === 'penelitian') {
            return 'PN';
        }

        if (str_contains($lower, 'pengabdian')) {
            return 'PKM';
        }

        $words = preg_split('/[^A-Za-z0-9]+/', trim($namaJenis));
        if (! is_array($words)) {
            return '';
        }

        $prefix = '';
        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') {
                continue;
            }

            $prefix .= strtoupper(substr($word, 0, 1));
        }

        if ($prefix === '' && trim($namaJenis) !== '') {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $namaJenis) ?? '', 0, 3));
        }

        return substr($prefix, 0, 4);
    }

    private function generateNextStandarMutuKode(int $jenisStandarId, string $namaJenis): string
    {
        $prefix = $this->getStandarMutuPrefixByJenis($namaJenis);
        if ($prefix === '') {
            return '';
        }

        $model = new StandarMutuModel();
        $existing = $model->select('kode_standar')->where('jenis_standar_id', $jenisStandarId)->findAll();
        $maxNumber = 0;

        foreach ($existing as $row) {
            $kode = trim((string) ($row['kode_standar'] ?? ''));
            if (preg_match('/^STD-' . preg_quote($prefix, '/') . '-(\d+)$/i', $kode, $matches)) {
                $number = (int) $matches[1];
                $maxNumber = max($maxNumber, $number);
            }
        }

        return sprintf('STD-%s-%02d', $prefix, $maxNumber + 1);
    }

    private function roleLabel(?string $role): string
    {
        $map = [
            'admin' => 'Admin',
            'kepala_lpm' => 'Kepala LPM',
            'dosen' => 'Dosen',
        ];

        $key = strtolower(trim((string) $role));
        if ($key === '') {
            return '-';
        }

        return $map[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    private function payloadDokumenStandar(array $standar): array
    {
        return [
            'kode_dokumen' => (string) ($standar['kode_standar'] ?? ''),
            'tanggal_dokumen' => $this->request->getPost('tanggal_dokumen') ?: null,
            'revisi' => $this->request->getPost('revisi'),
            'halaman' => $this->request->getPost('halaman'),
            'rasional' => $this->sanitizeDokumenStandarHtml($this->request->getPost('rasional')),
            'subjek_bertanggung_jawab' => $this->sanitizeDokumenStandarHtml($this->request->getPost('subjek_bertanggung_jawab')),
            'definisi_istilah' => $this->sanitizeDokumenStandarHtml($this->request->getPost('definisi_istilah')),
            'pernyataan_isi_standar' => $this->sanitizeDokumenStandarHtml($this->request->getPost('pernyataan_isi_standar')),
            'indikator_ketercapaian' => $this->sanitizeDokumenStandarHtml($this->request->getPost('indikator_ketercapaian')),
            'strategi_pencapaian' => $this->sanitizeDokumenStandarHtml($this->request->getPost('strategi_pencapaian')),
            'dokumen_terkait' => $this->sanitizeDokumenStandarHtml($this->request->getPost('dokumen_terkait')),
            'referensi' => $this->sanitizeDokumenStandarHtml($this->request->getPost('referensi')),
            'status_publikasi' => $this->statusFilterValue((string) ($standar['status_publikasi'] ?? '')) ?: 'draft',
        ];
    }

    private function fieldsDokumenStandarYangBerubah(array $dokumenLama, array $payloadBaru): array
    {
        $changed = [];

        foreach (self::FIELD_RIWAYAT_DOKUMEN_STANDAR as $field => $label) {
            $lama = trim((string) ($dokumenLama[$field] ?? ''));
            $baru = trim((string) ($payloadBaru[$field] ?? ''));
            if ($lama !== $baru) {
                $changed[] = $label;
            }
        }

        return $changed;
    }

    private function simpanRiwayatDokumenStandar(array $dokumenLama, array $changedFields): void
    {
        if ($changedFields === []) {
            return;
        }

        $historyModel = new RiwayatPerubahanDokumenStandarModel();

        $historyModel->insert([
            'dokumen_standar_id' => (int) ($dokumenLama['id'] ?? 0),
            'standar_mutu_id' => (int) ($dokumenLama['standar_mutu_id'] ?? 0),
            'updated_by' => (int) (session()->get('user_id') ?? 0) ?: null,
            'changed_fields' => json_encode($changedFields, JSON_UNESCAPED_UNICODE),
            'rasional' => $dokumenLama['rasional'] ?? null,
            'subjek_bertanggung_jawab' => $dokumenLama['subjek_bertanggung_jawab'] ?? null,
            'definisi_istilah' => $dokumenLama['definisi_istilah'] ?? null,
            'pernyataan_isi_standar' => $dokumenLama['pernyataan_isi_standar'] ?? null,
            'indikator_ketercapaian' => $dokumenLama['indikator_ketercapaian'] ?? null,
            'strategi_pencapaian' => $dokumenLama['strategi_pencapaian'] ?? null,
            'dokumen_terkait' => $dokumenLama['dokumen_terkait'] ?? null,
            'referensi' => $dokumenLama['referensi'] ?? null,
        ]);
    }

    private function getPenugasanStandarUntukForm(int $standarId): array
    {
        if ($standarId <= 0) {
            return [];
        }

        $model = new PenugasanPenandatanganStandarModel();
        $rows = $model->where('standar_mutu_id', $standarId)->findAll();
        $result = [];

        foreach ($rows as $row) {
            $proses = strtolower(trim((string) ($row['proses'] ?? '')));
            if ($proses === '' || ! array_key_exists($proses, self::PROSES_PENANDATANGAN)) {
                continue;
            }
            $result[$proses] = [
                'user_id' => (int) ($row['user_id'] ?? 0),
                'tanggal_ttd' => $row['tanggal_ttd'] ?? null,
            ];
        }

        return $result;
    }

    private function validasiPenugasanPenandatangan(): ?string
    {
        $input = (array) $this->request->getPost('penandatangan');
        $tanggalInput = (array) $this->request->getPost('tanggal_ttd');
        $aktifIds = array_map(
            static fn($value) => (int) $value,
            (new UserModel())->select('id')->where('is_active', 1)->findColumn('id') ?? []
        );

        $wajibKosong = [];
        $tidakValid = [];
        $tanggalKosong = [];
        $tanggalTidakValid = [];

        foreach (self::PROSES_PENANDATANGAN as $key => $label) {
            $userId = (int) ($input[$key] ?? 0);
            $tanggalTtd = trim((string) ($tanggalInput[$key] ?? ''));
            if ($userId <= 0) {
                $wajibKosong[] = $label;
                continue;
            }

            if (! in_array($userId, $aktifIds, true)) {
                $tidakValid[] = $label;
            }

            if ($tanggalTtd === '') {
                $tanggalKosong[] = $label;
                continue;
            }

            $isFormatValid = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalTtd) === 1;
            $tanggalObj = \DateTime::createFromFormat('Y-m-d', $tanggalTtd);
            $isTanggalValid = $tanggalObj && $tanggalObj->format('Y-m-d') === $tanggalTtd;
            if (! $isFormatValid || ! $isTanggalValid) {
                $tanggalTidakValid[] = $label;
            }
        }

        if ($wajibKosong !== []) {
            return 'Penugasan wajib diisi untuk semua proses: ' . implode(', ', $wajibKosong) . '.';
        }

        if ($tidakValid !== []) {
            return 'Pengguna pada proses berikut tidak valid/nonaktif: ' . implode(', ', $tidakValid) . '.';
        }

        if ($tanggalKosong !== []) {
            return 'Tanggal tanda tangan wajib diisi untuk proses: ' . implode(', ', $tanggalKosong) . '.';
        }

        if ($tanggalTidakValid !== []) {
            return 'Format tanggal tanda tangan tidak valid pada proses: ' . implode(', ', $tanggalTidakValid) . '.';
        }

        return null;
    }

    private function simpanPenugasanPenandatanganStandar(int $standarId): void
    {
        $model = new PenugasanPenandatanganStandarModel();
        $model->where('standar_mutu_id', $standarId)->delete();

        $input = (array) $this->request->getPost('penandatangan');
        $tanggalInput = (array) $this->request->getPost('tanggal_ttd');
        foreach (self::PROSES_PENANDATANGAN as $key => $label) {
            $userId = (int) ($input[$key] ?? 0);
            if ($userId <= 0) {
                continue;
            }

            $model->insert([
                'standar_mutu_id' => $standarId,
                'proses' => $key,
                'user_id' => $userId,
                'tanggal_ttd' => trim((string) ($tanggalInput[$key] ?? '')) ?: null,
            ]);
        }
    }

    private function sinkronStatusDokumenStandar(int $standarId, string $statusPublikasi): void
    {
        if ($standarId <= 0) {
            return;
        }

        $status = $this->statusFilterValue($statusPublikasi) ?: 'draft';
        (new DokumenStandarModel())
            ->where('standar_mutu_id', $standarId)
            ->set(['status_publikasi' => $status])
            ->update();
    }

    private function validasiPenandatanganTersimpanUntukPublish(int $standarId): ?string
    {
        if ($standarId <= 0) {
            return 'Standar mutu tidak valid.';
        }

        $model = new PenugasanPenandatanganStandarModel();
        $rows = $model->where('standar_mutu_id', $standarId)->findAll();
        $penugasan = [];

        foreach ($rows as $row) {
            $proses = strtolower(trim((string) ($row['proses'] ?? '')));
            if ($proses === '' || ! array_key_exists($proses, self::PROSES_PENANDATANGAN)) {
                continue;
            }

            $penugasan[$proses] = [
                'user_id' => (int) ($row['user_id'] ?? 0),
                'tanggal_ttd' => trim((string) ($row['tanggal_ttd'] ?? '')),
            ];
        }

        $aktifIds = array_map(
            static fn($value) => (int) $value,
            (new UserModel())->select('id')->where('is_active', 1)->findColumn('id') ?? []
        );

        $belumAda = [];
        $userTidakValid = [];
        $tanggalKosong = [];
        $tanggalTidakValid = [];

        foreach (self::PROSES_PENANDATANGAN as $key => $label) {
            $item = $penugasan[$key] ?? null;
            if ($item === null) {
                $belumAda[] = $label;
                continue;
            }

            $userId = (int) ($item['user_id'] ?? 0);
            if ($userId <= 0) {
                $belumAda[] = $label;
                continue;
            }

            if (! in_array($userId, $aktifIds, true)) {
                $userTidakValid[] = $label;
            }

            $tanggal = trim((string) ($item['tanggal_ttd'] ?? ''));
            if ($tanggal === '') {
                $tanggalKosong[] = $label;
                continue;
            }

            $isFormatValid = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) === 1;
            $tanggalObj = \DateTime::createFromFormat('Y-m-d', $tanggal);
            $isTanggalValid = $tanggalObj && $tanggalObj->format('Y-m-d') === $tanggal;
            if (! $isFormatValid || ! $isTanggalValid) {
                $tanggalTidakValid[] = $label;
            }
        }

        if ($belumAda !== []) {
            return 'Dokumen tidak dapat diterbitkan. Penandatangan belum lengkap pada proses: ' . implode(', ', $belumAda) . '.';
        }

        if ($userTidakValid !== []) {
            return 'Dokumen tidak dapat diterbitkan. Penandatangan tidak aktif/invalid pada proses: ' . implode(', ', $userTidakValid) . '.';
        }

        if ($tanggalKosong !== []) {
            return 'Dokumen tidak dapat diterbitkan. Tanggal tanda tangan belum diisi pada proses: ' . implode(', ', $tanggalKosong) . '.';
        }

        if ($tanggalTidakValid !== []) {
            return 'Dokumen tidak dapat diterbitkan. Format tanggal tanda tangan tidak valid pada proses: ' . implode(', ', $tanggalTidakValid) . '.';
        }

        return null;
    }

    private function getPenandatanganProsesByStandar(int $standarId): array
    {
        $assignmentModel = new PenugasanPenandatanganStandarModel();
        $userModel = new UserModel();

        $rows = $assignmentModel->where('standar_mutu_id', $standarId)->findAll();
        $assigned = [];
        foreach ($rows as $row) {
            $prosesKey = strtolower(trim((string) ($row['proses'] ?? '')));
            if ($prosesKey === '' || ! array_key_exists($prosesKey, self::PROSES_PENANDATANGAN)) {
                continue;
            }
            $assigned[$prosesKey] = [
                'user_id' => (int) ($row['user_id'] ?? 0),
                'tanggal_ttd' => $row['tanggal_ttd'] ?? null,
            ];
        }

        $userIds = [];
        foreach ($assigned as $item) {
            $userId = (int) ($item['user_id'] ?? 0);
            if ($userId > 0) {
                $userIds[] = $userId;
            }
        }
        $userIds = array_values(array_unique($userIds));
        $usersById = [];
        if ($userIds !== []) {
            $users = $userModel->whereIn('id', $userIds)->findAll();
            foreach ($users as $user) {
                $usersById[(int) ($user['id'] ?? 0)] = $user;
            }
        }

        $result = [];
        foreach (self::PROSES_PENANDATANGAN as $prosesKey => $prosesLabel) {
            $assignedItem = $assigned[$prosesKey] ?? [];
            $userId = (int) ($assignedItem['user_id'] ?? 0);
            if ($userId > 0 && isset($usersById[$userId])) {
                $user = $usersById[$userId];
                $jabatan = trim((string) ($user['jabatan'] ?? ''));
                $result[$prosesLabel] = [
                    'nama' => $user['nama'] ?? '-',
                    'jabatan' => $jabatan !== '' ? $jabatan : $this->roleLabel($user['role'] ?? ''),
                    'ttd_digital' => $user['ttd_digital'] ?? null,
                    'tanggal_ttd' => $assignedItem['tanggal_ttd'] ?? null,
                ];
            }
        }

        return $result;
    }

    public function peraturanKategori(string $slug)
    {
        $kategori = $this->kategoriPeraturanDariSlug($slug);
        if ($kategori === '') {
            throw PageNotFoundException::forPageNotFound('Kategori peraturan tidak ditemukan.');
        }

        return $this->peraturan($kategori);
    }

    public function peraturan(string $kategoriPreset = '')
    {
        $model = new PeraturanModel();
        $perPage = $this->resolvePerPage();

        $status = $this->statusFilterValue(trim((string) $this->request->getGet('status')));
        $kategori = trim($kategoriPreset) !== '' ? trim($kategoriPreset) : trim((string) $this->request->getGet('kategori'));
        $keyword = trim((string) $this->request->getGet('keyword'));

        $daftarKategori = ['Landasan Hukum', 'Peraturan Dikti', 'Peraturan Rektor'];

        if ($status !== '') {
            $model->where('status_publikasi', $status);
        }

        if ($kategori !== '' && in_array($kategori, $daftarKategori, true)) {
            $model->where('kategori', $kategori);
        } else {
            $kategori = '';
        }

        if ($keyword !== '') {
            $model->groupStart()
                ->like('judul', $keyword)
                ->orLike('nomor_dokumen', $keyword)
                ->groupEnd();
        }

        $pageDesc = 'Kelola data peraturan sebagai dokumen dasar dalam SIPENA.';
        if ($kategori !== '') {
            $pageDesc = 'Daftar dokumen untuk kategori ' . $kategori . '.';
        }

        return view('dokumen/peraturan/index', [
            'title' => 'Peraturan',
            'pageTitle' => 'Peraturan',
            'pageDesc' => $pageDesc,
            'peraturan' => $model->orderBy('id', 'DESC')->paginate($perPage, 'peraturan'),
            'pager' => $model->pager,
            'perPage' => $perPage,
            'perPageAktif' => $perPage,
            'opsiPerPage' => [15, 25, 50],
            'statusAktif' => $status,
            'kategoriAktif' => $kategori,
            'keywordAktif' => $keyword,
            'daftarStatus' => self::STATUS_OPTIONS,
            'daftarKategori' => $daftarKategori,
        ]);
    }

    private function kategoriPeraturanDariSlug(string $slug): string
    {
        $map = [
            'landasan-hukum' => 'Landasan Hukum',
            'peraturan-dikti' => 'Peraturan Dikti',
            'peraturan-rektor' => 'Peraturan Rektor',
        ];

        return $map[strtolower(trim($slug))] ?? '';
    }

    public function tambahPeraturan()
    {
        $kategori = trim((string) $this->request->getGet('kategori'));

        return view('dokumen/peraturan/form', [
            'title' => 'Tambah Peraturan',
            'pageTitle' => 'Tambah Peraturan',
            'pageDesc' => 'Form input data peraturan.',
            'peraturan' => [
                'kategori' => $kategori,
            ],
            'action' => base_url('/peraturan/simpan'),
        ]);
    }

    public function simpanPeraturan()
    {
        $rules = [
            'kategori' => 'required',
            'judul' => 'required',
            'file_pdf' => 'permit_empty|uploaded[file_pdf]|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Kategori dan judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $model = new PeraturanModel();
        $fileName = $this->uploadPdf('peraturan');

        $model->save([
            'kategori' => $this->request->getPost('kategori'),
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to('/peraturan')->with('success', 'Data peraturan berhasil ditambahkan.');
    }

    public function detailPeraturan($id)
    {
        $model = new PeraturanModel();
        $peraturan = $model->find($id);

        if (! $peraturan) {
            throw PageNotFoundException::forPageNotFound('Data peraturan tidak ditemukan.');
        }

        return view('dokumen/peraturan/detail', [
            'title' => 'Detail Peraturan',
            'pageTitle' => 'Detail Peraturan',
            'pageDesc' => 'Informasi lengkap peraturan.',
            'peraturan' => $peraturan,
        ]);
    }

    public function editPeraturan($id)
    {
        $model = new PeraturanModel();
        $peraturan = $model->find($id);

        if (! $peraturan) {
            throw PageNotFoundException::forPageNotFound('Data peraturan tidak ditemukan.');
        }

        return view('dokumen/peraturan/form', [
            'title' => 'Edit Peraturan',
            'pageTitle' => 'Edit Peraturan',
            'pageDesc' => 'Form edit data peraturan.',
            'peraturan' => $peraturan,
            'action' => base_url('/peraturan/update/' . $id),
        ]);
    }

    public function updatePeraturan($id)
    {
        $model = new PeraturanModel();
        $peraturan = $model->find($id);

        if (! $peraturan) {
            throw PageNotFoundException::forPageNotFound('Data peraturan tidak ditemukan.');
        }

        $rules = [
            'kategori' => 'required',
            'judul' => 'required',
            'file_pdf' => 'permit_empty|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Kategori dan judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $fileName = $this->uploadPdf('peraturan', 'file_pdf', $peraturan['file_pdf'] ?? null);

        $model->update($id, [
            'kategori' => $this->request->getPost('kategori'),
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to('/peraturan')->with('success', 'Data peraturan berhasil diperbarui.');
    }

    public function hapusPeraturan($id)
    {
        $model = new PeraturanModel();
        $peraturan = $model->find($id);

        if (! $peraturan) {
            throw PageNotFoundException::forPageNotFound('Data peraturan tidak ditemukan.');
        }

        $this->deletePdf('peraturan', $peraturan['file_pdf'] ?? null);
        $model->delete($id);

        return redirect()->to('/peraturan')->with('success', 'Data peraturan berhasil dihapus.');
    }

    public function kebijakanMutu()
    {
        $model = new KebijakanMutuModel();
        $perPage = $this->resolvePerPage();

        $status = $this->statusFilterValue(trim((string) $this->request->getGet('status')));
        $keyword = trim((string) $this->request->getGet('keyword'));

        if ($status !== '') {
            $model->where('status_publikasi', $status);
        }

        if ($keyword !== '') {
            $model->groupStart()
                ->like('judul', $keyword)
                ->orLike('nomor_dokumen', $keyword)
                ->groupEnd();
        }

        return view('dokumen/kebijakan_mutu/index', [
            'title' => 'Kebijakan Mutu',
            'pageTitle' => 'Kebijakan Mutu',
            'pageDesc' => 'Kelola dokumen Kebijakan Mutu dalam SIPENA.',
            'kebijakan' => $model->orderBy('id', 'DESC')->paginate($perPage, 'kebijakan_mutu'),
            'pager' => $model->pager,
            'perPage' => $perPage,
            'perPageAktif' => $perPage,
            'opsiPerPage' => [15, 25, 50],
            'statusAktif' => $status,
            'keywordAktif' => $keyword,
            'daftarStatus' => self::STATUS_OPTIONS,
        ]);
    }

    public function tambahKebijakanMutu()
    {
        return view('dokumen/kebijakan_mutu/form', [
            'title' => 'Tambah Kebijakan Mutu',
            'pageTitle' => 'Tambah Kebijakan Mutu',
            'pageDesc' => 'Form input dokumen Kebijakan Mutu.',
            'kebijakan' => null,
            'action' => base_url('/kebijakan-mutu/simpan'),
        ]);
    }

    public function simpanKebijakanMutu()
    {
        $rules = [
            'judul' => 'required',
            'file_pdf' => 'permit_empty|uploaded[file_pdf]|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $model = new KebijakanMutuModel();
        $fileName = $this->uploadPdf('kebijakan_mutu');

        $model->save([
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to('/kebijakan-mutu')->with('success', 'Dokumen Kebijakan Mutu berhasil ditambahkan.');
    }

    public function detailKebijakanMutu($id)
    {
        $model = new KebijakanMutuModel();
        $kebijakan = $model->find($id);

        if (! $kebijakan) {
            throw PageNotFoundException::forPageNotFound('Dokumen Kebijakan Mutu tidak ditemukan.');
        }

        return view('dokumen/kebijakan_mutu/detail', [
            'title' => 'Detail Kebijakan Mutu',
            'pageTitle' => 'Detail Kebijakan Mutu',
            'pageDesc' => 'Informasi lengkap dokumen Kebijakan Mutu.',
            'kebijakan' => $kebijakan,
        ]);
    }

    public function editKebijakanMutu($id)
    {
        $model = new KebijakanMutuModel();
        $kebijakan = $model->find($id);

        if (! $kebijakan) {
            throw PageNotFoundException::forPageNotFound('Dokumen Kebijakan Mutu tidak ditemukan.');
        }

        return view('dokumen/kebijakan_mutu/form', [
            'title' => 'Edit Kebijakan Mutu',
            'pageTitle' => 'Edit Kebijakan Mutu',
            'pageDesc' => 'Form edit data Kebijakan Mutu.',
            'kebijakan' => $kebijakan,
            'action' => base_url('/kebijakan-mutu/update/' . $id),
        ]);
    }

    public function updateKebijakanMutu($id)
    {
        $model = new KebijakanMutuModel();
        $kebijakan = $model->find($id);

        if (! $kebijakan) {
            throw PageNotFoundException::forPageNotFound('Dokumen Kebijakan Mutu tidak ditemukan.');
        }

        $rules = [
            'judul' => 'required',
            'file_pdf' => 'permit_empty|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $fileName = $this->uploadPdf('kebijakan_mutu', 'file_pdf', $kebijakan['file_pdf'] ?? null);

        $model->update($id, [
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to('/kebijakan-mutu')->with('success', 'Dokumen Kebijakan Mutu berhasil diperbarui.');
    }

    public function hapusKebijakanMutu($id)
    {
        $model = new KebijakanMutuModel();
        $kebijakan = $model->find($id);

        if (! $kebijakan) {
            throw PageNotFoundException::forPageNotFound('Dokumen Kebijakan Mutu tidak ditemukan.');
        }

        $this->deletePdf('kebijakan_mutu', $kebijakan['file_pdf'] ?? null);
        $model->delete($id);

        return redirect()->to('/kebijakan-mutu')->with('success', 'Dokumen Kebijakan Mutu berhasil dihapus.');
    }

    public function kebijakanSpmi()
    {
        $model = new KebijakanSpmiModel();
        $perPage = $this->resolvePerPage();

        $status = $this->statusFilterValue(trim((string) $this->request->getGet('status')));
        $keyword = trim((string) $this->request->getGet('keyword'));

        if ($status !== '') {
            $model->where('status_publikasi', $status);
        }

        if ($keyword !== '') {
            $model->groupStart()
                ->like('judul', $keyword)
                ->orLike('nomor_dokumen', $keyword)
                ->groupEnd();
        }

        return view('dokumen/kebijakan_spmi/index', [
            'title' => 'Kebijakan SPMI',
            'pageTitle' => 'Kebijakan SPMI',
            'pageDesc' => 'Kelola dokumen Kebijakan SPMI dalam SIPENA.',
            'kebijakan' => $model->orderBy('id', 'DESC')->paginate($perPage, 'kebijakan_spmi'),
            'pager' => $model->pager,
            'perPage' => $perPage,
            'perPageAktif' => $perPage,
            'opsiPerPage' => [15, 25, 50],
            'statusAktif' => $status,
            'keywordAktif' => $keyword,
            'daftarStatus' => self::STATUS_OPTIONS,
        ]);
    }

    public function tambahKebijakanSpmi()
    {
        return view('dokumen/kebijakan_spmi/form', [
            'title' => 'Tambah Kebijakan SPMI',
            'pageTitle' => 'Tambah Kebijakan SPMI',
            'pageDesc' => 'Form input dokumen Kebijakan SPMI.',
            'kebijakan' => null,
            'action' => base_url('/kebijakan-spmi/simpan'),
        ]);
    }

    public function simpanKebijakanSpmi()
    {
        $rules = [
            'judul' => 'required',
            'file_pdf' => 'permit_empty|uploaded[file_pdf]|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $model = new KebijakanSpmiModel();
        $fileName = $this->uploadPdf('kebijakan_spmi');

        $model->save([
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to('/kebijakan-spmi')->with('success', 'Dokumen Kebijakan SPMI berhasil ditambahkan.');
    }

    public function detailKebijakanSpmi($id)
    {
        $model = new KebijakanSpmiModel();
        $kebijakan = $model->find($id);

        if (! $kebijakan) {
            throw PageNotFoundException::forPageNotFound('Dokumen Kebijakan SPMI tidak ditemukan.');
        }

        return view('dokumen/kebijakan_spmi/detail', [
            'title' => 'Detail Kebijakan SPMI',
            'pageTitle' => 'Detail Kebijakan SPMI',
            'pageDesc' => 'Informasi lengkap dokumen Kebijakan SPMI.',
            'kebijakan' => $kebijakan,
        ]);
    }

    public function editKebijakanSpmi($id)
    {
        $model = new KebijakanSpmiModel();
        $kebijakan = $model->find($id);

        if (! $kebijakan) {
            throw PageNotFoundException::forPageNotFound('Dokumen Kebijakan SPMI tidak ditemukan.');
        }

        return view('dokumen/kebijakan_spmi/form', [
            'title' => 'Edit Kebijakan SPMI',
            'pageTitle' => 'Edit Kebijakan SPMI',
            'pageDesc' => 'Form edit data Kebijakan SPMI.',
            'kebijakan' => $kebijakan,
            'action' => base_url('/kebijakan-spmi/update/' . $id),
        ]);
    }

    public function updateKebijakanSpmi($id)
    {
        $model = new KebijakanSpmiModel();
        $kebijakan = $model->find($id);

        if (! $kebijakan) {
            throw PageNotFoundException::forPageNotFound('Dokumen Kebijakan SPMI tidak ditemukan.');
        }

        $rules = [
            'judul' => 'required',
            'file_pdf' => 'permit_empty|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $fileName = $this->uploadPdf('kebijakan_spmi', 'file_pdf', $kebijakan['file_pdf'] ?? null);

        $model->update($id, [
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to('/kebijakan-spmi')->with('success', 'Dokumen Kebijakan SPMI berhasil diperbarui.');
    }

    public function hapusKebijakanSpmi($id)
    {
        $model = new KebijakanSpmiModel();
        $kebijakan = $model->find($id);

        if (! $kebijakan) {
            throw PageNotFoundException::forPageNotFound('Dokumen Kebijakan SPMI tidak ditemukan.');
        }

        $this->deletePdf('kebijakan_spmi', $kebijakan['file_pdf'] ?? null);
        $model->delete($id);

        return redirect()->to('/kebijakan-spmi')->with('success', 'Dokumen Kebijakan SPMI berhasil dihapus.');
    }

    private function renderPpeppByJenis(string $jenisDokumen, string $pageTitle)
    {
        $model = new PedomanPpeppModel();
        $perPage = $this->resolvePerPage();

        $status = $this->statusFilterValue(trim((string) $this->request->getGet('status')));
        $keyword = trim((string) $this->request->getGet('keyword'));

        $model->where('jenis_dokumen', $jenisDokumen);

        if ($status !== '') {
            $model->where('status_publikasi', $status);
        }

        if ($keyword !== '') {
            $model->groupStart()
                ->like('judul', $keyword)
                ->orLike('nomor_dokumen', $keyword)
                ->groupEnd();
        }

        return view('dokumen/pedoman_ppepp/index', [
            'title' => $pageTitle,
            'pageTitle' => $pageTitle,
            'pageDesc' => 'Kelola dokumen ' . $pageTitle . ' dalam modul Pedoman PPEPP.',
            'dokumen' => $model->orderBy('id', 'DESC')->paginate($perPage, 'pedoman_ppepp'),
            'pager' => $model->pager,
            'perPage' => $perPage,
            'perPageAktif' => $perPage,
            'opsiPerPage' => [15, 25, 50],
            'statusAktif' => $status,
            'keywordAktif' => $keyword,
            'daftarStatus' => self::STATUS_OPTIONS,
            'jenisDokumen' => $jenisDokumen,
        ]);
    }

    public function ppepp()
    {
        return redirect()->to('/pedoman-ppepp/dokumen');
    }

    public function dokumenPpepp()
    {
        return $this->renderPpeppByJenis('Dokumen PPEPP', 'Dokumen PPEPP');
    }

    public function sopPpepp()
    {
        return $this->renderPpeppByJenis('SOP', 'SOP');
    }

    public function formulirPpepp()
    {
        return $this->renderPpeppByJenis('Formulir', 'Formulir');
    }

    public function tambahPedomanPpepp()
    {
        $jenisDokumen = trim((string) $this->request->getGet('jenis'));
        $allowedJenis = ['Dokumen PPEPP', 'SOP', 'Formulir'];
        if (! in_array($jenisDokumen, $allowedJenis, true)) {
            $jenisDokumen = 'Dokumen PPEPP';
        }

        return view('dokumen/pedoman_ppepp/form', [
            'title' => 'Tambah ' . $jenisDokumen,
            'pageTitle' => 'Tambah ' . $jenisDokumen,
            'pageDesc' => 'Form input data ' . $jenisDokumen . '.',
            'dokumen' => ['jenis_dokumen' => $jenisDokumen],
            'jenisOptions' => $allowedJenis,
            'action' => base_url('/pedoman-ppepp/simpan'),
        ]);
    }

    public function simpanPedomanPpepp()
    {
        $rules = [
            'jenis_dokumen' => 'required',
            'judul' => 'required',
            'file_pdf' => 'permit_empty|uploaded[file_pdf]|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Jenis dokumen dan judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $model = new PedomanPpeppModel();
        $fileName = $this->uploadPdf('pedoman_ppepp');
        $jenisDokumen = (string) $this->request->getPost('jenis_dokumen');

        $model->save([
            'jenis_dokumen' => $jenisDokumen,
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to($this->getPpeppRedirectUrl($jenisDokumen))->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function detailPedomanPpepp($id)
    {
        $model = new PedomanPpeppModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        return view('dokumen/pedoman_ppepp/detail', [
            'title' => 'Detail ' . ($dokumen['jenis_dokumen'] ?? 'Pedoman PPEPP'),
            'pageTitle' => 'Detail ' . ($dokumen['jenis_dokumen'] ?? 'Pedoman PPEPP'),
            'pageDesc' => 'Informasi lengkap dokumen.',
            'dokumen' => $dokumen,
        ]);
    }

    public function editPedomanPpepp($id)
    {
        $model = new PedomanPpeppModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        return view('dokumen/pedoman_ppepp/form', [
            'title' => 'Edit ' . ($dokumen['jenis_dokumen'] ?? 'Pedoman PPEPP'),
            'pageTitle' => 'Edit ' . ($dokumen['jenis_dokumen'] ?? 'Pedoman PPEPP'),
            'pageDesc' => 'Form edit data dokumen.',
            'dokumen' => $dokumen,
            'jenisOptions' => ['Dokumen PPEPP', 'SOP', 'Formulir'],
            'action' => base_url('/pedoman-ppepp/update/' . $id),
        ]);
    }

    public function updatePedomanPpepp($id)
    {
        $model = new PedomanPpeppModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        $rules = [
            'jenis_dokumen' => 'required',
            'judul' => 'required',
            'file_pdf' => 'permit_empty|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Jenis dokumen dan judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $fileName = $this->uploadPdf('pedoman_ppepp', 'file_pdf', $dokumen['file_pdf'] ?? null);
        $jenisDokumen = (string) $this->request->getPost('jenis_dokumen');

        $model->update($id, [
            'jenis_dokumen' => $jenisDokumen,
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to($this->getPpeppRedirectUrl($jenisDokumen))->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function hapusPedomanPpepp($id)
    {
        $model = new PedomanPpeppModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        $this->deletePdf('pedoman_ppepp', $dokumen['file_pdf'] ?? null);
        $model->delete($id);

        $jenisDokumen = (string) ($dokumen['jenis_dokumen'] ?? 'Dokumen PPEPP');
        return redirect()->to($this->getPpeppRedirectUrl($jenisDokumen))->with('success', 'Dokumen berhasil dihapus.');
    }

    public function standarMutu()
    {
        $model = new StandarMutuModel();
        $dokumenModel = new DokumenStandarModel();
        $jenisModel = new JenisStandarModel();
        $kategoriModel = new KategoriStandarModel();
        $allowedPerPage = [15, 25, 50];
        $requestedPerPage = (int) $this->request->getGet('per_page');
        $perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 15;

        $status = $this->statusFilterValue(trim((string) $this->request->getGet('status')));
        $keyword = trim((string) $this->request->getGet('keyword'));
        $jenisStandarId = (int) $this->request->getGet('jenis_standar_id');
        $kategoriStandarId = (int) $this->request->getGet('kategori_standar_id');

        if ($status !== '') {
            $model->where('status_publikasi', $status);
        }

        if ($keyword !== '') {
            $model->groupStart()
                ->like('kode_standar', $keyword)
                ->orLike('nama_standar', $keyword)
                ->groupEnd();
        }

        if ($jenisStandarId > 0) {
            $model->where('standar_mutu.jenis_standar_id', $jenisStandarId);
        }

        if ($kategoriStandarId > 0) {
            $model->where('standar_mutu.kategori_standar_id', $kategoriStandarId);
        }

        $model->select('standar_mutu.*, master_jenis_standar.nama_jenis, master_kategori_standar.nama_kategori')
            ->join('master_jenis_standar', 'master_jenis_standar.id = standar_mutu.jenis_standar_id', 'left')
            ->join('master_kategori_standar', 'master_kategori_standar.id = standar_mutu.kategori_standar_id', 'left');

        $jumlahDokumenPerStandar = [];
        $dokumenUtamaPerStandar = [];
        $rekapDokumen = $dokumenModel
            ->select('standar_mutu_id, COUNT(*) AS total_dokumen')
            ->groupBy('standar_mutu_id')
            ->findAll();

        foreach ($rekapDokumen as $row) {
            $standarMutuId = (int) ($row['standar_mutu_id'] ?? 0);
            $jumlahDokumenPerStandar[$standarMutuId] = (int) ($row['total_dokumen'] ?? 0);
        }

        $dokumenTerbaru = $dokumenModel
            ->select('id, standar_mutu_id')
            ->orderBy('id', 'DESC')
            ->findAll();

        foreach ($dokumenTerbaru as $row) {
            $standarMutuId = (int) ($row['standar_mutu_id'] ?? 0);
            if ($standarMutuId > 0 && ! isset($dokumenUtamaPerStandar[$standarMutuId])) {
                $dokumenUtamaPerStandar[$standarMutuId] = (int) ($row['id'] ?? 0);
            }
        }

        return view('dokumen/standar_mutu/index', [
            'title' => 'Standar Mutu',
            'pageTitle' => 'Standar Mutu',
            'pageDesc' => 'Kelola daftar standar mutu sebagai induk standar dalam SIPENA.',
            'standar' => $model->orderBy('kode_standar', 'ASC')->paginate($perPage, 'standar'),
            'jumlahDokumenPerStandar' => $jumlahDokumenPerStandar,
            'dokumenUtamaPerStandar' => $dokumenUtamaPerStandar,
            'pager' => $model->pager,
            'perPage' => $perPage,
            'statusAktif' => $status,
            'keywordAktif' => $keyword,
            'jenisStandarAktif' => $jenisStandarId,
            'kategoriStandarAktif' => $kategoriStandarId,
            'perPageAktif' => $perPage,
            'opsiPerPage' => $allowedPerPage,
            'daftarStatus' => self::STATUS_OPTIONS,
            'opsiJenisAktif' => $jenisModel->where('is_aktif', 1)->orderBy('nama_jenis', 'ASC')->findAll(),
            'opsiKategoriAktif' => $kategoriModel->where('is_aktif', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
        ]);
    }

    public function tambahStandarMutu()
    {
        $jenisModel = new JenisStandarModel();
        $kategoriModel = new KategoriStandarModel();
        $userModel = new UserModel();

        $daftarJenis = $jenisModel->where('is_aktif', 1)->orderBy('nama_jenis', 'ASC')->findAll();
        $nextKodeStandarByJenis = [];
        foreach ($daftarJenis as $jenis) {
            $nextKodeStandarByJenis[(int) ($jenis['id'] ?? 0)] = $this->generateNextStandarMutuKode((int) ($jenis['id'] ?? 0), (string) ($jenis['nama_jenis'] ?? ''));
        }

        return view('dokumen/standar_mutu/form', [
            'title' => 'Tambah Standar Mutu',
            'pageTitle' => 'Tambah Standar Mutu',
            'pageDesc' => 'Form input data standar mutu.',
            'standar' => null,
            'action' => base_url('/standar-mutu/simpan'),
            'daftarJenis' => $daftarJenis,
            'daftarKategori' => $kategoriModel->where('is_aktif', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
            'daftarUserAktif' => $userModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll(),
            'penugasanAktif' => [],
            'prosesPenandatangan' => self::PROSES_PENANDATANGAN,
            'nextKodeStandarByJenis' => $nextKodeStandarByJenis,
        ]);
    }

    public function simpanStandarMutu()
    {
        $rules = [
            'nama_standar' => 'required',
            'jenis_standar_id' => 'required|integer',
            'kategori_standar_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Kode, nama, jenis, dan kategori standar wajib diisi.');
        }

        $statusPublikasi = $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft';
        if ($statusPublikasi === 'publish') {
            $errorPenugasan = $this->validasiPenugasanPenandatangan();
            if ($errorPenugasan !== null) {
                return redirect()->back()->withInput()->with('error', $errorPenugasan);
            }
        }

        $jenisStandarId = (int) $this->request->getPost('jenis_standar_id');
        $kodeStandar = trim((string) $this->request->getPost('kode_standar'));
        if ($kodeStandar === '') {
            $jenisStandar = (new JenisStandarModel())->find($jenisStandarId);
            $kodeStandar = $this->generateNextStandarMutuKode($jenisStandarId, (string) ($jenisStandar['nama_jenis'] ?? ''));
        }

        if ($kodeStandar === '') {
            return redirect()->back()->withInput()->with('error', 'Kode standar tidak valid. Pilih jenis standar terlebih dahulu.');
        }

        $model = new StandarMutuModel();
        $model->save([
            'kode_standar' => $kodeStandar,
            'nama_standar' => $this->request->getPost('nama_standar'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status_publikasi' => $statusPublikasi,
            'jenis_standar_id' => $jenisStandarId,
            'kategori_standar_id' => (int) $this->request->getPost('kategori_standar_id'),
        ]);

        $standarIdBaru = (int) $model->getInsertID();
        if ($standarIdBaru > 0) {
            $this->simpanPenugasanPenandatanganStandar($standarIdBaru);
        }

        return redirect()->to('/standar-mutu')->with('success', 'Data standar mutu berhasil ditambahkan.');
    }

    public function detailStandarMutu($id)
    {
        $model = new StandarMutuModel();
        $standar = $model
            ->select('standar_mutu.*, master_jenis_standar.nama_jenis, master_kategori_standar.nama_kategori')
            ->join('master_jenis_standar', 'master_jenis_standar.id = standar_mutu.jenis_standar_id', 'left')
            ->join('master_kategori_standar', 'master_kategori_standar.id = standar_mutu.kategori_standar_id', 'left')
            ->find($id);

        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Data standar mutu tidak ditemukan.');
        }

        return view('dokumen/standar_mutu/detail', [
            'title' => 'Detail Standar Mutu',
            'pageTitle' => 'Detail Standar Mutu',
            'pageDesc' => 'Informasi lengkap standar mutu.',
            'standar' => $standar,
        ]);
    }

    public function editStandarMutu($id)
    {
        $model = new StandarMutuModel();
        $standar = $model->find($id);
        $jenisModel = new JenisStandarModel();
        $kategoriModel = new KategoriStandarModel();
        $userModel = new UserModel();

        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Data standar mutu tidak ditemukan.');
        }

        return view('dokumen/standar_mutu/form', [
            'title' => 'Edit Standar Mutu',
            'pageTitle' => 'Edit Standar Mutu',
            'pageDesc' => 'Form edit data standar mutu.',
            'standar' => $standar,
            'action' => base_url('/standar-mutu/update/' . $id),
            'daftarJenis' => $jenisModel->where('is_aktif', 1)->orWhere('id', (int) ($standar['jenis_standar_id'] ?? 0))->orderBy('nama_jenis', 'ASC')->findAll(),
            'daftarKategori' => $kategoriModel->where('is_aktif', 1)->orWhere('id', (int) ($standar['kategori_standar_id'] ?? 0))->orderBy('nama_kategori', 'ASC')->findAll(),
            'daftarUserAktif' => $userModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll(),
            'penugasanAktif' => $this->getPenugasanStandarUntukForm((int) $id),
            'prosesPenandatangan' => self::PROSES_PENANDATANGAN,
        ]);
    }

    public function updateStandarMutu($id)
    {
        $rules = [
            'kode_standar' => 'required',
            'nama_standar' => 'required',
            'jenis_standar_id' => 'required|integer',
            'kategori_standar_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Kode, nama, jenis, dan kategori standar wajib diisi.');
        }

        $statusPublikasi = $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft';
        if ($statusPublikasi === 'publish') {
            $errorPenugasan = $this->validasiPenugasanPenandatangan();
            if ($errorPenugasan !== null) {
                return redirect()->back()->withInput()->with('error', $errorPenugasan);
            }
        }

        $model = new StandarMutuModel();
        $standar = $model->find($id);

        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Data standar mutu tidak ditemukan.');
        }

        $model->update($id, [
            'kode_standar' => $standar['kode_standar'],
            'nama_standar' => $this->request->getPost('nama_standar'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status_publikasi' => $statusPublikasi,
            'jenis_standar_id' => (int) $this->request->getPost('jenis_standar_id'),
            'kategori_standar_id' => (int) $this->request->getPost('kategori_standar_id'),
        ]);

        $this->simpanPenugasanPenandatanganStandar((int) $id);
        $this->sinkronStatusDokumenStandar((int) $id, $statusPublikasi);

        return redirect()->to('/standar-mutu')->with('success', 'Data standar mutu berhasil diperbarui.');
    }

    public function hapusStandarMutu($id)
    {
        $model = new StandarMutuModel();
        $standar = $model->find($id);

        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Data standar mutu tidak ditemukan.');
        }

        $model->delete($id);

        return redirect()->to('/standar-mutu')->with('success', 'Data standar mutu berhasil dihapus.');
    }

    public function dokumenStandar($standarId)
    {
        return redirect()->to('/standar-mutu');
    }

    public function tambahDokumenStandar($standarId)
    {
        $standarModel = new StandarMutuModel();
        $standar = $standarModel->find($standarId);

        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        return view('dokumen/dokumen_standar/form', [
            'title' => 'Tambah Dokumen Standar',
            'pageTitle' => 'Tambah Dokumen Standar',
            'pageDesc' => 'Form input dokumen standar.',
            'standar' => $standar,
            'dokumen' => null,
            'action' => base_url('/standar-mutu/' . $standarId . '/dokumen/simpan'),
        ]);
    }

    public function simpanDokumenStandar($standarId)
    {
        $standarModel = new StandarMutuModel();
        $standar = $standarModel->find($standarId);

        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        $dokumenModel = new DokumenStandarModel();
        $payload = $this->payloadDokumenStandar($standar);
        if (($payload['status_publikasi'] ?? 'draft') === 'publish') {
            $errorPublish = $this->validasiPenandatanganTersimpanUntukPublish((int) $standarId);
            if ($errorPublish !== null) {
                return redirect()->back()->withInput()->with('error', $errorPublish);
            }
        }
        $payload['standar_mutu_id'] = $standarId;
        $dokumenModel->save($payload);

        return redirect()->to('/standar-mutu')->with('success', 'Dokumen standar berhasil ditambahkan.');
    }

    public function detailDokumenStandar($id)
    {
        $dokumenModel = new DokumenStandarModel();
        $standarModel = new StandarMutuModel();

        $dokumen = $dokumenModel->find($id);
        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen standar tidak ditemukan.');
        }

        $standar = $standarModel->find($dokumen['standar_mutu_id'] ?? 0);
        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        $profil = (new ProfilInstitusiModel())->first() ?? [];
        $penandatanganProses = $this->getPenandatanganProsesByStandar((int) ($standar['id'] ?? 0));

        $dokumen['indikator_ketercapaian'] = $this->renderIndikatorKetercapaian($dokumen['indikator_ketercapaian'] ?? null);

        return view('dokumen/dokumen_standar/detail', [
            'title' => 'Detail Dokumen Standar',
            'pageTitle' => 'Detail Dokumen Standar',
            'pageDesc' => 'Informasi lengkap dokumen standar.',
            'dokumen' => $dokumen,
            'standar' => $standar,
            'profil' => $profil,
            'penandatanganProses' => $penandatanganProses,
        ]);
    }

    public function riwayatDokumenStandar($id)
    {
        $dokumenModel = new DokumenStandarModel();
        $standarModel = new StandarMutuModel();
        $historyModel = new RiwayatPerubahanDokumenStandarModel();

        $dokumen = $dokumenModel->find($id);
        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen standar tidak ditemukan.');
        }

        $standar = $standarModel->find((int) ($dokumen['standar_mutu_id'] ?? 0));
        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        $riwayat = $historyModel
            ->select('riwayat_perubahan_dokumen_standar.*, users.nama AS nama_pengubah')
            ->join('users', 'users.id = riwayat_perubahan_dokumen_standar.updated_by', 'left')
            ->where('riwayat_perubahan_dokumen_standar.dokumen_standar_id', (int) $id)
            ->orderBy('riwayat_perubahan_dokumen_standar.id', 'DESC')
            ->findAll();

        foreach ($riwayat as &$item) {
            $changed = json_decode((string) ($item['changed_fields'] ?? ''), true);
            $item['changed_fields_list'] = is_array($changed) ? $changed : [];
            $item['indikator_ketercapaian'] = $this->renderIndikatorKetercapaian($item['indikator_ketercapaian'] ?? null);
        }
        unset($item);

        return view('dokumen/dokumen_standar/riwayat', [
            'title' => 'Riwayat Perubahan Dokumen Standar',
            'pageTitle' => 'Riwayat Perubahan Butir Standar',
            'pageDesc' => 'Riwayat pembaruan konten dokumen standar.',
            'dokumen' => $dokumen,
            'standar' => $standar,
            'riwayat' => $riwayat,
        ]);
    }

    public function editDokumenStandar($id)
    {
        $dokumenModel = new DokumenStandarModel();
        $standarModel = new StandarMutuModel();

        $dokumen = $dokumenModel->find($id);
        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen standar tidak ditemukan.');
        }

        $standar = $standarModel->find($dokumen['standar_mutu_id'] ?? 0);
        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        return view('dokumen/dokumen_standar/form', [
            'title' => 'Edit Dokumen Standar',
            'pageTitle' => 'Edit Dokumen Standar',
            'pageDesc' => 'Form edit dokumen standar.',
            'standar' => $standar,
            'dokumen' => $dokumen,
            'action' => base_url('/dokumen-standar/update/' . $id),
        ]);
    }

    public function updateDokumenStandar($id)
    {
        $dokumenModel = new DokumenStandarModel();
        $standarModel = new StandarMutuModel();

        $dokumen = $dokumenModel->find($id);
        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen standar tidak ditemukan.');
        }

        $standar = $standarModel->find((int) ($dokumen['standar_mutu_id'] ?? 0));
        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        $payload = $this->payloadDokumenStandar($standar);
        if (($payload['status_publikasi'] ?? 'draft') === 'publish') {
            $errorPublish = $this->validasiPenandatanganTersimpanUntukPublish((int) ($standar['id'] ?? 0));
            if ($errorPublish !== null) {
                return redirect()->back()->withInput()->with('error', $errorPublish);
            }
        }
        $changedFields = $this->fieldsDokumenStandarYangBerubah($dokumen, $payload);

        $this->simpanRiwayatDokumenStandar($dokumen, $changedFields);

        $dokumenModel->update($id, $payload);

        return redirect()->to('/standar-mutu')->with('success', 'Dokumen standar berhasil diperbarui.');
    }

    public function hapusDokumenStandar($id)
    {
        $dokumenModel = new DokumenStandarModel();

        $dokumen = $dokumenModel->find($id);
        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen standar tidak ditemukan.');
        }

        $dokumenModel->delete($id);

        return redirect()->to('/standar-mutu')->with('success', 'Dokumen standar berhasil dihapus.');
    }

    public function cetakDokumenStandar($id)
    {
        $dokumenModel = new DokumenStandarModel();
        $standarModel = new StandarMutuModel();

        $dokumen = $dokumenModel->find($id);
        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen standar tidak ditemukan.');
        }

        $standar = $standarModel->find($dokumen['standar_mutu_id'] ?? 0);
        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        $profil = (new ProfilInstitusiModel())->first() ?? [];
        $penandatanganProses = $this->getPenandatanganProsesByStandar((int) ($standar['id'] ?? 0));

        return view('dokumen/dokumen_standar/cetak', [
            'title' => 'Cetak Dokumen Standar',
            'dokumen' => $dokumen,
            'standar' => $standar,
            'profil' => $profil,
            'penandatanganProses' => $penandatanganProses,
            'mode_pdf' => false,
        ]);
    }

    public function pdfDokumenStandar($id)
    {
        $dokumenModel = new DokumenStandarModel();
        $standarModel = new StandarMutuModel();

        $dokumen = $dokumenModel->find($id);
        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen standar tidak ditemukan.');
        }

        $standar = $standarModel->find($dokumen['standar_mutu_id'] ?? 0);
        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        $profil = (new ProfilInstitusiModel())->first() ?? [];
        $penandatanganProses = $this->getPenandatanganProsesByStandar((int) ($standar['id'] ?? 0));

        $html = view('dokumen/dokumen_standar/cetak', [
            'dokumen' => $dokumen,
            'standar' => $standar,
            'profil' => $profil,
            'penandatanganProses' => $penandatanganProses,
            'mode_pdf' => true,
        ]);

        $pdfGenerator = new \App\Libraries\PdfGenerator();
        $dompdf = $pdfGenerator->generate($html, 'A4', 'portrait');

        $safeKode = preg_replace('/[^A-Za-z0-9\\-_]/', '-', (string) ($dokumen['kode_dokumen'] ?? 'dokumen-standar'));
        $filename = 'dokumen-standar-' . trim((string) $safeKode, '-') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function ami()
    {
        $model = new AuditMutuInternalModel();
        $perPage = $this->resolvePerPage();

        $status = $this->statusFilterValue(trim((string) $this->request->getGet('status')));
        $keyword = trim((string) $this->request->getGet('keyword'));

        if ($status !== '') {
            $model->where('status_publikasi', $status);
        }

        if ($keyword !== '') {
            $model->groupStart()
                ->like('judul', $keyword)
                ->orLike('nomor_dokumen', $keyword)
                ->groupEnd();
        }

        return view('dokumen/audit_mutu_internal/index', [
            'title' => 'Audit Mutu Internal',
            'pageTitle' => 'Audit Mutu Internal',
            'pageDesc' => 'Kelola dokumen Audit Mutu Internal dalam SIPENA.',
            'dokumen' => $model->orderBy('id', 'DESC')->paginate($perPage, 'audit_mutu_internal'),
            'pager' => $model->pager,
            'perPage' => $perPage,
            'perPageAktif' => $perPage,
            'opsiPerPage' => [15, 25, 50],
            'statusAktif' => $status,
            'keywordAktif' => $keyword,
            'daftarStatus' => self::STATUS_OPTIONS,
        ]);
    }

    public function tambahAmi()
    {
        return view('dokumen/audit_mutu_internal/form', [
            'title' => 'Tambah Audit Mutu Internal',
            'pageTitle' => 'Tambah Audit Mutu Internal',
            'pageDesc' => 'Form input dokumen Audit Mutu Internal.',
            'dokumen' => null,
            'action' => base_url('/audit-mutu-internal/simpan'),
        ]);
    }

    public function simpanAmi()
    {
        $rules = [
            'judul' => 'required',
            'file_pdf' => 'permit_empty|uploaded[file_pdf]|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $model = new AuditMutuInternalModel();
        $fileName = $this->uploadPdf('audit_mutu_internal');

        $model->save([
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to('/audit-mutu-internal')->with('success', 'Dokumen Audit Mutu Internal berhasil ditambahkan.');
    }

    public function detailAmi($id)
    {
        $model = new AuditMutuInternalModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen Audit Mutu Internal tidak ditemukan.');
        }

        return view('dokumen/audit_mutu_internal/detail', [
            'title' => 'Detail Audit Mutu Internal',
            'pageTitle' => 'Detail Audit Mutu Internal',
            'pageDesc' => 'Informasi lengkap dokumen Audit Mutu Internal.',
            'dokumen' => $dokumen,
        ]);
    }

    public function editAmi($id)
    {
        $model = new AuditMutuInternalModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen Audit Mutu Internal tidak ditemukan.');
        }

        return view('dokumen/audit_mutu_internal/form', [
            'title' => 'Edit Audit Mutu Internal',
            'pageTitle' => 'Edit Audit Mutu Internal',
            'pageDesc' => 'Form edit dokumen Audit Mutu Internal.',
            'dokumen' => $dokumen,
            'action' => base_url('/audit-mutu-internal/update/' . $id),
        ]);
    }

    public function updateAmi($id)
    {
        $model = new AuditMutuInternalModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen Audit Mutu Internal tidak ditemukan.');
        }

        $rules = [
            'judul' => 'required',
            'file_pdf' => 'permit_empty|ext_in[file_pdf,pdf]|mime_in[file_pdf,application/pdf]|max_size[file_pdf,5120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul wajib diisi. File harus PDF maksimal 5 MB.');
        }

        $fileName = $this->uploadPdf('audit_mutu_internal', 'file_pdf', $dokumen['file_pdf'] ?? null);

        $model->update($id, [
            'judul' => $this->request->getPost('judul'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tahun' => $this->request->getPost('tahun'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_pdf' => $fileName,
            'status_publikasi' => $this->statusFilterValue((string) $this->request->getPost('status_publikasi')) ?: 'draft',
        ]);

        return redirect()->to('/audit-mutu-internal')->with('success', 'Dokumen Audit Mutu Internal berhasil diperbarui.');
    }

    public function hapusAmi($id)
    {
        $model = new AuditMutuInternalModel();
        $dokumen = $model->find($id);

        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen Audit Mutu Internal tidak ditemukan.');
        }

        $this->deletePdf('audit_mutu_internal', $dokumen['file_pdf'] ?? null);
        $model->delete($id);

        return redirect()->to('/audit-mutu-internal')->with('success', 'Dokumen Audit Mutu Internal berhasil dihapus.');
    }
}

