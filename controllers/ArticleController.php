<?php
class ArticleController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=login');
            exit;
        }
    }

    // Lists articles belonging to the logged-in journalist.
    public function list(): void
    {
        $this->requireLogin();

        $articleModel = new Article($this->db);
        $articles     = $articleModel->getByAuthor($_SESSION['user_id']);

        $pageTitle = 'My Articles';
        require 'views/layout/header.php';
        require 'views/article/list.php';
        require 'views/layout/footer.php';
    }

    public function show(): void
    {
        $id           = (int) ($_GET['id'] ?? 0);
        $articleModel = new Article($this->db);
        $article      = $articleModel->findById($id);

        if (!$article) {
            http_response_code(404);
            require 'views/layout/header.php';
            echo '<p class="alert alert-error">Article not found.</p>';
            require 'views/layout/footer.php';
            return;
        }

        // Non-published articles are only visible to their author or the editor.
        if ($article['status'] !== 'published') {
            $this->requireLogin();
            if ($_SESSION['role'] !== 'editor' && (int) $_SESSION['user_id'] !== (int) $article['author_id']) {
                http_response_code(403);
                require 'views/layout/header.php';
                echo '<p class="alert alert-error">Access denied.</p>';
                require 'views/layout/footer.php';
                return;
            }
        }

        $tagModel     = new Tag($this->db);
        $commentModel = new Comment($this->db);
        $historyModel = new ArticleHistory($this->db);

        $tags    = $tagModel->getByArticle($id);
        $comments = $commentModel->getApprovedByArticle($id);

        $pendingComments = [];
        $history         = [];

        $canModerate = isset($_SESSION['user_id']) &&
            ($_SESSION['role'] === 'editor' || (int) $_SESSION['user_id'] === (int) $article['author_id']);

        if ($canModerate) {
            $pendingComments = $commentModel->getPendingByArticle($id);
            $history         = $historyModel->getByArticle($id);
        }

        $pageTitle = htmlspecialchars($article['title']);
        require 'views/layout/header.php';
        require 'views/article/show.php';
        require 'views/layout/footer.php';
    }

    public function create(): void
    {
        $this->requireLogin();

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid request. Please try again.';
            } else {
                $title      = trim($_POST['title'] ?? '');
                $body       = trim($_POST['body']  ?? '');
                $tags       = trim($_POST['tags']  ?? '');
                $actionType = $_POST['action_type'] ?? 'draft';

                if ($title === '' || $body === '') {
                    $error = 'Title and body are required.';
                } else {
                    $imagePath = null;

                    if (!empty($_FILES['image']['name'])) {
                        $imagePath = $this->handleImageUpload($_FILES['image'], $error);
                    }

                    if ($error === '') {
                        $articleModel = new Article($this->db);
                        $tagModel     = new Tag($this->db);

                        $articleId = $articleModel->create($_SESSION['user_id'], $title, $body, $imagePath);
                        $tagModel->syncTags($articleId, $tags);

                        if ($actionType === 'submit') {
                            $articleModel->submitForReview($articleId);
                            (new ArticleHistory($this->db))->add($articleId, $_SESSION['user_id'], 'submitted');
                        }

                        header('Location: index.php?route=article&action=list');
                        exit;
                    }
                }
            }
        }

        $pageTitle = 'Create Article';
        require 'views/layout/header.php';
        require 'views/article/create.php';
        require 'views/layout/footer.php';
    }

    public function edit(): void
    {
        $this->requireLogin();

        $id           = (int) ($_GET['id'] ?? 0);
        $articleModel = new Article($this->db);
        $tagModel     = new Tag($this->db);
        $article      = $articleModel->findById($id);

        // Only the author or the editor may edit an article.
        if (!$article ||
            ((int) $article['author_id'] !== (int) $_SESSION['user_id'] && $_SESSION['role'] !== 'editor')
        ) {
            http_response_code(403);
            require 'views/layout/header.php';
            echo '<p class="alert alert-error">Access denied.</p>';
            require 'views/layout/footer.php';
            return;
        }

        $error       = '';
        $currentTags = $tagModel->getTagsAsString($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid request. Please try again.';
            } else {
                $title      = trim($_POST['title'] ?? '');
                $body       = trim($_POST['body']  ?? '');
                $tags       = trim($_POST['tags']  ?? '');
                $actionType = $_POST['action_type'] ?? 'draft';

                if ($title === '' || $body === '') {
                    $error = 'Title and body are required.';
                } else {
                    $imagePath = null;

                    if (!empty($_FILES['image']['name'])) {
                        $imagePath = $this->handleImageUpload($_FILES['image'], $error);
                    }

                    if ($error === '') {
                        $prevStatus = $article['status'];
                        $articleModel->update($id, $title, $body, $imagePath);
                        $tagModel->syncTags($id, $tags);

                        if ($actionType === 'submit') {
                            $articleModel->submitForReview($id);
                            $action = ($prevStatus === 'rejected') ? 'resubmitted' : 'submitted';
                            (new ArticleHistory($this->db))->add($id, $_SESSION['user_id'], $action);
                        }

                        header('Location: index.php?route=article&action=list');
                        exit;
                    }
                }
            }
        }

        $pageTitle = 'Edit Article';
        require 'views/layout/header.php';
        require 'views/article/edit.php';
        require 'views/layout/footer.php';
    }

    public function submit(): void
    {
        $this->requireLogin();

        if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            return;
        }

        $id           = (int) ($_GET['id'] ?? 0);
        $articleModel = new Article($this->db);
        $article      = $articleModel->findById($id);

        if (!$article || (int) $article['author_id'] !== (int) $_SESSION['user_id']) {
            http_response_code(403);
            echo 'Access denied.';
            return;
        }

        $prevStatus = $article['status'];
        $articleModel->submitForReview($id);
        $action = ($prevStatus === 'rejected') ? 'resubmitted' : 'submitted';
        (new ArticleHistory($this->db))->add($id, $_SESSION['user_id'], $action);
        header('Location: index.php?route=article&action=list');
        exit;
    }

    public function delete(): void
    {
        $this->requireLogin();

        if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            return;
        }

        $id           = (int) ($_GET['id'] ?? 0);
        $articleModel = new Article($this->db);
        $article      = $articleModel->findById($id);

        if (!$article ||
            ((int) $article['author_id'] !== (int) $_SESSION['user_id'] && $_SESSION['role'] !== 'editor')
        ) {
            http_response_code(403);
            echo 'Access denied.';
            return;
        }

        // Remove the associated image file if one exists.
        if ($article['image_path'] && file_exists('uploads/' . $article['image_path'])) {
            unlink('uploads/' . $article['image_path']);
        }

        $articleModel->delete($id);
        header('Location: index.php?route=article&action=list');
        exit;
    }

    // Validates and moves an uploaded image; returns the stored filename or null on failure.
    private function handleImageUpload(array $file, string &$error): ?string
    {
        $allowedExts  = ['jpg', 'jpeg', 'png', 'gif'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxBytes     = 2 * 1024 * 1024;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts, true)) {
            $error = 'Only JPEG, PNG and GIF images are allowed.';
            return null;
        }

        if ($file['size'] > $maxBytes) {
            $error = 'Image must be 2 MB or smaller.';
            return null;
        }

        // Verify real MIME type rather than trusting the file extension.
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes, true)) {
            $error = 'Invalid image file.';
            return null;
        }

        // Random filename prevents directory enumeration.
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], 'uploads/' . $filename)) {
            $error = 'Image upload failed. Check server permissions.';
            return null;
        }

        return $filename;
    }
}
