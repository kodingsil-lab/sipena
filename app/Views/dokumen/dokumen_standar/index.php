<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= base_url('/standar-mutu'); ?>">Standar Mutu</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= esc($standar['nama_standar'] ?? 'Dokumen Standar'); ?></li>
    </ol>
</nav>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="page-title"><?= esc($pageTitle ?? 'Dokumen Standar'); ?></h1>
        <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
        <div class="text-muted small mt-1">
            <strong><?= esc($standar['kode_standar'] ?? '-'); ?></strong> - <?= esc($standar['nama_standar'] ?? '-'); ?>
        </div>
    </div>
    <div class="d-grid gap-2 d-md-block">
        <a href="<?= base_url('/standar-mutu/' . $standar['id'] . '/dokumen/tambah'); ?>" class="btn btn-primary">Tambah Dokumen</a>
        <a href="<?= base_url('/standar-mutu'); ?>" class="btn btn-secondary">Kembali ke Standar</a>
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
        <form action="" method="get" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Cari Kode / Revisi</label>
                <input type="text" name="keyword" class="form-control" value="<?= esc($keywordAktif ?? ''); ?>" placeholder="Masukkan kata kunci...">
            </div>

            <div class="col-md-3">
                <label class="form-label">Filter Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <?php foreach (($daftarStatus ?? []) as $status): ?>
                        <option value="<?= esc($status); ?>" <?= ($statusAktif ?? '') === $status ? 'selected' : ''; ?>>
                            <?= esc($status === 'publish' ? 'Terbit' : ($status === 'draft' ? 'Draf' : ucfirst($status))); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>

            <div class="col-md-auto">
                <a href="<?= current_url(); ?>" class="btn btn-secondary">Reset</a>
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
                        <th>Kode Dokumen</th>
                        <th>Tanggal</th>
                        <th>Revisi</th>
                        <th>Halaman</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($dokumen)): ?>
                        <?php foreach ($dokumen as $i => $item): ?>
                            <tr>
                                <td><?= $i + 1; ?></td>
                                <td><?= esc($item['kode_dokumen']); ?></td>
                                <td><?= esc($item['tanggal_dokumen'] ?? '-'); ?></td>
                                <td><?= esc($item['revisi'] ?? '-'); ?></td>
                                <td><?= esc($item['halaman'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge bg-<?= ($item['status_publikasi'] ?? 'draft') === 'publish' ? 'success' : 'secondary'; ?>">
                                        <?= esc((($item['status_publikasi'] ?? 'draft') === 'publish') ? 'Terbit' : 'Draf'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="<?= base_url('/dokumen-standar/edit/' . $item['id']); ?>" class="action-icon-btn action-edit" data-bs-toggle="tooltip" title="Edit" aria-label="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="<?= base_url('/dokumen-standar/hapus/' . $item['id']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="action-icon-btn action-delete" data-bs-toggle="tooltip" title="Hapus" aria-label="Hapus" onclick="return confirm('Yakin hapus dokumen ini?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>

                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Lainnya
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="<?= base_url('/dokumen-standar/detail/' . $item['id']); ?>">
                                                        <i class="bi bi-eye me-2"></i>Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="<?= base_url('/dokumen-standar/pdf/' . $item['id']); ?>" target="_blank">
                                                        <i class="bi bi-printer me-2"></i>Cetak
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada dokumen standar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>



