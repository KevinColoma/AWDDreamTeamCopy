<?php
$conn = new mysqli("localhost", "root", "", "bazar");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}






/*

CREATE TABLE client (
  TaxID VARCHAR(20) PRIMARY KEY,
  FullName VARCHAR(100),
  Address TEXT,
  `ReferenceNote` TEXT,
  Phone VARCHAR(20),
  Email VARCHAR(100)
);

*/ 
?>

