<?php
include 'connection.php';

try {
    $stmt = $conn->query("
        SELECT p.id, p.name, c.type AS category, p.purchasePrice, p.salePrice, p.stock, 
               p.availability, GROUP_CONCAT(s.company) AS suppliers,
               p.purchaseUnit, p.quantityIncluded, p.saleUnit, p.categoryId,
               GROUP_CONCAT(ps.supplierId) AS supplierIds
        FROM products p
        LEFT JOIN categories c ON p.categoryId = c.id
        LEFT JOIN productsupplier ps ON p.id = ps.productId
        LEFT JOIN suppliers s ON ps.supplierId = s.id
        GROUP BY p.id
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($products) === 0) {
        echo "<tr><td colspan='9' class='empty-state'><i class='fas fa-box-open'></i><br>No hay productos registrados.</td></tr>";
    } else {
        foreach ($products as $product) {
            $availability = $product['availability'] ? 'Disponible' : 'No disponible';
            $suppliers = $product['suppliers'] ?? 'Sin proveedores';
            $supplierIds = explode(',', $product['supplierIds'] ?? '');

            $productData = [
                'id' => $product['id'],
                'name' => $product['name'],
                'categoryId' => $product['categoryId'],
                'purchaseUnit' => $product['purchaseUnit'],
                'quantityIncluded' => $product['quantityIncluded'],
                'saleUnit' => $product['saleUnit'],
                'purchasePrice' => $product['purchasePrice'],
                'salePrice' => $product['salePrice'],
                'stock' => $product['stock'],
                'supplierId' => $supplierIds[0] ?? ''
            ];
            $productJson = htmlspecialchars(json_encode($productData), ENT_QUOTES, 'UTF-8');

            echo "<tr>
                <td>{$product['id']}</td>
                <td>{$product['name']}</td>
                <td>{$product['category']}</td>
                <td>\${$product['purchasePrice']}</td>
                <td>\${$product['salePrice']}</td>
                <td>{$product['stock']}</td>
                <td>$availability</td>
                <td>$suppliers</td>
                <td class='actions'>
                    <button class='edit' data-tooltip='Editar' onclick='openProductModal($productJson)'><i class='fas fa-edit'></i></button>
                    <button class='delete' data-tooltip='Eliminar' onclick=\"openConfirmModal('product', '{$product['id']}')\"><i class='fas fa-trash-alt'></i></button>
                </td>
            </tr>";
        }
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='9' class='empty-state'>Error al cargar productos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>