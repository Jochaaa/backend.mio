<?php
require_once 'models/AlbumModel.php';
require_once 'models/AutoresModel.php'; 
require_once 'view/AlbumView.php'; 
require_once 'controller/AuthController.php'; 

class AlbumController {
    private $albumModel;
    private $autorModel;
    private $view; 

    public function __construct() {
        $this->albumModel = new AlbumModel();
        $this->autorModel = new AutoresModel();
        $this->view = new AlbumView(); 
    }

    public function showAll() {
        $albumes = $this->albumModel->getAllWithAuthor();
        $this->view->showAllAlbumes($albumes); 
    }

    public function showDetail($id) {
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

    public function showAdminList() {
        
        AuthController::checkLoggedIn(); 
        
        $albumes = $this->albumModel->getAllWithAuthor();
        $this->view->showAdminAlbumList($albumes); 
    }

    public function showAlbumForm($id = null) {
        
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
        
        if (empty($_POST['nombre_album']) || empty($_POST['ID_autor'])) {
            header('Location: ' . BASE_URL . '/admin/albumes'); 
            return;
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
    }

    public function deleteAlbum($id) {
       
        AuthController::checkLoggedIn(); 
        
        if (is_numeric($id) && $id > 0) {
            $this->albumModel->deleteAlbum($id);
        }
        header('Location: ' . BASE_URL . '/admin/albumes');
    }
}