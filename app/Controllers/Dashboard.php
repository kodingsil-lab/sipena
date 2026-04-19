<?php

namespace App\Controllers;

use App\Models\AuditMutuInternalModel;
use App\Models\DokumenStandarModel;
use App\Models\KebijakanMutuModel;
use App\Models\KebijakanSpmiModel;
use App\Models\PedomanPpeppModel;
use App\Models\PeraturanModel;
use App\Models\StandarMutuModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    private function buildDokumenItem(array $row, string $jenis, string $detailPathPrefix): array
    {
        $updatedAt = $row['updated_at'] ?? $row['created_at'] ?? null;
        $timestamp = is_string($updatedAt) ? strtotime($updatedAt) : false;

        return [
            'id' => (int) ($row['id'] ?? 0),
            'jenis' => $jenis,
            'nomor' => trim((string) ($row['nomor'] ?? '')) !== '' ? (string) $row['nomor'] : '-',
            'judul' => trim((string) ($row['judul'] ?? '')) !== '' ? (string) $row['judul'] : '-',
            'updated_at' => $updatedAt,
            'updated_at_ts' => $timestamp !== false ? $timestamp : 0,
            'detail_url' => base_url($detailPathPrefix . '/' . (int) ($row['id'] ?? 0)),
        ];
    }

    private function getDokumenTerbaru(int $limit = 15): array
    {
        $peraturanRows = (new PeraturanModel())
            ->select('id, nomor_dokumen AS nomor, judul, updated_at, created_at')
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        $kebijakanRows = (new KebijakanMutuModel())
            ->select('id, nomor_dokumen AS nomor, judul, updated_at, created_at')
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        $standarRows = (new StandarMutuModel())
            ->select('id, kode_standar AS nomor, nama_standar AS judul, updated_at, created_at')
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        $kebijakanSpmiRows = (new KebijakanSpmiModel())
            ->select('id, nomor_dokumen AS nomor, judul, updated_at, created_at')
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        $pedomanPpeppRows = (new PedomanPpeppModel())
            ->select('id, nomor_dokumen AS nomor, judul, jenis_dokumen, updated_at, created_at')
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        $auditRows = (new AuditMutuInternalModel())
            ->select('id, nomor_dokumen AS nomor, judul, updated_at, created_at')
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        $dokumenStandarRows = (new DokumenStandarModel())
            ->select('dokumen_standar.id, dokumen_standar.kode_dokumen AS nomor, standar_mutu.nama_standar AS judul, dokumen_standar.updated_at, dokumen_standar.created_at')
            ->join('standar_mutu', 'standar_mutu.id = dokumen_standar.standar_mutu_id', 'left')
            ->orderBy('dokumen_standar.updated_at', 'DESC')
            ->orderBy('dokumen_standar.created_at', 'DESC')
            ->findAll($limit);

        $merged = [];

        foreach ($peraturanRows as $row) {
            $merged[] = $this->buildDokumenItem($row, 'Peraturan', '/peraturan/detail');
        }
        foreach ($kebijakanRows as $row) {
            $merged[] = $this->buildDokumenItem($row, 'Kebijakan Mutu', '/kebijakan-mutu/detail');
        }
        foreach ($standarRows as $row) {
            $merged[] = $this->buildDokumenItem($row, 'Standar Mutu', '/standar-mutu/detail');
        }
        foreach ($kebijakanSpmiRows as $row) {
            $merged[] = $this->buildDokumenItem($row, 'Kebijakan SPMI', '/kebijakan-spmi/detail');
        }
        foreach ($pedomanPpeppRows as $row) {
            $jenisDokumen = trim((string) ($row['jenis_dokumen'] ?? ''));
            $jenisLabel = 'Pedoman PPEPP' . ($jenisDokumen !== '' ? ' - ' . $jenisDokumen : '');
            $merged[] = $this->buildDokumenItem($row, $jenisLabel, '/pedoman-ppepp/detail');
        }
        foreach ($auditRows as $row) {
            $merged[] = $this->buildDokumenItem($row, 'Audit Mutu Internal', '/audit-mutu-internal/detail');
        }
        foreach ($dokumenStandarRows as $row) {
            $merged[] = $this->buildDokumenItem($row, 'Dokumen Standar', '/dokumen-standar/detail');
        }

        usort($merged, static function (array $a, array $b): int {
            return ($b['updated_at_ts'] ?? 0) <=> ($a['updated_at_ts'] ?? 0);
        });

        return array_slice($merged, 0, $limit);
    }

    public function index()
    {
        $standarModel = new StandarMutuModel();
        $dokumenModel = new DokumenStandarModel();
        $amiModel     = new AuditMutuInternalModel();
        $userModel    = new UserModel();

        return view('dashboard/index', [
            'title' => 'Dashboard SIPENA',

            'user' => session()->get(),

            // statistik
            'totalStandar' => $standarModel->countAll(),
            'totalDokumen' => $dokumenModel->countAll(),
            'totalAmi'     => $amiModel->countAll(),
            'totalPublishDokumen' => $dokumenModel->where('status_publikasi', 'publish')->countAllResults(),
            'totalDraftDokumen' => $dokumenModel->where('status_publikasi', 'draft')->countAllResults(),
            'totalUser' => $userModel->countAll(),
            'dokumenTerbaru' => $this->getDokumenTerbaru(15),
        ]);
    }
}
