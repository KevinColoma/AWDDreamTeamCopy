<?php

include_once 'connection.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Buscar el usuario en la base de datos
    $sql = "SELECT * FROM usuarios WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Inicio de sesión exitoso
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: ../HTML/nav.html');
            exit();
        } else {
            header('Location: ../LoginFrm.php?error=1');
            exit();
        }
    } else {
        header('Location: ../LoginFrm.php?error=1');
        exit();
    }
}
?>