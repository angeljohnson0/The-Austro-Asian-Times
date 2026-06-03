<h1>Sign In</h1>

<div class="form-card">

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?route=login">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   placeholder="Enter your username"
                   required autocomplete="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Enter your password"
                   required autocomplete="current-password">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">Sign In</button>
            <a href="index.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>

</div>
