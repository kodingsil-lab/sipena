<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="dokumen-standar-wrap">
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="page-title"><?= esc($pageTitle ?? 'Detail Dokumen Standar'); ?></h1>
        <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
    </div>
    <div class="d-grid gap-2 d-md-block">
        <a href="<?= base_url('/dokumen-standar/edit/' . $dokumen['id']); ?>" class="btn btn-warning">Edit</a>
        <a href="<?= base_url('/standar-mutu'); ?>" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card card-clean mb-4">
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Kode Standar</label>
                <div><?= esc($standar['kode_standar'] ?? '-'); ?></div>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-bold">Nama Standar</label>
                <div><?= esc($standar['nama_standar'] ?? '-'); ?></div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Kode Dokumen</label>
                <div><?= esc($dokumen['kode_dokumen'] ?? '-'); ?></div>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal</label>
                <div><?= esc($dokumen['tanggal_dokumen'] ?? '-'); ?></div>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Revisi</label>
                <div><?= esc($dokumen['revisi'] ?? '-'); ?></div>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Halaman</label>
                <div><?= esc($dokumen['halaman'] ?? '-'); ?></div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Status Publikasi</label>
                <div>
                    <span class="badge bg-<?= ($dokumen['status_publikasi'] ?? 'draft') === 'publish' ? 'success' : 'secondary'; ?>">
                        <?= esc((($dokumen['status_publikasi'] ?? 'draft') === 'publish') ? 'Terbit' : 'Draf'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-clean mb-4">
    <div class="card-body p-4">
        <h5 class="mb-3">Data Institusi</h5>
        <div class="mb-3"><strong>Nama Institusi:</strong><br><?= esc($profil['nama_institusi'] ?? '-'); ?></div>
        <div class="mb-3"><strong>Singkatan:</strong><br><?= esc($profil['singkatan_institusi'] ?? '-'); ?></div>
        <div class="mb-3"><strong>Visi:</strong><br><?= ($profil['visi'] ?? '') !== '' ? $profil['visi'] : '-'; ?></div>
        <div class="mb-3"><strong>Misi:</strong><br><?= ($profil['misi'] ?? '') !== '' ? $profil['misi'] : '-'; ?></div>
        <div class="mb-3"><strong>Tujuan:</strong><br><?= ($profil['tujuan'] ?? '') !== '' ? $profil['tujuan'] : '-'; ?></div>
        <div class="mb-0"><strong>Sasaran:</strong><br><?= ($profil['sasaran'] ?? '') !== '' ? $profil['sasaran'] : '-'; ?></div>
    </div>
</div>
<div class="card card-clean mb-4">
    <div class="card-body p-4">
        <h5 class="mb-3">Penandatangan Dokumen</h5>

        <?php
        $urutanProses = ['Perumusan', 'Pemeriksaan', 'Persetujuan', 'Pengesahan', 'Pengendalian'];
        ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Proses</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>TTD Digital</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($urutanProses as $proses): ?>
                        <?php $user = $penandatanganProses[$proses] ?? null; ?>
                        <tr>
                            <td><strong><?= esc($proses); ?></strong></td>
                            <td><?= esc($user['nama'] ?? '-'); ?></td>
                            <td><?= esc($user['jabatan'] ?? '-'); ?></td>
                            <td>
                                <?php if (! empty($user['ttd_digital'])): ?>
                                    <a href="<?= esc(base_url('uploads/ttd_standar/' . rawurlencode((string) $user['ttd_digital']))); ?>" target="_blank" class="btn btn-primary btn-sm">
                                        Lihat TTD
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($user['tanggal_ttd'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card card-clean">
    <div class="card-body p-4">
        <div class="mb-4">
            <label class="form-label fw-bold">Rasional</label>
            <div class="border rounded-3 p-3 bg-light"><?= $dokumen['rasional']; ?></div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Subjek / Pihak yang Bertanggung Jawab</label>
            <div class="border rounded-3 p-3 bg-light"><?= $dokumen['subjek_bertanggung_jawab']; ?></div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Definisi Istilah</label>
            <div class="border rounded-3 p-3 bg-light"><?= $dokumen['definisi_istilah']; ?></div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Pernyataan Isi Standar</label>
            <div class="border rounded-3 p-3 bg-light"><?= $dokumen['pernyataan_isi_standar']; ?></div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Indikator Ketercapaian</label>
            <div class="border rounded-3 p-3 bg-light"><?= $dokumen['indikator_ketercapaian']; ?></div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Strategi Pencapaian</label>
            <div class="border rounded-3 p-3 bg-light"><?= $dokumen['strategi_pencapaian']; ?></div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Dokumen Terkait</label>
            <div class="border rounded-3 p-3 bg-light"><?= $dokumen['dokumen_terkait']; ?></div>
        </div>

        <div class="mb-0">
            <label class="form-label fw-bold">Referensi</label>
            <div class="border rounded-3 p-3 bg-light"><?= $dokumen['referensi']; ?></div>
        </div>
    </div>
</div>
</div>

<style>
    .dokumen-standar-wrap {
        max-width: 1080px;
        margin: 0 auto;
    }
</style>

<?= $this->endSection(); ?>


