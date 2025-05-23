<?php
require_once '../core/Database.php';
require_once '../models/Categoria.php';
require_once '../models/UnidadMedida.php';

class Producto {
    private $id;
    private $nombre;
    private $descripcion;
    private $precio;
    private $unidad_medida;
    private $categoria;
    private $img_ruta;

    public function __construct(
        $id=null,
        $nombre=null,
        $descripcion=null,
        $precio=null,
        $unidad_medida=null,
        $categoria=null,
        $img_ruta=null,
    )
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->unidad_medida = $unidad_medida;
        $this->categoria = $categoria;
        $this->img_ruta = $img_ruta;
        
    }


    public static function getAll() {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM producto");
            $productos = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $unidad = UnidadMedida::getById($row['um_um_id']);
                $categoria = Categoria::getById($row['categoria_categoria_id']);

                $productos[] = new Producto(
                    $row['producto_id'],
                    $row['nombre'],
                    $row['descripcion'],
                    $row['precio'],
                    $unidad,
                    $categoria,
                    $row['img_ruta']
                );
            }
            
            return $productos;
            
        } catch (PDOException $e) {
            error_log("Error en Producto::getAll(): " . $e->getMessage());
            return [];
        }
    }

    public static function getById($id) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM producto WHERE producto_id = ?");
            $stmt->execute([$id]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Producto(
                    $row['producto_id'],
                    $row['nombre'],
                    $row['descripcion'],
                    $row['precio'],
                    UnidadMedida::getById($row['um_um_id']),
                    Categoria::getById($row['categoria_categoria_id']),
                    $row['img_ruta']
                );
            }
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en Producto::getById(): " . $e->getMessage());
            return null;
        }
    }

    public function save() {
        try {
            $db = Database::getConnection();

            $unidadId = $this->unidad_medida->getId();
            $categoriaId = $this->categoria->getId();
            
            if ($this->id != null) {
                // Update existing record
                $stmt = $db->prepare("UPDATE producto SET 
                nombre = ?,
                descripcion = ?,
                precio = ?,
                um_um_id = ?,
                categoria_categoria_id = ?,
                img_ruta = ?
                WHERE producto_id = ?");
                $stmt->execute([          
                    $this->nombre,
                    $this->descripcion,
                    $this->precio,
                    $unidadId,
                    $categoriaId,
                    $this->img_ruta,
                    $this->id
                ]);
                return true;
            } else {
                // Insert new record
                $stmt = $db->prepare("INSERT INTO producto (nombre,
                descripcion,
                precio,
                um_um_id,
                categoria_categoria_id,
                img_ruta) VALUES (?,?,?,?,?,?)");
                $stmt->execute([          
                    $this->nombre,
                    $this->descripcion,
                    $this->precio,
                    $unidadId,
                    $categoriaId,
                    $this->img_ruta,
                ]);
                $this->id = $db->lastInsertId();
                return true;
            } 

         
        } catch (PDOException $e) {
            error_log("Error en Producto->save(): ".$e->getMessage());
            return;
        }
    }

    public function delete() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM producto WHERE producto_id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Error en Producto->delete(): ".$e->getMessage());
            return false;
        }
    }


    //Getters
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function getUnidadMedida() {
        return $this->unidad_medida;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function getImgRuta() {
        return $this->img_ruta;
    }

    //Setters
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setPrecio($precio) {
        $this->precio = $precio;
    }    

    public function setUnidadMedida($unidad_medida) {
        $this->unidad_medida = $unidad_medida;
    }

    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

    public function setImgRuta($unidad_medida) {
        $this->img_ruta = $unidad_medida;
    }    




}