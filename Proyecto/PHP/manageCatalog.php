<?php
include 'connection.php';

// Configurar el encabezado para devolver JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['action'])) {
            throw new Exception("Acción no especificada.");
        }

        $action = $_POST['action'];

        if ($action === 'delete') {
            if (!isset($_POST['catalogId'])) {
                throw new Exception("ID del catálogo no especificado.");
            }

            $catalogId = $_POST['catalogId'];

            $stmt = $conn->prepare("SELECT filePath FROM catalogs WHERE id = ?");
            $stmt->execute([$catalogId]);
            $catalog = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$catalog) {
                throw new Exception("Catálogo no encontrado.");
            }

            $filePath = __DIR__ . "/../../" . $catalog['filePath'];
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    throw new Exception("Error al eliminar el archivo del sistema.");
                }
            }

            $deleteStmt = $conn->prepare("DELETE FROM catalogs WHERE id = ?");
            if (!$deleteStmt->execute([$catalogId])) {
                throw new Exception("Error al eliminar el catálogo de la base de datos.");
            }

            echo json_encode(['status' => 'success', 'message' => 'Catálogo eliminado exitosamente']);
            exit;
        } elseif ($action === 'edit') {
            if (!isset($_POST['catalogId']) || !isset($_POST['newFileName'])) {
                throw new Exception("Datos incompletos para la edición.");
            }

            $catalogId = $_POST['catalogId'];
            $newFileName = trim($_POST['newFileName']);
            $newFileName = preg_replace('/[^A-Za-z0-9\-_\s]/', '', $newFileName);
            $newFileName = str_replace(' ', '_', $newFileName);
            if (empty($newFileName)) {
                throw new Exception("El nuevo nombre del archivo no puede estar vacío.");
            }

            $stmt = $conn->prepare("SELECT supplierId, filePath FROM catalogs WHERE id = ?");
            $stmt->execute([$catalogId]);
            $catalog = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$catalog) {
                throw new Exception("Catálogo no encontrado.");
            }

            $supplierId = $catalog['supplierId'];
            $oldFilePath = __DIR__ . "/../../" . $catalog['filePath'];

            $updateStmt = $conn->prepare("UPDATE catalogs SET customName = ? WHERE id = ?");
            if (!$updateStmt->execute([$newFileName, $catalogId])) {
                throw new Exception("Error al actualizar el nombre en la base de datos.");
            }

            if (isset($_FILES['newFileUpload']) && $_FILES['newFileUpload']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
                $fileType = $_FILES['newFileUpload']['type'];
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception("Solo se permiten archivos PDF y Excel (.xlsx, .xls).");
                }

                $maxFileSize = 5 * 1024 * 1024;
                if ($_FILES['newFileUpload']['size'] > $maxFileSize) {
                    throw new Exception("El archivo no debe exceder los 5MB.");
                }

                if (file_exists($oldFilePath)) {
                    if (!unlink($oldFilePath)) {
                        throw new Exception("Error al eliminar el archivo anterior.");
                    }
                }

                $targetDir = __DIR__ . "/../../uploads/";
                $fileInfo = pathinfo($_FILES['newFileUpload']['name']);
                $extension = isset($fileInfo['extension']) ? "." . $fileInfo['extension'] : "";
                $uniqueFileName = $newFileName . "_" . $supplierId . "_" . time() . $extension;
                $targetFile = $targetDir . $uniqueFileName;
                $dbFilePath = "uploads/" . $uniqueFileName;

                if (!move_uploaded_file($_FILES['newFileUpload']['tmp_name'], $targetFile)) {
                    throw new Exception("Error al subir el nuevo archivo.");
                }

                $updateFileStmt = $conn->prepare("UPDATE catalogs SET filePath = ? WHERE id = ?");
                if (!$updateFileStmt->execute([$dbFilePath, $catalogId])) {
                    unlink($targetFile);
                    throw new Exception("Error al actualizar la ruta del archivo en la base de datos.");
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Catálogo actualizado exitosamente']);
            exit;
        } else {
            throw new Exception("Acción no válida.");
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}
?>