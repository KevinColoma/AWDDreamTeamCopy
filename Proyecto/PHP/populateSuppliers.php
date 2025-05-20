<?php
error_log("Ejecutando populateSuppliers.php: " . date('Y-m-d H:i:s'));

include 'connection.php';

try {
    $stmt = $conn->query("SELECT id, company FROM suppliers");
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Proveedores encontrados: " . count($suppliers) . " - " . date('Y-m-d H:i:s'));
    if (count($suppliers) === 0) {
        echo "<option value='' disabled>No hay proveedores disponibles</option>";
    } else {
        foreach ($suppliers as $row) {
            $id = htmlspecialchars($row['id']);
            $company = htmlspecialchars($row['company']);
            echo "<option value='$id'>$company</option>";
        }
    }
} catch (PDOException $e) {
    error_log("Error al cargar proveedores: " . $e->getMessage() . " - " . date('Y-m-d H:i:s'));
    echo "<option value='' disabled>Error al cargar proveedores: " . htmlspecialchars($e->getMessage()) . "</option>";
}
?>