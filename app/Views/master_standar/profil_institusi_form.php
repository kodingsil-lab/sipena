<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Master Profil Institusi'); ?></h1>
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
                    <label class="form-label">Nama Institusi</label>
                    <input type="text" name="nama_institusi" class="form-control" value="<?= esc(old('nama_institusi', $profil['nama_institusi'] ?? '')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Singkatan Institusi</label>
                    <input type="text" name="singkatan_institusi" class="form-control" value="<?= esc(old('singkatan_institusi', $profil['singkatan_institusi'] ?? '')); ?>">
                </div>

                <div class="col-md-6">

                    <label class="form-label">Visi</label>
                    <textarea name="visi" class="form-control tiny-editor" rows="3"><?= esc(old('visi', $profil['visi'] ?? '')); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Misi</label>
                    <textarea name="misi" class="form-control tiny-editor" rows="6"><?= esc(old('misi', $profil['misi'] ?? '')); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tujuan</label>
                    <textarea name="tujuan" class="form-control tiny-editor" rows="6"><?= esc(old('tujuan', $profil['tujuan'] ?? '')); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sasaran</label>
                    <textarea name="sasaran" class="form-control tiny-editor" rows="6"><?= esc(old('sasaran', $profil['sasaran'] ?? '')); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3"><?= esc(old('alamat', $profil['alamat'] ?? '')); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Logo Institusi</label>
                    <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png,.webp">

                    <?php if (! empty($profil['logo'])): ?>
                        <div class="mt-2">
                            <img src="<?= esc(base_url('uploads/logo_institusi/' . rawurlencode((string) $profil['logo']))); ?>" alt="Logo Institusi" style="max-height:80px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
                <a href="<?= base_url('/dashboard'); ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Profil Institusi</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.tiny-editor',
        plugins: 'lists advlist',
        height: 300,
        menubar: false,
        toolbar: 'bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent',
        entity_encoding: 'raw'
    });
</script>

<?= $this->endSection(); ?>
