<?php
require_once '../core/Database.php';

class Categoria {
    private $id;
    private $nombre;

    public function __construct( $id = null, $nombre=null) {
        $this->nombre = $nombre;
        $this->id = $id;       
    }

    public static function getAll() {

        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM categoria");
            $results = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = new Categoria(
                    $row['categoria_id'],
                    $row['nombre']
                ); 
            }
        
            return $results;
        } catch (PDOException $e) {
            error_log("Error en Producto::getAll(): " . $e->getMessage());
            return [];
        }
        
    }

    public function save() {
        $db = Database::getConnection();
        if ($this->id) {
            // Update existing record
            $stmt = $db->prepare("UPDATE categoria SET nombre = ? WHERE categoria_id = ?");
            $stmt->execute([$this->nombre, $this->id]);
        } else {
            // Insert new record
            $stmt = $db->prepare("INSERT INTO categoria (nombre) VALUES (?)");
            $stmt->execute([$this->nombre]);
            $this->id = $db->lastInsertId();
        }
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categoria WHERE categoria_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new Categoria( $row['categoria_id'],$row['nombre']);
        }
        return null;
    }

    public function delete() {
        if ($this->id) {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM categoria WHERE categoria_id = ?");
            $stmt->execute([$this->id]);
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getProductos() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM producto WHERE categoria_categoria_id = ?");
        $stmt->execute([$this->id]);
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = Producto::getById($row['producto_id']);
        }
        return $results;
    }

    // Setters
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
}