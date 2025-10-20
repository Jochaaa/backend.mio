<?php
require_once 'models/AutoresModel.php';
require_once 'models/AlbumModel.php';
require_once 'view/AlbumView.php';
require_once 'controller/AuthController.php';
require_once 'config.php';

class AutoresController {

    private $model;
    private $view;
    // Eliminamos la propiedad $authHelper

    public function __construct() {
        $this->model = new AutoresModel();
        $this->view = new AlbumView();
        // Ya no instanciamos AuthHelper
    }

    public function showAdminAutores() {
        AuthController::checkLoggedIn(); // Usamos método estático
        $autores = $this->model->getAll();
        $this->view->showAdminAutoresList($autores);
    }

    public function showAddAutorForm() {
        AuthController::checkLoggedIn(); // Usamos método estático
        $this->view->showAutorForm(); // Llama a la vista sin pasarle autor
    }

    public function showEditAutorForm($id) {
        AuthController::checkLoggedIn(); // Usamos método estático
        $autor = $this->model->get($id);
        if ($autor) {
            $this->view->showAutorForm($autor); // Pasa el autor a editar
        } else {
            // Si no encuentra el autor, muestra 404 (asumiendo que showNotFound está en AlbumView)
            $this->view->showNotFound();
        }
    }

    public function saveAutor() {
        AuthController::checkLoggedIn(); // Usamos método estático

        $id = filter_input(INPUT_POST, 'id_autor', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre_autor', FILTER_SANITIZE_STRING);
        $pais = filter_input(INPUT_POST, 'pais_autor', FILTER_SANITIZE_STRING);
        $cantAlbumesActual = 0; // Valor por defecto para nuevos autores

        // Validación simple de campos obligatorios
        if (empty($nombre) || empty($pais)) {
            header('Location: ' . BASE_URL . '/admin/autores'); // Redirige si faltan datos
            die(); // Termina ejecución
        }

        if ($id) {
             // Si es edición, obtenemos la cantidad actual para no perderla
             $autorExistente = $this->model->get($id);
             if ($autorExistente) {
                $cantAlbumesActual = $autorExistente->cant_albumes;
             }
            // Actualiza manteniendo la cantidad de álbumes
            $this->model->update($id, $nombre, $pais, $cantAlbumesActual);
        } else {
            // Crea un nuevo autor con 0 álbumes iniciales
            $this->model->save($nombre, $pais, 0);
        }
        // Redirige a la lista después de guardar
        header('Location: ' . BASE_URL . '/admin/autores');
        die(); // Termina ejecución
    }

    public function deleteAutor($id) {
        AuthController::checkLoggedIn(); // Usamos método estático

        // Valida que el ID sea un entero positivo
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            header('Location: ' . BASE_URL . '/admin/autores');
            die();
        }

        try {
            // Intenta borrar
            $this->model->delete($id);
            // Si tiene éxito, redirige a la lista
            header('Location: ' . BASE_URL . '/admin/autores');

        } catch (PDOException $e) {
            // Si falla (ej. por clave foránea, código 23000), redirige con error
            if ($e->getCode() == 23000) {
                 header('Location: ' . BASE_URL . '/admin/autores?error=foreign_key');
            } else {
                 // Otro error de base de datos
                 header('Location: ' . BASE_URL . '/admin/autores?error=db_error');
            }
        }
        die(); // Termina ejecución después de redirigir
    }

    // --- Métodos Públicos (sin checkLoggedIn) ---

    public function showPublicAuthorList() {
        $authors = $this->model->getAll();
        // Llama al método correspondiente en la vista (debes crearlo en AlbumView)
        $this->view->showPublicAuthors($authors);
    }

    public function showAlbumsByAuthor($author_id) {
        // Necesitamos instanciar AlbumModel para buscar álbumes
        $albumModel = new AlbumModel();
        // Usamos el método que agregamos a AlbumModel
        $albums = $albumModel->getAlbumsByAuthorId($author_id);
        // Llama al método correspondiente en la vista (debes crearlo en AlbumView)
        $this->view->showPublicAlbumsByAuthor($albums);
    }
}
?>