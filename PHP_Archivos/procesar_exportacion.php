<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fecha = $_POST['fecha'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $unidad_medida = $_POST['unidad_medida'] ?? '';
    $pais = $_POST['pais'] ?? '';
    $tipo_cambio = $_POST['tipo_cambio'] ?? 0;
    $valor_dolares = $_POST['valor_dolares'] ?? 0;
    $costo_unitario = $_POST['costo_unitario'] ?? 0;
    $peso_neto = $_POST['peso_neto'] ?? 0;
    $descripcion = $_POST['descripcion'] ?? '';
    $total = $_POST['total'] ?? 0;
    $peso_bruto = $_POST['peso_bruto'] ?? 0;
    $tipo_expo = $_POST['tipo_expo'] ?? ''; 
    
    $total = $_POST['total'] ?? 0;
    
    // Conexión a PostgreSQL
    $host = "localhost";
    $port = "5432";
    $dbname = "nombre_de_la_base_de_datos";
    $user = "usuario";
    $password = "contraseña";

    $dbconn = pg_connect("host={$host} port={$port} dbname={$dbname} user={$user} password={$password}");

    if (!$dbconn) {
        die("Error: No se pudo conectar a la base de datos.");
    }

    // Insertar en la tabla de exportaciones
    $query = "INSERT INTO exportaciones (
                fecha, cantidad, tipo_expo, total /* Agrega las demás columnas aquí */
              ) VALUES ($1, $2, $3, $4)";

    $result = pg_query_params($dbconn, $query, array(
        $fecha, $cantidad, $tipo_expo, $total // Y agrega las demás variables aquí
    ));

    if ($result) {
        echo "<script>alert('Exportación guardada con éxito'); window.location.href='exportacion.html';</script>";
    } else {
        echo "Error: " . pg_last_error($dbconn);
    }

    pg_close($dbconn);
}
?>