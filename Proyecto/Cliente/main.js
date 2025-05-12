

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

