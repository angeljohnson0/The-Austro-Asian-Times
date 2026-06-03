<article class="article-single">

    <h1><?= htmlspecialchars($article['title']) ?></h1>

    <p class="meta">
        By <a href="index.php?route=profile&id=<?= $article['author_id'] ?>">
            <?= htmlspecialchars($article['author_name']) ?>
        </a>
        <span class="meta-divider">|</span>
        <?= date('d M Y, H:i', strtotime($article['updated_at'])) ?>
        <span class="meta-divider">|</span>
        Updated <?= timeAgo($article['updated_at']) ?>
    </p>

    <?php if (!empty($tags)): ?>
    <div class="tags">
        <?php foreach ($tags as $tag): ?>
            <a href="index.php?route=tag&name=<?= urlencode($tag['name']) ?>"
               class="tag"><?= htmlspecialchars($tag['name']) ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($article['image_path']): ?>
        <img src="uploads/<?= htmlspecialchars($article['image_path']) ?>"
             alt="Article image" class="article-image">
    <?php endif; ?>

    <div class="article-body">
        <?= nl2br(htmlspecialchars($article['body'])) ?>
    </div>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'editor'): ?>
    <div class="editor-controls">
        <strong>Comments:</strong>
        <?php if ($article['comments_enabled']): ?>
            <a href="index.php?route=editor&action=toggleComments&id=<?= $article['id'] ?>&enabled=0&csrf_token=<?= csrfToken() ?>"
               class="btn btn-secondary btn-sm">Disable</a>
        <?php else: ?>
            <a href="index.php?route=editor&action=toggleComments&id=<?= $article['id'] ?>&enabled=1&csrf_token=<?= csrfToken() ?>"
               class="btn btn-success btn-sm">Enable</a>
        <?php endif; ?>
        <a href="index.php?route=editor" class="btn btn-outline btn-sm">&larr; Dashboard</a>
    </div>
    <?php endif; ?>

</article>

<?php if (!empty($pendingComments)): ?>
<section class="comments-section">
    <h3>Pending Comments (<?= count($pendingComments) ?>)</h3>

    <?php foreach ($pendingComments as $c): ?>
    <div class="comment comment-pending">
        <p class="comment-meta">
            <strong><?= htmlspecialchars($c['name']) ?></strong>
            on <?= date('d M Y, H:i', strtotime($c['created_at'])) ?>
        </p>
        <p><?= nl2br(htmlspecialchars($c['body'])) ?></p>
        <div class="btn-group">
            <a href="index.php?route=comment&action=approve&id=<?= $c['id'] ?>&from=article&csrf_token=<?= csrfToken() ?>"
               class="btn btn-success btn-sm">Approve</a>
            <a href="index.php?route=comment&action=delete&id=<?= $c['id'] ?>&from=article&csrf_token=<?= csrfToken() ?>"
               class="btn btn-danger btn-sm">Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if ($article['comments_enabled'] && $article['status'] === 'published'): ?>
<section class="comments-section">

    <h3>Comments (<?= count($comments) ?>)</h3>

    <?php if (isset($_GET['comment']) && $_GET['comment'] === 'submitted'): ?>
        <div class="alert alert-success">
            Your comment has been submitted and is awaiting moderation. Thank you!
        </div>
    <?php endif; ?>

    <?php if (empty($comments)): ?>
        <p class="text-muted">No comments yet. Be the first to leave one below.</p>
    <?php else: ?>
        <?php foreach ($comments as $c): ?>
        <div class="comment">
            <p class="comment-meta">
                <strong><?= htmlspecialchars($c['name']) ?></strong>
                on <?= date('d M Y, H:i', strtotime($c['created_at'])) ?>
            </p>
            <p><?= nl2br(htmlspecialchars($c['body'])) ?></p>
            <?php if (isset($_SESSION['user_id']) &&
                      ($_SESSION['role'] === 'editor' ||
                       (int) $_SESSION['user_id'] === (int) $article['author_id'])): ?>
            <div class="btn-group">
                <a href="index.php?route=comment&action=delete&id=<?= $c['id'] ?>&from=article&csrf_token=<?= csrfToken() ?>"
                   class="btn btn-danger btn-sm">Delete</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="comment-form">
        <h4>Leave a Comment</h4>

        <?php if (isset($_GET['comment_error'])): ?>
            <div class="alert alert-error">
                <?php if ($_GET['comment_error'] === 'required'): ?>
                    All fields are required. Please fill in your name, email and comment.
                <?php elseif ($_GET['comment_error'] === 'email'): ?>
                    Please enter a valid email address.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?route=comment&action=submit">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">

            <div class="form-group">
                <label for="c_name">Name</label>
                <input type="text" id="c_name" name="name"
                       placeholder="Your full name" required>
            </div>

            <div class="form-group">
                <label for="c_email">Email</label>
                <input type="email" id="c_email" name="email"
                       placeholder="your@email.com" required>
                <span class="form-hint">Your email will not be published.</span>
            </div>

            <div class="form-group">
                <label for="c_body">Comment</label>
                <textarea id="c_body" name="body" rows="5"
                          placeholder="Write your comment here..." required></textarea>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Submit Comment</button>
                <span class="text-muted">Awaiting moderation before publishing.</span>
            </div>
        </form>
    </div>

</section>
<?php endif; ?>

<?php if ($canModerate): ?>
<section class="article-history">
    <h3>Article History</h3>

    <?php if (empty($history)): ?>
        <p class="text-muted" style="font-family:Arial,sans-serif;font-size:0.85rem">
            No workflow events recorded yet. History is logged when articles are submitted, reviewed, approved or rejected.
        </p>
    <?php else: ?>
    <div class="history-timeline">
        <?php foreach ($history as $h): ?>
        <div class="history-item">
            <span class="history-dot history-dot-<?= $h['action'] ?>"></span>
            <div class="history-content">
                <span class="history-action"><?= ucfirst($h['action']) ?></span>
                <span class="history-by">
                    by <strong><?= htmlspecialchars($h['actor_name']) ?></strong>
                    (<?= ucfirst($h['actor_role']) ?>)
                </span>
                <span class="history-time">
                    <?= date('d M Y, H:i', strtotime($h['created_at'])) ?>
                </span>
                <?php if (!empty($h['note'])): ?>
                    <span class="history-note"><?= htmlspecialchars($h['note']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>
