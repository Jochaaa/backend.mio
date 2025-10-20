<?php
require_once 'models/AlbumModel.php';
require_once 'models/AutoresModel.php';
require_once 'view/AlbumView.php';
require_once 'controller/AuthController.php';
require_once 'config.php';

class AlbumController {
    private $albumModel;
    private $autorModel;
    private $view;

    public function __construct() {
        $this->albumModel = new AlbumModel();
        $this->autorModel = new AutoresModel();
        $this->view = new AlbumView();
    }

    public function showAllAlbums() {
        $albumes = $this->albumModel->getAllWithAuthor();
        $this->view->showAllAlbumes($albumes);
    }

    public function showAlbumDetail($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->view->showNotFound();
            return;
        }

        $album = $this->albumModel->getDetailWithAuthor($id);

        if (!$album) {
            $this->view->showNotFound();
            return;
        }

        $this->view->showAlbumDetail($album);
    }

    public function showAdminAlbums() {
        AuthController::checkLoggedIn();
        $albumes = $this->albumModel->getAllWithAuthor();
        $this->view->showAdminAlbumList($albumes);
    }

    public function showAddAlbumForm() {
         AuthController::checkLoggedIn();
         $autores = $this->autorModel->getAll();
         $this->view->showAlbumForm(null, $autores);
    }

    public function showEditAlbumForm($id) {
        AuthController::checkLoggedIn();
        $album = null;
        if ($id) {
            $album = $this->albumModel->getDetailWithAuthor($id);
            if (!$album) {
                $this->view->showNotFound();
                return;
            }
        }
        $autores = $this->autorModel->getAll();
        $this->view->showAlbumForm($album, $autores);
    }


    public function saveAlbum() {
        AuthController::checkLoggedIn();

        
        if (empty($_POST['nombre_album']) || !isset($_POST['ID_autor']) || $_POST['ID_autor'] === '' ||
            empty($_POST['lanzamiento_album']) || !isset($_POST['cantidad_canciones']) || empty($_POST['genero_album'])) {
            
            header('Location: ' . BASE_URL . '/admin/albumes');
            exit();
        }

        $id_album = $_POST['id_album'] ?? null;
        $nombre = $_POST['nombre_album'];
        $lanzamiento = $_POST['lanzamiento_album'];
        $canciones = $_POST['cantidad_canciones'];
        $genero = $_POST['genero_album'];
        $id_autor = $_POST['ID_autor'];

        if ($id_album) {
            $this->albumModel->updateAlbum($id_album, $nombre, $lanzamiento, $canciones, $genero, $id_autor);
        } else {
            $this->albumModel->insertAlbum($nombre, $lanzamiento, $canciones, $genero, $id_autor);
        }
        header('Location: ' . BASE_URL . '/admin/albumes');
        exit();
    }

    public function deleteAlbum($id) {
        AuthController::checkLoggedIn();

        if (filter_var($id, FILTER_VALIDATE_INT) && $id > 0) {
             try {
                $this->albumModel->deleteAlbum($id);
             } catch (PDOException $e) {
                 // Manejar posible error si existe alguna restricción futura, aunque con Cascade debería funcionar
                 // Por ahora, solo redirigimos, pero podríamos loguear el error $e->getMessage()
             }
        }
        header('Location: ' . BASE_URL . '/admin/albumes');
        exit(); // Usar exit() o die()
    }

     // Método para página no encontrada (usado por el router si falla el match)
     public function showNotFound() {
        $this->view->showNotFound();
    }
}
?>