<?php

require_once '../models/Inventario.php';
class InventarioController {

    public static function listar() {
        try {
            return Inventario::getAll();
        } catch (\PDOException $e) {
            error_log('Error al listar inventarios: ' . $e->getMessage());
            return [];
        }
    }

    public static function crear() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        try {
            // Validar datos requeridos
            if (empty($_POST['ubicacion'])) {
                throw new Exception('El nombre del proyecto es requerido');
            }

            if (empty($_POST['herramientas'])) {
                throw new Exception('Debe seleccionar al menos un herramienta');
            }

            // Obtener y validar datos
            $ubicacion = trim($_POST['ubicacion']);
            $herramientas = json_decode($_POST['herramientas'], true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar los herramientas');
            }

            
            // Crear el inventario
            $inventario = new Inventario(null, $ubicacion);
            $inventario->save();

            // Guardar herramientas de el inventario
            foreach ($herramientas as $herramienta) {
                $inventario->saveTool($herramienta['id'], $herramienta['quantity']);
            }


            echo json_encode([
                'success' => true,
                'message' => 'Inventario creado exitosamente',
                'inventario_id' => $inventario->getId()
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (PDOException $e) {
            error_log('Error en InventarioController::crear(): ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    public static function editar(){
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        try {
            // Validar datos requeridos
            if (empty($_POST['id'])) {
                throw new Exception('El ID de el inventario es requerido');
            }

            if (empty($_POST['ubicacion'])) {
                throw new Exception('El nombre del proyecto es requerido');
            }


            if (empty($_POST['herramientas'])) {
                throw new Exception('Debe seleccionar al menos un herramienta');
            }

            $id = $_POST['id'];
            $ubicacion = trim($_POST['ubicacion']);
            $herramientas = json_decode($_POST['herramientas'], true);

                     
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar los herramientas');
            }

            $inventario = Inventario::getById($id);

            if (!$inventario) {
                throw new Exception('Inventario no encontrada');
            }   

            $inventario->setUbicacion($ubicacion);

            $inventario->save();

            // Eliminar herramientas de el inventario
            $oldHerramientas = $inventario->getHerramientas();

            foreach ($oldHerramientas as $herramienta) {
                $inventario->removeTool($herramienta['herramienta']->getId());
            }

            // Actualizar herramientas de el inventario
            foreach ($herramientas as $herramienta) {
                $inventario->saveTool($herramienta['id'], $herramienta['quantity']);
            }

            

            echo json_encode([
                'success' => true,
                'message' => 'Inventario actualizado exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (PDOException $e) {
            error_log('Error en InventarioController::editar(): ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }   
    }

    public static function borrar(){
        $id = $_POST['id'];

        $error = null;
        
        try {

            $inventario = Inventario::getById($id);

            if (!$inventario) {
                $error = 'Inventario no encontrado';
                header('Location: ../views/inventories.php?status=error&message=' . urlencode($error));
                exit;
            }   

            if ($inventario->delete()) {
                // Aqui tengo que eliminar el pdf de la cotizacion
                
                header('Location: ../views/inventories.php?status=success');
                exit;
            }

            $error = 'Error al eliminar el inventario';
            header('Location: ../views/inventories.php?status=error&message=' . urlencode($error));
            exit;

        } catch (PDOException $e) {
            error_log('Error en InventarioController::borrar(): ' . $e->getMessage());
            $error = 'Error al eliminar el inventario';
            header('Location: ../views/inventories.php?status=error&message=' . urlencode($error));
            exit;
        }
    }
}

