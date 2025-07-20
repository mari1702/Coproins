<?php
require_once '../models/Cliente.php';


class ClienteController {
    public static function listar() {
        try {
            return Cliente::getAll();
        } catch (\PDOException $e) {
            error_log('Error al listar clientes: ' . $e->getMessage());
            return [];
        }
    }
}
