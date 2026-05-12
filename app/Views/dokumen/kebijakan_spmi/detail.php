<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<?php $canEditDokumen = in_array(strtolower((string) session('role')), ['admin', 'kepala_lpm'], true); ?>

<?php
$pdfUrl = '';
if (! empty($kebijakan['file_pdf'])) {
    $pdfUrl = base_url('/uploads/kebijakan_spmi/' . rawurlencode($kebijakan['file_pdf']));
}
?>

<div class="detail-wrap">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title mb-1"><?= esc($pageTitle ?? 'Detail Kebijakan SPMI'); ?></h1>
            <p class="page-subtitle mb-0"><?= esc($pageDesc ?? ''); ?></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <?php if ($canEditDokumen): ?>
                <a href="<?= base_url('/kebijakan-spmi/edit/' . $kebijakan['id']); ?>" class="btn btn-warning">Edit</a>
            <?php endif; ?>
            <a href="<?= base_url('/kebijakan-spmi'); ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="row g-4 align-items-stretch">
        <div class="col-lg-4">
            <div class="card card-clean h-100">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Judul</label>
                            <div><?= esc($kebijakan['judul'] ?? '-'); ?></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Nomor Dokumen</label>
                            <div><?= esc($kebijakan['nomor_dokumen'] ?? '-'); ?></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Tahun</label>
                            <div><?= esc($kebijakan['tahun'] ?? '-'); ?></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Status Publikasi</label>
                            <div>
                                <span class="badge bg-<?= ($kebijakan['status_publikasi'] ?? 'draft') === 'publish' ? 'success' : 'secondary'; ?>">
                                    <?= esc((($kebijakan['status_publikasi'] ?? 'draft') === 'publish') ? 'Terbit' : 'Draf'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <div class="border rounded-3 p-3 bg-light">
                                <?= nl2br(esc($kebijakan['deskripsi'] ?? '-')); ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-clean h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Preview Dokumen PDF</h6>
                        <?php if ($pdfUrl !== ''): ?>
                            <a href="<?= esc($pdfUrl); ?>" target="_blank" class="btn btn-primary btn-sm">
                                Buka Tab Baru
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if ($pdfUrl !== ''): ?>
                        <iframe
                            src="<?= esc($pdfUrl); ?>#toolbar=1&navpanes=0&scrollbar=1"
                            title="Preview PDF"
                            style="width:100%;height:72vh;min-height:520px;border:1px solid #e2e8f0;border-radius:12px;background:#fff;"
                        ></iframe>
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center text-muted" style="height:72vh;min-height:520px;border:1px dashed #cbd5e1;border-radius:12px;background:#f8fafc;">
                            File PDF belum tersedia.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .detail-wrap {
        width: 100%;
        max-width: 100%;
    }
</style>

<?= $this->endSection(); ?>



