<?php
error_log("Ejecutando populateCategories.php: " . date('Y-m-d H:i:s'));

include 'connection.php';

try {
    $stmt = $conn->query("SELECT id, type, description FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Categorías encontradas: " . count($categories) . " - " . date('Y-m-d H:i:s'));
    if (count($categories) === 0) {
        echo "<option value='' disabled>No hay categorías disponibles</option>";
    } else {
        foreach ($categories as $row) {
            $id = htmlspecialchars($row['id']);
            $type = htmlspecialchars($row['type']);
            $description = htmlspecialchars($row['description']);
            echo "<option value='$id' title='$description'>$type 
                <button type='button' class='edit' onclick=\"openCategoryModal('$id', '$type', '$description')\" style='margin-left: 10px;'><i class='fas fa-edit'></i></button>
                <button type='button' class='delete' onclick=\"openConfirmModal('category', '$id')\" style='margin-left: 5px;'><i class='fas fa-trash-alt'></i></button>
            </option>";
        }
    }
} catch (PDOException $e) {
    error_log("Error al cargar categorías: " . $e->getMessage() . " - " . date('Y-m-d H:i:s'));
    echo "<option value='' disabled>Error al cargar categorías: " . htmlspecialchars($e->getMessage()) . "</option>";
}
?>