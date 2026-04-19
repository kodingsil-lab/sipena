<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="dokumen-standar-wrap">
<div class="page-header">
    <h1 class="page-title"><?= esc($pageTitle ?? 'Form Dokumen Standar'); ?></h1>
    <p class="page-subtitle"><?= esc($pageDesc ?? ''); ?></p>
    <div class="text-muted small mt-1">
        <strong><?= esc($standar['kode_standar'] ?? '-'); ?></strong> - <?= esc($standar['nama_standar'] ?? '-'); ?>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
<?php endif; ?>

<?php
$indikatorRaw = old('indikator_ketercapaian', $dokumen['indikator_ketercapaian'] ?? '');

if (is_string($indikatorRaw) && $indikatorRaw !== '') {
    $decodedIndikator = json_decode($indikatorRaw, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedIndikator)) {
        $indikatorItems = [];

        foreach ($decodedIndikator as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $indikatorItems[] = $item;
            }
        }

        if ($indikatorItems !== []) {
            $indikatorRaw = '<ol>';
            foreach ($indikatorItems as $item) {
                $indikatorRaw .= '<li>' . esc($item) . '</li>';
            }
            $indikatorRaw .= '</ol>';
        }
    }
}
?>

<div class="card card-clean">
    <div class="card-body p-4">
        <form action="<?= esc($action); ?>" method="post">
            <?= csrf_field(); ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal_dokumen" class="form-control" value="<?= esc(old('tanggal_dokumen', $dokumen['tanggal_dokumen'] ?? '')); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Revisi</label>
                    <input type="text" name="revisi" class="form-control" value="<?= esc(old('revisi', $dokumen['revisi'] ?? '')); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Halaman</label>
                    <input type="text" name="halaman" class="form-control" value="<?= esc(old('halaman', $dokumen['halaman'] ?? '')); ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">Rasional Standar</label>
                    <textarea name="rasional" class="form-control" rows="5"><?= esc(old('rasional', $dokumen['rasional'] ?? '')); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Subjek / Pihak yang Bertanggung Jawab</label>
                    <textarea name="subjek_bertanggung_jawab" class="form-control" rows="4"><?= esc(old('subjek_bertanggung_jawab', $dokumen['subjek_bertanggung_jawab'] ?? '')); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Definisi Istilah</label>
                    <textarea name="definisi_istilah" class="form-control" rows="5"><?= esc(old('definisi_istilah', $dokumen['definisi_istilah'] ?? '')); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Pernyataan Isi Standar</label>
                    <textarea name="pernyataan_isi_standar" class="form-control" rows="6"><?= esc(old('pernyataan_isi_standar', $dokumen['pernyataan_isi_standar'] ?? '')); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Indikator Ketercapaian</label>
                    <textarea name="indikator_ketercapaian" class="form-control" rows="6"><?= esc($indikatorRaw); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Strategi Pencapaian</label>
                    <textarea name="strategi_pencapaian" class="form-control" rows="5"><?= esc(old('strategi_pencapaian', $dokumen['strategi_pencapaian'] ?? '')); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Dokumen Terkait</label>
                    <textarea name="dokumen_terkait" class="form-control" rows="4"><?= esc(old('dokumen_terkait', $dokumen['dokumen_terkait'] ?? '')); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Referensi</label>
                    <textarea name="referensi" class="form-control" rows="6"><?= esc(old('referensi', $dokumen['referensi'] ?? '')); ?></textarea>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
                <a href="<?= base_url('/standar-mutu'); ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea',
        plugins: 'lists advlist',
        height: 300,
        menubar: false,
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | removeformat',
        advlist_number_styles: 'default,lower-alpha,lower-roman,upper-alpha,upper-roman',
        advlist_bullet_styles: 'default,circle,square',
        lists_indent_on_tab: true,
        content_style: 'ol{margin:0 0 6px 0;padding-left:20px;} ol ol{list-style-type:lower-alpha;padding-left:20px;} ol ol ol{list-style-type:lower-roman;padding-left:18px;} li{margin:0 0 4px 0;}',
        entity_encoding: 'raw'
    });
</script>
</div>

<style>
    .dokumen-standar-wrap {
        max-width: 1080px;
        margin: 0 auto;
    }
</style>

<?= $this->endSection(); ?>
