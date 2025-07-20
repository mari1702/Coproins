<?php
require_once '../core/Database.php';
require_once '../models/Herramienta.php';

class Inventario{
    private $id;
    private $ubicacion;
    private $herramientas = [];
    private $fecha;

    public function __construct($id = null, $ubicacion = null, $fecha = null) {
        $this->id = $id;
        $this->ubicacion = $ubicacion;
        $this->fecha = $fecha;
        
        if ($id !== null) {
            $this->loadHerramientas();
        }
    }

    public static function getAll() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM inventario_herramienta ORDER BY fecha DESC");
            $stmt->execute();
            
            $results = [];  
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $inventario = new Inventario(
                    $row['h_inventario_id'],
                    $row['ubicacion'],
                    $row['fecha']
                );
                $results[] = $inventario;
            }
            return $results;
        } catch (PDOException $e) {
            error_log("Error en Inventario::getAll(): " . $e->getMessage());
            return [];
        }
    }


    public static function getById($id) {
        try {
            $db = Database::getConnection();
            
            $stmt = $db->prepare("SELECT * FROM inventario_herramienta WHERE h_inventario_id = ?");
            $stmt->execute([$id]);

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Inventario(
                    $row['h_inventario_id'], 
                    $row['ubicacion'],
                    $row['fecha']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error en Inventario->getById(): ".$e->getMessage());
            return null;
        }
    }

    public function save() {
        try {
            $db = Database::getConnection();

            if($this->id != null) {
                // Update existing record
                $stmt = $db->prepare("UPDATE inventario_herramienta SET ubicacion = ? WHERE h_inventario_id = ?");
                $stmt->execute([$this->ubicacion, $this->id]);
            } else {
                // Insert new record
                $stmt = $db->prepare("INSERT INTO inventario_herramienta (ubicacion, fecha) VALUES ( ?, NOW())");
                $stmt->execute([$this->ubicacion]);
                $this->id = $db->lastInsertId();
                $this->fecha = date('Y-m-d');
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error en Inventario->save(): ".$e->getMessage());
            return false;
        }
    }

    private function loadHerramientas() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT h.herramienta_id, ihh.cantidad 
                FROM herramienta h 
                JOIN inventario_herramienta_has_herramienta ihh ON h.herramienta_id = ihh.herramienta_herramienta_id 
                WHERE ihh.inventario_herramienta_h_inventario_id = ?
            ");
            $stmt->execute([$this->id]);
            
            $this->herramientas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $herramienta = Herramienta::getById($row['herramienta_id']);
                $this->herramientas[] = [
                    'herramienta' => $herramienta,
                    'cantidad' => $row['cantidad']
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en Inventario->loadHerramientas(): ".$e->getMessage());
        }
    }

    public function saveTool($herramienta_id, $cantidad) {
        try {
            $db = Database::getConnection();
            
            $stmt = $db->prepare("
                SELECT cantidad FROM inventario_herramienta_has_herramienta
                WHERE herramienta_herramienta_id = ? AND inventario_herramienta_h_inventario_id = ?
            ");
            $stmt->execute([$herramienta_id, $this->id]);
            
            if ($row = $stmt->fetch()) {
                // update 
                $stmt = $db->prepare("
                    UPDATE inventario_herramienta_has_herramienta 
                    SET cantidad = ? 
                    WHERE herramienta_herramienta_id = ? AND inventario_herramienta_h_inventario_id = ?
                ");
                $stmt->execute([$cantidad, $herramienta_id, $this->id]);
            } else {
                // Insertar nuevo producto
                $stmt = $db->prepare("
                    INSERT INTO inventario_herramienta_has_herramienta
                    (inventario_herramienta_h_inventario_id, herramienta_herramienta_id, cantidad) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([ $this->id, $herramienta_id, $cantidad]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error en Inventario->saveTool(): ".$e->getMessage());
            return false;
        }
    }


    public function removeTool($herramienta_id) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                DELETE FROM inventario_herramienta_has_herramienta 
                WHERE herramienta_herramienta_id = ? AND inventario_herramienta_h_inventario_id = ?
            ");
            $stmt->execute([$herramienta_id,$this->id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en Inventario->removeTool(): ".$e->getMessage());
            return false;
        }
    }


    public function delete() {
        try {
            $db = Database::getConnection();
            $this->loadHerramientas();
        
            $herramientas = $this->getHerramientas();

            foreach ($herramientas as $herramienta) {
                $this->removeTool($herramienta['herramienta']->getId());
            }

            $stmt = $db->prepare("DELETE FROM inventario_herramienta WHERE h_inventario_id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Error en Inventario->delete(): ".$e->getMessage());
            return false;
        }
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUbicacion() {
        return $this->ubicacion;
    }

    public function getHerramientas() {
        return $this->herramientas;
    }

    public function getFecha() {
        return $this->fecha;
    }   

    // Setters
    public function setUbicacion($ubicacion) {
        $this->ubicacion = $ubicacion;
    }
}