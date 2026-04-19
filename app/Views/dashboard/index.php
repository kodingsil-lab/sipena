<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<style>
    .dash-sipena {
        width: 100%;
        display: grid;
        gap: 18px;
    }

    .dash-hero {
        position: relative;
        overflow: hidden;
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
        text-transform: uppercase;
    }

    .dash-title {
        margin: 14px 0 0;
        font-size: 2.05rem;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -0.02em;
        max-width: 880px;
    }

    .dash-subtitle {
        margin: 10px 0 0;
        color: rgba(238, 244, 255, 0.92);
        font-size: 0.98rem;
        line-height: 1.65;
        max-width: 860px;
    }

    .dash-mini-stats {
        margin-top: 16px;
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }

    .dash-mini-item {
        min-width: 120px;
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
    }

    .dash-mini-bar {
        margin-top: 14px;
        height: 5px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.25);
        width: min(420px, 100%);
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
        row-gap: 14px;
    }

    .dash-card {
        border-radius: 16px;
        border: 1px solid #dde7f6;
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        padding: 14px 14px 12px;
        height: 100%;
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
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        border: 1px solid transparent;
        flex-shrink: 0;
    }

    .dash-card--blue .dash-icon-box {
        background: #e8f1ff;
        color: #3468cb;
        border-color: #d6e4fb;
    }

    .dash-card--purple .dash-icon-box {
        background: #f0ebff;
        color: #7c3aed;
        border-color: #e2d8ff;
    }

    .dash-card--amber .dash-icon-box {
        background: #fff3df;
        color: #d97706;
        border-color: #ffe5bd;
    }

    .dash-card--green .dash-icon-box {
        background: #e9fbf3;
        color: #10b981;
        border-color: #c9f3e1;
    }

    .dash-live-pill {
        border-radius: 999px;
        border: 1px solid #d7e3f6;
        background: #f8fbff;
        color: #4168b6;
        font-size: 0.73rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        font-weight: 700;
        padding: 7px 12px;
        line-height: 1;
    }

    .dash-title-label {
        margin: 11px 0 0;
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .dash-main-value {
        margin: 6px 0 0;
        font-size: 2.05rem;
        line-height: 1.05;
        color: #1f2937;
        font-weight: 800;
    }

    .dash-desc {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 0.86rem;
        line-height: 1.55;
        min-height: 38px;
    }

    .dash-progress-row {
        margin-top: 10px;
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

    .dash-doc-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .dash-doc-subtitle {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 0.88rem;
    }

    .dash-table-flex thead th,
    .dash-table-flex tbody td {
        white-space: normal;
    }

    .dash-col-jenis {
        min-width: 150px;
    }

    .dash-col-judul {
        min-width: 260px;
    }

    @media (max-width: 991.98px) {
        .dash-hero {
            padding: 22px 20px 20px;
        }

        .dash-title {
            font-size: 1.7rem;
        }
    }

    @media (max-width: 575.98px) {
        .dash-title {
            font-size: 1.45rem;
        }

        .dash-subtitle {
            font-size: 0.9rem;
        }

        .dash-mini-stats {
            gap: 16px;
        }

        .dash-mini-value {
            font-size: 1.45rem;
        }
    }
</style>

<?php
$role = strtolower((string) ($user['role'] ?? ''));
$isKepalaLpm = $role === 'kepala_lpm';

$heroSubtitle = $isKepalaLpm
    ? 'Monitoring implementasi standar mutu, evaluasi ketercapaian, serta tindak lanjut dokumen penjaminan mutu.'
    : 'Monitoring sistem, aktivitas pengguna, dan pengelolaan dokumen mutu secara menyeluruh.';

$cards = $isKepalaLpm
    ? [
        [
            'variant' => 'blue',
            'icon' => 'bi-award-fill',
            'label' => 'Total Standar',
            'value' => $totalStandar,
            'desc' => 'Jumlah standar mutu aktif sebagai acuan pelaksanaan SPMI.',
        ],
        [
            'variant' => 'purple',
            'icon' => 'bi-journal-text',
            'label' => 'Dokumen Standar',
            'value' => $totalDokumen,
            'desc' => 'Total dokumen standar yang dipantau untuk implementasi.',
        ],
        [
            'variant' => 'amber',
            'icon' => 'bi-patch-check-fill',
            'label' => 'Dokumen Terbit',
            'value' => $totalPublishDokumen,
            'desc' => 'Dokumen terbit yang siap dipakai sebagai rujukan unit kerja.',
        ],
        [
            'variant' => 'green',
            'icon' => 'bi-hourglass-split',
            'label' => 'Dokumen Draf',
            'value' => $totalDraftDokumen ?? 0,
            'desc' => 'Dokumen draf yang perlu ditinjau dan ditindaklanjuti.',
        ],
    ]
    : [
        [
            'variant' => 'blue',
            'icon' => 'bi-people-fill',
            'label' => 'Pengguna Sistem',
            'value' => $totalUser ?? 0,
            'desc' => 'Jumlah akun pengguna aktif dalam sistem SIPENA.',
        ],
        [
            'variant' => 'purple',
            'icon' => 'bi-journal-text',
            'label' => 'Dokumen Standar',
            'value' => $totalDokumen,
            'desc' => 'Total dokumen standar yang telah tersimpan.',
        ],
        [
            'variant' => 'amber',
            'icon' => 'bi-patch-check-fill',
            'label' => 'Dokumen Terbit',
            'value' => $totalPublishDokumen,
            'desc' => 'Dokumen dengan status terbit dan siap digunakan.',
        ],
        [
            'variant' => 'green',
            'icon' => 'bi-clipboard2-data-fill',
            'label' => 'Audit Mutu Internal',
            'value' => $totalAmi,
            'desc' => 'Rekap dokumen Audit Mutu Internal yang tersedia.',
        ],
    ];
?>

<div class="dash-sipena">
    <section class="dash-hero" aria-labelledby="dash-title">
        <div class="dash-hero-content">
            <span class="dash-badge">SIPENA</span>
            <h1 id="dash-title" class="dash-title">Selamat Datang, <?= esc($user['nama'] ?? 'Administrator SIPENA'); ?></h1>
            <p class="dash-subtitle"><?= esc($heroSubtitle); ?></p>

            <div class="dash-mini-stats">
                <div class="dash-mini-item">
                    <span class="dash-mini-value"><?= esc($totalStandar); ?></span>
                    <span class="dash-mini-label">Total Standar</span>
                </div>
                <div class="dash-mini-item">
                    <span class="dash-mini-value"><?= esc($totalDokumen); ?></span>
                    <span class="dash-mini-label">Dokumen Standar</span>
                </div>
                <div class="dash-mini-item">
                    <span class="dash-mini-value"><?= esc($totalPublishDokumen); ?></span>
                    <span class="dash-mini-label">Dokumen Terbit</span>
                </div>
                <div class="dash-mini-item">
                    <span class="dash-mini-value"><?= esc($totalAmi); ?></span>
                    <span class="dash-mini-label">Audit Mutu Internal</span>
                </div>
            </div>

            <div class="dash-mini-bar"><span></span></div>
        </div>
    </section>

    <section aria-label="Ringkasan Dashboard">
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

    <section aria-label="Dokumen Terbaru">
        <div class="card card-clean">
            <div class="card-body p-4">
                <h2 class="dash-doc-title">Daftar Dokumen Terbaru</h2>
                <p class="dash-doc-subtitle">Data update terbaru dari seluruh dokumen pada menu utama SIPENA.</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle dash-table-flex">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Dokumen</th>
                                <th>Judul</th>
                                <th>Terakhir Diupdate</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($dokumenTerbaru)): ?>
                                <?php foreach ($dokumenTerbaru as $i => $item): ?>
                                    <tr>
                                        <td><?= $i + 1; ?></td>
                                        <td class="dash-col-jenis"><?= esc($item['jenis'] ?? '-'); ?></td>
                                        <td class="dash-col-judul"><?= esc($item['judul'] ?? '-'); ?></td>
                                        <td>
                                            <?php if (! empty($item['updated_at']) && strtotime((string) $item['updated_at']) !== false): ?>
                                                <?= esc(date('d M Y H:i', strtotime((string) $item['updated_at']))); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= esc($item['detail_url'] ?? '#'); ?>" class="btn btn-sm btn-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas update dokumen.</td>
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
