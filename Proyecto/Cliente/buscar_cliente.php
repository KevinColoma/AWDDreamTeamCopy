<?php
include '../PHP/connection.php';

$taxid = $_GET['taxid'] ?? '';

try {
    // Preparar la consulta con PDO
    $query = $conn->prepare("SELECT * FROM client WHERE TaxID = :taxid");
    
    // Bindear parámetro (PDO usa bindParam o bindValue)
    $query->bindParam(':taxid', $taxid, PDO::PARAM_STR);
    
    // Ejecutar la consulta
    $query->execute();
    
    // Obtener resultados como array asociativo
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    // Establecer cabecera JSON
    header('Content-Type: application/json');
    
    // Devolver resultados en formato JSON
    echo json_encode($result ?: ['error' => 'Cliente no encontrado']);
    
} catch (PDOException $e) {
    // Manejo de errores
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>