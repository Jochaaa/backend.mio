<?php
require_once 'config.php';

class UserModel {
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
            if ($e->getCode() == 1049) { // Unknown database
                try {
                   $dsnBase = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
                   $dbBase = new PDO($dsnBase, DB_USER, DB_PASS);
                   $dbBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                   $dbBase->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
                   // Reintenta la conexión original ahora que la DB debería existir
                   $this->db = new PDO($dsn, DB_USER, DB_PASS);
                   $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e2) {
                   $this->db = null;
                   // Podríamos loguear el error $e2->getMessage()
                   die("La creación/conexión a la base de datos falló.");
                }
            } else {
               $this->db = null;
               // Podríamos loguear el error $e->getMessage()
               die("La conexión a la base de datos falló.");
            }
        }
    }

    private function deployDatabase() {
        try {
             // Intenta consultar la tabla usuarios; si falla, asume que no existe y ejecuta deploy
             $this->db->query('SELECT 1 FROM `usuarios` LIMIT 1');
        } catch (PDOException $e) {
            // Código '42S02': Table doesn't exist
            if ($e->getCode() == '42S02') {
                try {
                    // Lee el archivo SQL completo (asegúrate que esté actualizado)
                    $sql = file_get_contents('disqueria.sql');
                    if ($sql === false) { throw new Exception("No se pudo leer disqueria.sql para el deploy."); }
                    // Ejecuta todo el SQL del archivo
                    $this->db->query($sql);

                    // Hashea la contraseña del admin 'webadmin' que se insertó en texto plano
                    $adminPasswordPlain = 'admin';
                    $adminPasswordHash = password_hash($adminPasswordPlain, PASSWORD_BCRYPT);
                    // Actualiza el registro del admin con el hash
                    $updateQuery = $this->db->prepare('UPDATE usuarios SET password_hash = ? WHERE username = ?');
                    $updateQuery->execute([$adminPasswordHash, 'webadmin']);

                 } catch (Exception $eDeploy) { // Captura PDOException o Exception general
                    // Loguea el error si falla el deploy
                    error_log("Error durante el deploy SQL: " . $eDeploy->getMessage());
                 }
            } else {
                 // Loguea si hubo otro error al verificar la tabla
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