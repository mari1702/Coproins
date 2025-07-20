console.log("El archivo sucursal.js se ha cargado correctamente");

// Habilitar edición de los campos específicos
window.habilitarEdicion = function () {
    console.log("Función habilitarEdicion llamada");

    // Habilitar solo los campos permitidos
    document.getElementById("localidad").removeAttribute("readonly");
    document.getElementById("estado").removeAttribute("readonly");
    document.getElementById("encargado").removeAttribute("readonly");
    document.getElementById("costoInicial").removeAttribute("readonly");
    document.getElementById("fecha").removeAttribute("readonly");

    // Mostrar el botón de guardar
    document.querySelector(".edit-button").style.display = "none";
    document.querySelector(".save-button").style.display = "inline-block";
};

// Guardar cambios en la base de datos
window.guardarEdicion = function () {
    let idSucursal = document.getElementById("sucursalId").value;
    let localidad = document.getElementById("localidad").value;
    let estado = document.getElementById("estado").value;
    let encargado = document.getElementById("encargado").value;
    let costoInicial = document.getElementById("costoInicial").value;
    let fecha = document.getElementById("fecha").value;

    console.log("Enviando datos:", { idSucursal, localidad, estado, encargado, costoInicial, fecha });

    fetch('../php/actualizar_sucursal.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `idSucursal=${idSucursal}&localidad=${encodeURIComponent(localidad)}&estado=${encodeURIComponent(estado)}&encargado=${encodeURIComponent(encargado)}&costoInicial=${encodeURIComponent(costoInicial)}&fecha=${encodeURIComponent(fecha)}`
    })
    .then(response => response.text())
    .then(data => {
        console.log("Respuesta del servidor:", data);
        alert("Sucursal actualizada correctamente.");
        location.reload();
    })
    .catch(error => {
        console.error("Error al guardar:", error);
        alert("Hubo un error al actualizar la sucursal.");
    });
};





window.eliminarSucursal = function () {
    console.log("✅ Botón Eliminar presionado");

    let idSucursal = document.getElementById("sucursalId").value;
    if (!idSucursal) {
        console.error("⛔ No se encontró el ID de la sucursal.");
        alert("Error: No se encontró el ID de la sucursal.");
        return;
    }

    if (!confirm("⚠️ ¿Estás seguro de que deseas eliminar esta sucursal? Esta acción no se puede deshacer.")) {
        return; // Si el usuario cancela, no hace nada
    }

    console.log("🗑️ Eliminando sucursal con ID:", idSucursal);

    fetch('../php/eliminar_sucursal.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `idSucursal=${idSucursal}`
    })
    .then(response => response.text())
    .then(data => {
        console.log("📨 Respuesta del servidor:", data);
        alert(data); // Mostrar respuesta del servidor

        // 🔹 Cerrar el modal correctamente
        let modal = document.getElementById("projectModal");
        modal.classList.remove("show");

        // 🔹 Evitar que el modal vuelva a abrirse
        modal.innerHTML = "";
        modal.onclick = null;

        // 🔄 Redirigir a la página SIN reenvío de formulario (evita el error)
        window.location.href = window.location.pathname + window.location.search;
    })
    .catch(error => {
        console.error("❌ Error al eliminar:", error);
        alert("Hubo un error al eliminar la sucursal.");
    });
};
