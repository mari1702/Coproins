<?php
require_once '../core/Database.php';

class Cliente {
    private $id;
    private $cliente;
    private $telefono;
    private $createdBy;
    private $status;


    public function __construct($id = null, $cliente = null, $telefono = null, $createdBy = null, $status = null) {
        $this->id=$id;
        $this->cliente=$cliente;
        $this->telefono=$telefono;
        $this->createdBy=$createdBy;
        $this->status=$status;
    }

    public static function getAll() {

        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM cliente");
            $results = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = new Cliente(
                    $row['id_cliente'],
                    $row['cliente'],
                    $row['telefono_cliente'],
                    $row['id_admin_creador'],
                    $row['estado_cliente']
                ); 
            }
        
            return $results;
        } catch (PDOException $e) {
            error_log("Error en Cliente::getAll(): " . $e->getMessage());
            return [];
        }
        
    }

    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM cliente WHERE id_cliente = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new Cliente( 
                $row['id_cliente'],
                $row['cliente'],
                $row['telefono_cliente'],
                $row['id_admin_creador'],
                $row['estado_cliente']
            );
        }
        return null;
    }

        public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCliente() {
        return $this->cliente;
    }

    public function setCliente($cliente) {
        $this->cliente = $cliente;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function getCreatedBy() {
        return $this->createdBy;
    }

    public function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

}
