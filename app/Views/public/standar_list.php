<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<div class="public-title-hero">
    <span class="public-title-badge"><i class="bi bi-award-fill"></i> Informasi Publik</span>
    <h1 class="public-title-main"><?= esc($pageTitle ?? 'Standar Mutu Terbit'); ?></h1>
    <p class="public-title-sub"><?= esc($pageDesc ?? ''); ?></p>
</div>

<div class="card card-clean mb-3">
    <div class="card-body p-3">
        <form action="" method="get" class="row g-3 align-items-end">
            <div class="col-lg-4 col-md-12">
                <label class="form-label">Cari Dokumen</label>
                <input type="text" name="keyword" value="<?= esc($keyword ?? ''); ?>" class="form-control" placeholder="Ketik nomor atau judul dokumen...">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Filter Jenis</label>
                <select name="jenis_standar_id" class="form-select">
                    <option value="">Semua Jenis</option>
                    <?php foreach (($opsiJenis ?? []) as $jenis): ?>
                        <option value="<?= esc((string) $jenis['id']); ?>" <?= (int) ($jenisStandarAktif ?? 0) === (int) $jenis['id'] ? 'selected' : ''; ?>>
                            <?= esc($jenis['nama_jenis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Filter Kategori</label>
                <select name="kategori_standar_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach (($opsiKategori ?? []) as $kategori): ?>
                        <option value="<?= esc((string) $kategori['id']); ?>" <?= (int) ($kategoriStandarAktif ?? 0) === (int) $kategori['id'] ? 'selected' : ''; ?>>
                            <?= esc($kategori['nama_kategori']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                <a href="<?= current_url(); ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-clean">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:70px;">No.</th>
                        <th style="min-width:140px;">Nomor</th>
                        <th style="min-width:240px;">Judul Dokumen</th>
                        <th style="min-width:180px;">Jenis Standar</th>
                        <th style="min-width:180px;">Kategori</th>
                        <th style="min-width:170px;">Terakhir Diupdate</th>
                        <th style="width:90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($rows)): ?>
                        <?php
                        $currentPage = ! empty($pager) ? (int) $pager->getCurrentPage('publik_standar') : 1;
                        $startNo = ($currentPage - 1) * (int) ($perPage ?? 15);
                        ?>
                        <?php foreach ($rows as $i => $row): ?>
                            <tr>
                                <td><?= $startNo + $i + 1; ?></td>
                                <td><?= esc($row['nomor'] ?? '-'); ?></td>
                                <td><?= esc($row['judul'] ?? '-'); ?></td>
                                <td><?= esc($row['jenis'] ?? '-'); ?></td>
                                <td><?= esc($row['kategori'] ?? '-'); ?></td>
                                <td><?= esc($row['updated_label'] ?? '-'); ?></td>
                                <td>
                                    <?php if (! empty($row['action_url'])): ?>
                                        <a
                                            href="<?= esc($row['action_url']); ?>"
                                            class="action-icon-btn action-view"
                                            data-bs-toggle="tooltip"
                                            title="Lihat Standar"
                                            aria-label="Lihat Standar"
                                        >
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada dokumen standar terbit.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                Menampilkan <?= count($rows ?? []); ?> data pada halaman ini
                <?php if (! empty($pager)): ?>
                    (Halaman <?= (int) $pager->getCurrentPage('publik_standar'); ?> dari <?= (int) $pager->getPageCount('publik_standar'); ?>)
                <?php endif; ?>
            </small>
            <?php if (! empty($pager)): ?>
                <?= $pager->only(['keyword', 'jenis_standar_id', 'kategori_standar_id'])->links('publik_standar', 'default_full'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
