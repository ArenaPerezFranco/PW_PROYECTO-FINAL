<?php
session_start();


$host = 'localhost';
$db_name = 'BD_Aduanas';
$usuario = 'arena2977';
$password = '6674';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // 1. Recibir datos de la Factura
        $num_factura = $_POST['num_factura'] ?? '';
        $fecha_factura = $_POST['fecha_factura'] ?? '';
        $metodo_pago = $_POST['metodo_pago'] ?? '';
        $moneda = $_POST['moneda'] ?? '';
        
        // 2. Recibir datos de Importación
        $fecha = $_POST['fecha'] ?? '';
        $cantidad = $_POST['cantidad'] ?? 0;
        $unidad_medida = $_POST['unidad_medida'] ?? '';
        $pais = $_POST['pais'] ?? '';
        $tipo_cambio = $_POST['tipo_cambio'] ?? 0;
        $valor_dolares = $_POST['valor_dolares'] ?? 0;
        $costo_unitario = $_POST['costo_unitario'] ?? 0;
        $tipo_impo = $_POST['tipo_impo'] ?? '';
        $peso_neto = $_POST['peso_neto'] ?? 0;
        $descripcion = $_POST['descripcion'] ?? '';
        $total = $_POST['total'] ?? 0;
        $peso_bruto = $_POST['peso_bruto'] ?? 0;

        // INICIAMOS LA TRANSACCIÓN PARA LA RELACIÓN 1:1
        $conexion->beginTransaction();

        // PASO A: Insertar en la tabla Factura
        $sqlFactura = "INSERT INTO Factura (numero_factura, fecha_factura, metodo_pago, moneda, monto_total) 
                       VALUES (:num, :fecha_fact, :metodo, :moneda, :total)";
        $stmtFactura = $conexion->prepare($sqlFactura);
        $stmtFactura->execute([
            ':num' => $num_factura,
            ':fecha_fact' => $fecha_factura,
            ':metodo' => $metodo_pago,
            ':moneda' => $moneda,
            ':total' => $total
        ]);

        // Obtenemos el ID de la factura que se acaba de crear
        $id_factura = $conexion->lastInsertId();

        // PASO B: Insertar en la tabla Importacion usando el id_factura (Foreign Key)
        $sqlImpo = "INSERT INTO Importacion (id_factura, fecha, cantidad, unidad_medida, pais, tipo_cambio, valor_dolares, costo_unitario, tipo_impo, peso_neto, descripcion, peso_bruto, total) 
                    VALUES (:id_factura, :fecha, :cantidad, :unidad_medida, :pais, :tipo_cambio, :valor_dolares, :costo_unitario, :tipo_impo, :peso_neto, :descripcion, :peso_bruto, :total)";
        $stmtImpo = $conexion->prepare($sqlImpo);
        $stmtImpo->execute([
            ':id_factura' => $id_factura,
            ':fecha' => $fecha,
            ':cantidad' => $cantidad,
            ':unidad_medida' => $unidad_medida,
            ':pais' => $pais,
            ':tipo_cambio' => $tipo_cambio,
            ':valor_dolares' => $valor_dolares,
            ':costo_unitario' => $costo_unitario,
            ':tipo_impo' => $tipo_impo,
            ':peso_neto' => $peso_neto,
            ':descripcion' => $descripcion,
            ':peso_bruto' => $peso_bruto,
            ':total' => $total
        ]);

        // SI TODO SALIÓ BIEN, CONFIRMAMOS AMBOS REGISTROS
        $conexion->commit();

        echo "<script>alert('Factura e Importación guardadas exitosamente.'); window.location.href='importacion.html';</script>";
    }
} catch (PDOException $e) {
    // Si hay algún error, deshace todo para no dejar registros huérfanos
    if (isset($conexion)) {
        $conexion->rollBack();
    }
    echo "Error en la base de datos: " . $e->getMessage();
}
?>