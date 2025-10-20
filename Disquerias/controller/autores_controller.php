<?php
require_once 'models/AutoresModel.php';
require_once 'models/AlbumModel.php';
require_once 'view/AlbumView.php';
require_once 'controller/AuthController.php';
require_once 'config.php';

class AutoresController {

    private $model;
    private $view;

    public function __construct() {
        $this->model = new AutoresModel();
        $this->view = new AlbumView();
    }

    public function showAdminAutores() {
        AuthController::checkLoggedIn();
        $autores = $this->model->getAll();
        $this->view->showAdminAutoresList($autores);
    }

    public function showAddAutorForm() {
        AuthController::checkLoggedIn();
        $this->view->showAutorForm();
    }

    public function showEditAutorForm($id) {
        AuthController::checkLoggedIn();
        $autor = $this->model->get($id);
        if ($autor) {
            $this->view->showAutorForm($autor);
        } else {
            $this->view->showNotFound();
        }
    }

    public function saveAutor() {
        AuthController::checkLoggedIn();

        $id = filter_input(INPUT_POST, 'id_autor', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre_autor', FILTER_SANITIZE_STRING);
        $pais = filter_input(INPUT_POST, 'pais_autor', FILTER_SANITIZE_STRING);
        $cantAlbumesActual = 0;

        if (empty($nombre) || empty($pais)) {
            header('Location: ' . BASE_URL . '/admin/autores');
            die();
        }

        if ($id) {
             $autorExistente = $this->model->get($id);
             if ($autorExistente) {
                $cantAlbumesActual = $autorExistente->cant_albumes;
             }
            $this->model->update($id, $nombre, $pais, $cantAlbumesActual);
        } else {
            $this->model->save($nombre, $pais, 0);
        }
        header('Location: ' . BASE_URL . '/admin/autores');
        die();
    }

    public function deleteAutor($id) {
        AuthController::checkLoggedIn();

        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            header('Location: ' . BASE_URL . '/admin/autores');
            die();
        }

        try {
            $this->model->delete($id);
            header('Location: ' . BASE_URL . '/admin/autores');

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                 header('Location: ' . BASE_URL . '/admin/autores?error=foreign_key');
            } else {
                 header('Location: ' . BASE_URL . '/admin/autores?error=db_error');
            }
        }
        die();
    }

    public function showPublicAuthorList() {
        $authors = $this->model->getAll();
        $this->view->showPublicAuthors($authors);
    }

    public function showAlbumsByAuthor($author_id) {
        $albumModel = new AlbumModel();
        $albums = $albumModel->getAlbumsByAuthorId($author_id);
        $this->view->showPublicAlbumsByAuthor($albums);
    }
}