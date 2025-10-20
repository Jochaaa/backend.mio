<?php
// Asume que config.php ya tiene las constantes DB_HOST, DB_NAME, etc.
require_once 'config.php'; 

class UserModel {
    private $db;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->db = new PDO($dsn, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos (UserModel): " . $e->getMessage());
        }
    }

    /**
     * Busca un usuario por su nombre de usuario.
     * @param string $username El nombre de usuario a buscar.
     * @return object|false El objeto usuario si existe, o false.
     */
    public function getUserByUsername($username) {
        // NOTA DE SEGURIDAD: En un entorno de producción, las contraseñas deberían estar hasheadas (bcrypt).
        // Para este TPE, usaremos la tabla `usuarios` y la contraseña simple 'admin' como pide la consigna.
        
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$username]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
?>