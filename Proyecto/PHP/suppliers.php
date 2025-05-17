<?php
header('Content-Type: application/json');
include 'connection.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';
$id = isset($_POST['id']) ? $_POST['id'] : null;

function response($success, $message, $data = null) {
    if (!$success) {
        error_log("Error: $message");
    }
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

try {
    switch ($action) {
        case 'create':
        case 'update':
            $id = $_POST['id'] ?? '';
            $idNumber = $_POST['idNumber'] ?? '';
            $company = $_POST['company'] ?? '';
            $contactName = $_POST['contactName'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $bankAccount = $_POST['bankAccount'] ?? '';
            $bankName = $_POST['bankName'] ?? '';
            $catalog = $_POST['catalog'] ?? '';

            if (empty($idNumber) || empty($company) || empty($contactName) || empty($phone) || empty($bankAccount) || empty($bankName) || empty($catalog)) {
                response(false, 'Todos los campos son obligatorios');
            }
            if (!preg_match('/^\d{10}$|^\d{13}$/', $idNumber)) {
                response(false, 'Cédula (10 dígitos) o RUC (13 dígitos) no válido');
            }
            if (!preg_match('/^\d{7,15}$/', $phone)) {
                response(false, 'Teléfono no válido (7-15 dígitos)');
            }
            if (!preg_match('/^\d{10,20}$/', $bankAccount)) {
                response(false, 'Cuenta bancaria no válida (10-20 dígitos)');
            }

            $stmt = $conn->prepare("SELECT id FROM suppliers WHERE idNumber = :idNumber AND id != :id");
            $stmt->execute(['idNumber' => $idNumber, 'id' => $id ?: 0]);
            if ($stmt->rowCount() > 0) {
                response(false, 'Ya existe un proveedor con esa cédula/RUC');
            }

            if ($action == 'create') {
                $stmt = $conn->prepare("INSERT INTO suppliers (idNumber, company, contactName, phone, bankAccount, bankName, catalog) VALUES (:idNumber, :company, :contactName, :phone, :bankAccount, :bankName, :catalog)");
                $stmt->execute([
                    'idNumber' => $idNumber,
                    'company' => $company,
                    'contactName' => $contactName,
                    'phone' => $phone,
                    'bankAccount' => $bankAccount,
                    'bankName' => $bankName,
                    'catalog' => $catalog
                ]);
                response(true, 'Proveedor creado exitosamente');
            } else {
                $stmt = $conn->prepare("UPDATE suppliers SET idNumber = :idNumber, company = :company, contactName = :contactName, phone = :phone, bankAccount = :bankAccount, bankName = :bankName, catalog = :catalog WHERE id = :id");
                $stmt->execute([
                    'idNumber' => $idNumber,
                    'company' => $company,
                    'contactName' => $contactName,
                    'phone' => $phone,
                    'bankAccount' => $bankAccount,
                    'bankName' => $bankName,
                    'catalog' => $catalog,
                    'id' => $id
                ]);
                response(true, 'Proveedor actualizado exitosamente');
            }
            break;

        case 'read':
            if ($id) {
                $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                response(true, 'Proveedor obtenido', $suppliers);
            } else {
                $stmt = $conn->query("SELECT * FROM suppliers");
                $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                response(true, 'Proveedores obtenidos', $suppliers);
            }
            break;

        case 'delete':
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                response(false, 'ID no proporcionado');
            }
            $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = :id");
            $stmt->execute(['id' => $id]);
            response(true, 'Proveedor eliminado exitosamente');
            break;

        case 'search':
            $query = $_POST['query'] ?? '';
            $stmt = $conn->prepare("SELECT * FROM suppliers WHERE idNumber LIKE :query OR company LIKE :query OR contactName LIKE :query OR phone LIKE :query OR bankAccount LIKE :query OR bankName LIKE :query OR catalog LIKE :query");
            $stmt->execute(['query' => "%$query%"]);
            $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            response(true, 'Resultados de búsqueda', $suppliers);
            break;

        default:
            response(false, 'Acción no válida');
    }
} catch (PDOException $e) {
    response(false, 'Error en la base de datos: ' . $e->getMessage());
}
?>
