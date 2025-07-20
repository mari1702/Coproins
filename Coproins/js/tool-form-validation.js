document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const brandSelect = document.getElementById('brand');
    const departmentSelect = document.getElementById('department');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Validar unidad de medida
        if (brandSelect.selectedIndex === 0) {
            alert('Por favor, seleccione una marca válida');
            brandSelect.focus();
            return;
        }

        // Validar categoría
        if (departmentSelect.selectedIndex === 0) {
            alert('Por favor, seleccione un departamento válido');
            departmentSelect.focus();
            return;
        }

        // Si todo está bien, enviar el formulario
        this.submit();
    });
}); 