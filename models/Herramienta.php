<?php
require_once '../core/Database.php';
require_once '../models/Departamento.php';
require_once '../models/UnidadMedida.php';

class Herramienta {
    private $id;
    private $descripcion;
    private $marca;
    private $departamento;
    private $img_ruta;

    public function __construct(
        $id=null,
        $descripcion=null,
        $marca=null,
        $departamento=null,
        $img_ruta=null,
    )
    {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->marca = $marca;
        $this->departamento = $departamento;
        $this->img_ruta = $img_ruta;
        
    }


    public static function getAll() {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM herramienta");
            $herramientas = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $departamento = Departamento::getById($row['departamento_departamento_id']);
                $marca = Marca::getById($row['marca_marca_id']);

                $herramientas[] = new Herramienta(
                    $row['herramienta_id'],
                    $row['descripcion'],
                    $marca,
                    $departamento,
                    $row['img_ruta']
                );
            }
            
            return $herramientas;
            
        } catch (PDOException $e) {
            error_log("Error en Herramienta::getAll(): " . $e->getMessage());
            return [];
        }
    }

    public static function getById($id) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM herramienta WHERE herramienta_id = ?");
            $stmt->execute([$id]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Herramienta(
                    $row['herramienta_id'],
                    $row['descripcion'],
                    Marca::getById($row['marca_marca_id']),
                    Departamento::getById($row['departamento_departamento_id']),
                    $row['img_ruta']
                );
            }
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en Herramienta::getById(): " . $e->getMessage());
            return null;
        }
    }

    public function save() {
        try {
            $db = Database::getConnection();

            $departamentoId = $this->departamento->getId();
            $marcaId = $this->marca->getId();
            
            if ($this->id != null) {
                // Update existing record
                $stmt = $db->prepare("UPDATE herramienta SET 
                descripcion = ?,
                marca_marca_id = ?,
                departamento_departamento_id = ?,
                img_ruta = ?
                WHERE herramienta_id = ?");
                $stmt->execute([          
                    $this->descripcion,
                    $marcaId,
                    $departamentoId,
                    $this->img_ruta,
                    $this->id
                ]);
                return true;
            } else {
                // Insert new record
                $stmt = $db->prepare("INSERT INTO herramienta (
                descripcion,
                marca_marca_id,
                departamento_departamento_id,
                img_ruta) VALUES (?,?,?,?)");
                $stmt->execute([          
                    $this->descripcion,
                    $marcaId,
                    $departamentoId,
                    $this->img_ruta,
                ]);
                $this->id = $db->lastInsertId();
                return true;
            } 

         
        } catch (PDOException $e) {
            error_log("Error en Herramienta->save(): ".$e->getMessage());
            return;
        }
    }

    public function delete() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM herramienta WHERE herramienta_id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Error en Herramienta->delete(): ".$e->getMessage());
            return false;
        }
    }


    //Getters
    public function getId() {
        return $this->id;
    }



    public function getDescripcion(){
        return $this->descripcion;
    }

    public function getMarca() {
        return $this->marca;
    }

    public function getDepartamento() {
        return $this->departamento;
    }

    public function getImgRuta() {
        return $this->img_ruta;
    }

    //Setters

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setMarca($marca) {
        $this->marca = $marca;
    }    

    public function setDepartamento($departamento) {
        $this->departamento = $departamento;
    }

    public function setImgRuta($unidad_medida) {
        $this->img_ruta = $unidad_medida;
    }    




}