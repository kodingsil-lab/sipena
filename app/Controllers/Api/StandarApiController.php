<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\DokumenStandarModel;
use CodeIgniter\HTTP\ResponseInterface;

class StandarApiController extends BaseController
{
    private const STATUS_TERBIT = 'publish';
    private const DEFAULT_PER_PAGE = 20;
    private const MAX_PER_PAGE = 100;

    public function index(): ResponseInterface
    {
        if ($authResponse = $this->authorizeRequest()) {
            return $authResponse;
        }

        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = self::DEFAULT_PER_PAGE;
        }
        $perPage = min(self::MAX_PER_PAGE, $perPage);
        $offset = ($page - 1) * $perPage;

        $keyword = trim((string) $this->request->getGet('keyword'));
        $jenisStandarId = (int) $this->request->getGet('jenis_standar_id');
        $kategoriStandarId = (int) $this->request->getGet('kategori_standar_id');

        $builder = $this->baseBuilder();
        $this->applyFilters($builder, $keyword, $jenisStandarId, $kategoriStandarId);

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();
        $rows = $builder
            ->orderBy('dokumen_standar.updated_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $items = array_map(fn(array $row): array => $this->transformRow($row, false), $rows);
        $lastPage = max(1, (int) ceil($total / $perPage));

        return $this->respondJson([
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'has_next' => $page < $lastPage,
            ],
            'data' => $items,
        ]);
    }

    public function show(int $id): ResponseInterface
    {
        if ($authResponse = $this->authorizeRequest()) {
            return $authResponse;
        }

        $row = $this->baseBuilder()
            ->where('dokumen_standar.id', $id)
            ->get()
            ->getRowArray();

        if (! is_array($row)) {
            return $this->respondJson([
                'error' => 'Dokumen standar tidak ditemukan.',
            ], 404);
        }

        return $this->respondJson([
            'data' => $this->transformRow($row, true),
        ]);
    }

    public function changes(): ResponseInterface
    {
        if ($authResponse = $this->authorizeRequest()) {
            return $authResponse;
        }

        $sinceRaw = trim((string) $this->request->getGet('since'));
        if ($sinceRaw === '') {
            return $this->respondJson([
                'error' => 'Parameter since wajib diisi (ISO 8601 atau format tanggal yang valid).',
            ], 400);
        }

        $sinceTs = strtotime($sinceRaw);
        if ($sinceTs === false) {
            return $this->respondJson([
                'error' => 'Parameter since tidak valid.',
            ], 400);
        }

        $sinceDb = date('Y-m-d H:i:s', $sinceTs);
        $limit = (int) $this->request->getGet('limit');
        if ($limit <= 0) {
            $limit = self::DEFAULT_PER_PAGE;
        }
        $limit = min(self::MAX_PER_PAGE, $limit);

        $builder = $this->baseBuilder();
        $builder->groupStart()
            ->where('dokumen_standar.updated_at >', $sinceDb)
            ->orWhere('dokumen_standar.created_at >', $sinceDb)
            ->groupEnd();

        $rows = $builder
            ->orderBy('dokumen_standar.updated_at', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $items = array_map(fn(array $row): array => $this->transformRow($row, true), $rows);
        $nextSince = $sinceDb;
        if ($rows !== []) {
            $lastRow = end($rows);
            $lastUpdated = (string) ($lastRow['updated_at'] ?? $lastRow['created_at'] ?? $sinceDb);
            if (trim($lastUpdated) !== '') {
                $nextSince = $lastUpdated;
            }
        }

        return $this->respondJson([
            'meta' => [
                'since' => $this->toIso8601($sinceDb),
                'next_since' => $this->toIso8601($nextSince),
                'limit' => $limit,
                'count' => count($items),
            ],
            'data' => $items,
        ]);
    }

    private function baseBuilder()
    {
        return (new DokumenStandarModel())
            ->select('
                dokumen_standar.id,
                dokumen_standar.standar_mutu_id,
                dokumen_standar.kode_dokumen,
                dokumen_standar.tanggal_dokumen,
                dokumen_standar.revisi,
                dokumen_standar.halaman,
                dokumen_standar.indikator_ketercapaian,
                dokumen_standar.status_publikasi,
                dokumen_standar.updated_at,
                dokumen_standar.created_at,
                standar_mutu.kode_standar,
                standar_mutu.nama_standar,
                master_jenis_standar.id AS jenis_standar_id,
                master_jenis_standar.nama_jenis,
                master_kategori_standar.id AS kategori_standar_id,
                master_kategori_standar.nama_kategori
            ')
            ->join('standar_mutu', 'standar_mutu.id = dokumen_standar.standar_mutu_id', 'left')
            ->join('master_jenis_standar', 'master_jenis_standar.id = standar_mutu.jenis_standar_id', 'left')
            ->join('master_kategori_standar', 'master_kategori_standar.id = standar_mutu.kategori_standar_id', 'left')
            ->where('dokumen_standar.status_publikasi', self::STATUS_TERBIT);
    }

    private function applyFilters(object $builder, string $keyword, int $jenisStandarId, int $kategoriStandarId): void
    {
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('dokumen_standar.kode_dokumen', $keyword)
                ->orLike('standar_mutu.nama_standar', $keyword)
                ->groupEnd();
        }

        if ($jenisStandarId > 0) {
            $builder->where('standar_mutu.jenis_standar_id', $jenisStandarId);
        }

        if ($kategoriStandarId > 0) {
            $builder->where('standar_mutu.kategori_standar_id', $kategoriStandarId);
        }
    }

    private function transformRow(array $row, bool $withIndicatorDetail): array
    {
        $indikatorItems = $this->normalizeIndikator($row['indikator_ketercapaian'] ?? null);
        $payload = [
            'id' => (int) ($row['id'] ?? 0),
            'standar_mutu_id' => (int) ($row['standar_mutu_id'] ?? 0),
            'kode_dokumen' => trim((string) ($row['kode_dokumen'] ?? '')),
            'kode_standar' => trim((string) ($row['kode_standar'] ?? '')),
            'nama_standar' => trim((string) ($row['nama_standar'] ?? '')),
            'jenis_standar' => [
                'id' => (int) ($row['jenis_standar_id'] ?? 0),
                'nama' => trim((string) ($row['nama_jenis'] ?? '')),
            ],
            'kategori_standar' => [
                'id' => (int) ($row['kategori_standar_id'] ?? 0),
                'nama' => trim((string) ($row['nama_kategori'] ?? '')),
            ],
            'tanggal_dokumen' => $this->nullableDate($row['tanggal_dokumen'] ?? null),
            'revisi' => trim((string) ($row['revisi'] ?? '')),
            'halaman' => trim((string) ($row['halaman'] ?? '')),
            'status_publikasi' => trim((string) ($row['status_publikasi'] ?? '')),
            'indikator_ketercapaian_count' => count($indikatorItems),
            'updated_at' => $this->toIso8601($row['updated_at'] ?? null),
            'created_at' => $this->toIso8601($row['created_at'] ?? null),
            'links' => [
                'detail' => base_url('/api/v1/standar/' . (int) ($row['id'] ?? 0)),
                'web' => base_url('/publik/standar-mutu/detail/' . (int) ($row['id'] ?? 0)),
            ],
        ];

        if ($withIndicatorDetail) {
            $payload['indikator_ketercapaian'] = $indikatorItems;
        }

        return $payload;
    }

    private function normalizeIndikator($value): array
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $items = array_values(array_filter(array_map(
                static fn($item): string => trim(strip_tags((string) $item)),
                $decoded
            ), static fn(string $text): bool => $text !== ''));

            return $items;
        }

        $stripped = strip_tags($raw);
        $chunks = preg_split('/\r\n|\r|\n|;|\|/', $stripped);
        if (! is_array($chunks)) {
            return $stripped !== '' ? [$stripped] : [];
        }

        $items = [];
        foreach ($chunks as $chunk) {
            $text = trim((string) $chunk);
            $text = preg_replace('/^\d+[\.\)]\s*/', '', $text) ?? $text;
            if ($text !== '') {
                $items[] = $text;
            }
        }

        if ($items !== []) {
            return $items;
        }

        return [$stripped];
    }

    private function nullableDate($value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $ts = strtotime($raw);
        if ($ts === false) {
            return null;
        }

        return date('Y-m-d', $ts);
    }

    private function toIso8601($value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $ts = strtotime($raw);
        if ($ts === false) {
            return null;
        }

        return date(DATE_ATOM, $ts);
    }

    private function authorizeRequest(): ?ResponseInterface
    {
        $expectedToken = trim((string) env('API_INTEGRATION_TOKEN', app_setting('api_integration_token', '')));
        if ($expectedToken === '') {
            return $this->respondJson([
                'error' => 'API token belum dikonfigurasi. Set API_INTEGRATION_TOKEN atau setting api_integration_token.',
            ], 500);
        }

        $authHeader = trim((string) $this->request->getHeaderLine('Authorization'));
        $providedToken = '';
        if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            $providedToken = trim((string) ($matches[1] ?? ''));
        }

        if ($providedToken === '') {
            $providedToken = trim((string) $this->request->getHeaderLine('X-API-Key'));
        }

        if ($providedToken === '' || ! hash_equals($expectedToken, $providedToken)) {
            return $this->respondJson([
                'error' => 'Unauthorized',
            ], 401);
        }

        return null;
    }

    private function respondJson(array $payload, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($payload);
    }
}

