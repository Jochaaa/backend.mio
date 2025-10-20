<?php
require_once 'config.php';

class AlbumModel {
    private $db;

    public function __construct() {
        $this->connect();
        if ($this->db) {
             $this->deployDatabase();
        }
    }

    private function connect() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->db = new PDO($dsn, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            if ($e->getCode() == 1049) {
                try {
                   $dsnBase = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
                   $dbBase = new PDO($dsnBase, DB_USER, DB_PASS);
                   $dbBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                   $dbBase->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
                   $this->db = new PDO($dsn, DB_USER, DB_PASS);
                   $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e2) {
                   $this->db = null;
                   die("La creación/conexión a la base de datos falló: " . $e2->getMessage());
                }
            } else {
               $this->db = null;
               die("La conexión a la base de datos falló: " . $e->getMessage());
            }
        }
    }

    private function deployDatabase() {
        try {
             $this->db->query('SELECT 1 FROM `usuarios` LIMIT 1');
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') {
                try {
                    $sql = file_get_contents('disqueria.sql');
                    if ($sql === false) { throw new Exception("No se pudo leer disqueria.sql"); }
                    $this->db->query($sql);

                    $adminPasswordPlain = 'admin';
                    $adminPasswordHash = password_hash($adminPasswordPlain, PASSWORD_BCRYPT);
                    $updateQuery = $this->db->prepare('UPDATE usuarios SET password_hash = ? WHERE username = ?');
                    $updateQuery->execute([$adminPasswordHash, 'webadmin']);

                 } catch (Exception $eDeploy) {
                    error_log("Error durante el deploy: " . $eDeploy->getMessage());
                 }
            } else {
                 error_log("Error verificando tabla para deploy: " . $e->getMessage());
            }
        }
    }


    public function getAllWithAuthor() {
        $query = "
            SELECT
                a.ID_album, a.nombre_album, a.lanzamiento_album, a.cantidad_canciones, a.genero_album,
                t.nombre_autor
            FROM
                albumes a
            JOIN
                autores t ON a.ID_autor = t.ID_autor
            ORDER BY
                a.lanzamiento_album DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDetailWithAuthor($id) {
        $query = "
            SELECT
                a.*,
                t.nombre_autor,
                t.pais_autor
            FROM
                albumes a
            JOIN
                autores t ON a.ID_autor = t.ID_autor
            WHERE
                a.ID_album = ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function insertAlbum($nombre, $lanzamiento, $canciones, $genero, $id_autor) {
        $query = "
            INSERT INTO albumes
                (nombre_album, lanzamiento_album, cantidad_canciones, genero_album, ID_autor)
            VALUES
                (?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$nombre, $lanzamiento, $canciones, $genero, $id_autor]);
    }

    public function updateAlbum($id, $nombre, $lanzamiento, $canciones, $genero, $id_autor) {
        $query = "
            UPDATE albumes
            SET
                nombre_album = ?,
                lanzamiento_album = ?,
                cantidad_canciones = ?,
                genero_album = ?,
                ID_autor = ?
            WHERE
                ID_album = ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$nombre, $lanzamiento, $canciones, $genero, $id_autor, $id]);
    }

    public function deleteAlbum($id) {
        $query = "DELETE FROM albumes WHERE ID_album = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
    }

    public function getAlbumsByAuthorId($author_id) {
        $query = "
            SELECT
                a.ID_album, a.nombre_album, a.lanzamiento_album, a.cantidad_canciones, a.genero_album,
                t.nombre_autor
            FROM
                albumes a
            JOIN
                autores t ON a.ID_autor = t.ID_autor
            WHERE
                a.ID_autor = ?
            ORDER BY
                a.lanzamiento_album DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$author_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>