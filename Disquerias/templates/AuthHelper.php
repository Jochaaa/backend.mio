<?php
// Asegúrate de incluir config.php si contiene BASE_URL
require_once 'config.php'; 

/**
 * Clase auxiliar para gestionar y verificar la autenticación de usuarios.
 */
class AuthHelper {

    /**
     * Verifica si hay un usuario administrador logueado. 
     * Si no lo hay, interrumpe la ejecución y redirige al login.
     */
    public static function checkLoggedIn() {
        // 1. Iniciar la sesión si aún no está activa
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        // 2. Verificar las variables de sesión
        // IS_LOGGED debe ser true, y opcionalmente, podrías verificar el rol si tuvieras más usuarios.
        if (!isset($_SESSION['IS_LOGGED']) || $_SESSION['IS_LOGGED'] !== true) {
            
            // 3. Redirigir si no está logueado
            header('Location: ' . BASE_URL . '/login');
            die(); // Detiene la ejecución para que no se muestre ningún contenido sensible
        }
        
        // Si la función llega hasta aquí, el usuario está logueado y puede continuar.
    }
}

// NOTA: Si no deseas usar una clase, puedes simplemente definir una función checkLoggedIn() 
// en un archivo de funciones global.
?>