<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Registro</title>
</head>
<body>
 
    <div class="LoginForm">
        <form action="PHP/registro.php" method="POST" class="inputsForm">
            <p>Registro</p>
            <label for="username">Usuario</label>
            <input type="text" id="username" name="username" required placeholder="Ingrese su usuario">

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required placeholder="Ingrese su correo">

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">

            <label for="confirm_password">Confirmar Contraseña</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirme su contraseña">

            <button type="submit">Registrarse</button>
            <div class="register-link">
                <span>¿Ya tienes cuenta?</span>
                <a href="index.html">Inicia sesión</a>
            </div>
        </form>
    </div>

</body>
</html>
