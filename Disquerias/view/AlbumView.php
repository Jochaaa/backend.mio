<?php

class AlbumView {
    
    /**
     * Renderiza el listado de todos los Álbumes. (PÚBLICO)
     * @param array $albumes Array de objetos/arrays de álbumes con su autor.
     */
    public function showAllAlbumes($albumes) {
        $titulo = "Listado Público de Álbumes"; 
        require 'templates/albumes.phtml'; // Nota: Cambié albunes.phtml por albumes.phtml por consistencia
    }

    /**
     * Renderiza la página de detalle de un Álbum específico. (PÚBLICO)
     * @param object $album Objeto con los datos del álbum y su autor.
     */
    public function showAlbumDetail($album) {
        $titulo = "Detalle del Álbum: " . $album->nombre_album;
        require 'templates/album_detalle.phtml';
    }

    /**
     * Método auxiliar para mostrar un mensaje de error o 404.
     */
    public function showNotFound() {
        header("HTTP/1.0 404 Not Found");
        $titulo = "Error 404 - No Encontrado";
        require 'templates/404.phtml'; 
    }

    // --- MÉTODOS DE ADMINISTRACIÓN (CRUD ÁLBUMES) ---

    /**
     * Renderiza el listado de todos los Álbumes en el panel de administración.
     * @param array $albumes Array de objetos/arrays de álbumes con su autor.
     */
    public function showAdminAlbumList($albumes) {
        $titulo = "Administración de Álbumes";
        require 'templates/admin_albumes.phtml'; 
    }

    /**
     * Renderiza el formulario para crear o editar un Álbum.
     * @param object|null $album Objeto del álbum a editar, o null si es nuevo.
     * @param array $autores Array de Autores (Categorías) para poblar el <select>.
     */
    public function showAlbumForm($album = null, $autores) {
        $titulo = $album ? "Editar Álbum: " . $album->nombre_album : "Agregar Nuevo Álbum";
        require 'templates/album_form.phtml'; 
    }

        // (PARTE B) 


    /**
     * Renderiza el listado de Autores en el panel de administración.
     * Usa la vista templates/admin_autores.phtml
     * @param array $autores Array de objetos/arrays de autores.
     */
    public function showAdminAutoresList($autores) {
        $titulo = "Administración de Autores (Categorías)";
        require 'templates/admin_autores.phtml'; 
    }

    /**
     * Renderiza el formulario para crear o editar un Autor.
     * Usa la vista templates/autor_form.phtml
     * @param object|null $autor Objeto del autor a editar, o null si es nuevo.
     */
    public function showAutorForm($autor = null) {
        $titulo = $autor ? "Editar Autor: " . $autor->nombre_autor : "Agregar Nuevo Autor";
        
        // RUTA AJUSTADA: Busca el archivo en /templates/
        require 'templates/autor_form.phtml'; 
    }
}
?>