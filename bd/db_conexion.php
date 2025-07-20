<?php

// Configuración de la base de datos
define('DB_HOST', 'localhost'); 
define('DB_NAME', ''); 
define('DB_USER', ''); 
define('DB_PASS', ''); 

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
            PDO::ATTR_EMULATE_PREPARES => false, 
        ]
    );
    //Conexión exitosa
    // Puedes eliminar este mensaje si no deseas mostrarlo
    //echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>
