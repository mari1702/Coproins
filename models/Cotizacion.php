<?php
require_once '../core/Database.php';
require_once '../models/Producto.php';
require_once '../models/Nota.php';

class Cotizacion {
    private $id;
    private $nombre_proyecto;
    private $nombre_cliente;
    private $productos = [];
    private $notas = [];
    private $total;
    private $fecha;

    public function __construct($id = null, $nombre_proyecto = null, $nombre_cliente = null, $total = null, $fecha = null) {
        $this->id = $id;
        $this->nombre_proyecto = $nombre_proyecto;
        $this->nombre_cliente = $nombre_cliente;
        $this->total = $total;
        $this->fecha = $fecha;
        
        if ($id !== null) {
            $this->loadProductos();
        }

        if ($id !== null) {
            $this->loadNotas();
        }
    }

    public static function getAll() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM cotizacion ORDER BY fecha DESC");
            $stmt->execute();
            
            $results = [];  
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cotizacion = new Cotizacion(
                    $row['cotizacion_id'],
                    $row['proyecto'],
                    $row['cliente'],
                    $row['total'],
                    $row['fecha']
                );
                $results[] = $cotizacion;
            }
            return $results;
        } catch (PDOException $e) {
            error_log("Error en Cotizacion::getAll(): " . $e->getMessage());
            return [];
        }
    }


    public static function getById($id) {
        try {
            $db = Database::getConnection();
            
            $stmt = $db->prepare("SELECT * FROM cotizacion WHERE cotizacion_id = ?");
            $stmt->execute([$id]);

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Cotizacion(
                    $row['cotizacion_id'], 
                    $row['proyecto'], 
                    $row['cliente'],
                    $row['total'], 
                    $row['fecha']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->getById(): ".$e->getMessage());
            return null;
        }
    }

    public function save() {
        try {
            $db = Database::getConnection();

            if($this->id != null) {
                // Update existing record
                $stmt = $db->prepare("UPDATE cotizacion SET proyecto = ?, cliente = ?, total = ? WHERE cotizacion_id = ?");
                $stmt->execute([$this->nombre_proyecto, $this->nombre_cliente, $this->total, $this->id]);
            } else {
                // Insert new record
                $stmt = $db->prepare("INSERT INTO cotizacion (proyecto, cliente, total, fecha) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$this->nombre_proyecto, $this->nombre_cliente, $this->total]);
                $this->id = $db->lastInsertId();
                $this->fecha = date('Y-m-d');
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->save(): ".$e->getMessage());
            return false;
        }
    }

    private function loadProductos() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT p.producto_id, phc.cantidad 
                FROM producto p 
                JOIN producto_has_cotizacion phc ON p.producto_id = phc.producto_producto_id 
                WHERE phc.cotizacion_cotizacion_id = ?
            ");
            $stmt->execute([$this->id]);
            
            $this->productos = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $producto = Producto::getById($row['producto_id']);
                $this->productos[] = [
                    'producto' => $producto,
                    'cantidad' => $row['cantidad']
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->loadProductos(): ".$e->getMessage());
        }
    }

    private function loadNotas() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT n.nota_id, n.nota 
                FROM nota n 
                JOIN cotizacion_has_nota chn ON n.nota_id = chn.nota_nota_id 
                WHERE chn.cotizacion_cotizacion_id = ?");
            $stmt->execute([$this->id]);

            $this->notas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->notas[] = new Nota($row['nota_id'], $row['nota']);
            }
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->loadNotas(): ".$e->getMessage());
        }
    }

    public function saveProduct($producto_id, $cantidad) {
        try {
            $db = Database::getConnection();
            
            // Verificar si el producto ya existe en la cotización
            $stmt = $db->prepare("
                SELECT cantidad FROM producto_has_cotizacion 
                WHERE producto_producto_id = ? AND cotizacion_cotizacion_id = ?
            ");
            $stmt->execute([$producto_id, $this->id]);
            
            if ($row = $stmt->fetch()) {
                // Actualizar cantidad si el producto ya existe
                $stmt = $db->prepare("
                    UPDATE producto_has_cotizacion 
                    SET cantidad = ? 
                    WHERE producto_producto_id = ? AND cotizacion_cotizacion_id = ?
                ");
                $stmt->execute([$cantidad, $producto_id, $this->id]);
            } else {
                // Insertar nuevo producto
                $stmt = $db->prepare("
                    INSERT INTO producto_has_cotizacion 
                    (producto_producto_id, cotizacion_cotizacion_id, cantidad) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$producto_id, $this->id, $cantidad]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->saveProduct(): ".$e->getMessage());
            return false;
        }
    }

    public function saveNote($nota) {
        try {
            $db = Database::getConnection();
            // Verificar si la nota ya existe en la cotización
            $stmt = $db->prepare("SELECT * FROM cotizacion_has_nota WHERE cotizacion_cotizacion_id = ? AND nota_nota_id = ?");
            $stmt->execute([$this->id, $nota->getId()]);

            if ($stmt->fetch()) {
                // Actualizar nota si ya existe
                $stmt = $db->prepare("UPDATE nota SET nota = ? WHERE nota_id = ?");
                $stmt->execute([$nota->getNota(), $nota->getId()]);
            } else {
                // Insertar nueva nota
                $stmt = $db->prepare("INSERT INTO cotizacion_has_nota (cotizacion_cotizacion_id, nota_nota_id) VALUES (?, ?)");
                $stmt->execute([$this->id, $nota->getId()]);
            }

            
            return true;
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->saveNote(): ".$e->getMessage());
        }
    }

    public function removeProduct($producto_id) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                DELETE FROM producto_has_cotizacion 
                WHERE producto_producto_id = ? AND cotizacion_cotizacion_id = ?
            ");
            $stmt->execute([$producto_id, $this->id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->removeProduct(): ".$e->getMessage());
            return false;
        }
    }

    public function removeNote($nota_id) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM cotizacion_has_nota WHERE cotizacion_cotizacion_id = ? AND nota_nota_id = ?");
            $stmt->execute([$this->id, $nota_id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->removeNote(): ".$e->getMessage());
            return false;
        }
    }

    public function delete() {
        try {
            $db = Database::getConnection();
            $this->loadProductos();
            $this->loadNotas();
        
            $productos = $this->getProductos();
            $notas = $this->getNotas();

            foreach ($productos as $producto) {
                $this->removeProduct($producto['producto']->getId());
            }

            foreach ($notas as $nota) {
                $this->removeNote($nota->getId());
            }

            Nota::deleteWithoutCotizacion();



            $stmt = $db->prepare("DELETE FROM cotizacion WHERE cotizacion_id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Error en Cotizacion->delete(): ".$e->getMessage());
            return false;
        }
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNombreProyecto() {
        return $this->nombre_proyecto;
    }

    public function getCliente() {
        return $this->nombre_cliente;
    }

    public function getProductos() {
        return $this->productos;
    }

    public function getNotas() {
        return $this->notas;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getFecha() {
        return $this->fecha;
    }   

    // Setters
    public function setNombreProyecto($nombre_proyecto) {
        $this->nombre_proyecto = $nombre_proyecto;
    }

    public function setCliente($cliente) {
        $this->nombre_cliente = $cliente;
    }

    public function setTotal($total) {
        $this->total = $total;
    }
}