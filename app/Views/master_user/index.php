<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="page-title"><?= esc($pageTitle ?? 'Master Pengguna'); ?></h1>
        <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
    </div>
    <div>
        <a href="<?= base_url('/pengaturan/pengguna/tambah'); ?>" class="btn btn-primary">Tambah Pengguna</a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
<?php endif; ?>

<div class="card card-clean mb-4">
    <div class="card-body p-4">
        <form action="<?= base_url('/pengaturan/pengguna'); ?>" method="get" class="row g-3 align-items-end">
            <div class="col-lg-8 col-md-6">
                <label class="form-label">Cari Nama / Username / Email</label>
                <input type="text" name="keyword" class="form-control" value="<?= esc($keywordAktif ?? ''); ?>" placeholder="Masukkan kata kunci...">
            </div>
            <div class="col-lg-2 col-md-2">
                <label class="form-label">Per Halaman</label>
                <select name="per_page" class="form-select">
                    <?php foreach (($opsiPerPage ?? [15, 25, 50]) as $opsi): ?>
                        <option value="<?= esc((string) $opsi); ?>" <?= (int) ($perPageAktif ?? 15) === (int) $opsi ? 'selected' : ''; ?>>
                            <?= esc((string) $opsi); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                    <a href="<?= base_url('/pengaturan/pengguna'); ?>" class="btn btn-secondary">Reset</a>
                </div>
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
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role Sistem</th>
                        <th>Jabatan</th>
                        <th>TTD</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($users)): ?>
                        <?php $startNo = (((int) (($pager ?? null) ? $pager->getCurrentPage('users') : 1)) - 1) * ((int) ($perPage ?? 15)); ?>
                        <?php foreach ($users as $i => $item): ?>
                            <tr>
                                <td><?= $startNo + $i + 1; ?></td>
                                <td><?= esc($item['nama']); ?></td>
                                <td><?= esc($item['username']); ?></td>
                                <td><?= esc($item['email']); ?></td>
                                <td><?= esc($item['role_label'] ?? $item['role']); ?></td>
                                <td><?= esc($item['jabatan'] ?? '-'); ?></td>
                                <td>
                                    <?php if (! empty($item['ttd_digital'])): ?>
                                        <span class="badge bg-success">Ada</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Belum</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= (int) ($item['is_active'] ?? 0) === 1 ? 'success' : 'secondary'; ?>">
                                        <?= (int) ($item['is_active'] ?? 0) === 1 ? 'Aktif' : 'Nonaktif'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="<?= base_url('/pengaturan/pengguna/edit/' . $item['id']); ?>" class="action-icon-btn action-edit" data-bs-toggle="tooltip" title="Edit" aria-label="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="<?= base_url('/pengaturan/pengguna/hapus/' . $item['id']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="action-icon-btn action-delete" data-bs-toggle="tooltip" title="Hapus" aria-label="Hapus" onclick="return confirm('Yakin hapus user ini?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Belum ada data user.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                Menampilkan <?= count($users); ?> data pada halaman ini
                <?php if (! empty($pager)): ?>
                    (Halaman <?= (int) $pager->getCurrentPage('users'); ?> dari <?= (int) $pager->getPageCount('users'); ?>)
                <?php endif; ?>
            </small>
            <?php if (! empty($pager)): ?>
                <?= $pager->only(['keyword', 'per_page'])->links('users', 'default_full'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

