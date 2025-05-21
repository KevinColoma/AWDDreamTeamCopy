function requireLogin(e) {
    if (!window.isLogged) {
        e.preventDefault();
        alert('Debes iniciar sesión para acceder a esta sección.');
    }
}

// Validación de login y registro
function validateLoginForm(form) {
    const username = form.username.value.trim();
    const password = form.password.value.trim();
    if (!username || !password) {
        alert('Por favor, completa todos los campos.');
        return false;
    }
    return true;
}

function validateRegisterForm(form) {
    const username = form.username.value.trim();
    const email = form.email.value.trim();
    const password = form.password.value.trim();
    const confirm = form.confirm_password.value.trim();
    if (!username || !email || !password || !confirm) {
        alert('Por favor, completa todos los campos.');
        return false;
    }
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
        alert('El correo electrónico no es válido.');
        return false;
    }
    if (password.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres.');
        return false;
    }
    if (password !== confirm) {
        alert('Las contraseñas no coinciden.');
        return false;
    }
    return true;
}


window.logoutUser = function(e) {
    if (e) e.preventDefault();
    window.location.href = '../PHP/logout.php';
};

document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('.menu-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            requireLogin(e);
        });
    });
    
    const loginForm = document.querySelector('.LoginForm[action*="login.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm(loginForm)) {
                e.preventDefault();
            }
        });
    }
   
    const registerForm = document.querySelector('.LoginForm[action*="registro.php"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            if (!validateRegisterForm(registerForm)) {
                e.preventDefault();
            }
        });
    }
});
