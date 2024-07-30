<?php
require_once 'app/models/Feed.php';

class FeedController {

    private function verifyField($text, $lenght) {
        if (strlen($text) == 0 || strlen($text) > $lenght) {
            return false;
        }
        return true;
    }

    private function cleanField($text) {
        $text = trim($text);
        $text = htmlspecialchars($text);
        return $text;
    }

    public function index() {
        $page = isset($_GET['page']) ? $_GET['page'] : '1'; 
        $page = ctype_digit($page) ? $page : 1;
        $page = intval($page);
        $resultsPerPage = 5;
        $totalFeeds = Feed::getTotalFeedCount();
        $totalPages = ceil($totalFeeds / $resultsPerPage);
        $firstResults = ($page - 1) * $resultsPerPage;
        $feeds = Feed::getAll($firstResults, $resultsPerPage);
        require 'app/views/feed/index.php';
        exit();
    }

    public function view($id) {
        $feed = Feed::getById($id);
        require 'app/views/feed/view.php';
        exit();
    }

    public function add() {
        if (isset($_SESSION['id'])) {
            if ($_SESSION['valide'] == '1') {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $mess = Feed::add($_POST['overlayImage']);
                }
                $feed = Feed::getUserFeed($_SESSION['id']);
                require 'app/views/feed/add.php';
            } else {
                header("Location: index.php?controller=user&action=verify");
                exit(); 
            }
        } else {
            require 'app/views/user/login.php';
            }
        exit();
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id'])) {
            if ($_SESSION['valide'] == '1') {
                if (isset($_POST['canvasData']) && isset($_POST['overlay'])) {
                    Feed::create($_POST['canvasData'], $_POST['overlay']);
                }
            } else {
                header("Location: index.php?controller=user&action=verify");
                exit(); 
            }
        }
    }

    public function del() {
        if (isset($_SESSION['id']) && $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id']) && $this->verifyField($_GET['id'], 50)) {
            $id = $this->cleanField($_GET['id']);
            Feed::del($_SESSION['id'], $id);
        }
        self::index();
    }

    public function comment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_SESSION['id'])) {
                if ($_SESSION['valide'] == '1') {
                    if (isset($_POST['comment']) && $this->verifyField($_POST['comment'], 255) && isset($_POST['id']) && $this->verifyField($_POST['id'], 50)) {
                        $comment = $this->cleanField($_POST['comment']);
                        $id = $this->cleanField($_POST['id']);
                        Feed::comment($id, $comment, $_SESSION['id']);
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    header("Location: index.php?controller=user&action=verify");
                    exit(); 
                }
            } else {
                require 'app/views/user/login.php';
                exit();
            }
        }
    }

    public function like() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SESSION['id']) && isset($_GET['id']) && $this->verifyField($_GET['id'], 50)) {
            $id = $this->cleanField($_GET['id']);
            Feed::like($_SESSION['id'], $id);
        }
        self::index();  
    }

    public function zoom() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['code'])) {
            $feed = Feed::getById($_GET['code']);
            require 'app/views/feed/zoom.php';
        } else {
            self::index();
        }
        
    }
}
?>