<h1>Article Archive</h1>

<?php if (empty($grouped)): ?>
    <div class="empty-state">No published articles yet.</div>
<?php else: ?>
    <?php foreach ($grouped as $monthData): ?>
    <div class="archive-month">
        <span class="archive-month-label"><?= htmlspecialchars($monthData['label']) ?></span>
        <ul>
            <?php foreach ($monthData['articles'] as $a): ?>
            <li>
                <a href="index.php?route=article&action=show&id=<?= $a['id'] ?>">
                    <?= htmlspecialchars($a['title']) ?>
                </a>
                <span class="text-muted">
                    <?= htmlspecialchars($a['author_name']) ?>
                    <?= date('d M Y', strtotime($a['updated_at'])) ?>
                </span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
