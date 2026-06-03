<?php
class SearchController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        $keyword  = trim($_GET['q']    ?? '');
        $tag      = trim($_GET['tag']  ?? '');
        $dateFrom = trim($_GET['from'] ?? '');
        $dateTo   = trim($_GET['to']   ?? '');

        $searched = ($keyword !== '' || $tag !== '' || $dateFrom !== '' || $dateTo !== '');
        $articles = [];

        if ($searched) {
            $articleModel = new Article($this->db);
            $tagModel     = new Tag($this->db);

            $articles = $articleModel->search($keyword, $tag, $dateFrom, $dateTo);

            foreach ($articles as &$article) {
                $article['tags'] = $tagModel->getByArticle($article['id']);
            }
            unset($article);
        }

        $pageTitle = 'Search';
        require 'views/layout/header.php';
        require 'views/search/results.php';
        require 'views/layout/footer.php';
    }
}
