<?php
require_once "../core/RoleHandler.php";

require_once '../models/Departamento.php';

class DepartamentoController {

    public static function listar() {
        try {
            return Departamento::getAll(); 
        } catch (\PDOException $e) {
            error_log('Error al listar categorías: ' . $e->getMessage());
            return []; 
        }
    }

    public static function crear() {
        RoleHandler::OnlyAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['name'];

            if ($nombre === '') {
                $error = 'El nombre de la categoría no puede estar vacío.';
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }

            try {
                $departamento = new Departamento(null, $nombre);
                $departamento->save();

                header('Location: ../views/tools.php?status=success&modal=NewTool');
                exit;

            } catch (\PDOException $e) {
                error_log('Error al guardar categoría: ' . $e->getMessage());
                $error = 'Ocurrió un error al guardar la categoría. Por favor, inténtalo de nuevo.';
                header('Location: ../views/tools.php?status=error&message=' . urlencode($error));
                exit;
            }

        } else {
            header('Location: ../views/tools.php');
            exit;   
        }
    }

    public static function editar() {
        RoleHandler::OnlyAdmin();
        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de categoría inválido'
            ]);
            exit;
        }

        $id = $_POST['id'];
        $nuevoNombre = $_POST['name'];

        if ($nuevoNombre === '') {
            echo json_encode([
                'success' => false,
                'message' => 'El nombre de la categoría no puede estar vacío.'
            ]);
            exit;
        }

        try {
            $departamento = Departamento::getById($id);
            
            if (!$departamento) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Categoría no encontrada'
                ]);
                exit;
            }

            $departamento->setNombre($nuevoNombre);
            $departamento->save();

            echo json_encode([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'modal' => 'NewTool'
            ]);
            exit;

        } catch (\PDOException $e) {
            error_log('Error al actualizar categoría: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la categoría. Por favor, intente nuevamente.'
            ]);
            exit;
        }
    }

    public static function borrar() {
        RoleHandler::OnlyAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
           echo "invalid request";
           exit;
        }

        $id = $_POST['id'];

        try {
            $departamento = Departamento::getById($id);
            
            if (!$departamento) {
                header('Location: ../views/tools.php?error=not_found');
                exit;
            }

            if ($departamento->delete()) {
                header('Location: ../views/tools.php?status=success&modal=NewTool');
                exit;
            }

            header('Location: ../views/tools.php?error=delete_failed');
            exit;

        } catch (\PDOException $e) {
            error_log('Error al eliminar categoría: ' . $e->getMessage());
            header('Location: ../views/tools.php?error=delete_error');
            exit;
        }
    }


}