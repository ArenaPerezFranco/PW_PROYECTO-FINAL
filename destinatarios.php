<?php
require 'conexion.php'; // misma conexión que el login

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $nombre   = trim($_POST['nombre']);
    $numero   = trim($_POST['numero']);
    $contacto = trim($_POST['contacto']);
    $calle    = trim($_POST['calle']);
    $colonia  = trim($_POST['colonia']);
    $pais     = trim($_POST['pais']);

    try {
        // Insertar en la tabla destinatarios
        $stmt = $conexion->prepare("INSERT INTO destinatarios 
            (nombre, numero, contacto, calle, colonia, pais) 
            VALUES (:nombre, :numero, :contacto, :calle, :colonia, :pais)");

        $stmt->execute([
            ':nombre'   => $nombre,
            ':numero'   => $numero,
            ':contacto' => $contacto,
            ':calle'    => $calle,
            ':colonia'  => $colonia,
            ':pais'     => $pais
        ]);

        echo "<script>alert('Destinatario agregado correctamente');</script>";
    } catch (PDOException $e) {
        die("Error al insertar: " . $e->getMessage());
    }
}
?>
