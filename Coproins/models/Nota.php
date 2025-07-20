<?php

class Nota {
    private $id;
    private $nota;

    public function __construct($id=null, $nota) {
        $this->id = $id;
        $this->nota = $nota;
    }

    public static function deleteWithoutCotizacion() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM nota WHERE nota_id NOT IN (SELECT nota_nota_id FROM cotizacion_has_nota)");
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Nota::deleteWithoutCotizacion(): " . $e->getMessage());
            return false;
        }
    }

    public static function getAll() {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM nota");
            $notas = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $notas[] = new Nota($row['nota_id'], $row['nota']);
            }

            return $notas;
            
            
        } catch (PDOException $e) {
            error_log("Error en Nota::getAll(): " . $e->getMessage());
            return [];
        }
    }

    public static function getById($id) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM nota WHERE nota_id = ?");
            $stmt->execute([$id]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Nota($row['nota_id'], $row['nota']);
            }
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en Nota::getById(): " . $e->getMessage());
            return null;
        }
    }

    public function save() {
        try {
            $db = Database::getConnection();

            if ($this->id != null) {
                // Update existing record
                $stmt = $db->prepare("UPDATE nota SET nota = ? WHERE nota_id = ?");
                $stmt->execute([$this->nota, $this->id]);
                return true;
            } else {
                // Insert new record
                $stmt = $db->prepare("INSERT INTO nota (nota) VALUES (?)");
                $stmt->execute([$this->nota]);
                $this->id = $db->lastInsertId();
                return true;
            }
        }catch (PDOException $e) {
            error_log("Error en Nota->save(): " . $e->getMessage());
            return false;
        }
    }

    public function delete() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM nota WHERE nota_id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Error en Nota->delete(): " . $e->getMessage());
            return false;
        }
    }
    
    //Getters
    public function getId() {
        return $this->id;
    }

    public function getNota() { 
        return $this->nota;
    }

    //Setters
    
    public function setNota($nota) {
        $this->nota = $nota;
    }   

    
    
}