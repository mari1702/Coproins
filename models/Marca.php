<?php
require_once '../core/Database.php';

class Marca {
    private $id;
    private $nombre;

    public function __construct( $id = null, $nombre=null) {
        $this->nombre = $nombre;
        $this->id = $id;       
    }

    public static function getAll() {

        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM marca");
            $results = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = new Marca(
                    $row['marca_id'],
                    $row['nombre']
                ); 
            }
        
            return $results;
        } catch (PDOException $e) {
            error_log("Error en Marca::getAll(): " . $e->getMessage());
            return [];
        }
        
    }

    public function save() {
        $db = Database::getConnection();
        if ($this->id) {
            // Update existing record
            $stmt = $db->prepare("UPDATE marca SET nombre = ? WHERE marca_id = ?");
            $stmt->execute([$this->nombre, $this->id]);
        } else {
            // Insert new record
            $stmt = $db->prepare("INSERT INTO marca (nombre) VALUES (?)");
            $stmt->execute([$this->nombre]);
            $this->id = $db->lastInsertId();
        }
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM marca WHERE marca_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new Marca( $row['marca_id'],$row['nombre']);
        }
        return null;
    }

    public function delete() {
        if ($this->id) {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM marca WHERE marca_id = ?");
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

    public function getHerramientas() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM herramienta WHERE marca_marca_id = ?");
        $stmt->execute([$this->id]);
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = Herramienta::getById($row['herramienta_id']);
        }
        return $results;
    }

    // Setters
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
}