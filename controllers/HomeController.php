<?php
class HomeController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        $articleModel = new Article($this->db);
        $tagModel     = new Tag($this->db);

        $articles = $articleModel->getTopFive();

        foreach ($articles as &$article) {
            $article['tags'] = $tagModel->getByArticle($article['id']);
        }
        unset($article);

        $pageTitle = 'The Austro-Asian Times';
        require 'views/layout/header.php';
        require 'views/home/index.php';
        require 'views/layout/footer.php';
    }
}
