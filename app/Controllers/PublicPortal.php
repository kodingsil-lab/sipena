<?php

namespace App\Controllers;

use App\Models\AuditMutuInternalModel;
use App\Models\DokumenStandarModel;
use App\Models\JenisStandarModel;
use App\Models\KategoriStandarModel;
use App\Models\KebijakanMutuModel;
use App\Models\KebijakanSpmiModel;
use App\Models\PedomanPpeppModel;
use App\Models\PenugasanPenandatanganStandarModel;
use App\Models\PeraturanModel;
use App\Models\ProfilInstitusiModel;
use App\Models\StandarMutuModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PublicPortal extends BaseController
{
    private const STATUS_TERBIT = 'publish';

    private const PROSES_PENANDATANGAN = [
        'perumusan' => 'Perumusan',
        'pemeriksaan' => 'Pemeriksaan',
        'persetujuan' => 'Persetujuan',
        'pengesahan' => 'Pengesahan',
        'pengendalian' => 'Pengendalian',
    ];

    private function publicAppName(): string
    {
        $name = trim((string) app_setting('nama_aplikasi', 'SIPENA'));
        return $name !== '' ? $name : 'SIPENA';
    }

    private function isStatusTerbit(?string $status): bool
    {
        return strtolower(trim((string) $status)) === self::STATUS_TERBIT;
    }

    private function filterStatusTerbit(array $rows): array
    {
        return array_values(array_filter($rows, fn(array $row): bool => $this->isStatusTerbit($row['status_publikasi'] ?? null)));
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

        $userIds = array_values(array_unique(array_filter(array_map(
            static fn(array $row): int => (int) ($row['user_id'] ?? 0),
            $assigned
        ))));
        $usersById = [];
        if ($userIds !== []) {
            foreach ($userModel->whereIn('id', $userIds)->findAll() as $user) {
                $usersById[(int) ($user['id'] ?? 0)] = $user;
            }
        }

        $result = [];
        foreach (self::PROSES_PENANDATANGAN as $prosesKey => $prosesLabel) {
            $assignedItem = $assigned[$prosesKey] ?? [];
            $userId = (int) ($assignedItem['user_id'] ?? 0);
            if ($userId <= 0 || ! isset($usersById[$userId])) {
                continue;
            }

            $user = $usersById[$userId];
            $jabatan = trim((string) ($user['jabatan'] ?? ''));
            $result[$prosesLabel] = [
                'nama' => $user['nama'] ?? '-',
                'jabatan' => $jabatan !== '' ? $jabatan : $this->roleLabel($user['role'] ?? ''),
                'ttd_digital' => $user['ttd_digital'] ?? null,
                'tanggal_ttd' => $assignedItem['tanggal_ttd'] ?? null,
            ];
        }

        return $result;
    }

    private function baseLayoutData(string $activeMenu): array
    {
        $dokumenModel = new DokumenStandarModel();
        $standarModel = new StandarMutuModel();
        $pedomanModel = new PedomanPpeppModel();

        return [
            'publicMode' => true,
            'publicActiveMenu' => $activeMenu,
            'totalStandar' => $standarModel->where('status_publikasi', self::STATUS_TERBIT)->countAllResults(),
            'totalDokumen' => $dokumenModel->where('status_publikasi', self::STATUS_TERBIT)->countAllResults(),
            'totalPublishDokumen' => $dokumenModel->where('status_publikasi', self::STATUS_TERBIT)->countAllResults(),
            'totalSop' => $pedomanModel
                ->where('status_publikasi', self::STATUS_TERBIT)
                ->where('jenis_dokumen', 'SOP')
                ->countAllResults(),
            'totalFormulir' => $pedomanModel
                ->where('status_publikasi', self::STATUS_TERBIT)
                ->where('jenis_dokumen', 'Formulir')
                ->countAllResults(),
            'totalAmi' => (new AuditMutuInternalModel())->where('status_publikasi', self::STATUS_TERBIT)->countAllResults(),
            'profilInstitusi' => (new ProfilInstitusiModel())->first() ?? [],
        ];
    }

    private function applyKeyword(object $builder, string $keyword, array $fields): void
    {
        if ($keyword === '' || $fields === []) {
            return;
        }

        $builder->groupStart();
        foreach ($fields as $index => $field) {
            if ($index === 0) {
                $builder->like($field, $keyword);
            } else {
                $builder->orLike($field, $keyword);
            }
        }
        $builder->groupEnd();
    }

    private function latestStamp(array $row): string
    {
        $updatedAt = $row['updated_at'] ?? $row['created_at'] ?? null;
        if (! is_string($updatedAt) || trim($updatedAt) === '') {
            return '-';
        }

        $ts = strtotime($updatedAt);
        return $ts ? date('d M Y H:i', $ts) : '-';
    }

    private function renderList(string $activeMenu, string $pageTitle, string $pageDesc, array $rows, $pager = null, int $perPage = 15): string
    {
        $appName = $this->publicAppName();

        return view('public/list', array_merge($this->baseLayoutData($activeMenu), [
            'title' => $pageTitle . ' - ' . $appName . ' Publik',
            'pageTitle' => $pageTitle,
            'pageDesc' => $pageDesc,
            'rows' => $rows,
            'keyword' => trim((string) $this->request->getGet('keyword')),
            'pager' => $pager,
            'perPage' => $perPage,
        ]));
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $jenisStandarId = (int) $this->request->getGet('jenis_standar_id');
        $kategoriStandarId = (int) $this->request->getGet('kategori_standar_id');

        $builder = (new DokumenStandarModel())
            ->select('dokumen_standar.id, dokumen_standar.kode_dokumen, dokumen_standar.status_publikasi, dokumen_standar.updated_at, dokumen_standar.created_at, standar_mutu.nama_standar, master_jenis_standar.nama_jenis, master_kategori_standar.nama_kategori')
            ->join('standar_mutu', 'standar_mutu.id = dokumen_standar.standar_mutu_id', 'left')
            ->join('master_jenis_standar', 'master_jenis_standar.id = standar_mutu.jenis_standar_id', 'left')
            ->join('master_kategori_standar', 'master_kategori_standar.id = standar_mutu.kategori_standar_id', 'left')
            ->where('dokumen_standar.status_publikasi', self::STATUS_TERBIT);

        $this->applyKeyword($builder, $keyword, ['dokumen_standar.kode_dokumen', 'standar_mutu.nama_standar']);
        if ($jenisStandarId > 0) {
            $builder->where('standar_mutu.jenis_standar_id', $jenisStandarId);
        }
        if ($kategoriStandarId > 0) {
            $builder->where('standar_mutu.kategori_standar_id', $kategoriStandarId);
        }

        $standarList = array_map(function (array $row): array {
            $updatedAt = $row['updated_at'] ?? $row['created_at'] ?? null;
            return [
                'nomor' => trim((string) ($row['kode_dokumen'] ?? '')) ?: '-',
                'judul' => trim((string) ($row['nama_standar'] ?? '-')) ?: '-',
                'jenis' => trim((string) ($row['nama_jenis'] ?? '')) ?: '-',
                'kategori' => trim((string) ($row['nama_kategori'] ?? '')) ?: '-',
                'updated_label' => (is_string($updatedAt) && strtotime($updatedAt)) ? date('d M Y H:i', strtotime($updatedAt)) : '-',
                'action_url' => base_url('/publik/standar-mutu/detail/' . (int) ($row['id'] ?? 0)),
            ];
        }, $this->filterStatusTerbit($builder->orderBy('dokumen_standar.updated_at', 'DESC')->findAll(15)));

        $opsiJenis = (new JenisStandarModel())->where('is_aktif', 1)->orderBy('nama_jenis', 'ASC')->findAll();
        $opsiKategori = (new KategoriStandarModel())->where('is_aktif', 1)->orderBy('nama_kategori', 'ASC')->findAll();
        $appName = $this->publicAppName();

        return view('public/dashboard', array_merge($this->baseLayoutData('dashboard'), [
            'title' => $appName . ' - Sistem Informasi Penjaminan Mutu Internal',
            'standarList' => $standarList,
            'keyword' => $keyword,
            'jenisStandarAktif' => $jenisStandarId,
            'kategoriStandarAktif' => $kategoriStandarId,
            'opsiJenis' => $opsiJenis,
            'opsiKategori' => $opsiKategori,
        ]));
    }

    public function peraturan(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $model = new PeraturanModel();
        $builder = $model->select('id, nomor_dokumen, judul, file_pdf, status_publikasi, updated_at, created_at')->where('status_publikasi', self::STATUS_TERBIT);
        $this->applyKeyword($builder, $keyword, ['nomor_dokumen', 'judul', 'kategori']);
        $perPage = 15;
        $items = $builder->orderBy('updated_at', 'DESC')->paginate($perPage, 'publik_list');

        $rows = array_map(function (array $item): array {
            return [
                'nomor' => trim((string) ($item['nomor_dokumen'] ?? '')) ?: '-',
                'judul' => trim((string) ($item['judul'] ?? '')) ?: '-',
                'updated_label' => $this->latestStamp($item),
                'action_url' => ! empty($item['file_pdf']) ? base_url('uploads/peraturan/' . rawurlencode((string) $item['file_pdf'])) : null,
                'action_label' => 'Unduh',
                'action_icon' => 'bi-download',
            ];
        }, $this->filterStatusTerbit($items));

        return $this->renderList('peraturan', 'Peraturan Terbit', 'Daftar dokumen peraturan yang telah dipublikasikan.', $rows, $model->pager, $perPage);
    }

    public function kebijakanMutu(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $model = new KebijakanMutuModel();
        $builder = $model->select('id, nomor_dokumen, judul, file_pdf, status_publikasi, updated_at, created_at')->where('status_publikasi', self::STATUS_TERBIT);
        $this->applyKeyword($builder, $keyword, ['nomor_dokumen', 'judul']);
        $perPage = 15;
        $items = $builder->orderBy('updated_at', 'DESC')->paginate($perPage, 'publik_list');

        $rows = array_map(function (array $item): array {
            return [
                'nomor' => trim((string) ($item['nomor_dokumen'] ?? '')) ?: '-',
                'judul' => trim((string) ($item['judul'] ?? '')) ?: '-',
                'updated_label' => $this->latestStamp($item),
                'action_url' => ! empty($item['file_pdf']) ? base_url('uploads/kebijakan_mutu/' . rawurlencode((string) $item['file_pdf'])) : null,
                'action_label' => 'Unduh',
                'action_icon' => 'bi-download',
            ];
        }, $this->filterStatusTerbit($items));

        return $this->renderList('kebijakan-mutu', 'Kebijakan Mutu Terbit', 'Daftar kebijakan mutu yang telah dipublikasikan.', $rows, $model->pager, $perPage);
    }

    public function kebijakanSpmi(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $model = new KebijakanSpmiModel();
        $builder = $model->select('id, nomor_dokumen, judul, file_pdf, status_publikasi, updated_at, created_at')->where('status_publikasi', self::STATUS_TERBIT);
        $this->applyKeyword($builder, $keyword, ['nomor_dokumen', 'judul']);
        $perPage = 15;
        $items = $builder->orderBy('updated_at', 'DESC')->paginate($perPage, 'publik_list');

        $rows = array_map(function (array $item): array {
            return [
                'nomor' => trim((string) ($item['nomor_dokumen'] ?? '')) ?: '-',
                'judul' => trim((string) ($item['judul'] ?? '')) ?: '-',
                'updated_label' => $this->latestStamp($item),
                'action_url' => ! empty($item['file_pdf']) ? base_url('uploads/kebijakan_spmi/' . rawurlencode((string) $item['file_pdf'])) : null,
                'action_label' => 'Unduh',
                'action_icon' => 'bi-download',
            ];
        }, $this->filterStatusTerbit($items));

        return $this->renderList('kebijakan-spmi', 'Kebijakan SPMI Terbit', 'Daftar kebijakan SPMI yang telah dipublikasikan.', $rows, $model->pager, $perPage);
    }

    public function pedomanPpepp(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $jenisDokumen = trim((string) $this->request->getGet('jenis_dokumen'));
        $jenisAllowed = ['Dokumen', 'SOP', 'Formulir'];
        if (! in_array($jenisDokumen, $jenisAllowed, true)) {
            $jenisDokumen = '';
        }

        $model = new PedomanPpeppModel();
        $builder = $model->select('id, nomor_dokumen, judul, jenis_dokumen, file_pdf, status_publikasi, updated_at, created_at')->where('status_publikasi', self::STATUS_TERBIT);
        $this->applyKeyword($builder, $keyword, ['nomor_dokumen', 'judul', 'jenis_dokumen']);
        if ($jenisDokumen !== '') {
            $builder->where('jenis_dokumen', $jenisDokumen);
        }
        $perPage = 15;
        $items = $builder->orderBy('updated_at', 'DESC')->paginate($perPage, 'publik_list');

        $rows = array_map(function (array $item): array {
            $jenis = trim((string) ($item['jenis_dokumen'] ?? ''));
            $judul = trim((string) ($item['judul'] ?? '')) ?: '-';
            if ($jenis !== '') {
                $judul = $judul . ' (' . $jenis . ')';
            }

            return [
                'nomor' => trim((string) ($item['nomor_dokumen'] ?? '')) ?: '-',
                'judul' => $judul,
                'updated_label' => $this->latestStamp($item),
                'action_url' => ! empty($item['file_pdf']) ? base_url('uploads/pedoman_ppepp/' . rawurlencode((string) $item['file_pdf'])) : null,
                'action_label' => 'Unduh',
                'action_icon' => 'bi-download',
            ];
        }, $this->filterStatusTerbit($items));

        $pageTitle = 'Pedoman PPEPP Terbit';
        $pageDesc = 'Daftar dokumen pedoman PPEPP yang telah dipublikasikan.';
        if ($jenisDokumen === 'Dokumen') {
            $pageTitle = 'Dokumen PPEPP Terbit';
            $pageDesc = 'Daftar dokumen PPEPP yang telah dipublikasikan.';
        } elseif ($jenisDokumen === 'SOP') {
            $pageTitle = 'SOP PPEPP Terbit';
            $pageDesc = 'Daftar SOP PPEPP yang telah dipublikasikan.';
        } elseif ($jenisDokumen === 'Formulir') {
            $pageTitle = 'Formulir PPEPP Terbit';
            $pageDesc = 'Daftar formulir PPEPP yang telah dipublikasikan.';
        }

        return $this->renderList('pedoman-ppepp', $pageTitle, $pageDesc, $rows, $model->pager, $perPage);
    }

    public function auditMutuInternal(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $model = new AuditMutuInternalModel();
        $builder = $model->select('id, nomor_dokumen, judul, file_pdf, status_publikasi, updated_at, created_at')->where('status_publikasi', self::STATUS_TERBIT);
        $this->applyKeyword($builder, $keyword, ['nomor_dokumen', 'judul']);
        $perPage = 15;
        $items = $builder->orderBy('updated_at', 'DESC')->paginate($perPage, 'publik_list');

        $rows = array_map(function (array $item): array {
            return [
                'nomor' => trim((string) ($item['nomor_dokumen'] ?? '')) ?: '-',
                'judul' => trim((string) ($item['judul'] ?? '')) ?: '-',
                'updated_label' => $this->latestStamp($item),
                'action_url' => ! empty($item['file_pdf']) ? base_url('uploads/audit_mutu_internal/' . rawurlencode((string) $item['file_pdf'])) : null,
                'action_label' => 'Unduh',
                'action_icon' => 'bi-download',
            ];
        }, $this->filterStatusTerbit($items));

        return $this->renderList('audit-mutu-internal', 'Audit Mutu Internal Terbit', 'Daftar dokumen audit mutu internal yang telah dipublikasikan.', $rows, $model->pager, $perPage);
    }

    public function standarMutu(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $jenisStandarId = (int) $this->request->getGet('jenis_standar_id');
        $kategoriStandarId = (int) $this->request->getGet('kategori_standar_id');

        $model = new DokumenStandarModel();
        $builder = $model
            ->select('dokumen_standar.id, dokumen_standar.kode_dokumen, dokumen_standar.status_publikasi, dokumen_standar.updated_at, dokumen_standar.created_at, standar_mutu.nama_standar, master_jenis_standar.nama_jenis, master_kategori_standar.nama_kategori')
            ->join('standar_mutu', 'standar_mutu.id = dokumen_standar.standar_mutu_id', 'left')
            ->join('master_jenis_standar', 'master_jenis_standar.id = standar_mutu.jenis_standar_id', 'left')
            ->join('master_kategori_standar', 'master_kategori_standar.id = standar_mutu.kategori_standar_id', 'left')
            ->where('dokumen_standar.status_publikasi', self::STATUS_TERBIT);
        $this->applyKeyword($builder, $keyword, ['dokumen_standar.kode_dokumen', 'standar_mutu.nama_standar']);

        if ($jenisStandarId > 0) {
            $builder->where('standar_mutu.jenis_standar_id', $jenisStandarId);
        }
        if ($kategoriStandarId > 0) {
            $builder->where('standar_mutu.kategori_standar_id', $kategoriStandarId);
        }

        $perPage = 15;
        $items = $builder->orderBy('dokumen_standar.updated_at', 'DESC')->paginate($perPage, 'publik_standar');

        $rows = array_map(function (array $item): array {
            return [
                'nomor' => trim((string) ($item['kode_dokumen'] ?? '')) ?: '-',
                'judul' => trim((string) ($item['nama_standar'] ?? '')) ?: '-',
                'jenis' => trim((string) ($item['nama_jenis'] ?? '')) ?: '-',
                'kategori' => trim((string) ($item['nama_kategori'] ?? '')) ?: '-',
                'updated_label' => $this->latestStamp($item),
                'action_url' => base_url('/publik/standar-mutu/detail/' . (int) ($item['id'] ?? 0)),
                'action_label' => 'Lihat Standar',
                'action_icon' => 'bi-eye',
                'is_detail' => true,
            ];
        }, $this->filterStatusTerbit($items));

        $opsiJenis = (new JenisStandarModel())->where('is_aktif', 1)->orderBy('nama_jenis', 'ASC')->findAll();
        $opsiKategori = (new KategoriStandarModel())->where('is_aktif', 1)->orderBy('nama_kategori', 'ASC')->findAll();
        $appName = $this->publicAppName();

        return view('public/standar_list', array_merge($this->baseLayoutData('standar-mutu'), [
            'title' => 'Standar Mutu Terbit - ' . $appName . ' Publik',
            'pageTitle' => 'Standar Mutu Terbit',
            'pageDesc' => 'Daftar dokumen standar mutu yang telah dipublikasikan.',
            'rows' => $rows,
            'pager' => $model->pager,
            'perPage' => $perPage,
            'keyword' => $keyword,
            'jenisStandarAktif' => $jenisStandarId,
            'kategoriStandarAktif' => $kategoriStandarId,
            'opsiJenis' => $opsiJenis,
            'opsiKategori' => $opsiKategori,
        ]));
    }

    private function normalizeIndikator(?string $value): string
    {
        $raw = (string) $value;
        if (trim($raw) === '') {
            return '-';
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return $raw;
        }

        $items = array_values(array_filter(array_map(static fn($item): string => trim((string) $item), $decoded), static fn($text): bool => $text !== ''));
        if ($items === []) {
            return '-';
        }

        $html = '<ol>';
        foreach ($items as $item) {
            $html .= '<li>' . esc($item) . '</li>';
        }
        $html .= '</ol>';

        return $html;
    }

    public function standarDetail(int $id): string
    {
        $dokumenModel = new DokumenStandarModel();
        $standarModel = new StandarMutuModel();

        $dokumen = $dokumenModel->where('status_publikasi', self::STATUS_TERBIT)->find($id);
        if (! $dokumen) {
            throw PageNotFoundException::forPageNotFound('Dokumen standar tidak ditemukan.');
        }

        $standar = $standarModel->find((int) ($dokumen['standar_mutu_id'] ?? 0));
        if (! $standar) {
            throw PageNotFoundException::forPageNotFound('Standar mutu tidak ditemukan.');
        }

        $dokumen['indikator_ketercapaian'] = $this->normalizeIndikator($dokumen['indikator_ketercapaian'] ?? null);
        $appName = $this->publicAppName();

        return view('public/standar_detail', array_merge($this->baseLayoutData('standar-mutu'), [
            'title' => 'Detail Dokumen Standar - ' . $appName . ' Publik',
            'pageTitle' => 'Detail Dokumen Standar',
            'pageDesc' => 'Informasi lengkap dokumen standar yang telah dipublikasikan.',
            'dokumen' => $dokumen,
            'standar' => $standar,
            'profil' => (new ProfilInstitusiModel())->first() ?? [],
            'penandatanganProses' => $this->getPenandatanganProsesByStandar((int) ($standar['id'] ?? 0)),
        ]));
    }
}
