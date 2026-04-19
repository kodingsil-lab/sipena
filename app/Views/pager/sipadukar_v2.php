<?php
/**
 * Pager template style SIPADUKAR v2.
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 * @var string $pagerGroup
 */
$pager->setSurroundCount(2);
?>
<nav aria-label="Navigasi Halaman">
    <ul class="pagination justify-content-start sipadukar-pagination mb-0">
        <?php if ($pager->hasPreviousPage()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= esc($pager->getPreviousPage()); ?>">Sebelumnya</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">Sebelumnya</span>
            </li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : ''; ?>">
                <a class="page-link" href="<?= esc($link['uri']); ?>">
                    <?= esc($link['title']); ?>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNextPage()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= esc($pager->getNextPage()); ?>">Berikutnya</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">Berikutnya</span>
            </li>
        <?php endif; ?>
    </ul>
</nav>
