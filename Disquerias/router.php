<?php
require_once 'config.php'; // Necesario para BASE_URL y la conexión en modelos

// Iniciar sesión ANTES de cualquier salida HTML o require de controladores que la usen
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cargar TODOS los controladores que el router podría necesitar
require_once 'controller/album_controller.php';
require_once 'controller/AuthController.php';
require_once 'controller/autores_controller.php'; // Agregado

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
        // Rutas Públicas (Álbumes)
        $this->addRoute('/', 'GET', 'AlbumController#showAllAlbums'); // Ajustado nombre método
        $this->addRoute('albumes', 'GET', 'AlbumController#showAllAlbums'); // Ajustado nombre método
        $this->addRoute('album/:id', 'GET', 'AlbumController#showAlbumDetail'); // Ajustado nombre método

        // Rutas Públicas (Autores - Parte B)
        $this->addRoute('autores', 'GET', 'AutoresController#showPublicAuthorList'); // Ruta agregada
        $this->addRoute('autor/:id/albumes', 'GET', 'AutoresController#showAlbumsByAuthor'); // Ruta agregada

        // Rutas de Autenticación
        $this->addRoute('login', 'GET', 'AuthController#showLogin');
        $this->addRoute('verify', 'POST', 'AuthController#verifyLogin');
        $this->addRoute('logout', 'GET', 'AuthController#logout');

        // Rutas de Administración (Álbumes)
        $this->addRoute('admin/albumes', 'GET', 'AlbumController#showAdminAlbums'); // Ajustado nombre método
        $this->addRoute('admin/album/add', 'GET', 'AlbumController#showAddAlbumForm'); // Ajustado nombre método
        $this->addRoute('admin/album/edit/:id', 'GET', 'AlbumController#showEditAlbumForm'); // Ajustado nombre método
        $this->addRoute('admin/album/save', 'POST', 'AlbumController#saveAlbum');
        $this->addRoute('admin/album/delete/:id', 'POST', 'AlbumController#deleteAlbum');

        // Rutas de Administración (Autores) - Habilitadas
        $this->addRoute('admin/autores', 'GET', 'AutoresController#showAdminAutores');
        $this->addRoute('admin/autor/add', 'GET', 'AutoresController#showAddAutorForm');
        $this->addRoute('admin/autor/edit/:id', 'GET', 'AutoresController#showEditAutorForm');
        $this->addRoute('admin/autor/save', 'POST', 'AutoresController#saveAutor');
        $this->addRoute('admin/autor/delete/:id', 'POST', 'AutoresController#deleteAutor'); // Usar POST para delete es más seguro
    }

    public function route($uri) {
        $uri = trim($uri, '/');
        $params = [];

        foreach ($this->routes as $route) {
            $routeUri = trim($route['uri'], '/');

            // Convertir ruta con parámetros (ej. :id) a una expresión regular
            $pattern = preg_replace('/:[a-zA-Z0-9_]+/', '([a-zA-Z0-9_]+)', $routeUri);

            // Comparar URI y Método HTTP
            if (preg_match("#^$pattern$#", $uri, $matches) && $_SERVER['REQUEST_METHOD'] === $route['method']) {

                $params = array_slice($matches, 1); // Extraer parámetros de la URL

                $this->callController($route['controllerMethod'], $params);
                return; // Ruta encontrada y ejecutada
            }
        }

        // Si ninguna ruta coincide, llamar al método 404
        // Usamos AlbumController porque ya tiene un método showNotFound
        $this->callController('AlbumController#showNotFound');
    }

    private function callController($controllerMethod, $params = []) {
        list($controllerName, $methodName) = explode('#', $controllerMethod);

        // Verificar si la clase y el método existen antes de llamar
        if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
            $controller = new $controllerName();
            // Llama al método del controlador pasándole los parámetros extraídos de la URL
            call_user_func_array([$controller, $methodName], $params);
        } else {
            // Error si el controlador o método no se encuentra (error de programación)
            header("HTTP/1.0 500 Internal Server Error");
            echo "Error 500: Controlador o método no encontrado: {$controllerName}#{$methodName}";
            // Podrías mostrar una vista de error más amigable
        }
    }
}

// --- Inicio de la Aplicación ---

// 1. Obtiene la URI solicitada
$uri = $_SERVER['REQUEST_URI'];

// 2. Elimina query strings (si las hay) para el ruteo
$uri = strtok($uri, '?');

// 3. Elimina la ruta base del proyecto si está en una subcarpeta
$base_path = parse_url(BASE_URL, PHP_URL_PATH);
if ($base_path && strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}

// 4. Instancia el Router, carga las rutas y ejecuta la que coincida
$router = new Router();
$router->routeAll();
$router->route($uri);