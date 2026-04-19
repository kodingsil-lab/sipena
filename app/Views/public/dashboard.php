<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<style>
    .dash-sipena {
        width: 100%;
        max-width: 100%;
        display: grid;
        gap: 18px;
        min-width: 0;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .dash-sipena * {
        box-sizing: border-box;
    }

    .dash-sipena section,
    .dash-sipena .card,
    .dash-sipena .card-body {
        min-width: 0;
        max-width: 100%;
    }

    .dash-hero {
        position: relative;
        overflow: hidden;
        min-width: 0;
        max-width: 100%;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 58%, var(--primary) 100%);
        padding: 28px 26px 24px;
        color: #ffffff;
        box-shadow: 0 14px 30px rgba(28, 66, 142, 0.22);
    }

    .dash-hero::before {
        content: '';
        position: absolute;
        width: 280px;
        height: 280px;
        right: -80px;
        top: -130px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .dash-hero::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        left: 38%;
        bottom: -130px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0) 74%);
        pointer-events: none;
    }

    .dash-hero-content {
        position: relative;
        z-index: 2;
        min-width: 0;
        max-width: 100%;
    }

    .dash-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: #eef4ff;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: none;
    }

    .dash-title {
        margin: 14px 0 0;
        font-size: 2.05rem;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -0.02em;
        max-width: 880px;
        overflow-wrap: anywhere;
        word-break: normal;
    }

    .dash-subtitle {
        margin: 10px 0 0;
        color: rgba(238, 244, 255, 0.92);
        font-size: 0.98rem;
        line-height: 1.65;
        max-width: 860px;
        overflow-wrap: anywhere;
    }

    .dash-mini-stats {
        margin-top: 16px;
        display: grid;
        grid-template-columns: repeat(4, minmax(160px, 1fr));
        gap: 10px 14px;
        min-width: 0;
        max-width: 100%;
    }

    .dash-mini-item {
        min-width: 0;
        max-width: 100%;
    }

    .dash-mini-value {
        display: block;
        font-size: 1.75rem;
        line-height: 1;
        font-weight: 800;
        color: #ffffff;
    }

    .dash-mini-label {
        display: block;
        margin-top: 4px;
        font-size: 0.82rem;
        color: rgba(234, 242, 255, 0.9);
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .dash-mini-bar {
        margin-top: 10px;
        height: 5px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.25);
        width: 100%;
    }

    .dash-mini-bar > span {
        display: block;
        height: 100%;
        width: 30%;
        border-radius: inherit;
        background: rgba(255, 255, 255, 0.86);
        animation: heroBarSlide 2.6s ease-in-out infinite alternate;
    }

    .dash-grid {
        row-gap: 12px;
        max-width: 100%;
    }

    .dash-sipena .row {
        margin-left: 0;
        margin-right: 0;
    }

    .dash-sipena .row > * {
        min-width: 0;
    }

    .dash-grid > * {
        padding-left: 6px;
        padding-right: 6px;
    }

    .dash-card {
        border-radius: 16px;
        border: 1px solid #dde7f6;
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        padding: 12px 12px 10px;
        height: 100%;
        max-width: 100%;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .dash-card:hover {
        transform: translateY(-3px);
        border-color: #c6d9f6;
        box-shadow: 0 14px 26px rgba(20, 48, 101, 0.12);
    }

    .dash-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .dash-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        border: 1px solid transparent;
        flex-shrink: 0;
    }

    .dash-card--blue .dash-icon-box { background: #e8f1ff; color: #3468cb; border-color: #d6e4fb; }
    .dash-card--purple .dash-icon-box { background: #f0ebff; color: #7c3aed; border-color: #e2d8ff; }
    .dash-card--amber .dash-icon-box { background: #fff3df; color: #d97706; border-color: #ffe5bd; }
    .dash-card--green .dash-icon-box { background: #e9fbf3; color: #10b981; border-color: #c9f3e1; }

    .dash-live-pill {
        border-radius: 999px;
        border: 1px solid #d7e3f6;
        background: #f8fbff;
        color: #4168b6;
        font-size: 0.7rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        font-weight: 700;
        padding: 6px 10px;
        line-height: 1;
    }

    .dash-title-label {
        margin: 9px 0 0;
        color: #6b7280;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .dash-main-value {
        margin: 4px 0 0;
        font-size: 1.85rem;
        line-height: 1.05;
        color: #1f2937;
        font-weight: 800;
    }

    .dash-desc {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 0.83rem;
        line-height: 1.45;
        min-height: 34px;
    }

    .dash-progress-row {
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dash-progress {
        flex: 1;
        height: 5px;
        border-radius: 999px;
        background: #edf2fb;
        overflow: hidden;
    }

    .dash-progress > span {
        display: block;
        height: 100%;
        width: 100%;
        border-radius: inherit;
        background-size: 220% 100% !important;
        animation: shimmerBar 1.9s ease-in-out infinite alternate;
    }

    .dash-card--blue .dash-progress > span { background-image: linear-gradient(90deg, #3568cc 0%, #5f86d8 50%, #3568cc 100%); }
    .dash-card--purple .dash-progress > span { background-image: linear-gradient(90deg, #7c3aed 0%, #9b67f1 50%, #7c3aed 100%); }
    .dash-card--amber .dash-progress > span { background-image: linear-gradient(90deg, #f59e0b 0%, #f7b84d 50%, #f59e0b 100%); }
    .dash-card--green .dash-progress > span { background-image: linear-gradient(90deg, #10b981 0%, #43d3a7 50%, #10b981 100%); }

    @keyframes shimmerBar {
        0% { background-position: 0% 50%; }
        100% { background-position: 100% 50%; }
    }

    @keyframes heroBarSlide {
        from { transform: translateX(0); }
        to { transform: translateX(178%); }
    }

    @media (prefers-reduced-motion: reduce) {
        .dash-mini-bar > span,
        .dash-progress > span {
            animation: none;
        }
    }

    .dash-percent {
        font-size: 0.84rem;
        font-weight: 700;
        color: #506188;
        min-width: 44px;
        text-align: right;
    }

    .dash-table-flex thead th,
    .dash-table-flex tbody td {
        white-space: normal;
    }

    .dash-table-flex {
        display: block;
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }

    .dash-table-flex .table {
        width: 100%;
        min-width: 840px;
    }

    .dash-col-jenis { min-width: 150px; }
    .dash-col-judul { min-width: 260px; }

    .dash-quick-doc-head {
        margin-bottom: 1.1rem;
    }

    .dash-quick-filter {
        margin-bottom: 1.2rem;
        row-gap: 0.8rem;
    }

    .dash-quick-filter .form-label {
        margin-bottom: 0.5rem;
    }

    .dash-quick-filter .form-control,
    .dash-quick-filter .form-select {
        min-height: 44px;
    }

    .dash-quick-actions {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: nowrap;
        min-height: 44px;
        width: 100%;
    }

    .dash-quick-actions .btn {
        min-width: 0;
        flex: 1 1 0;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    @media (min-width: 992px) {
        .dash-quick-actions {
            justify-content: flex-end;
        }
    }

    @media (max-width: 991.98px) {
        .dash-hero { padding: 22px 20px 20px; }
        .dash-title { font-size: 1.7rem; }
        .dash-mini-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
    }

    @media (max-width: 575.98px) {
        .dash-title { font-size: 1.45rem; }
        .dash-subtitle { font-size: 0.9rem; }
        .dash-mini-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .dash-mini-item { min-width: 0; }
        .dash-mini-label {
            font-size: 0.76rem;
            line-height: 1.25;
        }
        .dash-mini-value { font-size: 1.45rem; }
        .dash-title,
        .dash-subtitle {
            overflow-wrap: anywhere;
        }
    }
</style>

<?php
$publicAppName = trim((string) app_setting('nama_aplikasi', 'SIPENA'));
if ($publicAppName === '') {
    $publicAppName = 'SIPENA';
}

$publicInstitutionName = trim((string) ($profilInstitusi['nama_institusi'] ?? ''));
if ($publicInstitutionName === '') {
    $publicInstitutionName = $publicAppName;
}

$cards = [
    [
        'variant' => 'blue',
        'icon' => 'bi-award-fill',
        'label' => 'Dokumen Standar Mutu',
        'value' => $totalDokumen ?? 0,
        'desc' => 'Total dokumen standar mutu yang telah dipublikasikan.',
    ],
    [
        'variant' => 'purple',
        'icon' => 'bi-journal-check',
        'label' => 'Dokumen SOP',
        'value' => $totalSop ?? 0,
        'desc' => 'Jumlah dokumen SOP yang tersedia untuk akses publik.',
    ],
    [
        'variant' => 'amber',
        'icon' => 'bi-file-earmark-text-fill',
        'label' => 'Dokumen Formulir',
        'value' => $totalFormulir ?? 0,
        'desc' => 'Jumlah dokumen formulir yang dapat diakses pengunjung portal.',
    ],
    [
        'variant' => 'green',
        'icon' => 'bi-clipboard2-data-fill',
        'label' => 'Dokumen Audit Mutu Internal',
        'value' => $totalAmi ?? 0,
        'desc' => 'Dokumen audit mutu internal yang telah dipublikasikan.',
    ],
];
?>

<div class="dash-sipena">
    <section class="dash-hero" aria-labelledby="dash-title">
        <div class="dash-hero-content">
            <span class="dash-badge"><?= esc($publicInstitutionName); ?></span>
            <h1 id="dash-title" class="dash-title">Selamat Datang </h1>
            <p class="dash-subtitle">Portal publik untuk mengakses dokumen-dokumen Sistem Penjaminan Mutu Internal.</p>

            <div class="dash-mini-stats">
                <div class="dash-mini-item">
                    <span class="dash-mini-value"><?= esc($totalDokumen ?? 0); ?></span>
                    <span class="dash-mini-label">Dokumen Standar Mutu</span>
                </div>
                <div class="dash-mini-item">
                    <span class="dash-mini-value"><?= esc($totalSop ?? 0); ?></span>
                    <span class="dash-mini-label">Dokumen SOP</span>
                </div>
                <div class="dash-mini-item">
                    <span class="dash-mini-value"><?= esc($totalFormulir ?? 0); ?></span>
                    <span class="dash-mini-label">Dokumen Formulir</span>
                </div>
                <div class="dash-mini-item">
                    <span class="dash-mini-value"><?= esc($totalAmi ?? 0); ?></span>
                    <span class="dash-mini-label">Dokumen Audit Mutu Internal</span>
                </div>
            </div>

            <div class="dash-mini-bar"><span></span></div>
        </div>
    </section>

    <section aria-label="Ringkasan Publik">
        <div class="row dash-grid">
            <?php foreach ($cards as $card): ?>
                <div class="col-12 col-md-6 col-xl-3">
                    <article class="dash-card dash-card--<?= esc($card['variant']); ?>">
                        <div class="dash-card-head">
                            <span class="dash-icon-box"><i class="bi <?= esc($card['icon']); ?>"></i></span>
                            <span class="dash-live-pill">Terbaru</span>
                        </div>
                        <p class="dash-title-label"><?= esc($card['label']); ?></p>
                        <p class="dash-main-value"><?= esc($card['value']); ?></p>
                        <p class="dash-desc"><?= esc($card['desc']); ?></p>
                        <div class="dash-progress-row">
                            <div class="dash-progress"><span></span></div>
                            <span class="dash-percent">100%</span>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section aria-label="List Dokumen Standar Publik">
        <div class="card card-clean">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 dash-quick-doc-head">
                    <div>
                        <h5 class="mb-1">Akses Cepat Dokumen Standar</h5>
                        <p class="text-muted mb-0">Filter dan lihat cepat dokumen standar terbit (15 data terbaru).</p>
                    </div>
                    <a href="<?= base_url('/publik/standar-mutu'); ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right-circle me-1"></i>Lihat Semua
                    </a>
                </div>

                <form action="<?= base_url('/'); ?>" method="get" class="row g-3 align-items-end dash-quick-filter">
                    <div class="col-lg-3 col-md-12">
                        <label class="form-label">Cari Dokumen</label>
                        <input type="text" name="keyword" value="<?= esc($keyword ?? ''); ?>" class="form-control" placeholder="Ketik nomor atau judul dokumen...">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Filter Jenis</label>
                        <select name="jenis_standar_id" class="form-select">
                            <option value="">Semua Jenis</option>
                            <?php foreach (($opsiJenis ?? []) as $jenis): ?>
                                <option value="<?= esc((string) $jenis['id']); ?>" <?= (int) ($jenisStandarAktif ?? 0) === (int) $jenis['id'] ? 'selected' : ''; ?>>
                                    <?= esc($jenis['nama_jenis']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Filter Kategori</label>
                        <select name="kategori_standar_id" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach (($opsiKategori ?? []) as $kategori): ?>
                                <option value="<?= esc((string) $kategori['id']); ?>" <?= (int) ($kategoriStandarAktif ?? 0) === (int) $kategori['id'] ? 'selected' : ''; ?>>
                                    <?= esc($kategori['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-12 d-flex align-items-end">
                        <div class="dash-quick-actions">
                        <button type="submit" class="btn btn-primary">Terapkan</button>
                        <a href="<?= base_url('/'); ?>" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive dash-table-flex">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:70px;">No.</th>
                                <th style="min-width:130px;">Nomor</th>
                                <th class="dash-col-judul">Judul Dokumen</th>
                                <th class="dash-col-jenis">Jenis Standar</th>
                                <th class="dash-col-jenis">Kategori</th>
                                <th style="min-width:160px;">Terakhir Diupdate</th>
                                <th style="width:90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (! empty($standarList)): ?>
                            <?php foreach ($standarList as $index => $item): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= esc($item['nomor'] ?? '-'); ?></td>
                                    <td><?= esc($item['judul'] ?? '-'); ?></td>
                                    <td><?= esc($item['jenis'] ?? '-'); ?></td>
                                    <td><?= esc($item['kategori'] ?? '-'); ?></td>
                                    <td><?= esc($item['updated_label'] ?? '-'); ?></td>
                                    <td>
                                        <a
                                            href="<?= esc($item['action_url'] ?? '#'); ?>"
                                            class="action-icon-btn action-view"
                                            data-bs-toggle="tooltip"
                                            title="Lihat Standar"
                                            aria-label="Lihat Standar"
                                        >
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada dokumen standar terbit.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection(); ?>
