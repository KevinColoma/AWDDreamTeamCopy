function getClientData() {
    const taxid = document.getElementById("taxid").value;
    if (taxid === "") return;

    fetch("buscar_cliente.php?taxid=" + taxid)
      .then(response => response.json())
      .then(data => {
        if (data) {
          document.getElementById("fullname").value = data.FullName;
          document.getElementById("address").value = data.Address;
          document.getElementById("references").value = data.References;
          document.getElementById("phone").value = data.Phone;
          document.getElementById("email").value = data.Email;
        }
      });
  }

  function toggleCliente() {
    const tipo = document.getElementById("facturaTipo").value;
    const disabled = (tipo === "consumidor_final");
    
    ["taxid", "fullname", "address", "references", "phone", "email"].forEach(id => {
      const input = document.getElementById(id);
      input.disabled = disabled;
      if (disabled) input.value = "";
    });

    if (disabled) {
      document.getElementById("fullname").value = "Consumidor Final";
    }
  }

  // Ejecutar después de que cargue el DOM
  document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("invoiceForm").addEventListener("submit", function() {
      document.getElementById("facturaTipoInput").value = document.getElementById("facturaTipo").value;
    });
  });



   function toggleTheme() {
    document.body.classList.toggle("dark-mode");
    // Cambia también el tema de los formularios y otros elementos
    document.querySelectorAll('form, .theme-toggle, h2').forEach(el => {
        el.classList.toggle("dark-mode");
    });
    localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
  }

  // Al cargar, aplicar el tema guardado
  document.addEventListener("DOMContentLoaded", function() {
    const theme = localStorage.getItem("theme");
    if (theme === "dark") {
      document.body.classList.add("dark-mode");
      document.querySelectorAll('form, .theme-toggle, h2').forEach(el => {
        el.classList.add("dark-mode");
      });
    }
  });




  document.addEventListener("DOMContentLoaded", () => {
  const taxid = document.getElementById("taxid");
  const fullname = document.getElementById("fullname");
  const phone = document.getElementById("phone");

  // Solo números (taxid)
  taxid.addEventListener("input", () => {
    taxid.value = taxid.value.replace(/[^0-9]/g, '');
  });

  // Solo letras y espacios (fullname)
  fullname.addEventListener("input", () => {
    fullname.value = fullname.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g, '');
  });

  // Solo números, + opcional al inicio (phone)
  phone.addEventListener("input", () => {
    phone.value = phone.value.replace(/(?!^\+)[^\d]/g, '');
  });
});


/*function getClientData() {
    const taxid = document.getElementById("taxid").value;
    if (taxid === "") return;
  
    fetch("buscar_cliente.php?taxid=" + taxid)
      .then(response => response.json())
      .then(data => {
        if (data) {
          document.getElementById("fullname").value = data.FullName;
          document.getElementById("address").value = data.Address;
          document.getElementById("references").value = data.References;
          document.getElementById("phone").value = data.Phone;
          document.getElementById("email").value = data.Email;
        }
      });
  }
  
  function toggleCliente() {
    const tipo = document.getElementById("facturaTipo").value;
    const disabled = (tipo === "consumidor_final");
    ["taxid", "fullname", "address", "references", "phone", "email"]
      .forEach(id => document.getElementById(id).disabled = disabled);
  }
  

*/







// Función para buscar productos
function buscarProducto() {
    const query = document.getElementById("buscarProducto").value;
    if (!query) return;
    
    fetch(`buscar_productos.php?q=${query}`)
        .then(response => response.json())
        .then(data => {
            const lista = document.getElementById("lista-productos");
            lista.innerHTML = '';
            
            if (data.length === 0) {
                lista.innerHTML = '<li>No se encontraron productos</li>';
            } else {
                data.forEach(producto => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        ${producto.name} - $${producto.salePrice} 
                        <button onclick="agregarProducto('${producto.id}', '${producto.name.replace("'", "\\'")}', ${producto.salePrice})">
                            Agregar
                        </button>
                    `;
                    lista.appendChild(li);
                });
            }
            
            document.getElementById("resultados-busqueda").style.display = 'block';
        });
}

// Función para agregar productos a la factura
function agregarProducto(id, nombre, precio) {
    const tbody = document.querySelector("#tabla-productos tbody");
    const fila = document.createElement('tr');
    fila.id = `producto-${id}`;
    fila.innerHTML = `
        <td>${nombre}</td>
        <td><input type="number" min="1" value="1" class="cantidad" onchange="actualizarTotal(${id}, ${precio})"></td>
        <td class="precio-unitario">${precio.toFixed(2)}</td>
        <td class="total-producto">${precio.toFixed(2)}</td>
        <td><button type="button" onclick="eliminarProducto('${id}')">Eliminar</button></td>
    `;
    tbody.appendChild(fila);
    actualizarProductosJson();
}

// Funciones auxiliares
function eliminarProducto(id) {
    document.getElementById(`producto-${id}`).remove();
    actualizarProductosJson();
}

function actualizarTotal(id, precioUnitario) {
    const cantidad = document.querySelector(`#producto-${id} .cantidad`).value;
    const total = cantidad * precioUnitario;
    document.querySelector(`#producto-${id} .total-producto`).textContent = total.toFixed(2);
    actualizarProductosJson();
}

function actualizarProductosJson() {
    const productos = [];
    document.querySelectorAll("#tabla-productos tbody tr").forEach(fila => {
        const id = fila.id.replace('producto-', '');
        const nombre = fila.cells[0].textContent;
        const cantidad = fila.cells[1].querySelector('input').value;
        const precio = parseFloat(fila.cells[2].textContent);
        
        productos.push({
            id,
            nombre,
            cantidad: parseInt(cantidad),
            precio
        });
    });
    
    document.getElementById("productosJson").value = JSON.stringify(productos);
}