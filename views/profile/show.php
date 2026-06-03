<div class="profile-banner">
    <h1><?= htmlspecialchars($profileUser['full_name']) ?></h1>
    <p class="meta">
        <?= ucfirst($profileUser['role']) ?> at The Austro-Asian Times,
        member since <?= date('Y', strtotime($profileUser['created_at'])) ?>
    </p>
    <?php if (!empty($profileUser['bio'])): ?>
        <p style="margin-top:12px; font-size:0.95rem; color:#444; line-height:1.7">
            <?= nl2br(htmlspecialchars($profileUser['bio'])) ?>
        </p>
    <?php endif; ?>
</div>

<p class="section-label">Published Articles (<?= count($articles) ?>)</p>

<?php if (empty($articles)): ?>
    <div class="empty-state">No published articles yet.</div>
<?php else: ?>
    <div class="archive-month">
        <ul>
            <?php foreach ($articles as $a): ?>
            <li>
                <a href="index.php?route=article&action=show&id=<?= $a['id'] ?>">
                    <?= htmlspecialchars($a['title']) ?>
                </a>
                <span class="text-muted"><?= date('d M Y', strtotime($a['updated_at'])) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
