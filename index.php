<?php
// Front controller: starts the session, loads all classes, and dispatches requests.

session_start();

require_once 'config/database.php';
require_once 'helpers.php';
require_once 'models/User.php';
require_once 'models/Article.php';
require_once 'models/Tag.php';
require_once 'models/Comment.php';
require_once 'models/ArticleHistory.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/ArticleController.php';
require_once 'controllers/EditorController.php';
require_once 'controllers/CommentController.php';
require_once 'controllers/TagController.php';
require_once 'controllers/ArchiveController.php';
require_once 'controllers/ProfileController.php';
require_once 'controllers/SearchController.php';

$db    = Database::getInstance();
$route = $_GET['route'] ?? 'home';

switch ($route) {

    case 'home':
        (new HomeController($db))->index();
        break;

    case 'login':
        (new AuthController($db))->login();
        break;

    case 'logout':
        (new AuthController($db))->logout();
        break;

    case 'article':
        $ctrl   = new ArticleController($db);
        $action = $_GET['action'] ?? 'show';
        match ($action) {
            'list'   => $ctrl->list(),
            'show'   => $ctrl->show(),
            'create' => $ctrl->create(),
            'edit'   => $ctrl->edit(),
            'submit' => $ctrl->submit(),
            'delete' => $ctrl->delete(),
            default  => $ctrl->show(),
        };
        break;

    case 'editor':
        $ctrl   = new EditorController($db);
        $action = $_GET['action'] ?? 'dashboard';
        match ($action) {
            'dashboard'      => $ctrl->dashboard(),
            'approve'        => $ctrl->approve(),
            'reject'         => $ctrl->reject(),
            'toggleComments' => $ctrl->toggleComments(),
            default          => $ctrl->dashboard(),
        };
        break;

    case 'comment':
        $ctrl   = new CommentController($db);
        $action = $_GET['action'] ?? 'submit';
        match ($action) {
            'submit'  => $ctrl->submit(),
            'approve' => $ctrl->approve(),
            'delete'  => $ctrl->delete(),
            default   => $ctrl->submit(),
        };
        break;

    case 'tag':
        (new TagController($db))->index();
        break;

    case 'archive':
        (new ArchiveController($db))->index();
        break;

    case 'profile':
        (new ProfileController($db))->show();
        break;

    case 'search':
        (new SearchController($db))->index();
        break;

    default:
        (new HomeController($db))->index();
        break;
}
