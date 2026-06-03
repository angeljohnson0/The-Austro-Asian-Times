<!-- ============================================================
     SEARCH TOGGLE
     ============================================================ -->
<div class="home-search-toggle">
    <button class="btn btn-secondary"
            onclick="var p=document.getElementById('home-search-panel');p.style.display=p.style.display==='none'?'block':'none';">
        &#128269; Search Articles
    </button>
</div>

<div id="home-search-panel" style="display:none;">
<div class="search-panel">
    <p class="search-panel-title">Search The Austro-Asian Times</p>
    <form method="GET" action="index.php">
        <input type="hidden" name="route" value="search">
        <div class="search-row">

            <div class="search-field search-field-lg">
                <label for="sq">Global Search</label>
                <input type="text" id="sq" name="q"
                       placeholder="Search by keyword in title or body...">
            </div>

            <div class="search-field">
                <label for="stag">Tag Filter</label>
                <input type="text" id="stag" name="tag"
                       placeholder="e.g. darwin, trade...">
            </div>

            <div class="search-field search-field-sm">
                <label for="sfrom">From Date</label>
                <input type="date" id="sfrom" name="from">
            </div>

            <div class="search-field search-field-sm">
                <label for="sto">To Date</label>
                <input type="date" id="sto" name="to">
            </div>

            <div class="search-submit">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>

        </div>
    </form>
</div>
</div>

<?php if (empty($articles)): ?>
    <div class="empty-state">No articles have been published yet.</div>
<?php else:
    $featured  = $articles[0];
    $secondary = array_slice($articles, 1);
?>

<!-- ============================================================
     FEATURED ARTICLE (most recent)
     ============================================================ -->
<p class="section-label">Top Story</p>

<article class="home-featured <?= $featured['image_path'] ? '' : 'no-image' ?>">

    <?php if ($featured['image_path']): ?>
        <img src="uploads/<?= htmlspecialchars($featured['image_path']) ?>"
             alt="Article image" class="home-featured-img">
    <?php else: ?>
        <div class="home-featured-img-placeholder"></div>
    <?php endif; ?>

    <div class="home-featured-body">

        <?php if (!empty($featured['tags'])): ?>
        <div class="tags">
            <?php foreach ($featured['tags'] as $tag): ?>
                <a href="index.php?route=tag&name=<?= urlencode($tag['name']) ?>"
                   class="tag"><?= htmlspecialchars($tag['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h2>
            <a href="index.php?route=article&action=show&id=<?= $featured['id'] ?>">
                <?= htmlspecialchars($featured['title']) ?>
            </a>
        </h2>

        <p class="meta">
            By <a href="index.php?route=profile&id=<?= $featured['author_id'] ?>">
                <?= htmlspecialchars($featured['author_name']) ?>
            </a>
            <span class="meta-divider">|</span>
            <?= date('d M Y', strtotime($featured['updated_at'])) ?>
            <span class="meta-divider">|</span>
            Updated <?= timeAgo($featured['updated_at']) ?>
        </p>

        <p class="excerpt">
            <?= htmlspecialchars(mb_substr(strip_tags($featured['body']), 0, 280)) ?>...
        </p>

        <a href="index.php?route=article&action=show&id=<?= $featured['id'] ?>"
           class="read-more">Read full story &rarr;</a>

    </div>
</article>

<!-- ============================================================
     SECONDARY ARTICLES (articles 2-5)
     ============================================================ -->
<?php if (!empty($secondary)): ?>
<p class="home-grid-label">More Stories</p>
<div class="article-grid">
    <?php foreach ($secondary as $article): ?>
    <article class="article-card">

        <?php if ($article['image_path']): ?>
            <img src="uploads/<?= htmlspecialchars($article['image_path']) ?>"
                 alt="Article image" class="article-card-img">
        <?php endif; ?>

        <div class="article-card-body">

            <?php if (!empty($article['tags'])): ?>
            <div class="tags">
                <?php foreach ($article['tags'] as $tag): ?>
                    <a href="index.php?route=tag&name=<?= urlencode($tag['name']) ?>"
                       class="tag"><?= htmlspecialchars($tag['name']) ?></a>
                <?php endforeach; ?>
            </div>
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
                <?= timeAgo($article['updated_at']) ?>
            </p>

            <p class="excerpt">
                <?= htmlspecialchars(mb_substr(strip_tags($article['body']), 0, 160)) ?>...
            </p>

        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php endif; ?>
