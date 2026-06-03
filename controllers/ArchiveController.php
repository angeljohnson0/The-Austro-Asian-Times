<?php
class ArchiveController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        $articleModel = new Article($this->db);
        $rawArticles  = $articleModel->getArchive();

        // Group articles by month for the archive view.
        $grouped = [];
        foreach ($rawArticles as $article) {
            $key = $article['month_key'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['label' => $article['month_label'], 'articles' => []];
            }
            $grouped[$key]['articles'][] = $article;
        }

        $pageTitle = 'Archive';
        require 'views/layout/header.php';
        require 'views/archive/index.php';
        require 'views/layout/footer.php';
    }
}
