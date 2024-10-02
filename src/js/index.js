// Importa el archivo SCSS principal para tu plugin
import '../scss/main.scss';

document.addEventListener('DOMContentLoaded', function () {
    const openModalLinks = document.querySelectorAll('.open-modal');
    const closeModalButtons = document.querySelectorAll('.close');
    let openModal = null; // Para rastrear el modal actualmente abierto

    // Abrir el modal al hacer clic en una imagen
    openModalLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const modal = document.querySelector(this.getAttribute('href'));
            modal.style.display = 'block';
            openModal = modal; // Guarda el modal actualmente abierto
        });
    });

    // Cerrar el modal al hacer clic en el botón de cerrar
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.getAttribute('data-modal-id');
            document.querySelector(modalId).style.display = 'none';
            openModal = null; // No hay modales abiertos después de cerrar
        });
    });

    // Cerrar el modal al hacer clic fuera del contenido
    window.addEventListener('click', function (e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
            openModal = null;
        }
    });

    // Cerrar el modal al presionar la tecla Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && openModal) {
            openModal.style.display = 'none';
            openModal = null; // Restablece la referencia al modal
        }
    });
});

