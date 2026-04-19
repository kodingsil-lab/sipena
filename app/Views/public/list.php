<?= $this->extend('layouts/app'); ?>

<?= $this->section('content'); ?>

<style>
    .public-list-wrap {
        width: 100%;
        display: grid;
        gap: 14px;
    }
</style>

<div class="public-list-wrap">
    <div class="public-title-hero">
        <span class="public-title-badge"><i class="bi bi-info-circle-fill"></i> Informasi Publik</span>
        <h1 class="public-title-main"><?= esc($pageTitle ?? 'Dokumen Publik'); ?></h1>
        <p class="public-title-sub"><?= esc($pageDesc ?? ''); ?></p>
    </div>

    <div class="card card-clean">
        <div class="card-body p-3">
            <form action="" method="get" class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Cari Dokumen</label>
                    <input type="text" name="keyword" value="<?= esc($keyword ?? ''); ?>" class="form-control" placeholder="Ketik nomor atau judul dokumen...">
                </div>
                <div class="col-md-4 d-flex gap-2 justify-content-md-end">
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
                            <th style="min-width:160px;">Nomor</th>
                            <th style="min-width:320px;">Judul Dokumen</th>
                            <th style="min-width:170px;">Terakhir Diupdate</th>
                            <th style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($rows)): ?>
                            <?php
                            $currentPage = ! empty($pager) ? (int) $pager->getCurrentPage('publik_list') : 1;
                            $startNo = ($currentPage - 1) * (int) ($perPage ?? 15);
                            ?>
                            <?php foreach ($rows as $i => $row): ?>
                                <tr>
                                    <td><?= $startNo + $i + 1; ?></td>
                                    <td><?= esc($row['nomor'] ?? '-'); ?></td>
                                    <td><?= esc($row['judul'] ?? '-'); ?></td>
                                    <td><?= esc($row['updated_label'] ?? '-'); ?></td>
                                    <td>
                                        <?php if (! empty($row['action_url'])): ?>
                                            <?php $isDetail = ! empty($row['is_detail']); ?>
                                            <a
                                                href="<?= esc($row['action_url']); ?>"
                                                class="action-icon-btn <?= $isDetail ? 'action-view' : 'action-doc'; ?>"
                                                data-bs-toggle="tooltip"
                                                title="<?= esc($row['action_label'] ?? ($isDetail ? 'Lihat Standar' : 'Unduh')); ?>"
                                                aria-label="<?= esc($row['action_label'] ?? ($isDetail ? 'Lihat Standar' : 'Unduh')); ?>"
                                                <?= $isDetail ? '' : 'download target="_blank" rel="noopener"'; ?>
                                            >
                                                <i class="bi <?= esc($row['action_icon'] ?? ($isDetail ? 'bi-eye-fill' : 'bi-download')); ?>"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada dokumen terbit.</td>
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
                        (Halaman <?= (int) $pager->getCurrentPage('publik_list'); ?> dari <?= (int) $pager->getPageCount('publik_list'); ?>)
                    <?php endif; ?>
                </small>
                <?php if (! empty($pager)): ?>
                    <?= $pager->only(['keyword'])->links('publik_list', 'default_full'); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
