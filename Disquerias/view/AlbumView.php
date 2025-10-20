<?php
require_once 'config.php';

class AlbumView
{

    public function showAllAlbumes($albumes)
    {
        $titulo = "Listado Público de Álbumes";
        require 'templates/albunes.phtml';
    }

    public function showAlbumDetail($album)
    {
        $titulo = "Detalle del Álbum: ";
        if ($album && isset($album->nombre_album)) {
            $titulo .= htmlspecialchars($album->nombre_album);
        }
        require 'templates/album_detalle.phtml';
    }

    public function showNotFound()
    {
        header("HTTP/1.0 404 Not Found");
        $titulo = "Error 404 - No Encontrado";
        require 'templates/404.phtml';
    }

    public function showAdminAlbumList($albumes)
    {
        $titulo = "Administración de Álbumes";
        require 'templates/admin_albumes.phtml';
    }

    public function showAlbumForm($album = null, $autores)
    {
        $titulo = ($album && isset($album->nombre_album)) ? "Editar Álbum: " . htmlspecialchars($album->nombre_album) : "Agregar Nuevo Álbum";
        require 'templates/album_form.phtml';
    }

    public function showAdminAutoresList($autores)
    {
        $titulo = "Administración de Autores";
        require 'templates/admin_autores.phtml';
    }

    public function showAutorForm($autor = null)
    {
        $titulo = ($autor && isset($autor->nombre_autor)) ? "Editar Autor: " . htmlspecialchars($autor->nombre_autor) : "Agregar Nuevo Autor";
        require 'templates/autor_form.phtml';
    }

    public function showPublicAuthors($authors)
    {
        $titulo = "Nuestros Autores";
        $autores = $authors;
        require 'templates/public_author_list.phtml';
    }

    public function showPublicAlbumsByAuthor($albums)
    {
        $titulo = "Álbumes";

        if (!empty($albums) && isset($albums[0]->nombre_autor)) {
            $titulo = "Álbumes de " . htmlspecialchars($albums[0]->nombre_autor);
        } else if (isset($_GET['error'])) {
            $titulo = "Autor no encontrado";
        }
        $albumes = $albums;
        require 'templates/public_albums_by_author.phtml';
    }

    public function showError($message)
    {
        header("HTTP/1.0 500 Internal Server Error");
        $titulo = "Error";

        echo "<h1>Error</h1><p>" . htmlspecialchars($message) . "</p>";
        echo '<p><a href="' . BASE_URL . '/admin/autores">Volver a Autores</a></p>';
    }
}
