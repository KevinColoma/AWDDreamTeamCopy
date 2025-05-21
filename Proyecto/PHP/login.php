<?php

include_once 'connection.php'; // Asegúrate que $conn sea una instancia de PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Buscar el usuario usando PDO
    $sql = "SELECT * FROM usuarios WHERE username = :username LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $user['password'])) {
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
