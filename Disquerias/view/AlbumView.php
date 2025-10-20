<?php
require_once 'config.php'; // Agregado por si se usa BASE_URL en las plantillas

class AlbumView {

    public function showAllAlbumes($albumes) {
        $titulo = "Listado Público de Álbumes";
        require 'templates/albunes.phtml'; // Asegúrate que el nombre de archivo coincida
    }

    public function showAlbumDetail($album) {
        $titulo = "Detalle del Álbum: ";
        if ($album && isset($album->nombre_album)) {
             $titulo .= htmlspecialchars($album->nombre_album);
        }
        require 'templates/album_detalle.phtml';
    }

    public function showNotFound() {
        header("HTTP/1.0 404 Not Found");
        $titulo = "Error 404 - No Encontrado";
        require 'templates/404.phtml';
    }

    // --- Métodos de Administración (Álbumes) ---

    public function showAdminAlbumList($albumes) {
        $titulo = "Administración de Álbumes";
        require 'templates/admin_albumes.phtml';
    }

    public function showAlbumForm($album = null, $autores) {
        $titulo = ($album && isset($album->nombre_album)) ? "Editar Álbum: " . htmlspecialchars($album->nombre_album) : "Agregar Nuevo Álbum";
        // Pasamos $album (puede ser null) y $autores a la plantilla
        require 'templates/album_form.phtml';
    }

    // --- Métodos de Administración (Autores) ---

    public function showAdminAutoresList($autores) {
        $titulo = "Administración de Autores";
        require 'templates/admin_autores.phtml';
    }

    public function showAutorForm($autor = null) {
        $titulo = ($autor && isset($autor->nombre_autor)) ? "Editar Autor: " . htmlspecialchars($autor->nombre_autor) : "Agregar Nuevo Autor";
        // Pasamos $autor (puede ser null) a la plantilla
        require 'templates/autor_form.phtml';
    }

    // --- Métodos para Vistas Públicas (Parte B) ---

    public function showPublicAuthors($authors) {
        $titulo = "Nuestros Autores";
        $autores = $authors; // Renombramos para que coincida con el posible nombre en la plantilla
        require 'templates/public_author_list.phtml'; // Necesitas crear este archivo
    }

    public function showPublicAlbumsByAuthor($albums) {
        $titulo = "Álbumes";
         // Si la lista no está vacía, usamos el nombre del autor del primer álbum para el título
        if (!empty($albums) && isset($albums[0]->nombre_autor)) {
            $titulo = "Álbumes de " . htmlspecialchars($albums[0]->nombre_autor);
        } else if (isset($_GET['error'])) { // Ejemplo simple si quisiéramos mostrar un error si el autor no existe
             $titulo = "Autor no encontrado"; // O manejarlo mejor en el controlador
        }
        $albumes = $albums; // Renombramos para que coincida con el posible nombre en la plantilla
        require 'templates/public_albums_by_author.phtml'; // Necesitas crear este archivo
    }

     // --- Método Opcional para mostrar errores (si no usas redirect en deleteAutor) ---
     public function showError($message) {
         header("HTTP/1.0 500 Internal Server Error"); // O un código más apropiado
         $titulo = "Error";
         // Necesitarías crear una plantilla templates/error.phtml o mostrar el error directamente
         echo "<h1>Error</h1><p>" . htmlspecialchars($message) . "</p>";
         echo '<p><a href="' . BASE_URL . '/admin/autores">Volver a Autores</a></p>'; // Usar BASE_URL
     }

}
?>