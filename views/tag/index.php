<div class="page-header">
    <h1>Tag: <em><?= htmlspecialchars($tagName) ?></em></h1>
    <a href="index.php" class="btn btn-outline btn-sm">&larr; Back to Home</a>
</div>

<?php if (empty($articles)): ?>
    <div class="empty-state">No published articles found with this tag.</div>
<?php else: ?>
    <div class="article-list">
        <?php foreach ($articles as $article): ?>
        <article class="article-list-card">

            <?php if ($article['image_path']): ?>
                <img src="uploads/<?= htmlspecialchars($article['image_path']) ?>"
                     alt="Article image" class="article-thumb">
            <?php endif; ?>

            <h2>
                <a href="index.php?route=article&action=show&id=<?= $article['id'] ?>">
                    <?= htmlspecialchars($article['title']) ?>
                </a>
            </h2>

            <p class="meta">
                By <a href="index.php?route=profile&id=<?= $article['author_id'] ?>">
                    <?= htmlspecialchars($article['author_name']) ?>
                </a>
                <span class="meta-divider">|</span>
                <?= date('d M Y', strtotime($article['updated_at'])) ?>
                <span class="meta-divider">|</span>
                Updated <?= timeAgo($article['updated_at']) ?>
            </p>

            <?php if (!empty($article['tags'])): ?>
            <div class="tags">
                <?php foreach ($article['tags'] as $tag): ?>
                    <a href="index.php?route=tag&name=<?= urlencode($tag['name']) ?>"
                       class="tag"><?= htmlspecialchars($tag['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <p class="excerpt">
                <?= htmlspecialchars(mb_substr(strip_tags($article['body']), 0, 220)) ?>...
            </p>

        </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
