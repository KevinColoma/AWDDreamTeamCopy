<?php
header('Content-Type: application/json');
include 'connection.php';

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

if ($action === 'create' || $action === 'update') {
    $categoryId = $action === 'create' ? uniqid('cat_') : ($_POST['categoryId'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validar que el ID no esté vacío
    if (empty($categoryId)) {
        $response['message'] = 'El ID de la categoría no puede estar vacío.';
        echo json_encode($response);
        exit();
    }

    // Reemplazar múltiples espacios por un solo espacio
    $type = preg_replace('/\s+/', ' ', $type);

    // Validar solo letras y un espacio entre palabras
    if (!preg_match('/^[A-Za-z]+( [A-Za-z]+)*$/', $type)) {
        $response['message'] = 'El nombre de la categoría solo puede contener letras y un espacio entre palabras.';
        echo json_encode($response);
        exit();
    }

    // Capitalizar la primera letra de cada palabra
    $type = ucwords(strtolower($type));

    // Verificar duplicados (case-insensitive)
    try {
        $stmt = $conn->prepare("SELECT id FROM categories WHERE LOWER(type) = LOWER(?) AND id != ?");
        $stmt->execute([$type, $categoryId]);
        if ($stmt->fetch()) {
            $response['message'] = "Ya existe una categoría con el nombre '$type'.";
            echo json_encode($response);
            exit();
        }

        if ($action === 'create') {
            $stmt = $conn->prepare("INSERT INTO categories (id, type, description) VALUES (?, ?, ?)");
            $stmt->execute([$categoryId, $type, $description]);
            $response['message'] = 'Categoría creada exitosamente.';
        } else {
            $stmt = $conn->prepare("UPDATE categories SET type = ?, description = ? WHERE id = ?");
            $stmt->execute([$type, $description, $categoryId]);
            $response['message'] = 'Categoría actualizada exitosamente.';
        }
        $response['success'] = true;
    } catch (PDOException $e) {
        error_log("Error al procesar categoría: " . $e->getMessage());
        $response['message'] = 'Error al procesar categoría: ' . $e->getMessage();
    }
} elseif ($action === 'delete') {
    $categoryId = $_POST['categoryId'] ?? '';

    try {
        // Verificar si hay productos asociados
        $stmt = $conn->prepare("SELECT id FROM products WHERE categoryId = ?");
        $stmt->execute([$categoryId]);
        if ($stmt->fetch()) {
            $response['message'] = 'No se puede eliminar la categoría porque tiene productos asociados.';
            echo json_encode($response);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $response['success'] = true;
        $response['message'] = 'Categoría eliminada exitosamente.';
    } catch (PDOException $e) {
        error_log("Error al eliminar categoría: " . $e->getMessage());
        $response['message'] = 'Error al eliminar categoría: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>