<?php
require_once '../../bd/config.php';

class Database{
  
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    'mysql:host=' . Config::$DB_HOST . ';dbname=' . Config::$DB_NAME . ';charset=utf8',
                    Config::$DB_USER,
                    Config::$DB_PASS
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