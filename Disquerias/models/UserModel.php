<?php
require_once 'config.php';

class UserModel {
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
                   die("La creación/conexión a la base de datos falló.");
                }
            } else {
               $this->db = null;
               die("La conexión a la base de datos falló.");
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
                    if ($sql === false) { throw new Exception("No se pudo leer disqueria.sql para el deploy."); }
                    
                    $this->db->query($sql);
                    $adminPasswordPlain = 'admin';
                    $adminPasswordHash = password_hash($adminPasswordPlain, PASSWORD_BCRYPT);
                
                    $updateQuery = $this->db->prepare('UPDATE usuarios SET password_hash = ? WHERE username = ?');
                    $updateQuery->execute([$adminPasswordHash, 'webadmin']);

                 } catch (Exception $eDeploy) {
                
                    error_log("Error durante el deploy SQL: " . $eDeploy->getMessage());
                 }
            } else {
                
                 error_log("Error verificando tabla para deploy: " . $e->getMessage());
            }
        }
    }


    public function getUserByUsername($username) {
        $query = "SELECT * FROM usuarios WHERE username = ?"; // Cambiado nombre_usuario a username
        $stmt = $this->db->prepare($query);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
?>