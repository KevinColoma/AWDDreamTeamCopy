<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login-register.css">
    <title>Iniciar Sesión | Bazar</title>
    
</head>
<body>
    <div class="login-bg">
       
        <div class="login-main-panel">
            <header>
                <h2>Bazar DreamTeam</h2>
                <p class="subtitle">Inicia sesión para continuar</p>
            </header>
            <form action="PHP/login.php" method="POST" class="LoginForm">
                <p>Iniciar Sesión</p>
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">Usuario o contraseña incorrectos.</div>
                <?php endif; ?>
                <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
                    <div class="success-message">¡Registro exitoso! Ahora puedes iniciar sesión.</div>
                <?php endif; ?>
                <?php if (isset($_GET['logout'])): ?>
                    <div class="success-message">Sesión cerrada correctamente.</div>
                <?php endif; ?>
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required placeholder="Ingrese su usuario">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
                <button type="submit">Iniciar Sesión</button>
                <div class="register-link">
                    <span>¿No tienes cuenta?</span>
                    <a href="../PHP/RegistroFrm.php">Regístrate</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>