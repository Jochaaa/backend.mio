<?php
// Credenciales de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'musica_ddbb');
define('DB_USER', 'root');
define('DB_PASS', '');

// URL Base dinámica (Buena práctica enseñada en clase de Ruteo)
define('BASE_URL', '//'.$_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']).'/');

// Constantes de admin eliminadas porque el login usa la base de datos

?>