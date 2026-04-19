<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Profil Saya'); ?></h1>
    <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
<?php endif; ?>

<div class="card card-clean">
    <div class="card-body p-4">
        <form action="<?= esc($action); ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="<?= esc(old('nama', $userItem['nama'] ?? '')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= esc(old('email', $userItem['email'] ?? '')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= esc(old('username', $userItem['username'] ?? '')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role Sistem</label>
                    <input type="text" class="form-control" value="<?= esc(ucfirst(str_replace('_', ' ', (string) ($userItem['role'] ?? '-')))); ?>" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="<?= esc(old('jabatan', $userItem['jabatan'] ?? '')); ?>" placeholder="Contoh: Ketua LPM">
                </div>

                <div class="col-md-6">
                    <label class="form-label">TTD Digital</label>
                    <input type="file" name="ttd_digital" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <?php if (! empty($userItem['ttd_digital'])): ?>
                        <div class="mt-2">
                            <img src="<?= esc(base_url('uploads/ttd_standar/' . rawurlencode((string) $userItem['ttd_digital']))); ?>" alt="TTD Digital" style="max-height:70px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
                <a href="<?= base_url('/dashboard'); ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Profil</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
