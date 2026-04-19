<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Form Pengguna'); ?></h1>
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
                    <label class="form-label">Password <?= isset($userItem) && $userItem ? '(Kosongkan jika tidak diubah)' : ''; ?></label>
                    <input type="password" name="password" class="form-control" <?= isset($userItem) && $userItem ? '' : 'required'; ?>>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role Sistem</label>
                    <select name="role" class="form-select" required>
                        <option value="">Pilih role</option>
                        <?php foreach (($opsiRole ?? []) as $key => $label): ?>
                            <option value="<?= esc($key); ?>" <?= old('role', $userItem['role'] ?? '') === $key ? 'selected' : ''; ?>>
                                <?= esc($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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

                <div class="col-md-6">
                    <label class="form-label d-block">Status Aktif</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            <?= old('is_active', isset($userItem['is_active']) ? (string) $userItem['is_active'] : '1') == '1' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">
                            Aktif
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
                <a href="<?= base_url('/pengaturan/pengguna'); ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
