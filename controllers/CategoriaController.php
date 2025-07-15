<?php

require_once '../models/Categoria.php';

class CategoriaController {

    public static function listar() {
        try {
            return Categoria::getAll(); 
        } catch (\PDOException $e) {
            error_log('Error al listar categorías: ' . $e->getMessage());
            return []; 
        }
    }

    public static function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['name'];

            if ($nombre === '') {
                $error = 'El nombre de la categoría no puede estar vacío.';
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }

            try {
                $categoria = new Categoria(null, $nombre);
                $categoria->save();

                header('Location: ../views/products.php?status=success');
                exit;

            } catch (\PDOException $e) {
                error_log('Error al guardar categoría: ' . $e->getMessage());
                $error = 'Ocurrió un error al guardar la categoría. Por favor, inténtalo de nuevo.';
                header('Location: ../views/products.php?status=error&message=' . urlencode($error));
                exit;
            }

        } else {
            header('Location: ../views/products.php');
            exit;   
        }
    }

    public static function editar() {
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
            $categoria = Categoria::getById($id);
            
            if (!$categoria) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Categoría no encontrada'
                ]);
                exit;
            }

            $categoria->setNombre($nuevoNombre);
            $categoria->save();

            echo json_encode([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente'
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
           echo "invalid request";
           exit;
        }

        $id = $_POST['id'];

        try {
            $categoria = Categoria::getById($id);
            
            if (!$categoria) {
                header('Location: ../views/products.php?error=not_found');
                exit;
            }

            if ($categoria->delete()) {
                header('Location: ../views/products.php?status=success');
                exit;
            }

            header('Location: ../views/products.php?error=delete_failed');
            exit;

        } catch (\PDOException $e) {
            error_log('Error al eliminar categoría: ' . $e->getMessage());
            header('Location: ../views/products.php?error=delete_error');
            exit;
        }
    }


}