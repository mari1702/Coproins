function toggleEdit(button) {
    const labelName = button.closest('table').querySelector(`#label-name-${button.id}`);
    const inputName = button.closest('table').querySelector(`#input-name-${button.id}`);
    labelName.classList.toggle('d-none');
    inputName.classList.toggle('d-none');
    button.classList.toggle('d-none');
    button.nextElementSibling.classList.toggle('d-none');
}

function saveEdit(button) {
    const inputName = button.closest('table').querySelector(`#input-name-${button.id}`);
    const labelName = button.closest('table').querySelector(`#label-name-${button.id}`);

    const id = button.id;
    const nuevoNombre = inputName.value;
    const table = button.dataset.table;
    const formData = new FormData();
    formData.append('id', id);
    formData.append('name', nuevoNombre);

    fetch('../actions/' + table + '_editar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = window.location.pathname + "?modal=" + data.modal;
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
        inputName.value = labelName.textContent;
    });

    inputName.classList.toggle('d-none');
    labelName.classList.toggle('d-none');
    button.classList.toggle('d-none');
    button.previousElementSibling.classList.toggle('d-none');
}