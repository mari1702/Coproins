function confirmarEliminacion(boton) {
    if (confirm('¿Estás seguro de eliminar este elemento?')) {
        boton.closest('form').submit();
    }
}