<h1>Create New Article</h1>

<?php if ($error !== ''): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="index.php?route=article&action=create"
      enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title"
               placeholder="Enter article title"
               required
               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="body">Body</label>
        <textarea id="body" name="body"
                  placeholder="Write your article content here..."
                  rows="16" required><?= htmlspecialchars($_POST['body'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="tags">Tags</label>
        <input type="text" id="tags" name="tags"
               placeholder="e.g. darwin, trade, economy (comma-separated)"
               value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>">
        <span class="form-hint">Separate multiple tags with commas. Tags help readers find related articles.</span>
    </div>

    <div class="form-group">
        <label for="image">Image <span class="form-hint" style="display:inline">(optional, JPEG / PNG / GIF, max 2 MB)</span></label>
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
