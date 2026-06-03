<h1>Edit Article</h1>

<?php if (!empty($article['rejection_note']) && $article['status'] === 'rejected'): ?>
    <div class="alert alert-error">
        <div><strong>Rejection note:</strong> <?= htmlspecialchars($article['rejection_note']) ?></div>
    </div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST"
      action="index.php?route=article&action=edit&id=<?= $article['id'] ?>"
      enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title"
               placeholder="Enter article title"
               required
               value="<?= htmlspecialchars($_POST['title'] ?? $article['title']) ?>">
    </div>

    <div class="form-group">
        <label for="body">Body</label>
        <textarea id="body" name="body"
                  placeholder="Write your article content here..."
                  rows="16" required><?= htmlspecialchars($_POST['body'] ?? $article['body']) ?></textarea>
    </div>

    <div class="form-group">
        <label for="tags">Tags</label>
        <input type="text" id="tags" name="tags"
               placeholder="e.g. darwin, trade, economy (comma-separated)"
               value="<?= htmlspecialchars($_POST['tags'] ?? $currentTags) ?>">
        <span class="form-hint">Separate multiple tags with commas.</span>
    </div>

    <div class="form-group">
        <label for="image">Image <span class="form-hint" style="display:inline">(leave blank to keep existing)</span></label>
        <?php if ($article['image_path']): ?>
            <img src="uploads/<?= htmlspecialchars($article['image_path']) ?>"
                 alt="Current image" class="image-preview">
        <?php endif; ?>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif">
    </div>

    <div class="btn-group">
        <button type="submit" name="action_type" value="draft"
                class="btn btn-secondary">Save as Draft</button>
        <button type="submit" name="action_type" value="submit"
                class="btn btn-primary">Submit for Review</button>
        <a href="index.php?route=article&action=list"
           class="btn btn-outline">Cancel</a>
    </div>

</form>
