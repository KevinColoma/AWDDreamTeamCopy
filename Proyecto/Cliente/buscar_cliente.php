<?php
include '../conexion/conexion.php';

$taxid = $_GET['taxid'];
$query = $conn->prepare("SELECT * FROM client WHERE TaxID = ?");
$query->bind_param("s", $taxid);
$query->execute();
$result = $query->get_result();

echo json_encode($result->fetch_assoc());
?>
