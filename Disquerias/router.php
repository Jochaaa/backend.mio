<?php
require_once 'config.php';
require_once 'controller/autores_controller.php'; 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'controller/album_controller.php';
require_once 'controller/AuthController.php';


class Router {
    private $routes = [];

    public function addRoute($uri, $method, $controllerMethod) {
        $this->routes[] = [
            'uri' => $uri,
            'method' => $method,
            'controllerMethod' => $controllerMethod
        ];
    }

    public function routeAll() {
        $this->addRoute('/', 'GET', 'AlbumController#showAllAlbums');
        $this->addRoute('albumes', 'GET', 'AlbumController#showAllAlbums');
        $this->addRoute('album/:id', 'GET', 'AlbumController#showAlbumDetail');
        $this->addRoute('login', 'GET', 'AuthController#showLogin');
        $this->addRoute('verify', 'POST', 'AuthController#verifyLogin');
        $this->addRoute('logout', 'GET', 'AuthController#logout');
        $this->addRoute('admin/albumes', 'GET', 'AlbumController#showAdminAlbums'); 
        $this->addRoute('admin/album/add', 'GET', 'AlbumController#showAddAlbumForm');
        $this->addRoute('admin/album/edit/:id', 'GET', 'AlbumController#showEditAlbumForm');
        $this->addRoute('admin/album/save', 'POST', 'AlbumController#saveAlbum'); 
        $this->addRoute('admin/album/delete/:id', 'POST', 'AlbumController#deleteAlbum');
        $this->addRoute('autores', 'GET', 'AutoresController#showPublicAuthorList');
        $this->addRoute('autor/:id/albumes', 'GET', 'AutoresController#showAlbumsByAuthor');
    }
    public function route($uri) {
        $uri = trim($uri, '/');
        $params = [];
        
        foreach ($this->routes as $route) {
            $routeUri = trim($route['uri'], '/');
            
            $pattern = preg_replace('/:[a-zA-Z0-9]+/', '([a-zA-Z0-9]+)', $routeUri);
            
            if (preg_match("#^$pattern$#", $uri, $matches) && $_SERVER['REQUEST_METHOD'] === $route['method']) {
                
                $params = array_slice($matches, 1);
                
                $this->callController($route['controllerMethod'], $params);
                return;
            }
        }
        
        $this->callController('album_controller#showNotFound');
    }

    private function callController($controllerMethod, $params = []) {
        list($controllerName, $methodName) = explode('#', $controllerMethod);
        
        if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
            $controller = new $controllerName();
            call_user_func_array([$controller, $methodName], $params);
            return;
        }
        
        header("HTTP/1.0 500 Internal Server Error");
        echo "Error 500: Falló al cargar el controlador o método: $controllerMethod";
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$base_path = parse_url(BASE_URL, PHP_URL_PATH);
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}
$router = new Router();
$router->routeAll(); 
$router->route($uri);