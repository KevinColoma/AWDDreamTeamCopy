<?php

include_once '../../conexion/conexio';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);


    if ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        
        $sql = "SELECT id FROM usuarios WHERE username = ? OR email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "El usuario o correo ya existe.";
        } else {
            
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $username, $email, $hash);
            if ($stmt->execute()) {
                header('Location: index.html?registro=exitoso');
                exit();
            } else {
                $error = "Error al registrar. Intenta de nuevo.";
            }
        }
    }
}
?>
