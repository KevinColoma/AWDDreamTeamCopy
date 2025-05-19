<?php
include '../PHP/connection.php';
require('fpdf/fpdf.php');

// Configuración general de empresa (puede venir de BD si prefieres)
$empresa = [
    'nombre' => 'Bazar Artículos Variedades S.A.',
    'nif' => 'B-12345678',
    'direccion' => 'Av. Comercial #456, Quito',
    'telefono' => '(+1) 555-987-6543',
    'email' => 'ventas@bazarhogar.com',
    'iva' => 0.21
];

// Recibir datos del formulario
$taxid = $_POST['taxid'] ?? '';
$fullname = $_POST['fullname'] ?? '';
$address = $_POST['address'] ?? '';
$referenceNote = $_POST['referenceNote'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$facturaTipo = $_POST['facturaTipo'] ?? 'cliente';

if ($facturaTipo === "consumidor_final") {
    $fullname = "Consumidor Final";
    $taxid = $address = $referenceNote = $phone = $email = '';
}

// Guardar cliente si no existe y no es consumidor final
if ($facturaTipo !== "consumidor_final") {
    $check = $conn->prepare("SELECT TaxID FROM client WHERE TaxID = ?");
    $check->execute([$taxid]);

    if ($check->rowCount() === 0) {
        $stmt = $conn->prepare("INSERT INTO client (TaxID, FullName, Address, ReferenceNote, Phone, Email) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$taxid, $fullname, $address, $referenceNote, $phone, $email]);
    }
}

// Clase PDF personalizada
class PDF_Factura extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Factura - Bazar'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, utf8_decode('Documento Fiscal'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Datos básicos
$fecha = date("d/m/Y");
$numero_factura = "A-" . str_pad(rand(1, 99999), 5, "0", STR_PAD_LEFT);

// Productos simulados (pueden venir de DB)
$productos = [
    ['descripcion' => 'Artículo decorativo - Jarrón', 'cantidad' => 1, 'precio' => 45.50],
    ['descripcion' => 'Juego de cubiertos (24 piezas)', 'cantidad' => 1, 'precio' => 89.99],
    ['descripcion' => 'Mantel bordado 2x2m', 'cantidad' => 2, 'precio' => 32.50]
];

// Generar PDF
$pdf = new PDF_Factura();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Información de la empresa
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 6, utf8_decode($empresa['nombre']), 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, utf8_decode('NIF: ' . $empresa['nif']), 0, 1);
$pdf->Cell(0, 5, utf8_decode($empresa['direccion']), 0, 1);
$pdf->Cell(0, 5, utf8_decode('Tel: ' . $empresa['telefono']), 0, 1);
$pdf->Cell(0, 5, 'Email: ' . $empresa['email'], 0, 1);
$pdf->Ln(5);

// Datos del cliente
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(0, 6, utf8_decode('DATOS DEL CLIENTE'), 1, 1, 'C', true);
$pdf->Cell(100, 6, utf8_decode("Cliente: $fullname"), 'L', 0);
$pdf->Cell(90, 6, utf8_decode("Factura N°: $numero_factura"), 'R', 1);
$pdf->Cell(100, 6, utf8_decode("Cédula/NIF: $taxid"), 'L', 0);
$pdf->Cell(90, 6, utf8_decode("Fecha: $fecha"), 'R', 1);
$pdf->Cell(190, 6, utf8_decode("Dirección: $address"), 'LR', 1);
$pdf->Cell(100, 6, utf8_decode("Teléfono: $phone"), 'L', 0);
$pdf->Cell(90, 6, utf8_decode("Email: $email"), 'R', 1);
$pdf->Cell(190, 6, utf8_decode("Referencia: $referenceNote"), 'LBR', 1);
$pdf->Ln(5);

// Tabla de productos
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(15, 6, 'CANT.', 1, 0, 'C', true);
$pdf->Cell(115, 6, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', true);
$pdf->Cell(30, 6, 'PRECIO UNIT.', 1, 0, 'C', true);
$pdf->Cell(30, 6, 'IMPORTE', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$total = 0;

foreach ($productos as $producto) {
    $importe = $producto['cantidad'] * $producto['precio'];
    $total += $importe;

    $pdf->Cell(15, 6, $producto['cantidad'], 1, 0, 'C');
    $pdf->Cell(115, 6, utf8_decode($producto['descripcion']), 1, 0, 'L');
    $pdf->Cell(30, 6, number_format($producto['precio'], 2) . ' $', 1, 0, 'R');
    $pdf->Cell(30, 6, number_format($importe, 2) . ' $', 1, 1, 'R');
}

// Totales
$pdf->SetFont('Arial', 'B', 9);
$subtotal = $total;
$iva = $subtotal * $empresa['iva'];
$total += $iva;

$pdf->Ln(2);
$pdf->Cell(130, 6, '', 0, 0);
$pdf->Cell(30, 6, 'Subtotal:', 'LT', 0, 'R');
$pdf->Cell(30, 6, number_format($subtotal, 2) . ' $', 'TR', 1, 'R');

$pdf->Cell(130, 6, '', 0, 0);
$pdf->Cell(30, 6, 'IVA (' . ($empresa['iva'] * 100) . '%):', 'L', 0, 'R');
$pdf->Cell(30, 6, number_format($iva, 2) . ' $', 'R', 1, 'R');

$pdf->Cell(130, 6, '', 0, 0);
$pdf->Cell(30, 6, 'TOTAL:', 'LB', 0, 'R');
$pdf->Cell(30, 6, number_format($total, 2) . ' $', 'BR', 1, 'R');

// Forma de pago y notas
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, utf8_decode('FORMA DE PAGO'), 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, utf8_decode('Pago al contado'), 0, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, 'NOTAS:', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(0, 4, utf8_decode("- Esta factura sirve como garantía para cualquier cambio o devolución (15 días).
- Los artículos personalizados no tienen devolución.
- Gracias por su compra."), 0, 'L');

// cerramos el pdf
$nombre_archivo = "Factura_" . ($facturaTipo === "consumidor_final" ? "ConsumidorFinal" : str_replace(' ', '_', $fullname));
$pdf->Output('I', $nombre_archivo . '.pdf');
?>
