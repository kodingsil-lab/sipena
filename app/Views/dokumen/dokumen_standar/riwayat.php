<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="dokumen-riwayat-wrap">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title"><?= esc($pageTitle ?? 'Riwayat Perubahan Butir Standar'); ?></h1>
            <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
        </div>
        <div class="d-grid gap-2 d-md-block">
            <a href="<?= base_url('/dokumen-standar/detail/' . (int) ($dokumen['id'] ?? 0)); ?>" class="btn btn-primary">Detail Butir</a>
            <a href="<?= base_url('/standar-mutu'); ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card card-clean mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Kode Standar</label>
                    <div><?= esc($standar['kode_standar'] ?? '-'); ?></div>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Nama Standar</label>
                    <div><?= esc($standar['nama_standar'] ?? '-'); ?></div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Kode Dokumen</label>
                    <div><?= esc($dokumen['kode_dokumen'] ?? '-'); ?></div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Terakhir Diubah</label>
                    <div>
                        <?php if (! empty($dokumen['updated_at']) && strtotime((string) $dokumen['updated_at']) !== false): ?>
                            <?= esc(date('d M Y H:i', strtotime((string) $dokumen['updated_at']))); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Total Riwayat</label>
                    <div><?= esc((string) count($riwayat ?? [])); ?> perubahan</div>
                </div>
            </div>
        </div>
    </div>

    <?php if (! empty($riwayat)): ?>
        <?php foreach ($riwayat as $index => $item): ?>
            <div class="card card-clean mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Riwayat #<?= esc((string) ($index + 1)); ?></h5>
                            <p class="text-muted mb-0">
                                <?php if (! empty($item['created_at']) && strtotime((string) $item['created_at']) !== false): ?>
                                    <?= esc(date('d M Y H:i', strtotime((string) $item['created_at']))); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                                | Oleh: <?= esc($item['nama_pengubah'] ?? 'Sistem'); ?>
                            </p>
                        </div>
                    </div>

                    <?php if (! empty($item['changed_fields_list'])): ?>
                        <p class="mb-3"><strong>Bagian yang berubah:</strong> <?= esc(implode(', ', $item['changed_fields_list'])); ?></p>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Rasional</label>
                        <div class="border rounded-3 p-3 bg-light"><?= ($item['rasional'] ?? '') !== '' ? $item['rasional'] : '-'; ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Subjek / Pihak yang Bertanggung Jawab</label>
                        <div class="border rounded-3 p-3 bg-light"><?= ($item['subjek_bertanggung_jawab'] ?? '') !== '' ? $item['subjek_bertanggung_jawab'] : '-'; ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Definisi Istilah</label>
                        <div class="border rounded-3 p-3 bg-light"><?= ($item['definisi_istilah'] ?? '') !== '' ? $item['definisi_istilah'] : '-'; ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pernyataan Isi Standar</label>
                        <div class="border rounded-3 p-3 bg-light"><?= ($item['pernyataan_isi_standar'] ?? '') !== '' ? $item['pernyataan_isi_standar'] : '-'; ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Indikator Ketercapaian</label>
                        <div class="border rounded-3 p-3 bg-light"><?= ($item['indikator_ketercapaian'] ?? '') !== '' ? $item['indikator_ketercapaian'] : '-'; ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Strategi Pencapaian</label>
                        <div class="border rounded-3 p-3 bg-light"><?= ($item['strategi_pencapaian'] ?? '') !== '' ? $item['strategi_pencapaian'] : '-'; ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Dokumen Terkait</label>
                        <div class="border rounded-3 p-3 bg-light"><?= ($item['dokumen_terkait'] ?? '') !== '' ? $item['dokumen_terkait'] : '-'; ?></div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Referensi</label>
                        <div class="border rounded-3 p-3 bg-light"><?= ($item['referensi'] ?? '') !== '' ? $item['referensi'] : '-'; ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card card-clean">
            <div class="card-body p-4 text-center text-muted">
                Belum ada riwayat perubahan untuk butir standar ini.
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .dokumen-riwayat-wrap {
        max-width: 1080px;
        margin: 0 auto;
    }
</style>

<?= $this->endSection(); ?>
