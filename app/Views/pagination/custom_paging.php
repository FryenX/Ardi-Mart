<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="First">
                    <span aria-hidden="true">First</span>
                </a>
            </li>
            <li>
                <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="Previous">
                    <span aria-hidden="true">Previous</span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="Next">
                    <span aria-hidden="true">Next</span>
                </a>
            </li>
            <li>
                <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Last">
                    <span aria-hidden="true">Last</span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>
