<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Form Audit Mutu Internal'); ?></h1>
    <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
<?php endif; ?>

<div class="card card-clean">
    <div class="card-body p-4">
        <form action="<?= esc($action); ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control" value="<?= esc(old('judul', $dokumen['judul'] ?? '')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tahun</label>
                    <input type="text" name="tahun" class="form-control" maxlength="4" value="<?= esc(old('tahun', $dokumen['tahun'] ?? '')); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nomor Dokumen</label>
                    <input type="text" name="nomor_dokumen" class="form-control" value="<?= esc(old('nomor_dokumen', $dokumen['nomor_dokumen'] ?? '')); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status Publikasi</label>
                    <select name="status_publikasi" class="form-select">
                        <option value="draft" <?= old('status_publikasi', $dokumen['status_publikasi'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draf</option>
                        <option value="publish" <?= old('status_publikasi', $dokumen['status_publikasi'] ?? '') === 'publish' ? 'selected' : ''; ?>>Terbit</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="5"><?= esc(old('deskripsi', $dokumen['deskripsi'] ?? '')); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">File PDF</label>
                    <input type="file" name="file_pdf" class="form-control" accept=".pdf">

                    <?php if (! empty($dokumen['file_pdf'])): ?>
                        <div class="mt-2">
                            <a href="<?= esc(base_url('uploads/audit_mutu_internal/' . rawurlencode((string) $dokumen['file_pdf']))); ?>" target="_blank" class="btn btn-primary btn-sm">
                                Lihat File Saat Ini
                            </a>
                        </div>
                    <?php endif; ?>

                    <small class="text-muted">Format file harus PDF. Maksimal 5 MB.</small>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
                <a href="<?= base_url('/audit-mutu-internal'); ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
