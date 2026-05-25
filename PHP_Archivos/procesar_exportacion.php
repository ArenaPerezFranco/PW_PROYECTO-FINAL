<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibir los datos...
    $fecha = $_POST['fecha'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    // ... (Agrega el resto de las variables igual que en importación)
    
    // Ojo aquí: cambia a tipo_expo
    $tipo_expo = $_POST['tipo_expo'] ?? ''; 
    
    $total = $_POST['total'] ?? 0;
    
    // Conexión a PostgreSQL
    $host = "localhost";
    $port = "5432";
    $dbname = "nombre_de_tu_base_de_datos";
    $user = "tu_usuario";
    $password = "tu_contraseña";

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