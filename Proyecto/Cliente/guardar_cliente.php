<?php
include 'conexion.php';
require('fpdf/fpdf.php'); // Asegúrate de que esta ruta sea correcta

$taxid = $_POST['taxid'];
$fullname = $_POST['fullname'];
$address = $_POST['address'];
$referenceNote = $_POST['referenceNote'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$facturaTipo = $_POST['facturaTipo'] ?? 'cliente'; // por si falta

// Si es consumidor final, usar valores genéricos
if ($facturaTipo === "consumidor_final") {
    $fullname = "Consumidor Final";
    $taxid = "";
    $address = "";
    $referenceNote = "";
    $phone = "";
    $email = "";
}

// Guardar cliente si no es consumidor final
if ($facturaTipo !== "consumidor_final") {
    $check = $conn->prepare("SELECT TaxID FROM client WHERE TaxID = ?");
    $check->bind_param("s", $taxid);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO client (TaxID, FullName, Address, ReferenceNote, Phone, Email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $taxid, $fullname, $address, $referenceNote, $phone, $email);
        $stmt->execute();
    }
}

// Ahora generamos el PDF de la factura
$fecha = date("Y-m-d");

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,"Factura - Bazar",0,1,'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','',12);

$pdf->Cell(100,10,"Fecha: $fecha",0,1);
$pdf->Cell(100,10,"Cliente: $fullname",0,1);
$pdf->Cell(100,10,"Cédula: $taxid",0,1);
$pdf->Cell(100,10,"Dirección: $address",0,1);
$pdf->Cell(100,10,"Referencia: $referenceNote",0,1);
$pdf->Cell(100,10,"Teléfono: $phone",0,1);
$pdf->Cell(100,10,"Email: $email",0,1);
$pdf->Ln(10);

// Aquí puedes agregar productos o totales si los tienes
$pdf->Cell(100,10,"[Detalles de productos aquí]",0,1);
$pdf->Cell(100,10,"Total: $100.00",0,1);

$pdf->Output("I", "Factura_$fullname.pdf"); // Mostrar en navegador
?>
