<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<?php $canEditDokumen = in_array(strtolower((string) session('role')), ['admin', 'kepala_lpm'], true); ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="page-title"><?= esc($pageTitle ?? 'Detail Standar Mutu'); ?></h1>
        <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
    </div>
    <div class="d-grid gap-2 d-md-block">
        <?php if ($canEditDokumen): ?>
            <a href="<?= base_url('/standar-mutu/edit/' . $standar['id']); ?>" class="btn btn-warning">Edit</a>
        <?php endif; ?>
        <a href="<?= base_url('/standar-mutu'); ?>" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card card-clean">
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Kode Standar</label>
                <div><?= esc($standar['kode_standar'] ?? '-'); ?></div>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-bold">Nama Standar</label>
                <div><?= esc($standar['nama_standar'] ?? '-'); ?></div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Jenis Standar</label>
                <div><?= esc($standar['nama_jenis'] ?? '-'); ?></div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Kategori Standar</label>
                <div><?= esc($standar['nama_kategori'] ?? '-'); ?></div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Status Publikasi</label>
                <div>
                    <span class="badge bg-<?= ($standar['status_publikasi'] ?? 'draft') === 'publish' ? 'success' : 'secondary'; ?>">
                        <?= esc((($standar['status_publikasi'] ?? 'draft') === 'publish') ? 'Terbit' : 'Draf'); ?>
                    </span>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Deskripsi</label>
                <div class="border rounded-3 p-3 bg-light">
                    <?= nl2br(esc($standar['deskripsi'] ?? '-')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

