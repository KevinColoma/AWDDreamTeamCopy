<?php
include '../PHP/connection.php'; 

$query = $_GET['q'] ?? '';

try {
    $stmt = $conn->prepare("SELECT id, name, salePrice FROM products 
                           WHERE name LIKE :query OR id LIKE :query 
                           LIMIT 10");
    $searchTerm = "%$query%";
    $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($productos);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>