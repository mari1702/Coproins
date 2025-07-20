document.addEventListener("DOMContentLoaded", function () {
    // Obtener los modales
    const clientModal = document.getElementById("clientModal");
    const encargadoModal = document.getElementById("encargadoModal");
    const modal = document.getElementById("projectModal");
    const closeButton = document.querySelector(".close-button");
    
    window.closeModal = function () {
        modal.classList.remove("show");
        window.location.href = "proyectos_habilitados.php";};
    // Función para abrir el modal de agregar cliente
    window.openClientModal = function () {
        clientModal.classList.add("show");
    };

    // Función para cerrar el modal de agregar cliente
    window.closeClientModal = function () {
        clientModal.classList.remove("show");
    };

    // Función para abrir el modal de agregar encargado
    window.openEncargadoModal = function () {
        encargadoModal.classList.add("show");
    };

    // Función para cerrar el modal de agregar encargado
    window.closeEncargadoModal = function () {
        encargadoModal.classList.remove("show");
    };

    // Cerrar el modal si el usuario hace clic fuera de él
    window.onclick = function (event) {
        if (event.target.classList.contains("modal")) {
            event.target.classList.remove("show");
        }
    };

     // Función para abrir el modal detalles
     window.openModalDetalles = function () {
        openModal.classList.add("show");
    };

     // Función para cerrar el modal de detalles
     window.closeModalDetalles = function () {
        openModal.classList.remove("show");
    };
});
document.addEventListener("DOMContentLoaded", function () {
    // Seleccionar el modal y su contenido
    const modal = document.getElementById("projectModal");
    const modalBody = document.getElementById("modalBody");
    const closeButton = document.querySelector(".close-button");

    // Agregar evento a todos los botones de detalles
    document.querySelectorAll(".openModal").forEach(button => {
        button.addEventListener("click", function () {
            const clientId = this.getAttribute("data-id_cliente"); // Obtener ID del cliente
            fetch(`../php/detalles_proyecto.php?id_cliente=${clientId}`) // Cargar contenido dinámico
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data; // Mostrar contenido en el modal
                    modal.classList.add("show"); // Mostrar modal
                })
                .catch(error => console.error("Error al cargar detalles:", error));
        });
    });

    // Cerrar el modal al hacer clic en el botón de cierre
    closeButton.addEventListener("click", function () {
        modal.classList.remove("show");
    });

    // Cerrar el modal si el usuario hace clic fuera de él
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.classList.remove("show");
        }
    };
});

document.addEventListener("DOMContentLoaded", function () {
    // Modals
    window.openClientModal = () => document.getElementById("clientModal").classList.add("show");
    window.closeClientModal = () => document.getElementById("clientModal").classList.remove("show");

    window.openContactoModal = () => document.getElementById("contactoModal").classList.add("show");
    window.closeContactoModal = () => document.getElementById("contactoModal").classList.remove("show");

    window.openTrabajadorModal = () => document.getElementById("trabajadorModal").classList.add("show");
    window.closeTrabajadorModal = () => document.getElementById("trabajadorModal").classList.remove("show");

    // Cargar Contactos según el Cliente seleccionado
    window.cargarContactos = function () {
        const clienteId = document.getElementById("cliente").value;
        if (!clienteId) return;

        document.getElementById("contacto").innerHTML = `<option>Cargando...</option>`;
        
        fetch(`../php/obtener_contactos.php?id_cliente=${clienteId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById("contacto").innerHTML = data;
            })
            .catch(error => console.error("Error cargando contactos:", error));
    };
});






document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("projectModal");
    const closeButton = document.querySelector(".close-button");

    function asignarEventosModales() {
        const botonesDetalles = document.querySelectorAll(".openModalDetalles");

        if (botonesDetalles.length === 0) {
            console.log("❌ No se encontraron botones de detalles.");
            return;
        }

        botonesDetalles.forEach(button => {
            button.addEventListener("click", function () {
                console.log("🔍 Botón clickeado:", button);

                if (!modal) {
                    console.error("❌ Error: El modal no está en el DOM.");
                    return;
                }

                // Obtener los campos del modal
                const clienteId = document.getElementById("clienteId");
                const modalCliente = document.getElementById("modalCliente");
                const modalTelefono = document.getElementById("modalTelefono");
                const modalCostoInicial = document.getElementById("modalCostoInicial");
                const modalIngresos = document.getElementById("modalIngresos");
                const modalEgresosSucursal = document.getElementById("modalEgresosSucursal");
                const modalEgresosCliente = document.getElementById("modalEgresosCliente");
                const editButton = document.getElementById("editButton");
                const saveButton = document.getElementById("saveButton");

                if (!clienteId || !modalCliente || !modalTelefono || !modalCostoInicial || !modalIngresos || !modalEgresosSucursal || !modalEgresosCliente || !editButton || !saveButton) {
                    console.error("❌ Error: Algunos elementos del modal no existen en el DOM.");
                    return;
                }

                // Asignar valores
                clienteId.value = button.getAttribute("data-id");
                modalCliente.value = button.getAttribute("data-cliente");
                modalTelefono.value = button.getAttribute("data-telefono");
                modalCostoInicial.value = "$" + button.getAttribute("data-costoinicial");
                modalIngresos.value = "$" + button.getAttribute("data-ingresos");
                modalEgresosSucursal.value = "$" + button.getAttribute("data-egresossucursal");
                modalEgresosCliente.value = "$" + button.getAttribute("data-egresoscliente");

                // Bloquear los campos hasta que se haga clic en "Editar"
                modalCliente.setAttribute("readonly", true);
                modalTelefono.setAttribute("readonly", true);

                // Mostrar el modal
                modal.classList.add("show");

                // ✅ Asignar eventos a los botones dentro del modal
                asignarEventosBotonesModal();
            });
        });

        console.log("✅ Eventos de botón asignados correctamente.");
    }

    function asignarEventosBotonesModal() {
        const saveButton = document.getElementById("saveButton");
        const editButton = document.getElementById("editButton");
        const deleteButton = document.querySelector(".deleteProyecto");

        if (!saveButton || !editButton || !deleteButton) {
            console.error("❌ Error: Botones de acción no encontrados en el modal.");
            return;
        }

        // Evento de editar (Habilitar edición)
        editButton.onclick = function () {
            console.log("✏️ Habilitando edición...");
            document.getElementById("modalCliente").removeAttribute("readonly");
            document.getElementById("modalTelefono").removeAttribute("readonly");

            // Mostrar botón de guardar
            saveButton.style.display = "block";
            editButton.style.display = "none"; // Ocultar botón de editar
        };

        // Evento de guardar (Actualizar Cliente)
        saveButton.onclick = function () {
            const idCliente = document.getElementById("clienteId").value;
            const nuevoNombre = document.getElementById("modalCliente").value;
            const nuevoTelefono = document.getElementById("modalTelefono").value;

            fetch("../php/actualizar_cliente.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id_cliente=${idCliente}&cliente=${encodeURIComponent(nuevoNombre)}&telefono=${encodeURIComponent(nuevoTelefono)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("✅ Cliente actualizado correctamente.");
                    modal.classList.remove("show");
                    location.reload();
                } else {
                    alert("❌ Error al actualizar cliente: " + data.error);
                }
            })
            .catch(error => console.error("❌ Error en la actualización:", error));
        };

        // Evento de eliminar (Eliminar Cliente y sus Proyectos)
        deleteButton.onclick = function () {
            let idCliente = document.getElementById("clienteId").value;

            if (!idCliente) {
                alert("❌ Error: No se encontró el ID del cliente.");
                return;
            }

            if (!confirm("⚠️ ¿Estás seguro de eliminar este cliente y sus proyectos?")) {
                return;
            }

            fetch("../php/eliminar_proyecto.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id_cliente=${encodeURIComponent(idCliente)}`
            })
            .then(response => response.json())
            .then(data => {
                console.log("🔍 Respuesta del servidor:", data); // 👈 Agrega esto para depurar
            
                if (data.success) {
                    alert("✅ Cliente eliminado correctamente.");
                    
                    let modal = document.querySelector(".modal.show");
                    if (modal) {
                        modal.classList.remove("show");
                    }
            
                    // 🔄 Recargar la página después de 0.5 segundos
                    setTimeout(() => {
                        window.location.href = "proyectos_habilitados.php";
                    }, 500);
                    
                } else {
                    alert("❌ Error al eliminar: " + data.error);
                }
            })
            .catch(error => console.error("❌ Error en fetch:", error));
            
        };
    }

    asignarEventosModales();

    closeButton.addEventListener("click", function () {
        modal.classList.remove("show");
    });

    window.onclick = function (event) {
        if (event.target === modal) {
            modal.classList.remove("show");
        }
    };

    setTimeout(() => {
        console.log("⏳ Verificando botones después de carga...");
        asignarEventosModales();
    }, 500);
});
