

window.isLogged = false;

function requireLogin(e) {
    if (!window.isLogged) {
        e.preventDefault();
        alert('Debes iniciar sesión para acceder a esta sección.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Selecciona todos los menús
    document.querySelectorAll('.menu-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            requireLogin(e);
        });
    });
});
