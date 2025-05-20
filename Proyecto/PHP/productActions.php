<?php
header('Content-Type: application/json');
include 'connection.php';

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

// Depuración: Mostrar todos los datos recibidos
error_log("Datos recibidos: " . print_r($_POST, true));

if ($action === 'create' || $action === 'update') {
    $productId = $action === 'create' ? uniqid('prod_') : ($_POST['id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $categoryId = trim($_POST['categoryId'] ?? '');
    $purchaseUnit = trim($_POST['purchaseUnit'] ?? '');
    $quantityIncluded = isset($_POST['quantityIncluded']) ? (int)$_POST['quantityIncluded'] : 0;
    $saleUnit = trim($_POST['saleUnit'] ?? '');
    $purchasePrice = isset($_POST['purchasePrice']) ? (float)$_POST['purchasePrice'] : 0.0;
    $salePrice = isset($_POST['salePrice']) ? (float)$_POST['salePrice'] : 0.0;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    $supplierId = trim($_POST['supplierId'] ?? '');

    // Depuración: Verificar el valor recibido de categoryId
    error_log("categoryId recibido: $categoryId");

    // Validaciones
    if (empty($name)) {
        $response['message'] = 'El nombre del producto es obligatorio.';
        echo json_encode($response);
        exit();
    }
    if (empty($categoryId)) {
        $response['message'] = 'Debe seleccionar una categoría.';
        echo json_encode($response);
        exit();
    }
    if (empty($purchaseUnit)) {
        $response['message'] = 'La unidad de compra es obligatoria.';
        echo json_encode($response);
        exit();
    }
    if ($quantityIncluded < 1) {
        $response['message'] = 'La cantidad incluida debe ser mayor a 0.';
        echo json_encode($response);
        exit();
    }
    if (empty($saleUnit)) {
        $response['message'] = 'La unidad de venta es obligatoria.';
        echo json_encode($response);
        exit();
    }
    if ($purchasePrice < 0.01) {
        $response['message'] = 'El precio de compra debe ser mayor a 0.';
        echo json_encode($response);
        exit();
    }
    if ($salePrice < 0.01) {
        $response['message'] = 'El precio de venta debe ser mayor a 0.';
        echo json_encode($response);
        exit();
    }
    if ($stock < 1) {
        $response['message'] = 'El stock debe ser mayor a 0.';
        echo json_encode($response);
        exit();
    }
    if (empty($supplierId)) {
        $response['message'] = 'Debe seleccionar un proveedor.';
        echo json_encode($response);
        exit();
    }

    try {
        if ($action === 'create') {
            $stmt = $conn->prepare("
                INSERT INTO products (id, name, categoryId, purchaseUnit, quantityIncluded, saleUnit, purchasePrice, salePrice, stock)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$productId, $name, $categoryId, $purchaseUnit, $quantityIncluded, $saleUnit, $purchasePrice, $salePrice, $stock]);
            $response['message'] = 'Producto creado exitosamente.';
        } else {
            $stmt = $conn->prepare("
                UPDATE products 
                SET name = ?, categoryId = ?, purchaseUnit = ?, quantityIncluded = ?, saleUnit = ?, purchasePrice = ?, salePrice = ?, stock = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $categoryId, $purchaseUnit, $quantityIncluded, $saleUnit, $purchasePrice, $salePrice, $stock, $productId]);
            $response['message'] = 'Producto actualizado exitosamente.';
        }

        // Manejar proveedor
        $stmt = $conn->prepare("DELETE FROM productsupplier WHERE productId = ?");
        $stmt->execute([$productId]);
        $stmt = $conn->prepare("INSERT INTO productsupplier (productId, supplierId) VALUES (?, ?)");
        $stmt->execute([$productId, $supplierId]);

        $response['success'] = true;
    } catch (PDOException $e) {
        error_log("Error al procesar producto: " . $e->getMessage());
        $response['message'] = 'Error al procesar producto: ' . $e->getMessage();
    }
} elseif ($action === 'delete') {
    $productId = $_POST['id'] ?? '';

    try {
        $stmt = $conn->prepare("DELETE FROM productsupplier WHERE productId = ?");
        $stmt->execute([$productId]);
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $response['success'] = true;
        $response['message'] = 'Producto eliminado exitosamente.';
    } catch (PDOException $e) {
        error_log("Error al eliminar producto: " . $e->getMessage());
        $response['message'] = 'Error al eliminar producto: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>