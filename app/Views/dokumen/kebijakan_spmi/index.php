<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<?php $canEditDokumen = in_array(strtolower((string) session('role')), ['admin', 'kepala_lpm'], true); ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="page-title"><?= esc($pageTitle ?? 'Kebijakan SPMI'); ?></h1>
        <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
    </div>
    <?php if ($canEditDokumen): ?>
    <div>
        <a href="<?= base_url('/kebijakan-spmi/tambah'); ?>" class="btn btn-primary">Tambah Dokumen</a>
    </div>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
<?php endif; ?>

<div class="card card-clean mb-4">
    <div class="card-body p-4">
        <form action="<?= base_url('/kebijakan-spmi'); ?>" method="get" class="row g-3 align-items-end">
            <div class="col-lg-5 col-md-5">
                <label class="form-label">Cari Judul / Nomor</label>
                <input type="text" name="keyword" class="form-control" value="<?= esc($keywordAktif ?? ''); ?>" placeholder="Masukkan kata kunci...">
            </div>

            <div class="col-lg-3 col-md-3">
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
                    <a href="<?= base_url('/kebijakan-spmi'); ?>" class="btn btn-secondary">Reset</a>
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
                        <th>Nomor Dokumen</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($kebijakan)): ?>
                        <?php $startNo = (((int) (($pager ?? null) ? $pager->getCurrentPage('kebijakan_spmi') : 1)) - 1) * ((int) ($perPage ?? 15)); ?>
                        <?php foreach ($kebijakan as $i => $item): ?>
                            <tr>
                                <td><?= $startNo + $i + 1; ?></td>
                                <td><?= esc($item['nomor_dokumen'] ?? '-'); ?></td>
                                <td><?= esc($item['judul']); ?></td>
                                <td>
                                    <span class="text-muted">
                                        <?= esc(strlen(trim((string) ($item['deskripsi'] ?? ''))) > 120 ? substr(trim((string) ($item['deskripsi'] ?? '')), 0, 117) . '...' : (trim((string) ($item['deskripsi'] ?? '')) !== '' ? trim((string) ($item['deskripsi'] ?? '')) : '-')); ?>
                                    </span>
                                </td>
                                <td><?= esc($item['tahun'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge bg-<?= ($item['status_publikasi'] ?? 'draft') === 'publish' ? 'success' : 'secondary'; ?>">
                                        <?= esc((($item['status_publikasi'] ?? 'draft') === 'publish') ? 'Terbit' : 'Draf'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="<?= base_url('/kebijakan-spmi/detail/' . $item['id']); ?>" class="action-icon-btn action-view" data-bs-toggle="tooltip" title="Detail" aria-label="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if ($canEditDokumen): ?>
                                            <a href="<?= base_url('/kebijakan-spmi/edit/' . $item['id']); ?>" class="action-icon-btn action-edit" data-bs-toggle="tooltip" title="Edit" aria-label="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="<?= base_url('/kebijakan-spmi/hapus/' . $item['id']); ?>" method="post" class="d-inline">
                                                <?= csrf_field(); ?>
                                                <button type="submit" class="action-icon-btn action-delete" data-bs-toggle="tooltip" title="Hapus" aria-label="Hapus" onclick="return confirm('Yakin hapus data ini?')">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data Kebijakan SPMI.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                Menampilkan <?= count($kebijakan); ?> data pada halaman ini
                <?php if (! empty($pager)): ?>
                    (Halaman <?= (int) $pager->getCurrentPage('kebijakan_spmi'); ?> dari <?= (int) $pager->getPageCount('kebijakan_spmi'); ?>)
                <?php endif; ?>
            </small>
            <?php if (! empty($pager)): ?>
                <?= $pager->only(['keyword', 'status', 'per_page'])->links('kebijakan_spmi', 'default_full'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>




