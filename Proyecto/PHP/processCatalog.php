<?php
include 'connection.php';

// Configurar el encabezado para devolver JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['supplierId']) || empty($_POST['supplierId'])) {
            throw new Exception("Debes seleccionar un proveedor.");
        }
        
        if (!isset($_FILES['fileUpload']) || $_FILES['fileUpload']['error'] != UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => "El archivo excede el tamaño máximo permitido por el servidor.",
                UPLOAD_ERR_FORM_SIZE => "El archivo excede el tamaño máximo permitido por el formulario.",
                UPLOAD_ERR_PARTIAL => "El archivo no se subió completamente.",
                UPLOAD_ERR_NO_FILE => "No se recibió ningún archivo.",
                UPLOAD_ERR_NO_TMP_DIR => "Falta la carpeta temporal del servidor.",
                UPLOAD_ERR_CANT_WRITE => "No se pudo escribir el archivo en el disco.",
                UPLOAD_ERR_EXTENSION => "Una extensión de PHP detuvo la subida del archivo."
            ];
            $errorCode = $_FILES['fileUpload']['error'] ?? UPLOAD_ERR_NO_FILE;
            throw new Exception($errorMessages[$errorCode] ?? "Error desconocido al recibir el archivo.");
        }
        
        $supplierId = $_POST['supplierId'];
        
        $checkStmt = $conn->prepare("SELECT id FROM catalogs WHERE supplierId = ?");
        $checkStmt->execute([$supplierId]);
        if ($checkStmt->fetch()) {
            throw new Exception("Este proveedor ya tiene un catálogo asignado.");
        }
        
        $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
        $fileType = $_FILES['fileUpload']['type'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Solo se permiten archivos PDF y Excel (.xlsx, .xls).");
        }
        
        $maxFileSize = 5 * 1024 * 1024;
        if ($_FILES['fileUpload']['size'] > $maxFileSize) {
            throw new Exception("El archivo no debe exceder los 5MB.");
        }
        
        if (!isset($_POST['fileName']) || empty(trim($_POST['fileName']))) {
            throw new Exception("Debes ingresar un nombre para el archivo.");
        }
        $customFileName = trim($_POST['fileName']);
        $customFileName = preg_replace('/[^A-Za-z0-9\-_\s]/', '', $customFileName);
        $customFileName = str_replace(' ', '_', $customFileName);
        if (empty($customFileName)) {
            throw new Exception("El nombre del archivo no puede estar vacío después de sanitizar.");
        }
        
        $targetDir = __DIR__ . "/../../uploads/";
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception("No se pudo crear el directorio de uploads.");
            }
        }
        
        if (!is_writable($targetDir)) {
            throw new Exception("El directorio de uploads no tiene permisos de escritura.");
        }
        
        $fileInfo = pathinfo($_FILES['fileUpload']['name']);
        $extension = isset($fileInfo['extension']) ? "." . $fileInfo['extension'] : "";
        $uniqueFileName = $customFileName . "_" . $supplierId . "_" . time() . $extension;
        $targetFile = $targetDir . $uniqueFileName;
        $dbFilePath = "uploads/" . $uniqueFileName;
        
        if (!move_uploaded_file($_FILES['fileUpload']['tmp_name'], $targetFile)) {
            throw new Exception("Error al mover el archivo subido. Verifica los permisos de la carpeta.");
        }
        
        $insertStmt = $conn->prepare("INSERT INTO catalogs (supplierId, filePath, customName) VALUES (?, ?, ?)");
        if (!$insertStmt->execute([$supplierId, $dbFilePath, $customFileName])) {
            unlink($targetFile);
            throw new Exception("Error al guardar la información en la base de datos.");
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Catálogo subido exitosamente']);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}
?>