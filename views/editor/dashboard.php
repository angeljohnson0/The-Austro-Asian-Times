<h1>Editor Dashboard</h1>

<!-- ==================== PENDING ARTICLES ==================== -->
<p class="section-label">Articles Pending Review (<?= count($pendingArticles) ?>)</p>

<?php if (empty($pendingArticles)): ?>
    <div class="empty-state">No articles are currently awaiting review.</div>
<?php else: ?>
    <?php foreach ($pendingArticles as $a): ?>
    <div class="pending-card">

        <h3>
            <a href="index.php?route=article&action=show&id=<?= $a['id'] ?>">
                <?= htmlspecialchars($a['title']) ?>
            </a>
        </h3>

        <p class="meta">
            By <?= htmlspecialchars($a['author_name']) ?>
            <span class="meta-divider">|</span>
            Submitted <?= date('d M Y, H:i', strtotime($a['updated_at'])) ?>
        </p>

        <p class="excerpt">
            <?= htmlspecialchars(mb_substr(strip_tags($a['body']), 0, 200)) ?>...
        </p>

        <div class="moderation-actions">
            <a href="index.php?route=editor&action=approve&id=<?= $a['id'] ?>&csrf_token=<?= csrfToken() ?>"
               class="btn btn-success btn-sm"
               onclick="return confirm('Approve and publish this article?')">
                &#10003;&nbsp; Approve
            </a>

            <form method="POST" action="index.php?route=editor&action=reject"
                  class="reject-form">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <input type="text" name="rejection_note"
                       placeholder="Enter rejection reason..." required>
                <button type="submit" class="btn btn-danger btn-sm">
                    &#10007;&nbsp; Reject
                </button>
            </form>
        </div>

    </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- ==================== PENDING COMMENTS ==================== -->
<p class="section-divider mt-32">
    Comments Pending Approval (<?= count($pendingComments) ?>)
</p>

<?php if (empty($pendingComments)): ?>
    <div class="empty-state">No comments are awaiting approval.</div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>From</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($pendingComments as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['article_title']) ?></td>
                    <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                    <td class="text-muted">
                        <?= htmlspecialchars(mb_substr($c['body'], 0, 90)) ?>...
                    </td>
                    <td class="text-muted" style="white-space:nowrap">
                        <?= date('d M Y', strtotime($c['created_at'])) ?>
                    </td>
                    <td class="actions">
                        <a href="index.php?route=comment&action=approve&id=<?= $c['id'] ?>&csrf_token=<?= csrfToken() ?>"
                           class="btn btn-success btn-sm">Approve</a>
                        <a href="index.php?route=comment&action=delete&id=<?= $c['id'] ?>&csrf_token=<?= csrfToken() ?>"
                           class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
