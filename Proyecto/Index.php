<?php
session_start();

$isLogged = isset($_SESSION['user_id']) ? 'true' : 'false';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/main-menu.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <title>Bazar Prototipo - Página Principal</title>
</head>
<body>
    <script>
        window.isLogged = <?php echo $isLogged; ?>;
    </script>

    <div class="sushi-box">
        <div class="main-menu">
            <div class="menu-card" onclick="window.location.href='#productos'">
                <i class="ri-box-3-line menu-icon"></i>
                <h2>Productos</h2>
                <p>Explora y administra el inventario de productos.</p>
            </div>
            <div class="menu-card" onclick="window.location.href='HTML/suppliers.html'">
                <i class="ri-truck-line menu-icon"></i>
                <h2>Proveedores</h2>
                <p>Consulta y administra tus proveedores.</p>
            </div>
            <div class="menu-card" onclick="window.location.href='CLiente/Factura.html'">
                <i class="ri-file-list-3-line menu-icon"></i>
                <h2>Facturación</h2>
                <p>Genera y consulta facturas fácilmente.</p>
            </div>
            <!-- Agregar menu  desde aqui-->
        </div>
        <div style="text-align:center; width:100%;">
            <a href="PHP/LoginFrm.php" class="login-btn">Iniciar Sesión</a>
        </div>
    </div>

    <script src="JS/main-menu-login-guard.js"></script>
</body>
</html>
