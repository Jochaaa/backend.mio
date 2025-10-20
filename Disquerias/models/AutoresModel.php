<?php
require_once 'config.php';

class AutoresModel {

    private $db;

    public function __construct() {
        $this->connect();
        if ($this->db) { // Solo si la conexión fue exitosa
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
                   $this->db = new PDO($dsn, DB_USER, DB_PASS); // Reintenta conexión
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
            if ($e->getCode() == '42S02') { // Tabla no encontrada
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


    public function getAll() {
        $query = $this->db->prepare("SELECT * FROM autores ORDER BY nombre_autor ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function get($id) {
        $query = $this->db->prepare("SELECT * FROM autores WHERE ID_autor = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function save($nombre, $pais, $cantAlbumes) {
        $query = $this->db->prepare("INSERT INTO autores (nombre_autor, pais_autor, cant_albumes) VALUES (?, ?, ?)");
        $query->execute([$nombre, $pais, $cantAlbumes]);
        return $this->db->lastInsertId();
    }

    public function update($id, $nombre, $pais, $cantAlbumes) {
        $query = $this->db->prepare("UPDATE autores SET nombre_autor = ?, pais_autor = ?, cant_albumes = ? WHERE ID_autor = ?");
        $query->execute([$nombre, $pais, $cantAlbumes, $id]);
        return $query->rowCount();
    }

    public function delete($id) {
        $query = $this->db->prepare("DELETE FROM autores WHERE ID_autor = ?");
        $query->execute([$id]);
        // Podrías retornar $query->rowCount();
    }
}