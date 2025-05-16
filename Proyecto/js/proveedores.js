 class Supplier {
            constructor(idNumber, company, phone, bankAccount, contactName, bankName, catalog, createdAt = new Date()) {
                this.idNumber = idNumber;
                this.company = company;
                this.phone = phone;
                this.bankAccount = bankAccount;
                this.contactName = contactName;
                this.bankName = bankName;
                this.catalog = catalog;
                this.createdAt = createdAt;
            }

            topPurchasedProducts(param) {
                if (this.idNumber === param) {
                    return [
                        "Producto A - 150 unidades",
                        "Producto B - 120 unidades",
                        "Producto C - 80 unidades"
                    ];
                }
                return [];
            }

            purchaseHistory(param) {
                if (this.idNumber === param) {
                    return [
                        "2025-01-15: Compra de 50 unidades - $500",
                        "2025-02-20: Compra de 30 unidades - $300",
                        "2025-03-10: Compra de 70 unidades - $700"
                    ];
                }
                return [];
            }

            search(query) {
                return suppliers.filter(s =>
                    s.idNumber.toLowerCase().includes(query.toLowerCase()) ||
                    s.company.toLowerCase().includes(query.toLowerCase()) ||
                    s.contactName.toLowerCase().includes(query.toLowerCase()) ||
                    s.phone.includes(query) ||
                    s.bankAccount.toLowerCase().includes(query.toLowerCase()) ||
                    s.bankName.toLowerCase().includes(query.toLowerCase()) ||
                    s.catalog.toLowerCase().includes(query.toLowerCase())
                );
            }

            createSupplier() {
                suppliers.push(this);
                renderTable();
            }
        }

        let suppliers = [];
        let editIdNumber = null;
        let currentFilter = 'a-z';

        function renderTable(data = suppliers) {
            const tbody = document.getElementById('suppliersBody');
            tbody.style.opacity = '0';

            let sortedData = [...data];
            if (currentFilter === 'a-z') {
                sortedData.sort((a, b) => a.company.localeCompare(b.company));
            } else if (currentFilter === 'recent') {
                sortedData.sort((a, b) => b.createdAt - a.createdAt);
            } else if (currentFilter === 'oldest') {
                sortedData.sort((a, b) => a.createdAt - b.createdAt);
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
                            <button class="edit" data-tooltip="Editar" onclick="openModal('edit', '${supplier.idNumber}')"><i class="fas fa-edit"></i></button>
                            <button class="delete" data-tooltip="Eliminar" onclick="deleteSupplier('${supplier.idNumber}')"><i class="fas fa-trash"></i></button>
                            <button class="history" data-tooltip="Historial de Compras" onclick="showPurchaseHistory('${supplier.idNumber}')"><i class="fas fa-history"></i></button>
                            <button class="top-products" data-tooltip="Productos Más Comprados" onclick="showTopPurchasedProducts('${supplier.idNumber}')"><i class="fas fa-star"></i></button>
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

        function openModal(mode, idNumber = null) {
            const modal = document.getElementById('modal');
            const title = document.getElementById('modalTitle');
            const idNumberInput = document.getElementById('supplierCedulaRuc');
            const company = document.getElementById('supplierCompany');
            const contactName = document.getElementById('supplierContactName');
            const phone = document.getElementById('supplierPhone');
            const bankAccount = document.getElementById('supplierBankAccount');
            const bankName = document.getElementById('supplierBankName');
            const catalog = document.getElementById('supplierCatalog');

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
            });

            if (mode === 'add') {
                title.textContent = 'Nuevo Proveedor';
                idNumberInput.value = '';
                company.value = '';
                contactName.value = '';
                phone.value = '';
                bankAccount.value = '';
                bankName.value = '';
                catalog.value = '';
                editIdNumber = null;
            } else {
                title.textContent = 'Editar Proveedor';
                const supplier = suppliers.find(s => s.idNumber === idNumber);
                idNumberInput.value = supplier.idNumber;
                company.value = supplier.company;
                contactName.value = supplier.contactName;
                phone.value = supplier.phone;
                bankAccount.value = supplier.bankAccount;
                bankName.value = supplier.bankName;
                catalog.value = supplier.catalog;
                editIdNumber = idNumber;
            }

            inputs.forEach(input => {
                input._keypressHandler = (e) => restrictMultipleSpaces(e, input);
                input._blurHandler = () => normalizeSpaces(input);
                input.addEventListener('keypress', input._keypressHandler);
                input.addEventListener('blur', input._blurHandler);
            });
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

            if (suppliers.some(s => s.idNumber === idNumber && (editIdNumber === null || s.idNumber !== editIdNumber))) {
                alert('Ya existe un proveedor con esa cédula/RUC.');
                return;
            }

            const newSupplier = new Supplier(idNumber, company, phone, bankAccount, contactName, bankName, catalog);

            if (editIdNumber) {
                const supplier = suppliers.find(s => s.idNumber === editIdNumber);
                const originalCreatedAt = supplier.createdAt;

                suppliers = suppliers.filter(s => s.idNumber !== editIdNumber);

                newSupplier.createdAt = originalCreatedAt;
                newSupplier.createSupplier();
            } else {
                newSupplier.createSupplier();
            }

            closeModal();
        }

        function deleteSupplier(idNumber) {
            if (confirm('¿Estás seguro de eliminar este proveedor?')) {
                suppliers = suppliers.filter(s => s.idNumber !== idNumber);
                renderTable();
            }
        }

        function showPurchaseHistory(idNumber) {
            const supplier = suppliers.find(s => s.idNumber === idNumber);
            if (supplier) {
                const supplierInstance = new Supplier(
                    supplier.idNumber,
                    supplier.company,
                    supplier.phone,
                    supplier.bankAccount,
                    supplier.contactName,
                    supplier.bankName,
                    supplier.catalog
                );
                const history = supplierInstance.purchaseHistory(idNumber);
                openInfoModal('Historial de Compras', history);
            }
        }

        function showTopPurchasedProducts(idNumber) {
            const supplier = suppliers.find(s => s.idNumber === idNumber);
            if (supplier) {
                const supplierInstance = new Supplier(
                    supplier.idNumber,
                    supplier.company,
                    supplier.phone,
                    supplier.bankAccount,
                    supplier.contactName,
                    supplier.bankName,
                    supplier.catalog
                );
                const products = supplierInstance.topPurchasedProducts(idNumber);
                openInfoModal('Productos Más Comprados', products);
            }
        }

        function openInfoModal(title, items) {
            const modal = document.getElementById('infoModal');
            const modalTitle = document.getElementById('infoModalTitle');
            const modalList = document.getElementById('infoModalList');

            modalTitle.textContent = title;
            modalList.innerHTML = '';
            items.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item;
                modalList.appendChild(li);
            });

            modal.style.display = 'flex';
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.transition = 'opacity 0.3s ease';
                modal.style.opacity = '1';
            }, 10);
        }

        function closeInfoModal() {
            const modal = document.getElementById('infoModal');
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
                modal.style.opacity = '1';
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

        document.getElementById('filter').addEventListener('change', (e) => {
            currentFilter = e.target.value;
            const searchQuery = document.getElementById('search').value;
            let filteredData = suppliers;
            if (searchQuery) {
                const dummySupplier = new Supplier('', '', '', '', '', '', '');
                filteredData = dummySupplier.search(searchQuery);
            }
            renderTable(filteredData);
        });

        document.getElementById('search').addEventListener('input', (e) => {
            const query = e.target.value;
            const dummySupplier = new Supplier('', '', '', '', '', '', '');
            const filtered = dummySupplier.search(query);
            renderTable(filtered);
        });

        renderTable();