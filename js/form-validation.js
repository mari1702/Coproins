document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const measurementSelect = document.getElementById('measurement');
    const categorySelect = document.getElementById('category');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Validar unidad de medida
        if (measurementSelect.selectedIndex === 0) {
            alert('Por favor, seleccione una unidad de medida válida');
            measurementSelect.focus();
            return;
        }

        // Validar categoría
        if (categorySelect.selectedIndex === 0) {
            alert('Por favor, seleccione una categoría válida');
            categorySelect.focus();
            return;
        }

        // Si todo está bien, enviar el formulario
        this.submit();
    });
}); 