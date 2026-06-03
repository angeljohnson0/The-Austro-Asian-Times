<?php
class TagController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        $tagName = trim($_GET['name'] ?? '');

        if ($tagName === '') {
            header('Location: index.php');
            exit;
        }

        $tagModel = new Tag($this->db);
        $articles = $tagModel->getArticlesByTag($tagName);

        foreach ($articles as &$article) {
            $article['tags'] = $tagModel->getByArticle($article['id']);
        }
        unset($article);

        $pageTitle = 'Tag: ' . htmlspecialchars($tagName);
        require 'views/layout/header.php';
        require 'views/tag/index.php';
        require 'views/layout/footer.php';
    }
}
