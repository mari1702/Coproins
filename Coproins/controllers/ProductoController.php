<?php
require_once "../core/RoleHandler.php";

require_once '../models/Producto.php';
require_once '../models/UnidadMedida.php';

class ProductoController {
    public static function listar() {
        try {
            return Producto::getAll(); 
        } catch (\PDOException $e) {
            error_log('Error al listar productos: ' . $e->getMessage());
            return []; 
        }
    }

    public static function crear() {
        RoleHandler::OnlyAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = "NONAME";
            $descripcion = $_POST['description'];
            $precio = (float)$_POST['price'];
            $unidad_medida = (int)$_POST['measurement'];
            $categoria = (int)$_POST['category'];
            $imgruta = null; 
            
            // Validaciones básicas
            $error = null;
            if (empty($descripcion)) {
                $error = 'La descripción es obligatoria.';
            } elseif ($precio < 1) {
                $error = 'El precio debe ser mayor a 0.';
            }elseif($categoria == null ){
                $error = 'Debe seleccionar una categoria';
            }elseif($unidad_medida == null){
                $error = 'Debe seleccionar una unidad de medida';
            }

            if ($error) {
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }
    
            // Manejo de la imagen
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/products/';
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $maxSize = 2 * 1024 * 1024; // 2MB
    
                // Crear directorio si no existe
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                
    
                $file = $_FILES['image'];
                
                // Validar archivo
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Error al subir el archivo: ' . $file['error'];
                } elseif (!in_array($file['type'], $allowedTypes)) {
                    $error = 'Tipo de archivo no permitido. Formatos aceptados: JPG, PNG, WEBP';
                } elseif ($file['size'] > $maxSize) {
                    $error = 'El archivo excede el tamaño máximo (2MB)';
                }
    
                if ($error) {
                    header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                    exit;
                }
                
                if (!$error) {
                    $fileName = uniqid('prod_', true) . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file['name']);
                    $targetPath = $uploadDir . $fileName;

                    // if (!is_writable(dirname($targetPath))) {
                    //     die("Error: El directorio de destino no tiene permisos de escritura.");
                    // }
                    
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $imgruta = 'uploads/products/' . $fileName;
                    } else {
                        $error = 'Error al guardar el archivo en el servidor ';
                    }
                }
            }
    
            if ($error) {
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }
    
            try {

                $producto = new Producto(
                    null, 
                    $nombre,
                    $descripcion,
                    $precio,
                    UnidadMedida::getById($unidad_medida),
                    Categoria::getById($categoria),
                    $imgruta
                );
    
                if($producto->save()){
                    header('Location: ../views/products.php?status=success');
                    exit;
                } else {
                    error_log('Error al guardar producto');
                    $error = 'Error al guardar el producto. Intente nuevamente.';
                    header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                    exit;
                }
    
                
            } catch (\PDOException $e) {
                ProductoController::DeleteImg($imgruta);
                
                error_log('Error al guardar producto: ' . $e->getMessage());
                $error = 'Error al guardar el producto. Intente nuevamente.';
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }
        } else {
            echo "Invalid method";
            exit;
        }
    }

    public static function editar(){
        RoleHandler::OnlyAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            echo "invalid request";
            exit;
        }
        $id = (int)$_POST['id'];
        $nombre = 'NONAME';            
        $descripcion = $_POST['description'];
        $precio = (float)$_POST['price'];
        $unidad_medida = (int)$_POST['measurement'];
        $categoria = (int)$_POST['category'];
        $imgruta = null;

        $error = null;

        // Validaciones
        if ($precio < 1) {
            $error = 'El precio debe ser mayor a 0.';
        }
        if ($error) {
            header('Location: ../views/products.php?status=error&message=' . urlencode($error));
            exit;
        }

        $producto = new Producto()->getById($id);

        // Manejo de la imagen
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/products/';
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            

            $file = $_FILES['image'];
            
            // Validar archivo
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Error al subir el archivo: ' . $file['error'];
            } elseif (!in_array($file['type'], $allowedTypes)) {
                $error = 'Tipo de archivo no permitido. Formatos aceptados: JPG, PNG, WEBP';
            } elseif ($file['size'] > $maxSize) {
                $error = 'El archivo excede el tamaño máximo (2MB)';
            }

            if ($error) {
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }
            
            if (!$error) {
                $fileName = uniqid('prod_', true) . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $imgruta = 'uploads/products/' . $fileName;
                    ProductoController::DeleteImg($producto->getImgRuta());
                } else {
                    $error = 'Error al guardar el archivo en el servidor ' . $targetPath;
                }
            }
        }

        if ($error) {
            header('Location: ../views/products.php?status=error&message=' . urlencode($error));
            exit;
        }

        try {

            $producto->setNombre($nombre);
            $producto->setDescripcion($descripcion);
            $producto->setPrecio($precio);
            $producto->setUnidadMedida(UnidadMedida::getById($unidad_medida));
            $producto->setCategoria(Categoria::getById($categoria));
            
            if ($imgruta) {
                $producto->setImgRuta($imgruta);
            }

            if($producto->save()){
                header('Location: ../views/products.php?status=success');
                exit;
            } else {
                error_log('Error al guardar producto');
                $error = 'Error al guardar el producto. Intente nuevamente.';
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }

            
        } catch (\PDOException $e) {
            ProductoController::DeleteImg($imgruta);
            
            error_log('Error al guardar producto: ' . $e->getMessage());
            $error = 'Error al guardar el producto. Intente nuevamente.';
            header('Location: ../views/products.php?status=error&message=' . urlencode($error));
            exit;
        }        
    }


    public static function borrar(){
        RoleHandler::OnlyAdmin();
        
        $id = $_POST['id'];

        $error = null;

        try {
            $producto = Producto::getById($id);
            
            if (!$producto) {
                $error = 'Producto no encontrado:';
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }

            if ($producto->delete()) {
                if ($producto->getImgRuta()) {
                    ProductoController::DeleteImg($producto->getImgRuta());
                }
                header('Location: ../views/products.php?status=success');
                exit;
            }

            $error = 'Error al eliminar el producto';
            header('Location: ../views/products.php?status=error&message=' . urlencode($error));
            exit;
            
        } catch (\PDOException $e) {
            error_log('Error al eliminar categoría: ' . $e->getMessage());
            $error = 'Error al eliminar el producto';
            header('Location: ../views/products.php?status=error&message=' . urlencode($error));
            exit;
        }
    }

    

    // Funcion para eliminar imagen del servidor
    private static function DeleteImg($imgruta){
        if ($imgruta && file_exists($_SERVER['DOCUMENT_ROOT'] ."/" . $imgruta)) {
            unlink($_SERVER['DOCUMENT_ROOT' ]."/"  . $imgruta);
        }
    }
}