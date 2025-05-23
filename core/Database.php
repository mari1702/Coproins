<?php
class Database{

// Datos de la base de datos    

    private static $servername = "";
    private static $username = "";
    private static $password = "";
    private static $dbname = ""; 
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    'mysql:host=' . self::$servername . ';dbname=' . self::$dbname . ';charset=utf8',
                    self::$username,
                    self::$password
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Error de conexión: ' . $e->getMessage());
            }
        }
        return self::$connection; 
    }
}


?>