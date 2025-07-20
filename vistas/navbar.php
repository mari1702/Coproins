<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Depuración: Ver qué rol está guardado en la sesión
if (isset($_SESSION['rol'])) {
    echo "<script>console.log('Rol en navbar: " . $_SESSION['rol'] . "');</script>";
} else {
    echo "<script>console.log('No hay rol en la sesión');</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barra de Navegación</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-header">
           
            <button class="menu-toggle" id="menu-toggle">
            <img src="logo.png" alt="Logo" width="25" height="25" />

            </button>
        </div>
        <ul class="nav-list" id="nav-list">
            <img src="logo.png" alt="Logo" width="25" height="25" />
            <li></li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li><a href="ingresos_grafica.php" class="nav-link">Inicio</a></li>
                    <li><a href="proyectos_habilitados.php" class="nav-link">Proyectos</a></li>
                <li><a href="../Coproins/views/prices.php" class="nav-link">Cotizaciones</a></li>

                <?php endif; ?>
                <li><a href="gastos.php" class="nav-link">Gastos</a></li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li><a href="nuevo_usuario.php" class="nav-link">Usuarios</a></li>
                <?php endif; ?>
                <li>
                    <a href="../bd/logout.php" class="logout-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
        </ul>
    </nav>

    <script>
        // Seleccionamos los elementos
        const menuToggle = document.getElementById("menu-toggle");
        const navList = document.getElementById("nav-list");

        // Evento para mostrar u ocultar el menú
        menuToggle.addEventListener("click", () => {
            navList.classList.toggle("active");
        });
    </script>

</body>
</html>
