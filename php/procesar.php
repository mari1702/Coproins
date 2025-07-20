<?php
require '../bd/db_conexion.php';
session_start();

$idAdmin = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!empty($_POST['cliente']) && !empty($_POST['trabajador']) && !empty($_POST['localidad']) && isset($_POST['costo'])) {
            $idCliente = $_POST['cliente'];
            $idTrabajador = $_POST['trabajador'];
            $estado = $_POST['estado'];
            $localidad = $_POST['localidad'];
            $costo = str_replace(',', '', $_POST['costo']);  // Eliminar comas
            $costo = floatval($costo);  // Asegurar formato numérico
            $fecha = date('Y-m-d');
            $idAdminCompartido = $_POST['id_admin_compartido'] ?? null;  // Admin con el que se comparte
            $pdo->beginTransaction();  // Empezamos transacción

            // 1. Registrar el nuevo proyecto
            $stmt = $pdo->prepare("
                INSERT INTO nuevo_proyecto (id_cliente, id_encargado, estado, localidad, costo_inicial, fecha, id_admin_creador) 
                VALUES (:id_cliente, :id_trabajador, :estado, :localidad, :costo, :fecha, :id_admin_creador)
            ");
            $stmt->execute([
                ':id_cliente' => $idCliente,
                ':id_trabajador' => $idTrabajador,
                ':estado' => $estado,
                ':localidad' => $localidad,
                ':costo' => $costo,
                ':fecha' => $fecha,
                ':id_admin_creador' => $idAdmin
            ]);

            // 2. Obtener el ID del nuevo proyecto
            $idNuevoProyecto = $pdo->lastInsertId();
                // 🔥 2️⃣ Si se seleccionó otro admin, compartir el proyecto con él
                if (!empty($idAdminCompartido)) {
                    $sqlCompartir = "INSERT INTO proyectos_compartidos (id_proyecto, id_admin) VALUES (:id_proyecto, :id_admin)";
                    $stmtCompartir = $pdo->prepare($sqlCompartir);
                    $stmtCompartir->bindParam(':id_proyecto', $idNuevoProyecto, PDO::PARAM_INT); // 🔥 CORREGIDO
                    $stmtCompartir->bindParam(':id_admin', $idAdminCompartido, PDO::PARAM_INT);
                    $stmtCompartir->execute();
                }
                
            // 3. Registrar el primer costo inicial en historial_costo_proyecto
            $stmtHistorial = $pdo->prepare("
                INSERT INTO historial_costo_proyecto (id_nuevo_proyecto, costo, diferencia, fecha_modificacion) 
                VALUES (:id_nuevo_proyecto, :costo, :diferencia, NOW())
            ");
            $stmtHistorial->execute([
                ':id_nuevo_proyecto' => $idNuevoProyecto,
                ':costo' => $costo,
                ':diferencia' => 0  // Es el primer costo, no hay diferencia
            ]);

            $pdo->commit();  // Confirmamos cambios en BD

            echo "<script>
                    alert('Proyecto registrado correctamente con historial de costo inicial.');
                    window.location.href = '../vistas/proyectos_habilitados.php';
                  </script>";
            exit();

        }
        // Verificar si se está registrando una empresa
        
        if (!empty($_POST['clientName']) && !empty($_POST['empresaTelefono'])) {
            $clientName = trim($_POST['clientName']);
            $empresaTelefono = trim($_POST['empresaTelefono']);

            $stmt = $pdo->prepare("INSERT INTO cliente (cliente, telefono_cliente, id_admin_creador) VALUES (:clientName, :telefono, :id_admin)");
            $stmt->execute([
                ':clientName' => $clientName,
                ':telefono' => $empresaTelefono,
                ':id_admin' => $idAdmin
            ]);

            echo "<script>
                alert('Empresa registrada correctamente.');
                window.location.href = '../vistas/proyecto.php';
            </script>";
            exit();
        }
        // Registro de un nuevo contacto
        if (!empty($_POST['contactoNombre'])) {
            $contactoNombre = trim($_POST['contactoNombre']);
            $contactoTelefono = !empty($_POST['contactoTelefono']) ? trim($_POST['contactoTelefono']) : NULL;
            $idEmpresa = trim($_POST['id_cliente']);

            // Verificar si se seleccionó una empresa
            if (empty($idEmpresa)) {
                echo "<script>
                        alert('Por favor, seleccione una empresa antes de agregar un contacto.');
                        window.location.href = '../vistas/proyecto.php';
                    </script>";
                exit();
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO contacto (id_cliente, nombre, telefono) VALUES (:idEmpresa, :contactoNombre, :contactoTelefono)");
                $stmt->execute([
                    ':idEmpresa' => $idEmpresa,
                    ':contactoNombre' => $contactoNombre,
                    ':contactoTelefono' => $contactoTelefono
                ]);

                echo "<script>
                        alert('Contacto registrado correctamente.');
                        window.location.href = '../vistas/proyecto.php';
                    </script>";
                exit();
            } catch (PDOException $e) {
                echo "<script>
                        alert('Error al registrar el contacto: " . $e->getMessage() . "');
                        window.location.href = '../vistas/proyecto.php';
                    </script>";
                exit();
            }
        }


        // Registro de un nuevo encargado
        if (!empty($_POST['encargadoNombre'])) {
            $encargadoNombre = trim($_POST['encargadoNombre']);
            $encargadoTelefono = !empty($_POST['encargadoTelefono']) ? trim($_POST['encargadoTelefono']) : NULL;
            $idCliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
            $idAdmin = $_SESSION['id_usuario'];

            // Validar que se haya seleccionado una empresa
            if ($idCliente === 0) {
                echo "<script>
                        alert('Por favor, seleccione una empresa antes de agregar un responsable.');
                        window.location.href = '../vistas/proyecto.php';
                    </script>";
                exit();
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO encargado (nombre_completo, telefono_encargado, id_admin_creador, id_cliente) 
                                    VALUES (:nombre, :telefono, :id_admin, :id_cliente)");
                $stmt->execute([
                    ':nombre' => $encargadoNombre,
                    ':telefono' => $encargadoTelefono,
                    ':id_admin' => $idAdmin,
                    ':id_cliente' => $idCliente
                ]);

                echo "<script>
                        alert('Responsable registrado correctamente.');
                        window.location.href = '../vistas/proyecto.php';
                    </script>";
                exit();
            } catch (PDOException $e) {
                echo "<script>
                        alert('Error al registrar responsable: " . $e->getMessage() . "');
                        window.location.href = '../vistas/proyecto.php';
                    </script>";
                exit();
            }
        }



    } catch (PDOException $e) {
        die("Error al registrar el dato: " . $e->getMessage());
    }
}
?>
