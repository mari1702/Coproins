<?php

require_once '../models/Marca.php';

class MarcaController {

    public static function listar() {
        try {
            return Marca::getAll(); 
        } catch (\PDOException $e) {
            error_log('Error al listar marcas: ' . $e->getMessage());
            return []; 
        }
    }

    public static function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['name'];
            $herramienta_id = $_POST['herramienta_id'];

            if ($nombre === '') {
                $error = 'El nombre de la marca no puede estar vacío.';
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }

            try {
                $marca = new Marca(null, $nombre);
                $marca->save();

                if ($herramienta_id) {
                    header('Location: ../views/edit-tool.php?id='.$herramienta_id.'&status=success');
                    exit;
                }

                header('Location: ../views/tools.php?status=success');
                exit;

            } catch (\PDOException $e) {
                error_log('Error al guardar marca: ' . $e->getMessage());
                $error = 'Ocurrió un error al guardar la marca. Por favor, inténtalo de nuevo.';
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }

        } else {
            header('Location: ../views/tools.php');
            exit;   
        }
    }

    public static function editar() {
        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de marca inválido'
            ]);
            exit;
        }

        $id = $_POST['id'];
        $nuevoNombre = $_POST['name'];

        if ($nuevoNombre === '') {
            echo json_encode([
                'success' => false,
                'message' => 'El nombre de la marca no puede estar vacío.'
            ]);
            exit;
        }

        try {
            $marca = Marca::getById($id);
            
            if (!$marca) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Categoría no encontrada'
                ]);
                exit;
            }

            $marca->setNombre($nuevoNombre);
            $marca->save();

            echo json_encode([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente'
            ]);
            exit;

        } catch (\PDOException $e) {
            error_log('Error al actualizar marca: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la marca. Por favor, intente nuevamente.'
            ]);
            exit;
        }
    }

    public static function borrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
           echo "invalid request";
           exit;
        }

        $id = $_POST['id'];

        try {
            $marca = Marca::getById($id);
            
            if (!$marca) {
                header('Location: ../views/tools.php?error=not_found');
                exit;
            }

            if ($marca->delete()) {
                header('Location: ../views/tools.php?status=success');
                exit;
            }

            header('Location: ../views/tools.php?error=delete_failed');
            exit;

        } catch (\PDOException $e) {
            error_log('Error al eliminar marca: ' . $e->getMessage());
            header('Location: ../views/tools.php?error=delete_error');
            exit;
        }
    }


}