<?php
require_once 'config.php'; 

/**
 * Clase que gestiona la interacción con la tabla 'autores' (categorías).
 */
class AutoresModel {
    
    private $db;

    public function __construct() {
        // Conexión directa y simple, confiando en las constantes de config.php
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->db = new PDO($dsn, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Manejo de error de conexión.
            die("Error de conexión a la base de datos (AutoresModel): " . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los Autores. 
     * @return array Array de objetos con todos los campos del autor.
     */
    public function getAll() {
        $query = $this->db->prepare("SELECT * FROM autores ORDER BY nombre_autor ASC");
        $query->execute();
        // Retorna todos los campos para el CRUD de Autores
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Obtiene un autor específico por su ID.
     * @param int $id El ID del autor.
     * @return object|false Objeto autor o false si no se encuentra.
     */
    public function get($id) {
        $query = $this->db->prepare("SELECT * FROM autores WHERE ID_autor = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Inserta un nuevo autor en la base de datos.
     * @param string $nombre El nombre del autor.
     * @param string $pais El país de origen.
     * @param int $cantAlbumes Cantidad inicial de álbumes (debe ser 0).
     * @return int El ID del autor recién insertado.
     */
    public function save($nombre, $pais, $cantAlbumes) {
        $query = $this->db->prepare("INSERT INTO autores (nombre_autor, pais_autor, cant_albumes) VALUES (?, ?, ?)");
        $query->execute([$nombre, $pais, $cantAlbumes]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza un autor existente.
     * @param int $id El ID del autor a actualizar.
     * @param string $nombre El nuevo nombre.
     * @param string $pais El nuevo país.
     * @param int $cantAlbumes La nueva cantidad de álbumes.
     * @return int Cantidad de filas afectadas.
     */
    public function update($id, $nombre, $pais, $cantAlbumes) {
        $query = $this->db->prepare("UPDATE autores SET nombre_autor = ?, pais_autor = ?, cant_albumes = ? WHERE ID_autor = ?");
        $query->execute([$nombre, $pais, $cantAlbumes, $id]);
        return $query->rowCount();
    }

    /**
     * Elimina un autor de la base de datos.
     * @param int $id El ID del autor a eliminar.
     */
    public function delete($id) {
        $query = $this->db->prepare("DELETE FROM autores WHERE ID_autor = ?");
        $query->execute([$id]);
    }
}