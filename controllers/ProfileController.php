<?php
class ProfileController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function show(): void
    {
        // Default to the logged-in user's own profile when no id is given.
        $id = (int) ($_GET['id'] ?? $_SESSION['user_id'] ?? 0);

        $userModel   = new User($this->db);
        $profileUser = $userModel->findById($id);

        if (!$profileUser) {
            http_response_code(404);
            require 'views/layout/header.php';
            echo '<p class="alert alert-error">Profile not found.</p>';
            require 'views/layout/footer.php';
            return;
        }

        $articles  = $userModel->getPublishedArticles($id);
        $pageTitle = htmlspecialchars($profileUser['full_name']);

        require 'views/layout/header.php';
        require 'views/profile/show.php';
        require 'views/layout/footer.php';
    }
}
