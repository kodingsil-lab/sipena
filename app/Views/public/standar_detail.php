<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="dokumen-standar-wrap">
    <div class="public-title-hero">
        <div class="public-title-head">
            <div>
                <span class="public-title-badge"><i class="bi bi-file-earmark-richtext-fill"></i> Informasi Publik</span>
                <h1 class="public-title-main"><?= esc($pageTitle ?? 'Detail Dokumen Standar'); ?></h1>
                <p class="public-title-sub"><?= esc($pageDesc ?? ''); ?></p>
            </div>
            <div>
                <a href="<?= base_url('/publik/standar-mutu'); ?>" class="btn btn-secondary public-title-action">Kembali</a>
            </div>
        </div>
    </div>

    <div class="card card-clean mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-4"><label class="form-label fw-bold">Kode Standar</label><div><?= esc($standar['kode_standar'] ?? '-'); ?></div></div>
                <div class="col-md-8"><label class="form-label fw-bold">Nama Standar</label><div><?= esc($standar['nama_standar'] ?? '-'); ?></div></div>
                <div class="col-md-4"><label class="form-label fw-bold">Kode Dokumen</label><div><?= esc($dokumen['kode_dokumen'] ?? '-'); ?></div></div>
                <div class="col-md-3"><label class="form-label fw-bold">Tanggal</label><div><?= esc($dokumen['tanggal_dokumen'] ?? '-'); ?></div></div>
                <div class="col-md-2"><label class="form-label fw-bold">Revisi</label><div><?= esc($dokumen['revisi'] ?? '-'); ?></div></div>
                <div class="col-md-3"><label class="form-label fw-bold">Halaman</label><div><?= esc($dokumen['halaman'] ?? '-'); ?></div></div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Status</label>
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-3 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Terbit
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-clean mb-4">
        <div class="card-body p-4">
            <h5 class="public-section-title">Data Institusi</h5>
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
            <h5 class="public-section-title">Proses Dokumen</h5>
            <?php $urutanProses = ['Perumusan', 'Pemeriksaan', 'Persetujuan', 'Pengesahan', 'Pengendalian']; ?>
            <div class="table-responsive table-proses-wrap">
                <table class="table table-hover align-middle mb-0 table-proses">
                    <thead>
                        <tr>
                            <th style="width:70px;">No.</th>
                            <th>Proses</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Tanggal</th>
                            <th style="width:180px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($urutanProses as $idx => $proses): ?>
                        <?php $user = $penandatanganProses[$proses] ?? null; ?>
                        <?php $isDone = ! empty($user); ?>
                        <tr>
                            <td><?= $idx + 1; ?></td>
                            <td><strong><?= esc($proses); ?></strong></td>
                            <td><?= esc($user['nama'] ?? '-'); ?></td>
                            <td><?= esc($user['jabatan'] ?? '-'); ?></td>
                            <td><?= esc($user['tanggal_ttd'] ?? '-'); ?></td>
                            <td>
                                <?php if ($isDone): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i>Lulus Proses
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-semibold px-2 py-2">
                                        <i class="bi bi-dash-circle me-1"></i>Belum Diproses
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card card-clean">
        <div class="card-body p-4">
            <h5 class="public-section-title">Konten Dokumen Standar</h5>
            <div class="mb-4"><label class="form-label fw-bold">Rasional</label><div class="public-content-box"><?= $dokumen['rasional']; ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Subjek / Pihak yang Bertanggung Jawab</label><div class="public-content-box"><?= $dokumen['subjek_bertanggung_jawab']; ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Definisi Istilah</label><div class="public-content-box"><?= $dokumen['definisi_istilah']; ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Pernyataan Isi Standar</label><div class="public-content-box"><?= $dokumen['pernyataan_isi_standar']; ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Indikator Ketercapaian</label><div class="public-content-box"><?= $dokumen['indikator_ketercapaian']; ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Strategi Pencapaian</label><div class="public-content-box"><?= $dokumen['strategi_pencapaian']; ?></div></div>
            <div class="mb-4"><label class="form-label fw-bold">Dokumen Terkait</label><div class="public-content-box"><?= $dokumen['dokumen_terkait']; ?></div></div>
            <div class="mb-0"><label class="form-label fw-bold">Referensi</label><div class="public-content-box"><?= $dokumen['referensi']; ?></div></div>
        </div>
    </div>
</div>

<style>
    .dokumen-standar-wrap {
        max-width: 1080px;
        margin: 0 auto;
    }

    .public-section-title {
        margin: 0 0 14px;
        color: #1f2937;
        font-size: 1rem;
        font-weight: 700;
    }

    .table-proses-wrap {
        border: 1px solid #e3ebf8;
        border-radius: 14px;
    }

    .table-proses thead th {
        background: #f8fbff;
        color: #334155;
        font-weight: 700;
    }

    .public-content-box {
        border: 1px solid #dbe6f3;
        border-radius: 12px;
        padding: 0.9rem 1rem;
        background: #f8fbff;
        color: #1e293b;
    }
</style>

<?= $this->endSection(); ?>
