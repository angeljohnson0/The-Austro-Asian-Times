<h1>Search Articles</h1>

<!-- ============================================================
     SEARCH FORM
     ============================================================ -->
<div class="search-panel">
    <form method="GET" action="index.php">
        <input type="hidden" name="route" value="search">
        <div class="search-row">

            <div class="search-field search-field-lg">
                <label for="sq">Global Search</label>
                <input type="text" id="sq" name="q"
                       placeholder="Keywords in title or body..."
                       value="<?= htmlspecialchars($keyword) ?>">
            </div>

            <div class="search-field">
                <label for="stag">Tag Filter</label>
                <input type="text" id="stag" name="tag"
                       placeholder="e.g. darwin, trade..."
                       value="<?= htmlspecialchars($tag) ?>">
            </div>

            <div class="search-field search-field-sm">
                <label for="sfrom">From Date</label>
                <input type="date" id="sfrom" name="from"
                       value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div class="search-field search-field-sm">
                <label for="sto">To Date</label>
                <input type="date" id="sto" name="to"
                       value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <div class="search-submit">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>

        </div>
    </form>
</div>

<!-- ============================================================
     RESULTS
     ============================================================ -->
<?php if (!$searched): ?>
    <div class="search-prompt">
        <p>Use the filters above to search for articles. You can combine multiple filters.</p>
        <ul>
            <li><strong>Global Search</strong> — finds articles whose title or body contains your keywords</li>
            <li><strong>Tag Filter</strong> — narrows results to articles carrying a specific tag</li>
            <li><strong>Date Range</strong> — limits results to articles updated within the selected period</li>
        </ul>
    </div>
<?php elseif (empty($articles)): ?>
    <div class="empty-state">
        No articles matched your search. Try broader keywords or a wider date range.
    </div>
<?php else: ?>
    <p class="search-count">
        Found <strong><?= count($articles) ?></strong>
        article<?= count($articles) !== 1 ? 's' : '' ?>
        <?php if ($keyword !== ''): ?>
            for <em>"<?= htmlspecialchars($keyword) ?>"</em>
        <?php endif; ?>
        <?php if ($tag !== ''): ?>
            tagged <em><?= htmlspecialchars($tag) ?></em>
        <?php endif; ?>
        <?php if ($dateFrom !== '' || $dateTo !== ''): ?>
            <?php if ($dateFrom !== '' && $dateTo !== ''): ?>
                between <?= htmlspecialchars(date('d M Y', strtotime($dateFrom))) ?>
                and <?= htmlspecialchars(date('d M Y', strtotime($dateTo))) ?>
            <?php elseif ($dateFrom !== ''): ?>
                from <?= htmlspecialchars(date('d M Y', strtotime($dateFrom))) ?>
            <?php else: ?>
                up to <?= htmlspecialchars(date('d M Y', strtotime($dateTo))) ?>
            <?php endif; ?>
        <?php endif; ?>
    </p>

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
                <?php foreach ($article['tags'] as $t): ?>
                    <a href="index.php?route=tag&name=<?= urlencode($t['name']) ?>"
                       class="tag"><?= htmlspecialchars($t['name']) ?></a>
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
