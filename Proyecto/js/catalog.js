document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando eventos...');

    let originalTableRows = [];
    let initialTableState = '';

    const modals = ['messageModal', 'editCatalogModal', 'confirmDeleteModal', 'uploadCatalogModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'none';
    });

    const tableBody = document.querySelector('#catalogsTable tbody');
    const noCatalogsMessage = document.getElementById('noCatalogsMessage');
    
    initialTableState = tableBody.innerHTML;
    
    if (tableBody.rows.length === 0 || (tableBody.rows.length === 1 && 
        tableBody.rows[0].cells.length === 1 && 
        tableBody.rows[0].cells[0].textContent.includes('No hay catálogos guardados'))) {
        noCatalogsMessage.style.display = 'flex';
    } else {
        noCatalogsMessage.style.display = 'none';
        originalTableRows = Array.from(tableBody.rows);
    }

    // Abrir modal de subir catálogo
    const openUploadModalBtn = document.getElementById('openUploadModalBtn');
    if (openUploadModalBtn) {
        openUploadModalBtn.addEventListener('click', function() {
            console.log('Abriendo modal de subida');
            openModal('uploadCatalogModal');
            document.getElementById('uploadForm').reset();
            document.getElementById('fileNameDisplay').textContent = 'Ningún archivo seleccionado';
        });
    } else {
        console.error("El botón 'openUploadModalBtn' no se encontró.");
    }

    // Validación para evitar múltiples espacios en el nombre del catálogo (subir)
    const fileNameInput = document.getElementById('fileNameInput');
    if (fileNameInput) {
        fileNameInput.addEventListener('input', function() {
            this.value = this.value.replace(/\s+/g, ' ').trimStart();
        });
    }

    const editCatalogName = document.getElementById('editCatalogName');
    if (editCatalogName) {
        editCatalogName.addEventListener('input', function() {
            this.value = this.value.replace(/\s+/g, ' ').trimStart();
        });
    }

    const fileInput = document.getElementById('fileInput');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            fileNameDisplay.textContent = this.files.length > 0 ? this.files[0].name : 'Ningún archivo seleccionado';
        });
    }

    const editCatalogFile = document.getElementById('editCatalogFile');
    if (editCatalogFile) {
        editCatalogFile.addEventListener('change', function() {
            const fileNameDisplay = document.getElementById('editCatalogFileNameDisplay');
            fileNameDisplay.textContent = this.files.length > 0 ? this.files[0].name : 'Ningún archivo seleccionado';
        });
    }

    // Búsqueda y filtros
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    let lastSearchValue = '';
    let lastSortValue = 'all';
    
    if (searchInput && sortSelect) {
        searchInput.addEventListener('input', function() {
            if (this.value !== lastSearchValue) {
                lastSearchValue = this.value;
                filterCatalogs();
            }
        });
        
        sortSelect.addEventListener('change', function() {
            if (this.value !== lastSortValue) {
                lastSortValue = this.value;
                filterCatalogs();
            }
        });
    } else {
        console.error('searchInput o sortSelect no encontrados.');
    }

    function filterCatalogs() {
        console.log('Filtrando catálogos...');
        const searchValue = searchInput.value.toLowerCase();
        const sortValue = sortSelect.value;
        const table = document.getElementById('catalogsTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const noCatalogsMessage = document.getElementById('noCatalogsMessage');
        
        const allRows = originalTableRows.length > 0 ? 
            [...originalTableRows] : 
            Array.from(tbody.rows).filter(row => {
                const cellText = row.cells[0].textContent;
                return !cellText.includes('No hay catálogos guardados') && 
                       !cellText.includes('No se encontraron catálogos con esos filtros');
            });
        
        if (allRows.length === 0) {
            return;
        }
        
        let hasVisibleRows = false;
        let visibleRows = [];

        // Filtrar por búsqueda
        allRows.forEach(row => {
            if (row.cells.length >= 2) { 
                const catalogName = row.cells[0].textContent.toLowerCase();
                const supplierName = row.cells[1].textContent.toLowerCase();
                const shouldShow = catalogName.includes(searchValue) || supplierName.includes(searchValue);
                
                if (shouldShow) {
                    hasVisibleRows = true;
                    visibleRows.push(row.cloneNode(true)); 
                }
            }
        });

        // Ordenar las filas visibles
        if (sortValue !== 'all') {
            visibleRows.sort((a, b) => {
                if (sortValue === 'name_asc') {
                    return a.cells[1].textContent.toLowerCase().localeCompare(b.cells[1].textContent.toLowerCase());
                } else if (sortValue === 'oldest') {
                    return new Date(a.getAttribute('data-created-at')) - new Date(b.getAttribute('data-created-at'));
                } else if (sortValue === 'newest') {
                    return new Date(b.getAttribute('data-created-at')) - new Date(a.getAttribute('data-created-at'));
                }
                return 0;
            });
        }

        tbody.innerHTML = '';
        
        if (visibleRows.length > 0) {
            visibleRows.forEach(row => {
                let newRow = tbody.insertRow();
                newRow.innerHTML = row.innerHTML;
                newRow.setAttribute('data-created-at', row.getAttribute('data-created-at'));
                
                // Volver a agregar los event listeners
                const editBtn = newRow.querySelector('.edit-btn');
                const deleteBtn = newRow.querySelector('.delete-btn');
                
                if (editBtn) {
                    editBtn.addEventListener('click', function() {
                        const catalogId = this.getAttribute('data-id');
                        const catalogName = this.getAttribute('data-name');
                        document.getElementById('editCatalogId').value = catalogId;
                        document.getElementById('editCatalogName').value = catalogName;
                        document.getElementById('editCatalogFileNameDisplay').textContent = 'Ningún archivo seleccionado';
                        openModal('editCatalogModal');
                    });
                }
                
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function() {
                        const catalogId = this.getAttribute('data-id');
                        document.getElementById('deleteCatalogId').value = catalogId;
                        openModal('confirmDeleteModal');
                    });
                }
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 1rem; color: #64748b;">No se encontraron catálogos con esos filtros</td></tr>';
        }

        noCatalogsMessage.style.display = hasVisibleRows ? 'none' : 'flex';
    }

    function setupActionButtons() {
        // Botones de edición
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                console.log('Abriendo modal de edición');
                const catalogId = this.getAttribute('data-id');
                const catalogName = this.getAttribute('data-name');
                document.getElementById('editCatalogId').value = catalogId;
                document.getElementById('editCatalogName').value = catalogName;
                document.getElementById('editCatalogFileNameDisplay').textContent = 'Ningún archivo seleccionado';
                openModal('editCatalogModal');
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                console.log('Abriendo modal de eliminación');
                const catalogId = this.getAttribute('data-id');
                document.getElementById('deleteCatalogId').value = catalogId;
                openModal('confirmDeleteModal');
            });
        });
    }

    setupActionButtons();

    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Enviando formulario de subida');
            const formData = new FormData(this);
            fetch('../PHP/processCatalog.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showMessageModal(data.status, data.message, () => {
                    if (data.status === 'success') {
                        this.reset();
                        document.getElementById('fileNameDisplay').textContent = 'Ningún archivo seleccionado';
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error en subida:', error);
                showMessageModal('error', 'Error al procesar la solicitud: ' + error.message);
            });
        });
    }

    const editCatalogForm = document.getElementById('editCatalogForm');
    if (editCatalogForm) {
        editCatalogForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Enviando formulario de edición');
            const formData = new FormData(this);
            fetch('../PHP/manageCatalog.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showMessageModal(data.status, data.message, () => {
                    if (data.status === 'success') {
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error en edición:', error);
                showMessageModal('error', 'Error al procesar la solicitud: ' + error.message);
            });
        });
    }

    const deleteCatalogForm = document.getElementById('deleteCatalogForm');
    if (deleteCatalogForm) {
        deleteCatalogForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Enviando formulario de eliminación');
            const formData = new FormData(this);
            fetch('../PHP/manageCatalog.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showMessageModal(data.status, data.message, () => {
                    if (data.status === 'success') {
                        location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error en eliminación:', error);
                showMessageModal('error', 'Error al procesar la solicitud: ' + error.message);
            });
        });
    }

    document.getElementById('closeMessageModal').addEventListener('click', function() {
        closeModal('messageModal');
    });
    
    document.getElementById('acceptMessageModal').addEventListener('click', function() {
        closeModal('messageModal');
    });
    
    document.getElementById('closeEditModal').addEventListener('click', function() {
        closeModal('editCatalogModal');
    });
    
    document.getElementById('cancelEditButton').addEventListener('click', function() {
        closeModal('editCatalogModal');
    });
    
    document.getElementById('closeDeleteModal').addEventListener('click', function() {
        closeModal('confirmDeleteModal');
    });
    
    document.getElementById('cancelDeleteButton').addEventListener('click', function() {
        closeModal('confirmDeleteModal');
    });
    
    document.getElementById('closeUploadModal').addEventListener('click', function() {
        closeModal('uploadCatalogModal');
    });
    
    document.getElementById('cancelUploadButton').addEventListener('click', function() {
        closeModal('uploadCatalogModal');
    });

    // Funciones para modales
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } else {
            console.error(`Modal '${modalId}' no encontrado.`);
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    function closeAllModals() {
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'none';
        });
        document.body.style.overflow = 'auto';
    }

    function showMessageModal(status, message, callback = null) {
        closeAllModals();
        const modal = document.getElementById('messageModal');
        const title = document.getElementById('messageModalTitle');
        const text = document.getElementById('messageModalText');
        title.textContent = status === 'success' ? 'Éxito' : 'Error';
        text.textContent = message;
        modal.classList.remove('success', 'error');
        modal.classList.add(status);
        openModal('messageModal');
        if (status === 'success' && callback) {
            setTimeout(() => {
                closeModal('messageModal');
                callback();
            }, 2000);
        }
    }

    // Cerrar modales al hacer clic fuera
    window.addEventListener('click', function(event) {
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal && modal.style.display === 'flex') {
                closeModal(modalId);
            }
        });
    });
});