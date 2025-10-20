<?php
require_once 'models/UserModel.php';

class AuthController {
    private $model;
    
    public function __construct() {
        $this->model = new UserModel();
    }
    
    
    public function showLogin() {
        $titulo = "Iniciar Sesión (Administración)";
        require 'templates/login.phtml';
    }

    public function verifyLogin() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_POST['username']) || empty($_POST['password'])) {
            $this->showLoginError("Debe ingresar usuario y contraseña.");
            return;
        }

        $username = $_POST['username'];
        $password = $_POST['password'];
        $user = $this->model->getUserByUsername($username);

        $user = $this->model->getUserByUsername($username); // Asegurate que UserModel busca en la tabla usuarios

        // Usamos password_verify y los nombres de columna correctos de la tabla usuarios
        if ($user && password_verify($password, $user->password_hash) && $user->is_admin == 1) {

            // Asegurarse de iniciar sesión ANTES de usar $_SESSION
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['USER_ID'] = $user->user_id; // Guardamos el ID del usuario
            $_SESSION['USERNAME'] = $user->username; // Guardamos el nombre de usuario
            $_SESSION['IS_LOGGED'] = true;

            header('Location: ' . BASE_URL . '/admin/albumes');
            exit(); // Usar exit() o die() después de header
        } else {
            // Mensaje de error si falla el login
            $this->showLoginError("Usuario o contraseña incorrectos, o el usuario no es administrador.");
        }
    }
    
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        header('Location: ' . BASE_URL . '/login'); 
    }
    
    private function showLoginError($errorMsg) {
        $titulo = "Iniciar Sesión (Administración)";
        require 'templates/login.phtml'; 
    }

    public static function checkLoggedIn() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['IS_LOGGED']) || $_SESSION['IS_LOGGED'] !== true) {
            header('Location: ' . BASE_URL . '/login');
            die();
        }
    }
}