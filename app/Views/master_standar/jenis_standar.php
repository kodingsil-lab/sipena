<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Master Jenis Standar'); ?></h1>
    <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
<?php endif; ?>

<div class="card card-clean mb-4">
    <div class="card-body p-4">
        <form action="<?= esc($action); ?>" method="post">
            <?= csrf_field(); ?>
            <?php $isEditMode = ($formMode ?? 'create') === 'edit'; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Jenis Standar</label>
                    <input type="text" name="nama_jenis" class="form-control" value="<?= esc(old('nama_jenis', $editJenis['nama_jenis'] ?? '')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" name="deskripsi" class="form-control" value="<?= esc(old('deskripsi', $editJenis['deskripsi'] ?? '')); ?>">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <?php $isAktif = old('is_aktif', (string) ($editJenis['is_aktif'] ?? '1')); ?>
                        <input class="form-check-input" type="checkbox" role="switch" id="isAktifJenis" name="is_aktif" value="1" <?= $isAktif !== '0' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="isAktifJenis">Aktif</label>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><?= $isEditMode ? 'Update Jenis Standar' : 'Tambah Jenis Standar'; ?></button>
                <?php if ($isEditMode): ?>
                    <a href="<?= base_url('/master-data/jenis-standar'); ?>" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card card-clean">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jenis Standar</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($jenis)): ?>
                        <?php $startNo = (((int) (($pager ?? null) ? $pager->getCurrentPage('jenis') : 1)) - 1) * ((int) ($perPage ?? 15)); ?>
                        <?php foreach ($jenis as $i => $item): ?>
                            <tr>
                                <td><?= $startNo + $i + 1; ?></td>
                                <td><?= esc($item['nama_jenis']); ?></td>
                                <td><?= esc($item['deskripsi'] ?? '-'); ?></td>
                                <td>
                                    <?php if ((int) ($item['is_aktif'] ?? 1) === 1): ?>
                                        <i class="bi bi-check-square-fill text-success fs-5" data-bs-toggle="tooltip" title="Aktif"></i>
                                    <?php else: ?>
                                        <i class="bi bi-square text-secondary fs-5" data-bs-toggle="tooltip" title="Non Aktif"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="<?= base_url('/master-data/jenis-standar/edit/' . $item['id']); ?>" class="action-icon-btn action-edit" data-bs-toggle="tooltip" title="Edit Jenis Standar" aria-label="Edit Jenis Standar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="<?= base_url('/master-data/jenis-standar/hapus/' . $item['id']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="action-icon-btn action-delete" data-bs-toggle="tooltip" title="Hapus Jenis Standar" aria-label="Hapus Jenis Standar" onclick="return confirm('Yakin hapus data ini?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data jenis standar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">Menampilkan <?= count($jenis ?? []); ?> data pada halaman ini</small>
            <?php if (! empty($pager)): ?>
                <?= $pager->links('jenis', 'default_full'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

