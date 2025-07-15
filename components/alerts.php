<?php
function alerts() {
    if (isset($_GET['status']) && $_GET['status'] === 'success') {
        echo '<div class="alert alert-success" role="alert">Cambios realizados exitosamente!</div>';
    }

    if (isset($_GET['status']) && $_GET['status'] === 'error') {
        $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Ocurrió un error.';
        echo '<div class="alert alert-danger" role="alert">' . $message . '</div>';
    }
}
?>
