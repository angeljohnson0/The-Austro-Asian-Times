<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'The Austro-Asian Times') ?></title>
    <link rel="stylesheet" href="public/css/base.css">
    <link rel="stylesheet" href="public/css/layout.css">
    <link rel="stylesheet" href="public/css/components.css">
    <link rel="stylesheet" href="public/css/articles.css">
    <link rel="stylesheet" href="public/css/pages.css">
    <link rel="alternate" type="application/rss+xml"
          title="The Austro-Asian Times RSS Feed" href="feed.php">
</head>
<body>

<!-- ============================================================
     TOPBAR  —  date only
     ============================================================ -->
<div class="topbar">
    <div class="container topbar-inner">
        <span><?= date('l, d F Y') ?></span>
        <a href="feed.php" class="topbar-rss">RSS</a>
    </div>
</div>

<!-- ============================================================
     MASTHEAD  —  brand + signed-in user on the right
     ============================================================ -->
<div class="masthead">
    <div class="container masthead-inner">

        <div class="masthead-brand">
            <a href="index.php" class="masthead-title">The Austro-Asian Times</a>
            <span class="masthead-tagline">News for Northern Australia &amp; Southeast Asia</span>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="masthead-user">
            <span class="masthead-user-label">Signed in as</span>
            <strong class="masthead-user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></strong>
            <span class="masthead-user-role"><?= ucfirst($_SESSION['role']) ?></span>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- ============================================================
     MAIN NAV  —  links + profile icon + hamburger for mobile
     ============================================================ -->
<nav class="main-nav" id="main-nav">
    <div class="container nav-inner">

        <div class="nav-links" id="nav-links">
            <a href="index.php">Home</a>
            <a href="index.php?route=archive">Archive</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'editor'): ?>
                    <a href="index.php?route=editor">Dashboard</a>
                <?php else: ?>
                    <a href="index.php?route=article&action=list">My Articles</a>
                    <a href="index.php?route=article&action=create">New Article</a>
                <?php endif; ?>
            <?php endif; ?>

            <span class="nav-sep"></span>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Profile icon beside Logout -->
                <a href="index.php?route=profile&id=<?= $_SESSION['user_id'] ?>"
                   class="nav-icon-link"
                   title="My Profile — <?= htmlspecialchars($_SESSION['full_name']) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </a>
                <a href="index.php?route=logout" class="nav-logout">Logout</a>
            <?php else: ?>
                <a href="index.php?route=login">Login</a>
            <?php endif; ?>
        </div>

        <!-- Hamburger — visible on mobile only -->
        <button class="nav-toggle"
                onclick="document.getElementById('main-nav').classList.toggle('nav-open')"
                aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>
</nav>

<main class="container">
