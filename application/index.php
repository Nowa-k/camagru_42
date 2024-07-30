<?php

session_start();
require 'config.php';
require 'routes.php'; 

try {
    getDBConnection();   
    $controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'feed';
    $actionName = isset($_GET['action']) ? $_GET['action'] : 'index';
    
    $controllerClassName = ucfirst($controllerName) . 'Controller';
    $controllerFile = 'app/controllers/' . $controllerClassName . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($controllerClassName)) {
            $controller = new $controllerClassName();
            if (method_exists($controller, $actionName)) {
                $controller->$actionName();
            } else {
                handleNotFound();
            }
        } else {
            handleNotFound();
        }
    } else {
        handleNotFound();
    }
    
    function handleNotFound() {
        header("HTTP/1.0 404 Not Found");
        include 'app/views/404.php';
        exit;
    }
}
catch (PDOException $e) {
    header("HTTP/1.0 404 Not Found");
    include 'app/views/404.php';
    exit;
}

?>