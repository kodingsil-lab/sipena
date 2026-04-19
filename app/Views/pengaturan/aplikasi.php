<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<style>
    .theme-color-box {
        border: 1px solid #dbe6f3;
        border-radius: 12px;
        padding: 12px 14px;
        background: #f8fbff;
        height: 100%;
    }

    .theme-color-input {
        width: 100%;
        max-width: 130px;
        min-height: 42px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="page-header">
            <h1 class="page-title"><?= esc($pageTitle ?? 'Pengaturan Aplikasi'); ?></h1>
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
                <form action="<?= base_url('/pengaturan/aplikasi/update'); ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field(); ?>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label">Nama Aplikasi</label>
                            <input
                                type="text"
                                name="nama_aplikasi"
                                class="form-control"
                                maxlength="100"
                                value="<?= esc(old('nama_aplikasi', $settings['nama_aplikasi'] ?? 'SIPENA')); ?>"
                                required
                            >
                            <div class="small text-muted mt-1">Akan ditampilkan di judul halaman dan branding aplikasi.</div>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Footer Text</label>
                            <input
                                type="text"
                                name="footer_text"
                                class="form-control"
                                maxlength="180"
                                value="<?= esc(old('footer_text', $settings['footer_text'] ?? '')); ?>"
                                placeholder="Contoh: SIPENA - Sistem Informasi Penjaminan Mutu Internal"
                            >
                            <div class="small text-muted mt-1">Teks ini akan tampil pada footer aplikasi.</div>
                        </div>

                        <div class="col-lg-6">
                            <div class="theme-color-box">
                                <label class="form-label mb-2">Warna Tema (Internal)</label>
                                <input
                                    type="color"
                                    name="warna_tema"
                                    class="form-control form-control-color theme-color-input"
                                    value="<?= esc(old('warna_tema', $settings['warna_tema'] ?? '#3468CB')); ?>"
                                    title="Pilih warna tema internal"
                                    required
                                >
                                <div class="small text-muted mt-2">Digunakan untuk dashboard internal, tombol utama, dan elemen brand internal.</div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="theme-color-box">
                                <label class="form-label mb-2">Warna Tema Public</label>
                                <input
                                    type="color"
                                    name="warna_tema_public"
                                    class="form-control form-control-color theme-color-input"
                                    value="<?= esc(old('warna_tema_public', $settings['warna_tema_public'] ?? ($settings['warna_tema'] ?? '#3468CB'))); ?>"
                                    title="Pilih warna tema public"
                                    required
                                >
                                <div class="small text-muted mt-2">Digunakan untuk beranda public, navbar public, dan tombol/icon public.</div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Logo Aplikasi</label>
                            <input type="file" name="logo_pt" class="form-control" accept=".png,.jpg,.jpeg,.webp">
                            <div class="small text-muted mt-1">Dipakai untuk logo tampilan aplikasi. Maksimum 2 MB.</div>
                            <div class="mt-2">
                                <?php if (! empty($logoHeaderUrl)): ?>
                                    <img src="<?= esc($logoHeaderUrl); ?>" alt="Logo Aplikasi" style="width:64px;height:64px;object-fit:contain;object-position:center;padding:4px;background:#fff;border-radius:12px;border:1px solid #dbe3ef;">
                                <?php else: ?>
                                    <span class="small text-muted">Belum ada logo aplikasi.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Favicon</label>
                            <input type="file" name="favicon" class="form-control" accept=".png,.ico,.webp">
                            <div class="small text-muted mt-1">PNG/WEBP akan dioptimasi ke 64x64 agar rapi di tab browser. Maksimum 1 MB.</div>
                            <div class="mt-2">
                                <?php if (! empty($faviconUrl)): ?>
                                    <img src="<?= esc($faviconUrl); ?>" alt="Favicon" style="width:40px;height:40px;object-fit:contain;object-position:center;padding:3px;background:#fff;border-radius:8px;border:1px solid #dbe3ef;">
                                <?php else: ?>
                                    <span class="small text-muted">Belum ada favicon.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Zona Waktu</label>
                            <select name="app_timezone" class="form-select" required>
                                <?php foreach (($timezones ?? []) as $tz): ?>
                                    <option value="<?= esc($tz); ?>" <?= ($currentTz ?? 'Asia/Jakarta') === $tz ? 'selected' : ''; ?>>
                                        <?= esc($tz); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="small text-muted mt-1">Dipakai untuk tanggal/jam di seluruh sistem.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 flex-wrap mt-4">
                        <a href="<?= base_url('/dashboard'); ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
