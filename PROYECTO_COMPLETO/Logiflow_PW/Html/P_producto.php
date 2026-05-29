<?php
$host = 'localhost';
$db_name = 'BD_Aduanas';
$usuario = 'arena2977';
$password = '6674';

try {

    $conexion = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("¡Error de conexión!: " . $e->getMessage());
}


// COMPROBAR SI EL FORMULARIO FUE ENVIADO
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // RECIBIR Y LIMPIAR DATOS
    $nombre = trim($_POST['nombre']);
    $unidad = trim($_POST['unidad']);
    $peso = trim($_POST['peso']);
    $valor = trim($_POST['valor']);
    $descripcion = trim($_POST['descripcion']);

    try{

        // INSERTAR DATOS
        $stmt = $conexion->prepare("
            INSERT INTO insumos_productoterminado
            (nombre, descripcion,unidad_medida,peso_unitario,valor_unitario)
            VALUES
            (:nombre, :descripcion, :unidad_medida, :peso_unitario, :valor_unitario)
        ");

        $stmt->execute([
            ':nombre' => $nombre,
            ':unidad' => $unidad,
            ':peso' => $peso,
            ':valor' => $valor,
            ':descripcion' => $descripcion
        ]);

        echo "<script>alert('Producto agregado correctamente');</script>";

    } catch(PDOException $e){
        die("¡Error al guardar!: " . $e->getMessage());
    }
}


// OBTENER DATOS PARA LA TABLA
try{
    $consulta = $conexion->query("SELECT * FROM insumos_productoterminado");
    $productos = $consulta->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e){
    die("Error al consultar: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Producto Terminado</title>
<link rel="stylesheet" href="../Css/insumos.css">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div>MENU</div>
    <div class="logo">LOGIFLOW</div>
    <div>PRODUCTO TERMINADO</div>
</div>

<h1 class="title">PRODUCTO TERMINADO</h1>

<div class="nav-buttons">
    <button class="nav-btn active">PRODUCTO TERMINADO</button>
    <button class="nav-btn" onclick="window.location.href='insumos.php'">INSUMOS</button>
</div>

<!-- FORM -->
<div class="container">
    <form method="POST">
        <div class="form-grid">

            <div class="field" for="nombre">
                <label>NOMBRE</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="field">
                <label>UNIDAD DE MEDIDA</label>
                <input type="text" name="unidad" required>
            </div>

            <div class="field">
                <label>PESO UNITARIO (kg)</label>
                <input type="number" step="0.01" name="peso" required>
            </div>

            <div class="field">
                <label>VALOR UNITARIO ($)</label>
                <input type="number" step="0.01" name="valor" required>
            </div>

            <div class="field">
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
    <h2 class="table-title">LISTADO DE PRODUCTOS</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>NOMBRE</th>
                    <th>UNIDAD</th>
                    <th>PESO</th>
                    <th>VALOR</th>
                    <th>DESCRIPCIÓN</th>
                </tr>
            </thead>

            <tbody>

                <?php if(count($productos) > 0): ?>

                    <?php foreach($productos as $producto): ?>

                    <tr>
                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                        <td><?= htmlspecialchars($producto['unidad']) ?></td>
                        <td><?= htmlspecialchars($producto['peso']) ?></td>
                        <td>$<?= htmlspecialchars($producto['valor']) ?></td>
                        <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5">No hay productos registrados</td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>
<script src="../JavaScript/producto.js"></script>

</body>
</html>