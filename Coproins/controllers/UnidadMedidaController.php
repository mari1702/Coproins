<?php
require_once "../core/RoleHandler.php";

require_once '../models/UnidadMedida.php';

class UnidadMedidaController{

    public static function listar() {
        try {
            return UnidadMedida::getAll(); 
        } catch (\PDOException $e) {
            error_log('Error al listar las unidades de medida: ' . $e->getMessage());
            return []; 
        }
    }

    public static function crear() {
        RoleHandler::OnlyAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['name'];

            if ($nombre === '') {
                $error = 'El nombre de la unidad de medida no puede estar vacío.';
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }

            try {
                $unidadmedida = new UnidadMedida(null, $nombre);
                $unidadmedida->save();

                header('Location: ../views/products.php?status=success');
                exit;

            } catch (\PDOException $e) {
                error_log('Error al guardar unidad de medida: ' . $e->getMessage());
                $error = 'Ocurrió un error al guardar la unidad de medida. Por favor, inténtalo de nuevo.';
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }

        } else {
            header('Location: ../views/products.php');
            exit;
        }
    }

    public static function editar() {
        RoleHandler::OnlyAdmin();

        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de unidad de medida inválido'
            ]);
            exit;
        }

        $id = $_POST['id'];
        $nuevoNombre = $_POST['name'];

        if ($nuevoNombre === '') {
            echo json_encode([
                'success' => false,
                'message' => 'El nombre de la unidad de medida no puede estar vacío.'
            ]);
            exit;
        }

        try {
            $unidadmedida = UnidadMedida::getById($id);
            
            if (!$unidadmedida) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Unidad de medida no encontrada'
                ]);
                exit;
            }

            $unidadmedida->setNombre($nuevoNombre);
            $unidadmedida->save();

            echo json_encode([
                'success' => true,
                'message' => 'Unidad de medida actualizada exitosamente'
            ]);
            exit;

        } catch (\PDOException $e) {
            error_log('Error al actualizar unidad de medida: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la unidad de medida. Por favor, intente nuevamente.'
            ]);
            exit;
        }
    }

    public static function borrar() {
        RoleHandler::OnlyAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            header('Location: ../views/products.php?error=invalid_request');
            exit;
        }

        $id = $_POST['id'];

        try {
            $unidadmedida = UnidadMedida::getById($id);
            
            if (!$unidadmedida) {
                header('Location: ../views/products.php?error=not_found');
                exit;
            }

            if ($unidadmedida->delete()) {
                header('Location: ../views/products.php?status=success');
                exit;
            }

            header('Location: ../views/products.php?error=delete_failed');
            exit;

        } catch (\PDOException $e) {
            error_log('Error al eliminar unidad de medida: ' . $e->getMessage());
            header('Location: ../views/products.php?error=delete_error');
            exit;
        }
    }
}