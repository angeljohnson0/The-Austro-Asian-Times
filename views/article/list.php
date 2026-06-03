<div class="page-header">
    <h1>My Articles</h1>
    <a href="index.php?route=article&action=create" class="btn btn-primary">+ New Article</a>
</div>

<?php if (empty($articles)): ?>
    <div class="empty-state">You have not written any articles yet.</div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($articles as $a): ?>
                <tr>
                    <td>
                        <a href="index.php?route=article&action=show&id=<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['title']) ?>
                        </a>
                        <?php if ($a['status'] === 'rejected' && !empty($a['rejection_note'])): ?>
                            <div class="alert alert-error mt-8 mb-0">
                                <strong>Rejection note:</strong>
                                <?= htmlspecialchars($a['rejection_note']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="status status-<?= $a['status'] ?>">
                            <?= ucfirst($a['status']) ?>
                        </span>
                    </td>
                    <td class="text-muted"><?= date('d M Y H:i', strtotime($a['updated_at'])) ?></td>
                    <td class="actions">
                        <?php if (in_array($a['status'], ['draft', 'rejected'], true)): ?>
                            <a href="index.php?route=article&action=edit&id=<?= $a['id'] ?>"
                               class="btn btn-secondary btn-sm">Edit</a>
                            <a href="index.php?route=article&action=submit&id=<?= $a['id'] ?>&csrf_token=<?= csrfToken() ?>"
                               class="btn btn-success btn-sm"
                               onclick="return confirm('Submit this article for review?')">Submit</a>
                        <?php endif; ?>
                        <a href="index.php?route=article&action=delete&id=<?= $a['id'] ?>&csrf_token=<?= csrfToken() ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this article permanently?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
