<?php
require_once 'models/UserModel.php';
require_once 'config.php';

class AuthController {
    private $model;

    public function __construct() {
        $this->model = new UserModel();
    }


    public function showLogin() {
        require 'templates/login.phtml';
    }

    public function verifyLogin() {

        if (empty($_POST['username']) || empty($_POST['password'])) {
            $this->showLoginError("Debe ingresar usuario y contraseña.");
            return;
        }

        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = $this->model->getUserByUsername($username);

        if ($user && password_verify($password, $user->password_hash) && $user->is_admin == 1) {

            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['USER_ID'] = $user->user_id;
            $_SESSION['USERNAME'] = $user->username;
            $_SESSION['IS_LOGGED'] = true;

            header('Location: ' . BASE_URL . '/admin/albumes');
            exit();
        } else {
            $this->showLoginError("Usuario o contraseña incorrectos, o el usuario no es administrador.");
        }
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit();
    }

    private function showLoginError($errorMsg) {
        $errorMsg = $errorMsg;
        require 'templates/login.phtml';
    }

    public static function checkLoggedIn() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['IS_LOGGED']) || $_SESSION['IS_LOGGED'] !== true) {
            header('Location: ' . BASE_URL . '/login');
            die();
        }
    }
}
?>