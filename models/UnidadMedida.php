<?php
require_once '../core/Database.php';

class UnidadMedida {
    private $id;
    private $nombre;

    public function __construct($id = null, $nombre=null) {
        $this->nombre = $nombre;
        $this->id = $id;       
    }

    public static function getAll() {

        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM um");
            $results = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = new UnidadMedida(
                    $row['um_id'],
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
            $stmt = $db->prepare("UPDATE um SET nombre = ? WHERE um_id = ?");
            $stmt->execute([$this->nombre, $this->id]);
        } else {
            // Insert new record
            $stmt = $db->prepare("INSERT INTO um (nombre) VALUES (?)");
            $stmt->execute([$this->nombre]);
            $this->id = $db->lastInsertId();
        }
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM um WHERE um_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new UnidadMedida( $row['um_id'], $row['nombre']);
        }
        return null;
    }

    public function delete() {
        if ($this->id) {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM um WHERE um_id = ?");
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
        $stmt = $db->prepare("SELECT * FROM producto WHERE um_um_id = ?");
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