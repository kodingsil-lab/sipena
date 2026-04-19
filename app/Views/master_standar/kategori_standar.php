<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Master Kategori Standar'); ?></h1>
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
                    <label class="form-label">Nama Kategori Standar</label>
                    <input type="text" name="nama_kategori" class="form-control" value="<?= esc(old('nama_kategori', $editKategori['nama_kategori'] ?? '')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" name="deskripsi" class="form-control" value="<?= esc(old('deskripsi', $editKategori['deskripsi'] ?? '')); ?>">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <?php $isAktif = old('is_aktif', (string) ($editKategori['is_aktif'] ?? '1')); ?>
                        <input class="form-check-input" type="checkbox" role="switch" id="isAktifKategori" name="is_aktif" value="1" <?= $isAktif !== '0' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="isAktifKategori">Aktif</label>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><?= $isEditMode ? 'Update Kategori Standar' : 'Tambah Kategori Standar'; ?></button>
                <?php if ($isEditMode): ?>
                    <a href="<?= base_url('/master-data/kategori-standar'); ?>" class="btn btn-secondary">Batal</a>
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
                        <th>Nama Kategori Standar</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($kategori)): ?>
                        <?php $startNo = (((int) (($pager ?? null) ? $pager->getCurrentPage('kategori') : 1)) - 1) * ((int) ($perPage ?? 15)); ?>
                        <?php foreach ($kategori as $i => $item): ?>
                            <tr>
                                <td><?= $startNo + $i + 1; ?></td>
                                <td><?= esc($item['nama_kategori']); ?></td>
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
                                        <a href="<?= base_url('/master-data/kategori-standar/edit/' . $item['id']); ?>" class="action-icon-btn action-edit" data-bs-toggle="tooltip" title="Edit Kategori Standar" aria-label="Edit Kategori Standar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="<?= base_url('/master-data/kategori-standar/hapus/' . $item['id']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="action-icon-btn action-delete" data-bs-toggle="tooltip" title="Hapus Kategori Standar" aria-label="Hapus Kategori Standar" onclick="return confirm('Yakin hapus data ini?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data kategori standar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">Menampilkan <?= count($kategori ?? []); ?> data pada halaman ini</small>
            <?php if (! empty($pager)): ?>
                <?= $pager->links('kategori', 'default_full'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

