<?php

require_once '../models/Cotizacion.php';
require_once '../models/Cliente.php';
require_once '../models/Nota.php';
class CotizacionController {

    public static function listar() {
        try {
            return Cotizacion::getAll();
        } catch (\PDOException $e) {
            error_log('Error al listar cotizaciones: ' . $e->getMessage());
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
            if (empty($_POST['nombre_proyecto'])) {
                throw new Exception('El nombre del proyecto es requerido');
            }

            if (empty($_POST['cliente'])) {
                throw new Exception('El cliente es requerido');
            }

            if (empty($_POST['productos'])) {
                throw new Exception('Debe seleccionar al menos un producto');
            }

            // Obtener y validar datos
            $nombre_proyecto = trim($_POST['nombre_proyecto']);
            $clienteId = $_POST['cliente'];
            $total = floatval($_POST['total']);
            $productos = json_decode($_POST['productos'], true);
            $notas = json_decode($_POST['notas'], true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar los productos');
            }

            if(!$cliente = Cliente::getById($clienteId)){
                throw new Exception("Error al encontrar el cliente");
            }

            
            // Crear la cotización
            $cotizacion = new Cotizacion(null, $nombre_proyecto, $cliente, $total);
            $cotizacion->save();

            // Guardar productos de la cotización
            foreach ($productos as $producto) {
                $cotizacion->saveProduct($producto['id'], $producto['quantity']);
            }

            // Guardar notas de la cotización
            foreach ($notas as $nota) {
                $nota = new Nota(
                    null,
                    $nota['note']           
                );
                $nota->save();
                $cotizacion->saveNote($nota);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Cotización creada exitosamente',
                'cotizacion_id' => $cotizacion->getId()
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (PDOException $e) {
            error_log('Error en CotizacionController::crear(): ' . $e->getMessage());
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
                throw new Exception('El ID de la cotización es requerido');
            }

            if (empty($_POST['nombre_proyecto'])) {
                throw new Exception('El nombre del proyecto es requerido');
            }

            if (empty($_POST['cliente'])) {
                throw new Exception('El cliente es requerido');
            }

            if (empty($_POST['productos'])) {
                throw new Exception('Debe seleccionar al menos un producto');
            }

            $id = $_POST['id'];
            $nombre_proyecto = trim($_POST['nombre_proyecto']);
            $clienteId = $_POST['cliente'];
            $total = floatval($_POST['total']);
            $productos = json_decode($_POST['productos'], true);
            $notas = json_decode($_POST['notas'], true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar los productos');
            }

            if(!$cliente = Cliente::getById($clienteId)){
                throw new Exception("Error al encontrar el cliente");
            }

            if (!$cotizacion = Cotizacion::getById($id)) {
                throw new Exception('Cotización no encontrada');
            }   

            $cotizacion->setNombreProyecto($nombre_proyecto);
            $cotizacion->setCliente($cliente);
            $cotizacion->setTotal($total);

            $cotizacion->save();

            // Eliminar productos de la cotización
            $oldProductos = $cotizacion->getProductos();
            foreach ($oldProductos as $producto) {
                $cotizacion->removeProduct($producto['producto']->getId());
            }

            // Actualizar productos de la cotización
            foreach ($productos as $producto) {
                $cotizacion->saveProduct($producto['id'], $producto['quantity']);
            }

            // Eliminar notas de la cotización
            $oldNotas = $cotizacion->getNotas();
            foreach ($oldNotas as $nota) {
                $cotizacion->removeNote($nota->getId());
            }

            // Actualizar notas de la cotización
            foreach ($notas as $nota) {
                if ($nota['id'] == "") {
                    $nota = new Nota(
                        null,
                        $nota['note']     
                    );
                } else {
                    $nota = new Nota(
                        $nota['id'],
                        $nota['note']           
                    );
                }
                $nota->save();
                $cotizacion->saveNote($nota);
            }
            
            // Eliminar notas sin cotizacion
            Nota::deleteWithoutCotizacion();
            

            echo json_encode([
                'success' => true,
                'message' => 'Cotización actualizada exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (PDOException $e) {
            error_log('Error en CotizacionController::editar(): ' . $e->getMessage());
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

            $cotizacion = Cotizacion::getById($id);

            if (!$cotizacion) {
                $error = 'Cotización no encontrada';
                header('Location: ../views/prices.php?status=error&message=' . urlencode($error));
                exit;
            }   

            if ($cotizacion->delete()) {
                // Aqui tengo que eliminar el pdf de la cotizacion
                
                header('Location: ../views/prices.php?status=success');
                exit;
            }

            $error = 'Error al eliminar la cotización';
            header('Location: ../views/prices.php?status=error&message=' . urlencode($error));
            exit;

        } catch (PDOException $e) {
            error_log('Error en CotizacionController::borrar(): ' . $e->getMessage());
            $error = 'Error al eliminar la cotización';
            header('Location: ../views/prices.php?status=error&message=' . urlencode($error));
            exit;
        }
    }
}

