<?php

require_once '../models/Herramienta.php';
require_once '../models/Marca.php';
require_once '../models/Departamento.php';

class HerramientaController {
    public static function listar() {
        try {
            return Herramienta::getAll(); 
        } catch (\PDOException $e) {
            error_log('Error al listar herramientas: ' . $e->getMessage());
            return []; 
        }
    }

    public static function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $descripcion = $_POST['description'];
            $marca = (int)$_POST['brand'];
            $departamento = (int)$_POST['department'];
            $imgruta = null; 
            
            // Validaciones básicas
            $error = null;
            if (empty($descripcion)) {
                $error = 'La descripción es obligatoria.';
            }elseif($departamento == null ){
                $error = 'Debe seleccionar una departamento';
            }

            if ($error) {
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }
    
            // Manejo de la imagen
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/tools/';
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
                    header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                    exit;
                }
                
                if (!$error) {
                    $fileName = uniqid('prod_', true) . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file['name']);
                    $targetPath = $uploadDir . $fileName;

                    // if (!is_writable(dirname($targetPath))) {
                    //     die("Error: El directorio de destino no tiene permisos de escritura.");
                    // }
                    
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $imgruta = 'uploads/tools/' . $fileName;
                    } else {
                        $error = 'Error al guardar el archivo en el servidor ';
                    }
                }
            }
    
            if ($error) {
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }
    
            try {

                $herramienta = new Herramienta(
                    null, 
                    $descripcion,
                    Marca::getbyId($marca),
                    Departamento::getById($departamento),
                    $imgruta
                );
    
                if($herramienta->save()){
                    header('Location: ../views/tools.php?status=success');
                    exit;
                } else {
                    error_log('Error al guardar herramienta');
                    $error = 'Error al guardar el herramienta. Intente nuevamente.';
                    header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                    exit;
                }
    
                
            } catch (\PDOException $e) {
                HerramientaController::DeleteImg($imgruta);
                
                error_log('Error al guardar herramienta: ' . $e->getMessage());
                $error = 'Error al guardar el herramienta. Intente nuevamente.';
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }
        } else {
            echo "Invalid method";
            exit;
        }
    }

    public static function editar(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            echo "invalid request";
            exit;
        }
        $id = (int)$_POST['id'];
        $descripcion = $_POST['description'];
        $marca = (int) $_POST['brand'];
        $departamento = (int)$_POST['department'];
        $imgruta = null;

        $error = null;

        // Validaciones
        if ($error) {
            header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
            exit;
        }

        $herramienta = new Herramienta()->getById($id);

        // Manejo de la imagen
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/tools/';
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
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }
            
            if (!$error) {
                $fileName = uniqid('prod_', true) . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $imgruta = 'uploads/tools/' . $fileName;
                    HerramientaController::DeleteImg($herramienta->getImgRuta());
                } else {
                    $error = 'Error al guardar el archivo en el servidor ' . $targetPath;
                }
            }
        }

        if ($error) {
            header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
            exit;
        }

        try {

            $herramienta->setDescripcion($descripcion);
            $herramienta->setMarca(Marca::getById($marca));
            $herramienta->setDepartamento(Departamento::getById($departamento));
            
            if ($imgruta) {
                $herramienta->setImgRuta($imgruta);
            }

            if($herramienta->save()){
                header('Location: ../views/tools.php?status=success');
                exit;
            } else {
                error_log('Error al guardar herramienta');
                $error = 'Error al guardar el herramienta. Intente nuevamente.';
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }

            
        } catch (\PDOException $e) {
            HerramientaController::DeleteImg($imgruta);
            
            error_log('Error al guardar herramienta: ' . $e->getMessage());
            $error = 'Error al guardar el herramienta. Intente nuevamente.';
            header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
            exit;
        }        
    }


    public static function borrar(){
        
        $id = $_POST['id'];

        $error = null;

        try {
            $herramienta = Herramienta::getById($id);
            
            if (!$herramienta) {
                $error = 'Herramienta no encontrado:';
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }

            if ($herramienta->delete()) {
                if ($herramienta->getImgRuta()) {
                    HerramientaController::DeleteImg($herramienta->getImgRuta());
                }
                header('Location: ../views/tools.php?status=success');
                exit;
            }

            $error = 'Error al eliminar el herramienta';
            header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
            exit;
            
        } catch (\PDOException $e) {
            error_log('Error al eliminar categoría: ' . $e->getMessage());
            $error = 'Error al eliminar el herramienta';
            header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
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