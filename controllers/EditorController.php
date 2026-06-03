<?php
class EditorController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private function requireEditor(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'editor') {
            header('Location: index.php');
            exit;
        }
    }

    public function dashboard(): void
    {
        $this->requireEditor();

        $articleModel    = new Article($this->db);
        $commentModel    = new Comment($this->db);

        $pendingArticles = $articleModel->getPending();
        $pendingComments = $commentModel->getAllPending();

        $pageTitle = 'Editor Dashboard';
        require 'views/layout/header.php';
        require 'views/editor/dashboard.php';
        require 'views/layout/footer.php';
    }

    public function approve(): void
    {
        $this->requireEditor();

        if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            return;
        }

        $id           = (int) ($_GET['id'] ?? 0);
        $articleModel = new Article($this->db);
        $articleModel->updateStatus($id, 'published');
        (new ArticleHistory($this->db))->add($id, $_SESSION['user_id'], 'approved');

        header('Location: index.php?route=editor');
        exit;
    }

    public function reject(): void
    {
        $this->requireEditor();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            return;
        }

        $id   = (int) ($_POST['id'] ?? 0);
        $note = trim($_POST['rejection_note'] ?? '');

        $articleModel = new Article($this->db);
        $articleModel->updateStatus($id, 'rejected', $note);
        (new ArticleHistory($this->db))->add($id, $_SESSION['user_id'], 'rejected', $note);

        header('Location: index.php?route=editor');
        exit;
    }

    public function toggleComments(): void
    {
        $this->requireEditor();

        if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            return;
        }

        $id      = (int) ($_GET['id']      ?? 0);
        $enabled = (int) ($_GET['enabled'] ?? 0);

        $articleModel = new Article($this->db);
        $articleModel->toggleComments($id, $enabled);

        header('Location: index.php?route=article&action=show&id=' . $id);
        exit;
    }
}
