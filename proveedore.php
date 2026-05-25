<?php
require 'conexion.php'; // usa la misma conexión que el login

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $nombre         = trim($_POST['nombre']);
    $numero         = trim($_POST['numero']);
    $entidad_fiscal = trim($_POST['entidad_fiscal']);
    $calle          = trim($_POST['calle']);
    $contacto       = trim($_POST['contacto']);
    $colonia        = trim($_POST['colonia']);
    $pais           = trim($_POST['pais']);

    try {
        // Insertar en la tabla proveedores
        $stmt = $conexion->prepare("INSERT INTO proveedores 
            (nombre, numero, entidad_fiscal, calle, contacto, colonia, pais) 
            VALUES (:nombre, :numero, :entidad_fiscal, :calle, :contacto, :colonia, :pais)");

        $stmt->execute([
            ':nombre'         => $nombre,
            ':numero'         => $numero,
            ':entidad_fiscal' => $entidad_fiscal,
            ':calle'          => $calle,
            ':contacto'       => $contacto,
            ':colonia'        => $colonia,
            ':pais'           => $pais
        ]);

        echo "<script>alert('Proveedor agregado correctamente');</script>";
    } catch (PDOException $e) {
        die("Error al insertar: " . $e->getMessage());
    }
}
?>
