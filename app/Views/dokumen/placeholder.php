<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Halaman'); ?></h1>
    <p class="page-subtitle"><?= esc($pageDesc ?? 'Halaman placeholder modul.'); ?></p>
</div>

<div class="card card-clean">
    <div class="card-body p-4">
        <div class="mb-3">
            <span class="badge bg-primary">Placeholder</span>
        </div>

        <h5 class="mb-3">Modul belum dikembangkan penuh</h5>

        <p class="text-muted mb-0">
            Halaman ini sudah aktif sebagai placeholder awal untuk modul
            <strong><?= esc($pageTitle ?? ''); ?></strong>.
            Tahap berikutnya kita isi struktur data, tabel, form input, dan aksi CRUD sesuai kebutuhan SIPENA.
        </p>
    </div>
</div>

<?= $this->endSection(); ?>