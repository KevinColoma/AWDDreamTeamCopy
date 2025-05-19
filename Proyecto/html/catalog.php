<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Catálogos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/catalog.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Gestión de Catálogos</h1>
        </header>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Content Area -->
            <div class="content-area">
                <!-- Button to open upload modal -->
                <section class="form-section">
                    <h2>Subir Nuevo Catálogo</h2>
                    <button id="openUploadModalBtn" class="submit-btn">Subir Catálogo</button>
                </section>

                <!-- Catalog List Section -->
                <section class="catalogs-section">
                    <div class="filter-controls">
                        <input type="text" id="searchInput" placeholder="Buscar por nombre o proveedor..." class="search-input">
                        <div class="filter-select">
                            <select id="sortSelect" class="sort-select">
                                <option value="all">Todos</option>
                                <option value="name_asc">A-Z (Nombre Proveedor)</option>
                                <option value="oldest">Más Antiguo</option>
                                <option value="newest">Más Reciente</option>
                            </select>
                            <span class="icon-filter">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18M6 12h12M9 18h6"/>
                                </svg>
                            </span>
                            <span class="icon-chevron">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <h2>Catálogos Guardados</h2>
                    <div id="noCatalogsMessage" class="no-catalogs-message" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                        </svg>
                        Toca en "Subir Catálogo" para asignar uno al proveedor correspondiente
                    </div>
                    <table class="catalogs-table" id="catalogsTable">
                        <thead>
                            <tr>
                                <th>Nombre del Catálogo</th>
                                <th>Proveedor</th>
                                <th>Archivo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                include '../PHP/connection.php';
                                $catalogStmt = $conn->query("
                                    SELECT c.id, c.customName, c.filePath, s.company, c.created_at
                                    FROM catalogs c 
                                    JOIN suppliers s ON c.supplierId = s.id
                                    ORDER BY c.created_at DESC
                                ");
                                $hasCatalogs = false;
                                while ($catalogRow = $catalogStmt->fetch(PDO::FETCH_ASSOC)) {
                                    $hasCatalogs = true;
                                    echo "<tr data-created-at='" . htmlspecialchars($catalogRow['created_at']) . "'>";
                                    echo "<td>" . htmlspecialchars($catalogRow['customName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($catalogRow['company']) . "</td>";
                                    echo "<td><a href='../../" . htmlspecialchars($catalogRow['filePath']) . "' download>Descargar</a></td>";
                                    echo "<td>";
                                    echo "<div class='action-buttons'>";
                                    echo "<button class='action-btn icon-btn edit-btn' data-id='{$catalogRow['id']}' data-name='{$catalogRow['customName']}' data-tooltip='Editar'>";
                                    echo "<span class='icon-wrapper'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'/><path d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z'/></svg></span>";
                                    echo "</button>";
                                    echo "<button class='action-btn icon-btn delete-btn' data-id='{$catalogRow['id']}' data-tooltip='Eliminar'>";
                                    echo "<span class='icon-wrapper'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'/></svg></span>";
                                    echo "</button>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                if (!$hasCatalogs) {
                                    echo "<tr><td colspan='4' style='text-align: center; padding: 1rem; color: #64748b;'>No hay catálogos guardados</td></tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='4' style='color: red;'>Error al cargar catálogos: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal para mensajes -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="messageModalTitle"></h3>
                <span class="modal-close" id="closeMessageModal">×</span>
            </div>
            <div class="modal-body">
                <p id="messageModalText"></p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-primary" id="acceptMessageModal">Aceptar</button>
            </div>
        </div>
    </div>

    <!-- Modal para editar catálogo -->
    <div id="editCatalogModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Catálogo</h3>
                <span class="modal-close" id="closeEditModal">×</span>
            </div>
            <div class="modal-body">
                <form id="editCatalogForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="catalogId" id="editCatalogId">
                    <div class="formGroup">
                        <label for="editCatalogName">Nuevo Nombre del Catálogo:</label>
                        <input type="text" name="newFileName" id="editCatalogName" required>
                    </div>
                    <div class="formGroup full-width">
                        <label for="editCatalogFile">Reemplazar Archivo (opcional):</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="newFileUpload" id="editCatalogFile" accept="application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                            <span id="editCatalogFileNameDisplay" class="file-name-display">Ningún archivo seleccionado</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="modal-btn modal-btn-primary">Guardar Cambios</button>
                        <button type="button" class="modal-btn modal-btn-secondary" id="cancelEditButton">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación -->
    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmar Eliminación</h3>
                <span class="modal-close" id="closeDeleteModal">×</span>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este catálogo? Esta acción no se puede deshacer.</p>
                <form id="deleteCatalogForm" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="catalogId" id="deleteCatalogId">
                    <div class="modal-footer">
                        <button type="submit" class="modal-btn modal-btn-danger">Sí, Eliminar</button>
                        <button type="button" class="modal-btn modal-btn-secondary" id="cancelDeleteButton">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para subir nuevo catálogo -->
    <div id="uploadCatalogModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Subir Nuevo Catálogo</h3>
                <span class="modal-close" id="closeUploadModal">×</span>
            </div>
            <div class="modal-body">
                <form id="uploadForm" action="../PHP/processCatalog.php" method="POST" enctype="multipart/form-data" class="form-container">
                    <div class="form-grid">
                        <div class="formGroup">
                            <label for="supplierSelect">Seleccionar Proveedor:</label>
                            <select name="supplierId" id="supplierSelect" required>
                                <option value="">-- Selecciona un proveedor --</option>
                                <?php
                                try {
                                    $supplierStmt = $conn->query("SELECT id, company FROM suppliers");
                                    while ($supplierRow = $supplierStmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$supplierRow['id']}'>{$supplierRow['company']}</option>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<p style='color: red;'>Error al cargar proveedores: " . $e->getMessage() . "</p>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="formGroup">
                            <label for="fileNameInput">Nombre del Catálogo:</label>
                            <input type="text" name="fileName" id="fileNameInput" placeholder="Ej: Catalogo_Mayo_2025" required>
                        </div>
                        <div class="formGroup full-width">
                            <label for="fileInput">Subir Catálogo:</label>
                            <div class="file-input-wrapper">
                                <input type="file" name="fileUpload" id="fileInput" accept="application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required>
                                <span id="fileNameDisplay" class="file-name-display">Ningún archivo seleccionado</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="modal-btn modal-btn-primary">Subir Catálogo</button>
                        <button type="button" class="modal-btn modal-btn-secondary" id="cancelUploadButton">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/catalog.js"></script>
</body>
</html>