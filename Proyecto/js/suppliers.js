const API_BASE_URL = '../PHP/suppliers.php';

let currentFilter = 'a-z';

function renderTable(data) {
    const tbody = document.getElementById('suppliersBody');
    tbody.style.opacity = '0';

    let sortedData = [...data];
    if (currentFilter === 'a-z') {
        // Ordenar alfabéticamente por nombre de la empresa (A-Z)
        sortedData.sort((a, b) => a.company.localeCompare(b.company));
    } else if (currentFilter === 'recent') {
        sortedData.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
    } else if (currentFilter === 'oldest') {
        sortedData.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
    }

    tbody.innerHTML = '';
    if (sortedData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No hay proveedores registrados. Haz clic en "Nuevo Proveedor" para añadir uno.</p>
                </td>
            </tr>
        `;
    } else {
        sortedData.forEach(supplier => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${supplier.idNumber}</td>
                <td>${supplier.company}</td>
                <td>${supplier.contactName}</td>
                <td>${supplier.phone}</td>
                <td>${supplier.bankAccount}</td>
                <td>${supplier.bankName}</td>
                <td>${supplier.catalog}</td>
                <td class="actions">
                    <button class="edit" data-tooltip="Editar" onclick="openModal('edit', ${supplier.id})"><i class="fas fa-edit"></i></button>
                    <button class="delete" data-tooltip="Eliminar" onclick="openConfirmModal(${supplier.id}, '${supplier.company}')"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    setTimeout(() => {
        tbody.style.transition = 'opacity 0.4s ease';
        tbody.style.opacity = '1';
    }, 50);
}

function openModal(mode, id = null) {
    const modal = document.getElementById('modal');
    const title = document.getElementById('modalTitle');
    const idNumberInput = document.getElementById('supplierCedulaRuc');
    const company = document.getElementById('supplierCompany');
    const contactName = document.getElementById('supplierContactName');
    const phone = document.getElementById('supplierPhone');
    const bankAccount = document.getElementById('supplierBankAccount');
    const bankName = document.getElementById('supplierBankName');
    const catalog = document.getElementById('supplierCatalog');
    const supplierId = document.getElementById('supplierId');

    modal.style.display = 'flex';
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.transition = 'opacity 0.3s ease';
        modal.style.opacity = '1';
    }, 10);

    const inputs = [company, contactName, bankName, catalog];
    inputs.forEach(input => {
        input.removeEventListener('keypress', input._keypressHandler);
        input.removeEventListener('blur', input._blurHandler);
        input._keypressHandler = (e) => restrictMultipleSpaces(e, input);
        input._blurHandler = () => normalizeSpaces(input);
        input.addEventListener('keypress', input._keypressHandler);
        input.addEventListener('blur', input._blurHandler);
    });

    if (mode === 'add') {
        title.textContent = 'Nuevo Proveedor';
        supplierId.value = '';
        idNumberInput.value = '';
        company.value = '';
        contactName.value = '';
        phone.value = '';
        bankAccount.value = '';
        bankName.value = '';
        catalog.value = '';
    } else if (mode === 'edit' && id) {
        title.textContent = 'Editar Proveedor';
        supplierId.value = '';
        $.ajax({
            url: API_BASE_URL,
            type: 'POST',
            data: { action: 'read', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const supplier = response.data.find(s => s.id == id);
                    if (supplier) {
                        supplierId.value = supplier.id;
                        idNumberInput.value = supplier.idNumber;
                        company.value = supplier.company;
                        contactName.value = supplier.contactName;
                        phone.value = supplier.phone;
                        bankAccount.value = supplier.bankAccount;
                        bankName.value = supplier.bankName;
                        catalog.value = supplier.catalog;
                    } else {
                        alert('Proveedor no encontrado');
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al cargar los datos del proveedor: ' + error);
            }
        });
    }
}

function closeModal() {
    const modal = document.getElementById('modal');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.opacity = '1';
    }, 300);
}

function createSupplier() {
    const id = document.getElementById('supplierId').value;
    const idNumber = document.getElementById('supplierCedulaRuc').value.trim();
    const company = document.getElementById('supplierCompany').value.trim();
    const contactName = document.getElementById('supplierContactName').value.trim();
    const phone = document.getElementById('supplierPhone').value.trim();
    const bankAccount = document.getElementById('supplierBankAccount').value.trim();
    const bankName = document.getElementById('supplierBankName').value.trim();
    const catalog = document.getElementById('supplierCatalog').value.trim();

    if (!idNumber || !company || !contactName || !phone || !bankAccount || !bankName || !catalog) {
        alert('Por favor, completa todos los campos.');
        return;
    }
    if (!/^\d+$/.test(idNumber)) {
        alert('La cédula o RUC debe contener solo números.');
        return;
    }
    if (!/^\d+$/.test(phone)) {
        alert('El número de teléfono debe contener solo números.');
        return;
    }
    if (!/^\d+$/.test(bankAccount)) {
        alert('La cuenta bancaria debe contener solo números.');
        return;
    }
    if (!/^\d{10}$|^\d{13}$/.test(idNumber)) {
        alert('La cédula (10 dígitos) o RUC (13 dígitos) no es válida.');
        return;
    }
    if (!/^\d{7,15}$/.test(phone)) {
        alert('Ingresa un número de teléfono válido (7-15 dígitos).');
        return;
    }
    if (!/^\d{10,20}$/.test(bankAccount)) {
        alert('Ingresa una cuenta bancaria válida (10-20 dígitos).');
        return;
    }

    const action = id ? 'update' : 'create';
    $.ajax({
        url: API_BASE_URL,
        type: 'POST',
        data: {
            action: action,
            id: id,
            idNumber: idNumber,
            company: company,
            contactName: contactName,
            phone: phone,
            bankAccount: bankAccount,
            bankName: bankName,
            catalog: catalog
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                closeModal();
                showSuccessModal(company);
                loadSuppliers();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error al procesar la solicitud: ' + error);
        }
    });
}

function showSuccessModal(company) {
    const successModal = document.getElementById('successModal');
    const successModalMessage = document.getElementById('successModalMessage');
    successModalMessage.textContent = `${company} guardado`;
    successModal.style.display = 'flex';
    successModal.style.opacity = '0';
    setTimeout(() => {
        successModal.style.transition = 'opacity 0.3s ease';
        successModal.style.opacity = '1';
    }, 10);

    setTimeout(() => {
        successModal.style.opacity = '0';
        setTimeout(() => {
            successModal.style.display = 'none';
            successModal.style.opacity = '1';
        }, 300);
    }, 3000);
}

function deleteSupplier(id, company) {
    const confirmModal = document.getElementById('confirmModal');
    const confirmModalMessage = document.getElementById('confirmModalMessage');
    confirmModalMessage.textContent = `¿Estás seguro de eliminar a ${company} de forma definitiva?`;
    confirmModal.style.display = 'flex';
    confirmModal.style.opacity = '0';
    setTimeout(() => {
        confirmModal.style.transition = 'opacity 0.3s ease';
        confirmModal.style.opacity = '1';
    }, 10);

    document.getElementById('confirmDeleteBtn').onclick = function() {
        $.ajax({
            url: API_BASE_URL,
            type: 'POST',
            data: { action: 'delete', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    closeConfirmModal();
                    loadSuppliers();
                } else {
                    alert(response.message);
                    closeConfirmModal();
                }
            },
            error: function(xhr, status, error) {
                alert('Error al eliminar el proveedor: ' + error);
                closeConfirmModal();
            }
        });
    };
}

function openConfirmModal(id, company) {
    deleteSupplier(id, company);
}

function closeConfirmModal() {
    const confirmModal = document.getElementById('confirmModal');
    confirmModal.style.opacity = '0';
    setTimeout(() => {
        confirmModal.style.display = 'none';
        confirmModal.style.opacity = '1';
    }, 300);
}

function restrictToNumbers(event) {
    const charCode = event.which ? event.which : event.keyCode;
    if (charCode < 48 || charCode > 57) {
        event.preventDefault();
    }
}

function restrictPaste(event) {
    const pastedData = event.clipboardData.getData('text');
    if (!/^\d*$/.test(pastedData)) {
        event.preventDefault();
    }
}

function restrictMultipleSpaces(event, element) {
    const value = element.value;
    const key = event.key;
    if (key === ' ' && value.endsWith(' ')) {
        event.preventDefault();
    }
}

function normalizeSpaces(element) {
    element.value = element.value.replace(/\s+/g, ' ').trim();
}

function loadSuppliers(query = '') {
    const action = query ? 'search' : 'read';
    $.ajax({
        url: API_BASE_URL,
        type: 'POST',
        data: { action: action, query: query },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderTable(response.data);
            } else {
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error al cargar los proveedores: ' + error);
        }
    });
}

document.getElementById('filter').addEventListener('change', (e) => {
    currentFilter = e.target.value;
    loadSuppliers(document.getElementById('search').value);
});

document.getElementById('search').addEventListener('input', (e) => {
    loadSuppliers(e.target.value);
});

$(document).ready(function() {
    loadSuppliers();
});
