<?php
error_log("Ejecutando productManagement.php: " . date('Y-m-d H:i:s'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Variedades</title>
    <link rel="stylesheet" href="../css/productManagement.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-info">
                <h1>Gestión de Productos</h1>
                <p>Administra los productos y categorías de tu inventario.</p>
            </div>
            <div class="header-actions">
                <button class="add-button" onclick="openCategoryModal()">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>
                <button class="add-button" onclick="openProductModal()">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </button>
            </div>
        </div>

        <div class="controls">
            <div class="search-bar">
                <input type="text" placeholder="Buscar productos..." id="searchInput" onkeyup="filterProducts()">
                <i class="fas fa-search"></i>
            </div>
            <div class="filter-select">
                <i class="fas fa-filter icon-filter"></i>
                <select id="categoryFilter" onchange="filterProducts()">
                    <option value="">Filtrar por Categoría</option>
                    <?php include '../PHP/populateCategories.php'; ?>
                </select>
                <i class="fas fa-chevron-down icon-chevron"></i>
            </div>
        </div>

        <div class="table-wrapper">
            <table id="productsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Disponibilidad</th>
                        <th>Proveedores</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include '../PHP/populateProducts.php'; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Crear/Editar Categorías -->
    <div class="modal" id="categoryModal">
        <div class="modal-content">
            <h2 id="categoryModalTitle">Nueva Categoría</h2>
            <form id="categoryForm" onsubmit="submitCategoryForm(event)">
                <div class="form-grid">
                    <input type="hidden" name="action" id="categoryAction" value="create">
                    <input type="hidden" name="categoryId" id="categoryId">
                    <div class="form-group">
                        <label for="categoryType">Nombre de la Categoría:</label>
                        <input type="text" name="type" id="categoryType" required>
                        <span id="categoryError" style="color: red; font-size: 0.9rem; display: none;"></span>
                    </div>
                    <div class="form-group">
                        <label for="categoryDescription">Descripción de la Categoría:</label>
                        <textarea name="description" id="categoryDescription" required></textarea>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="cancel" onclick="closeCategoryModal()">Cancelar</button>
                    <button type="submit" class="save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Crear/Editar Productos -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <h2 id="productModalTitle">Registrar Producto</h2>
            <form id="productForm" onsubmit="submitProductForm(event)">
                <div class="form-grid">
                    <input type="hidden" name="action" id="productAction" value="create">
                    <input type="hidden" name="id" id="productId">
                    <div class="form-section">
                        <h3>Identificación</h3>
                        <div class="form-group">
                            <label for="productName">Nombre del Producto:</label>
                            <input type="text" name="name" id="productName" required>
                        </div>
                        <div class="form-group">
                            <label for="productCategoryId">Categoría del Producto:</label>
                            <select name="categoryId" id="productCategoryId" required>
                                <option value="">Seleccionar Categoría</option>
                                <?php include '../PHP/populateCategories.php'; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3>Unidades y Cantidades</h3>
                        <div class="form-group">
                            <label for="purchaseUnit">Unidad de Compra:</label>
                            <input type="text" name="purchaseUnit" id="purchaseUnit" required>
                        </div>
                        <div class="form-group">
                            <label for="quantityIncluded">Cantidad Incluida:</label>
                            <input type="number" name="quantityIncluded" id="quantityIncluded" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="saleUnit">Unidad de Venta:</label>
                            <input type="text" name="saleUnit" id="saleUnit" required>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3>Precios</h3>
                        <div class="form-group">
                            <div style="display: flex; align-items: center; gap: 1.5rem;">
                                <label for="purchasePrice">Precio de Compra:</label>
                                <input type="number" step="0.01" name="purchasePrice" id="purchasePrice" min="0.01" oninput="calculateTax()" required>
                                <span id="taxPrice">Precio con IVA: $0.00</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="salePrice">Precio de Venta:</label>
                            <input type="number" step="0.01" name="salePrice" id="salePrice" min="0.01" required>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3>Stock y Proveedores</h3>
                        <div class="form-group">
                            <label for="stock">Stock:</label>
                            <input type="number" name="stock" id="stock" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="supplierId">Proveedor:</label>
                            <select name="supplierId" id="supplierId" required>
                                <option value="">Seleccionar Proveedor</option>
                                <?php include '../PHP/populateSuppliers.php'; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="cancel" onclick="closeProductModal()">Cancelar</button>
                    <button type="submit" class="save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación para Eliminar -->
    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar este elemento?</p>
            <div class="modal-actions">
                <button type="button" class="cancel" onclick="closeConfirmModal()">Cancelar</button>
                <button type="button" class="save" onclick="confirmDelete()">Eliminar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Mensajes de Éxito/Error -->
    <div class="modal" id="messageModal">
        <div class="modal-content">
            <h2 id="messageModalTitle">Mensaje</h2>
            <p id="messageModalText"></p>
            <div class="modal-actions">
                <button type="button" class="save" onclick="closeMessageModal()">Aceptar</button>
            </div>
        </div>
    </div>

    <script src="../js/productManagement.js"></script>
</body>
</html>