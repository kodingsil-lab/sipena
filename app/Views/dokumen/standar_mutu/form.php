<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Form Standar Mutu'); ?></h1>
    <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
<?php endif; ?>

<?php
$penugasanOld = old('penandatangan');
if (! is_array($penugasanOld)) {
    $penugasanOld = [];
}

$tanggalTtdOld = old('tanggal_ttd');
if (! is_array($tanggalTtdOld)) {
    $tanggalTtdOld = [];
}
?>

<div class="card card-clean">
    <div class="card-body p-4">
        <form action="<?= esc($action); ?>" method="post">
            <?= csrf_field(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kode Standar</label>
                    <input type="text" name="kode_standar" class="form-control" value="<?= esc(old('kode_standar', $standar['kode_standar'] ?? '')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nama Standar</label>
                    <input type="text" name="nama_standar" class="form-control" value="<?= esc(old('nama_standar', $standar['nama_standar'] ?? '')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jenis Standar</label>
                    <select name="jenis_standar_id" class="form-select" required>
                        <option value="">Pilih Jenis Standar</option>
                        <?php foreach (($daftarJenis ?? []) as $jenis): ?>
                            <?php $selectedJenis = (string) old('jenis_standar_id', (string) ($standar['jenis_standar_id'] ?? '')); ?>
                            <option value="<?= esc((string) $jenis['id']); ?>" <?= $selectedJenis === (string) $jenis['id'] ? 'selected' : ''; ?>>
                                <?= esc($jenis['nama_jenis']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kategori Standar</label>
                    <select name="kategori_standar_id" class="form-select" required>
                        <option value="">Pilih Kategori Standar</option>
                        <?php foreach (($daftarKategori ?? []) as $kategori): ?>
                            <?php $selectedKategori = (string) old('kategori_standar_id', (string) ($standar['kategori_standar_id'] ?? '')); ?>
                            <option value="<?= esc((string) $kategori['id']); ?>" <?= $selectedKategori === (string) $kategori['id'] ? 'selected' : ''; ?>>
                                <?= esc($kategori['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status Publikasi</label>
                    <select name="status_publikasi" class="form-select">
                        <option value="draft" <?= old('status_publikasi', $standar['status_publikasi'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draf</option>
                        <option value="publish" <?= old('status_publikasi', $standar['status_publikasi'] ?? '') === 'publish' ? 'selected' : ''; ?>>Terbit</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="5"><?= esc(old('deskripsi', $standar['deskripsi'] ?? '')); ?></textarea>
                </div>

                <div class="col-12">
                    <hr class="my-2">
                    <h6 class="mb-1">Penugasan Penandatangan</h6>
                    <p class="text-muted mb-0">Setiap proses hanya memilih 1 orang untuk standar ini.</p>
                </div>

                <?php foreach (($prosesPenandatangan ?? []) as $prosesKey => $prosesLabel): ?>
                    <?php
                    $penugasanItem = $penugasanAktif[$prosesKey] ?? [];
                    $selectedUserId = (string) ($penugasanOld[$prosesKey] ?? ($penugasanItem['user_id'] ?? ''));
                    $selectedTanggal = (string) ($tanggalTtdOld[$prosesKey] ?? ($penugasanItem['tanggal_ttd'] ?? ''));
                    ?>
                    <div class="col-md-6">
                        <label class="form-label"><?= esc($prosesLabel); ?></label>
                        <select name="penandatangan[<?= esc($prosesKey); ?>]" class="form-select" required>
                            <option value="">-- Pilih Pengguna --</option>
                            <?php foreach (($daftarUserAktif ?? []) as $user): ?>
                                <option value="<?= esc((string) $user['id']); ?>" <?= $selectedUserId === (string) $user['id'] ? 'selected' : ''; ?>>
                                    <?= esc($user['nama'] ?? '-'); ?> (<?= esc($user['email'] ?? '-'); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Tanda Tangan <?= esc($prosesLabel); ?></label>
                        <input type="date" name="tanggal_ttd[<?= esc($prosesKey); ?>]" class="form-control" value="<?= esc($selectedTanggal); ?>" required>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
                <a href="<?= base_url('/standar-mutu'); ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
