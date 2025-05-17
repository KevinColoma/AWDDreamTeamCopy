<?php
$host = 'localhost';
$username = 'root'; // Cambia por tu usuario de MySQL
$password = 'rootroot'; // Cambia por tu contraseña de MySQL
$database = 'variedades';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>