<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="dokumen-standar-wrap">
    <div class="public-title-hero">
        <div class="public-title-head">
            <div>
                <span class="public-title-badge"><i class="bi bi-file-earmark-richtext-fill"></i> Informasi Publik</span>
                <h1 class="public-title-main"><?= esc($pageTitle ?? 'Detail Dokumen Standar'); ?></h1>
                <p class="public-title-sub"><?= esc($pageDesc ?? ''); ?></p>
            </div>
            <div>
                <a href="<?= base_url('/publik/standar-mutu'); ?>" class="btn btn-secondary public-title-action">Kembali</a>
            </div>
        </div>
    </div>

    <div class="card card-clean mb-4">
        <div class="card-body p-4">
            <div class="public-detail-box">
                <div class="public-detail-row">
                    <div class="public-detail-label">Kode Standar</div>
                    <div class="public-detail-value">: <?= esc($standar['kode_standar'] ?? '-'); ?></div>
                </div>
                <div class="public-detail-row">
                    <div class="public-detail-label">Nama Standar</div>
                    <div class="public-detail-value">: <?= esc($standar['nama_standar'] ?? '-'); ?></div>
                </div>
                <div class="public-detail-row">
                    <div class="public-detail-label">Tanggal</div>
                    <div class="public-detail-value">: <?= esc($dokumen['tanggal_dokumen'] ?? '-'); ?></div>
                </div>
                <div class="public-detail-row">
                    <div class="public-detail-label">Revisi</div>
                    <div class="public-detail-value">: <?= esc($dokumen['revisi'] ?? '-'); ?></div>
                </div>
                <div class="public-detail-row">
                    <div class="public-detail-label">Halaman</div>
                    <div class="public-detail-value">: <?= esc($dokumen['halaman'] ?? '-'); ?></div>
                </div>
                <div class="public-detail-row">
                    <div class="public-detail-label">Status</div>
                    <div class="public-detail-value">:
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-3 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Terbit
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-clean mb-4">
        <div class="card-body p-4">
            <h5 class="public-section-title">Data Institusi</h5>
            <div class="mb-3"><strong>Nama Institusi:</strong><br><?= esc($profil['nama_institusi'] ?? '-'); ?></div>
            <div class="mb-3"><strong>Singkatan:</strong><br><?= esc($profil['singkatan_institusi'] ?? '-'); ?></div>
            <div class="mb-3"><strong>Visi:</strong><br><?= ($profil['visi'] ?? '') !== '' ? sanitize_allowed_html($profil['visi'], 'profil') : '-'; ?></div>
            <div class="mb-3"><strong>Misi:</strong><br><?= ($profil['misi'] ?? '') !== '' ? sanitize_allowed_html($profil['misi'], 'profil') : '-'; ?></div>
            <div class="mb-3"><strong>Tujuan:</strong><br><?= ($profil['tujuan'] ?? '') !== '' ? sanitize_allowed_html($profil['tujuan'], 'profil') : '-'; ?></div>
            <div class="mb-0"><strong>Sasaran:</strong><br><?= ($profil['sasaran'] ?? '') !== '' ? sanitize_allowed_html($profil['sasaran'], 'profil') : '-'; ?></div>
        </div>
    </div>

    <div class="card card-clean mb-4">
        <div class="card-body p-4">
            <h5 class="public-section-title">Proses Dokumen</h5>
            <?php $urutanProses = ['Perumusan', 'Pemeriksaan', 'Persetujuan', 'Pengesahan', 'Pengendalian']; ?>
            <div class="table-responsive table-proses-wrap">
                <table class="table table-hover align-middle mb-0 table-proses">
                    <thead>
                        <tr>
                            <th style="width:70px;">No.</th>
                            <th>Proses</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Tanggal</th>
                            <th style="width:180px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($urutanProses as $idx => $proses): ?>
                        <?php $user = $penandatanganProses[$proses] ?? null; ?>
                        <?php $isDone = ! empty($user); ?>
                        <tr>
                            <td><?= $idx + 1; ?></td>
                            <td><strong><?= esc($proses); ?></strong></td>
                            <td><?= esc($user['nama'] ?? '-'); ?></td>
                            <td><?= esc($user['jabatan'] ?? '-'); ?></td>
                            <td><?= esc($user['tanggal_ttd'] ?? '-'); ?></td>
                            <td>
                                <?php if ($isDone): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i>Lulus Proses
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-semibold px-2 py-2">
                                        <i class="bi bi-dash-circle me-1"></i>Belum Diproses
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card card-clean">
        <div class="card-body p-4">
            <h5 class="public-section-title">Konten Dokumen Standar</h5>
            <div class="mb-4"><label class="form-label fw-bold">Rasional</label><div class="public-content-box"><?= sanitize_allowed_html($dokumen['rasional'] ?? '', 'dokumen'); ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Subjek / Pihak yang Bertanggung Jawab</label><div class="public-content-box"><?= sanitize_allowed_html($dokumen['subjek_bertanggung_jawab'] ?? '', 'dokumen'); ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Definisi Istilah</label><div class="public-content-box"><?= sanitize_allowed_html($dokumen['definisi_istilah'] ?? '', 'dokumen'); ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Pernyataan Isi Standar</label><div class="public-content-box"><?= sanitize_allowed_html($dokumen['pernyataan_isi_standar'] ?? '', 'dokumen'); ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Indikator Ketercapaian</label><div class="public-content-box"><?= sanitize_allowed_html($dokumen['indikator_ketercapaian'] ?? '', 'dokumen'); ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Strategi Pencapaian</label><div class="public-content-box"><?= sanitize_allowed_html($dokumen['strategi_pencapaian'] ?? '', 'dokumen'); ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Dokumen Terkait</label><div class="public-content-box"><?= sanitize_allowed_html($dokumen['dokumen_terkait'] ?? '', 'dokumen'); ?></div></div>
            <div class="mb-0"><label class="form-label fw-bold">Referensi</label><div class="public-content-box"><?= sanitize_allowed_html($dokumen['referensi'] ?? '', 'dokumen'); ?></div></div>
        </div>
    </div>
</div>

<style>
    .dokumen-standar-wrap {
        max-width: 1080px;
        margin: 0 auto;
    }

    .public-section-title {
        margin: 0 0 14px;
        color: #1f2937;
        font-size: 1rem;
        font-weight: 700;
    }

    .table-proses-wrap {
        border: 1px solid #e3ebf8;
        border-radius: 14px;
    }

    .table-proses thead th {
        background: #f8fbff;
        color: #334155;
        font-weight: 700;
    }

    .public-detail-box {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #ffffff;
    }

    .public-detail-row {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
        gap: 0.75rem;
        align-items: center;
        padding: 0.95rem 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .public-detail-row:last-child {
        border-bottom: none;
    }

    .public-detail-label {
        color: #334155;
        font-weight: 700;
    }

    .public-detail-value {
        color: #0f172a;
        font-weight: 500;
    }

    .public-content-box {
        border: 1px solid #dbe6f3;
        border-radius: 12px;
        padding: 0.9rem 1rem;
        background: #f8fbff;
        color: #1e293b;
    }
</style>

<?= $this->endSection(); ?>
