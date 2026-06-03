<?php
class CommentController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            return;
        }

        $articleId = (int) ($_POST['article_id'] ?? 0);
        $name      = trim($_POST['name']  ?? '');
        $email     = trim($_POST['email'] ?? '');
        $body      = trim($_POST['body']  ?? '');

        $articleModel = new Article($this->db);
        $article      = $articleModel->findById($articleId);

        // Reject if article does not exist, is not published, or has comments disabled.
        if (!$article || $article['status'] !== 'published' || !$article['comments_enabled']) {
            header('Location: index.php');
            exit;
        }

        if ($name === '' || $email === '' || $body === '') {
            header('Location: index.php?route=article&action=show&id=' . $articleId . '&comment_error=required');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: index.php?route=article&action=show&id=' . $articleId . '&comment_error=email');
            exit;
        }

        $commentModel = new Comment($this->db);
        $commentModel->create($articleId, $name, $email, $body);

        header('Location: index.php?route=article&action=show&id=' . $articleId . '&comment=submitted');
        exit;
    }

    public function approve(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=login');
            exit;
        }

        if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            return;
        }

        $id           = (int) ($_GET['id'] ?? 0);
        $commentModel = new Comment($this->db);
        $comment      = $commentModel->findById($id);

        if (!$comment) {
            header('Location: index.php?route=editor');
            exit;
        }

        $articleModel = new Article($this->db);
        $article      = $articleModel->findById($comment['article_id']);

        if ($_SESSION['role'] !== 'editor' && (int) $_SESSION['user_id'] !== (int) $article['author_id']) {
            http_response_code(403);
            echo 'Access denied.';
            return;
        }

        $commentModel->approve($id);

        $from = $_GET['from'] ?? 'editor';
        if ($from === 'article') {
            header('Location: index.php?route=article&action=show&id=' . $comment['article_id']);
        } else {
            header('Location: index.php?route=editor');
        }
        exit;
    }

    public function delete(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=login');
            exit;
        }

        if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            return;
        }

        $id           = (int) ($_GET['id'] ?? 0);
        $commentModel = new Comment($this->db);
        $comment      = $commentModel->findById($id);

        if (!$comment) {
            header('Location: index.php?route=editor');
            exit;
        }

        $articleModel = new Article($this->db);
        $article      = $articleModel->findById($comment['article_id']);

        if ($_SESSION['role'] !== 'editor' && (int) $_SESSION['user_id'] !== (int) $article['author_id']) {
            http_response_code(403);
            echo 'Access denied.';
            return;
        }

        $commentModel->delete($id);

        $from = $_GET['from'] ?? 'editor';
        if ($from === 'article') {
            header('Location: index.php?route=article&action=show&id=' . $comment['article_id']);
        } else {
            header('Location: index.php?route=editor');
        }
        exit;
    }
}
