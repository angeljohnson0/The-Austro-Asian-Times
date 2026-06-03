<?php
class AuthController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function login(): void
    {
        // Redirect already-logged-in users away from the login page.
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid request. Please try again.';
            } else {
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';

                $userModel = new User($this->db);
                $user      = $userModel->findByUsername($username);

                if ($user && password_verify($password, $user['password_hash'])) {
                    // Regenerate session ID to prevent session fixation attacks.
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['role']      = $user['role'];
                    $_SESSION['full_name'] = $user['full_name'];

                    if ($user['role'] === 'editor') {
                        header('Location: index.php?route=editor');
                    } else {
                        header('Location: index.php?route=article&action=list');
                    }
                    exit;
                }

                $error = 'Incorrect username or password.';
            }
        }

        $pageTitle = 'Login';
        require 'views/layout/header.php';
        require 'views/auth/login.php';
        require 'views/layout/footer.php';
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
