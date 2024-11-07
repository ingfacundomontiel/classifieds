document.addEventListener('DOMContentLoaded', function () {
    const classifiedForm = document.getElementById('classifiedForm');
    if (!classifiedForm) return;

    const submitButton = classifiedForm.querySelector('input[type="submit"]');
    const loader = document.getElementById('loader');

    // Mensaje de respuesta
    const responseMessage = document.createElement('div');
    responseMessage.classList.add('response-message');

    // Añadir los elementos al DOM
    classifiedForm.appendChild(responseMessage);

    classifiedForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Evita el envío normal del formulario

        // Muestra el loader y deshabilita el botón de enviar
        loader.style.display = 'block';
        responseMessage.style.display = 'none';
        submitButton.disabled = true;

        const formData = new FormData(classifiedForm);
        formData.append('action', 'submit_classified'); // Añadir la acción para WordPress AJAX

        fetch(classifiedForm.dataset.ajaxUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // Ocultar el loader y habilitar el botón de enviar
                loader.style.display = 'none';
                submitButton.disabled = true;
                submitButton.value = 'Enviado';

                if (data.success) {
                    responseMessage.textContent = '¡Tu Clasificado se ha creado correctamente!<br>La publicación puede demorarse un plazo efectivo máximo de hasta 48 hs.';
                    responseMessage.style.color = 'green';
                } else {
                    responseMessage.textContent = 'Hubo un error: ' + data.data;
                    responseMessage.style.color = 'red';
                }

                responseMessage.style.display = 'block';
            })
            .catch(error => {
                // Ocultar el loader y habilitar el botón de enviar
                loader.style.display = 'none';
                submitButton.disabled = false;

                responseMessage.textContent = 'Hubo un error en el envío del formulario. Inténtalo nuevamente.';
                responseMessage.style.color = 'red';
                responseMessage.style.display = 'block';
            });
    });
});
