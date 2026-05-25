<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibir los datos del formulario (los nombres coinciden con los atributos 'name' del HTML)
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

    //  Credenciales de conexión a PostgreSQL
    $host = "localhost";
    $port = "5432";
    $dbname = "nombre_de_la_base_de_datosjaja"; 
    $user = "usuario";                   
    $password = "contraseña";            

    $conn_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";
    $dbconn = pg_connect($conn_string);

    if (!$dbconn) {
        die("Error: No se pudo conectar a la base de datos.");
    }

    // Preparar la consulta SQL de Inserción
    // checar que los nombres sean los mismos que en psql
    $query = "INSERT INTO importaciones (
                fecha, cantidad, unidad_medida, pais, tipo_cambio, valor_dolares, 
                costo_unitario, tipo_impo, peso_neto, descripcion, total, peso_bruto
              ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";

    // Ejecutar la consulta pasando los parámetros
    $result = pg_query_params($dbconn, $query, array(
        $fecha, $cantidad, $unidad_medida, $pais, $tipo_cambio, $valor_dolares,
        $costo_unitario, $tipo_impo, $peso_neto, $descripcion, $total, $peso_bruto
    ));

    //  Verificar el resultado
    if ($result) {
        // Redirigir de vuelta con un mensaje de éxito
        echo "<script>alert('Importación guardada con éxito'); window.location.href='importacion.html';</script>";
    } else {
        echo "Error al guardar el registro: " . pg_last_error($dbconn);
    }

    // Cerrar la conexión
    pg_close($dbconn);
} else {
    echo "Acceso no autorizado.";
}
?>