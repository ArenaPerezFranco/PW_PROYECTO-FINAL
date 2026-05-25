<?php
$host = 'localhost';
$db_name = 'BD_Aduanas';
$usuario = 'arena2977';
$password = '';

try {
    
    $conexion = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("¡Error de conexión!: " . $e->getMessage());
}


// Comprobar que el usuario envió el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // Recibir y limpiar datos
    $nombre = trim($_POST['nombre']);
    $unidad = trim($_POST['unidad']);
    $cantidad = trim($_POST['cantidad']);
    $costo = trim($_POST['costo']);
    $descripcion = trim($_POST['descripcion']);

    try{

        // CONSULTA SQL
        $stmt = $conexion->prepare("
            INSERT INTO insumos 
            (nombre, unidad, cantidad, costo, descripcion)
            VALUES
            (:nombre, :unidad, :cantidad, :costo, :descripcion)
        ");

        // Ejecuta
        $stmt->execute([
            ':nombre' => $nombre,
            ':unidad' => $unidad,
            ':cantidad' => $cantidad,
            ':costo' => $costo,
            ':descripcion' => $descripcion
        ]);

        echo "<script>alert('¡Insumo agregado correctamente!');</script>";

    } catch (PDOException $e){
        die("¡Error de conexión!: " . $e->getMessage());
    }
}


// CONSULTAR INSUMOS
try{

    $stmt = $conexion->prepare("SELECT * FROM insumos");
    $stmt->execute();
    $insumos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e){
    die("Error al obtener insumos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Insumos</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div>MENU</div>
    <div class="logo">LOGIFLOW</div>
    <div>INSUMOS</div>
</div>

<h1 class="title">INSUMOS</h1>

<div class="nav-buttons">
    <button class="nav-btn" onclick="window.location.href='index.php'">
        PRODUCTO TERMINADO
    </button>
    <button class="nav-btn active">INSUMOS</button>
</div>

<!-- FORMULARIO -->
<div class="container">
    <form method="POST">

        <div class="form-grid">

            <div class="field">
                <label>NOMBRE</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="field">
                <label>UNIDAD DE MEDIDA</label>
                <input type="text" name="unidad" required>
            </div>

            <div class="field">
                <label>PESO UNITARIO</label>
                <input type="number" step="0.01" name="cantidad" required>
            </div>

            <div class="field">
                <label>VALOR UNITARIO ($)</label>
                <input type="number" step="0.01" name="costo" required>
            </div>

            <div class="field full-width">
                <label>DESCRIPCIÓN</label>
                <textarea name="descripcion"></textarea>
            </div>

            <div class="btn-container">
                <button type="submit">AGREGAR</button>
            </div>

        </div>
    </form>
</div>


<!-- TABLA -->
<div class="table-container">
    <h2 class="table-title">LISTADO DE INSUMOS</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>NOMBRE</th>
                    <th>UNIDAD</th>
                    <th>CANTIDAD</th>
                    <th>COSTO UNITARIO</th>
                    <th>VALOR TOTAL</th>
                    <th>DESCRIPCIÓN</th>
                </tr>
            </thead>

            <tbody>

            <?php if(count($insumos) > 0): ?>

                <?php foreach($insumos as $insumo): ?>

                    <tr>
                        <td><?= htmlspecialchars($insumo['nombre']) ?></td>
                        <td><?= htmlspecialchars($insumo['unidad']) ?></td>
                        <td><?= htmlspecialchars($insumo['cantidad']) ?></td>
                        <td>$<?= htmlspecialchars($insumo['costo']) ?></td>
                        <td>
                            $<?= number_format($insumo['cantidad'] * $insumo['costo'],2) ?>
                        </td>
                        <td><?= htmlspecialchars($insumo['descripcion']) ?></td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6">No hay insumos registrados</td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<script src="insumos.js"></script>
</body>
</html>