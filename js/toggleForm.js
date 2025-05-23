document.addEventListener('DOMContentLoaded', function() {
    const showFormButton = document.getElementById('showForm');
    const formCard = document.querySelector('.new'); 

    showFormButton.addEventListener('click', function() {
        formCard.classList.toggle('d-none');
        
        const icon = this.querySelector('i');
        if (formCard.classList.contains('d-none')) {
            this.innerHTML = '<i class="fas fa-plus"></i> Nuevo';
        } else {
            this.innerHTML = '<i class="fas fa-times"></i> Cancelar';
        }
    });
});