// Variables globales para la eliminación
let deleteType = null;
let deleteId = null;

// Variables para almacenar datos del formulario
let lastFormData = null;

function openCategoryModal(categoryId = null, type = '', description = '') {
    const modal = document.getElementById('categoryModal');
    const modalTitle = document.getElementById('categoryModalTitle');
    const actionInput = document.getElementById('categoryAction');
    const categoryIdInput = document.getElementById('categoryId');
    const typeInput = document.getElementById('categoryType');
    const descriptionInput = document.getElementById('categoryDescription');
    const errorSpan = document.getElementById('categoryError');

    modal.style.display = 'flex';
    if (categoryId) {
        modalTitle.textContent = 'Editar Categoría';
        actionInput.value = 'update';
        categoryIdInput.value = categoryId;
        typeInput.value = type;
        descriptionInput.value = description;
    } else {
        modalTitle.textContent = 'Nueva Categoría';
        actionInput.value = 'create';
        categoryIdInput.value = '';
        typeInput.value = lastFormData?.type || '';
        descriptionInput.value = lastFormData?.description || '';
    }
    errorSpan.style.display = 'none';
    typeInput.focus();
    lastFormData = null; // Resetear al abrir un nuevo modal
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
}

function openProductModal(product = null) {
    const modal = document.getElementById('productModal');
    const modalTitle = document.getElementById('productModalTitle');
    const actionInput = document.getElementById('productAction');
    const productIdInput = document.getElementById('productId');
    const categorySelect = document.getElementById('productCategoryId');

    modal.style.display = 'flex';
    if (product) {
        modalTitle.textContent = 'Editar Producto';
        actionInput.value = 'update';
        productIdInput.value = product.id;
        document.getElementById('productName').value = product.name;
        categorySelect.value = product.categoryId;
        document.getElementById('purchaseUnit').value = product.purchaseUnit;
        document.getElementById('quantityIncluded').value = product.quantityIncluded;
        document.getElementById('saleUnit').value = product.saleUnit;
        document.getElementById('purchasePrice').value = product.purchasePrice;
        document.getElementById('salePrice').value = product.salePrice;
        document.getElementById('stock').value = product.stock;
        document.getElementById('supplierId').value = product.supplierId;
        calculateTax();
    } else {
        modalTitle.textContent = 'Registrar Producto';
        actionInput.value = 'create';
        document.getElementById('productName').value = lastFormData?.name || '';
        categorySelect.value = lastFormData?.categoryId || '';
        document.getElementById('purchaseUnit').value = lastFormData?.purchaseUnit || '';
        document.getElementById('quantityIncluded').value = lastFormData?.quantityIncluded || '';
        document.getElementById('saleUnit').value = lastFormData?.saleUnit || '';
        document.getElementById('purchasePrice').value = lastFormData?.purchasePrice || '';
        document.getElementById('salePrice').value = lastFormData?.salePrice || '';
        document.getElementById('stock').value = lastFormData?.stock || '';
        document.getElementById('supplierId').value = lastFormData?.supplierId || '';
        document.getElementById('taxPrice').textContent = lastFormData?.taxPrice ? `Precio con IVA: $${lastFormData.taxPrice}` : 'Precio con IVA: $0.00';
    }
    lastFormData = null; // Resetear al abrir un nuevo modal
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

function openConfirmModal(type, id) {
    deleteType = type;
    deleteId = id;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal() {
    deleteType = null;
    deleteId = null;
    document.getElementById('confirmModal').style.display = 'none';
}

function openMessageModal(title, message, isSuccess = true) {
    const modal = document.getElementById('messageModal');
    const titleElement = document.getElementById('messageModalTitle');
    const textElement = document.getElementById('messageModalText');
    const button = modal.querySelector('.save');

    titleElement.textContent = title;
    textElement.textContent = message;
    button.style.backgroundColor = isSuccess ? '#4CAF50' : '#f44336'; // Verde para éxito, rojo para error
    modal.style.display = 'flex';
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
    // Recargar la página si es necesario (por ejemplo, después de una operación exitosa)
    if (document.getElementById('messageModalTitle').textContent.includes('Éxito')) {
        location.reload();
    }
}

function confirmDelete() {
    const formData = new FormData();
    if (deleteType === 'category') {
        formData.append('action', 'delete');
        formData.append('categoryId', deleteId);
        fetch('../PHP/categoryActions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            closeConfirmModal();
            if (data.success) {
                openMessageModal('Éxito', data.message, true);
            } else {
                openMessageModal('Error', data.message, false);
            }
        })
        .catch(error => {
            console.error('Error al eliminar categoría:', error);
            openMessageModal('Error', 'Ocurrió un error al procesar la solicitud.', false);
        });
    } else if (deleteType === 'product') {
        formData.append('action', 'delete');
        formData.append('id', deleteId);
        fetch('../PHP/productActions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            closeConfirmModal();
            if (data.success) {
                openMessageModal('Éxito', data.message, true);
            } else {
                openMessageModal('Error', data.message, false);
            }
        })
        .catch(error => {
            console.error('Error al eliminar producto:', error);
            openMessageModal('Error', 'Ocurrió un error al procesar la solicitud.', false);
        });
    }
}

// Calcular IVA (15%)
function calculateTax() {
    const purchasePrice = parseFloat(document.getElementById('purchasePrice').value) || 0;
    const tax = purchasePrice * 0.15;
    const totalWithTax = purchasePrice + tax;
    document.getElementById('taxPrice').textContent = `Precio con IVA: $${totalWithTax.toFixed(2)}`;
}

// Validar nombre de categoría antes de enviar
function validateCategoryForm() {
    const typeInput = document.getElementById('categoryType');
    const errorSpan = document.getElementById('categoryError');
    let value = typeInput.value.trim();

    // Reemplazar múltiples espacios por un solo espacio
    value = value.replace(/\s+/g, ' ');

    // Validar solo letras y un espacio entre palabras
    const isValid = /^[A-Za-z]+( [A-Za-z]+)*$/.test(value);
    if (!isValid || value === '') {
        errorSpan.textContent = 'Solo se permiten letras y un espacio entre palabras.';
        errorSpan.style.display = 'block';
        typeInput.value = value;
        return false;
    }
    errorSpan.style.display = 'none';
    typeInput.value = value;
    return true;
}

function submitCategoryForm(event) {
    event.preventDefault();
    if (!validateCategoryForm()) return;

    const form = document.getElementById('categoryForm');
    const formData = new FormData(form);
    lastFormData = {
        type: document.getElementById('categoryType').value,
        description: document.getElementById('categoryDescription').value
    };

    fetch('../PHP/categoryActions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCategoryModal();
            openMessageModal('Éxito', data.message, true);
        } else {
            openMessageModal('Error', data.message, false);
            openCategoryModal(); // Reabrir modal con datos previos
        }
    })
    .catch(error => {
        console.error('Error al procesar categoría:', error);
        openMessageModal('Error', 'Ocurrió un error al procesar la solicitud.', false);
        openCategoryModal(); // Reabrir modal con datos previos
    });
}

function submitProductForm(event) {
    event.preventDefault();
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    const categorySelect = document.getElementById('productCategoryId');
    lastFormData = {
        name: document.getElementById('productName').value,
        categoryId: categorySelect.value, // Asegurar que se capture el valor seleccionado
        purchaseUnit: document.getElementById('purchaseUnit').value,
        quantityIncluded: document.getElementById('quantityIncluded').value,
        saleUnit: document.getElementById('saleUnit').value,
        purchasePrice: document.getElementById('purchasePrice').value,
        salePrice: document.getElementById('salePrice').value,
        stock: document.getElementById('stock').value,
        supplierId: document.getElementById('supplierId').value,
        taxPrice: parseFloat((document.getElementById('purchasePrice').value * 0.15 + parseFloat(document.getElementById('purchasePrice').value) || 0).toFixed(2))
    };

    // Depuración: Verificar el valor de categoryId antes de enviar
    console.log('categoryId enviado:', lastFormData.categoryId);

    fetch('../PHP/productActions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeProductModal();
            openMessageModal('Éxito', data.message, true);
        } else {
            openMessageModal('Error', data.message, false);
            openProductModal(); // Reabrir modal con datos previos
        }
    })
    .catch(error => {
        console.error('Error al procesar producto:', error);
        openMessageModal('Error', 'Ocurrió un error al procesar la solicitud.', false);
        openProductModal(); // Reabrir modal con datos previos
    });
}

// Filtrar productos
function filterProducts() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const rows = document.querySelectorAll('#productsTable tbody tr');

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const category = row.cells[2].textContent;
        const matchesSearch = name.includes(searchInput);
        const matchesCategory = !categoryFilter || category === categoryFilter;

        row.style.display = matchesSearch && matchesCategory ? '' : 'none';
    });
}

// Evento para prevenir múltiples espacios al escribir
document.getElementById('categoryType').addEventListener('input', function(e) {
    let value = e.target.value;
    value = value.replace(/\s+/g, ' ').trim();
    e.target.value = value;
});