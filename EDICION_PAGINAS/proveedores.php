<?php
session_start();
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

//-----------------PROCESAR EL FORMULARIO (METODO POST)--------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- S3: SOLICITUD DE ELIMINAR PROVEEDOR ---
    if (isset($_POST['accion_eliminar']) && !empty($_POST['id_eliminar'])) {
        $id_eliminar = trim($_POST['id_eliminar']);
        try {
            $conexion->beginTransaction();
            
            // Eliminamos de proovedores usando el nombre exacto de tu BD: idProovedores de la tabla proovedores
            $sqlDelProv = "DELETE FROM proovedores WHERE idProovedores = :id";
            $stmtDelProv = $conexion->prepare($sqlDelProv);
            $stmtDelProv->execute([':id' => $id_eliminar]);
            
            // Después eliminamos de ENTIDAD FISCAL
            $sqlDelFiscal = "DELETE FROM entidad_fiscal WHERE idEntidadFiscal = :id";
            $stmtDelFiscal = $conexion->prepare($sqlDelFiscal);
            $stmtDelFiscal->execute([':id' => $id_eliminar]);
            
            $conexion->commit();
            echo "<script>alert('Proveedor eliminado correctamente'); window.location.href=window.location.href;</script>";
            exit;
        } catch (PDOException $e) {
            $conexion->rollback();
            die("Error al eliminar: " . $e->getMessage());
        }
    }

    // Recibir datos del formulario comunes para Inserción y Edición
    $nombre = trim($_POST['nombre']);
    $numero = trim($_POST['numero']);
    $contacto = trim($_POST['contacto']);
    $calle = trim($_POST['calle']);
    $colonia = trim($_POST['colonia']);
    $pais = trim($_POST['pais']);
    $id_editar = isset($_POST['id_proveedor_editar']) && !empty($_POST['id_proveedor_editar']) ? trim($_POST['id_proveedor_editar']) : '';

    try {
        $conexion->beginTransaction();

        // ------------------S1: UPDATE, EDITAR PROVEEDOR------------------------------
        if (!empty($id_editar)) {
            $sqlFiscal = "UPDATE entidad_fiscal ef
                          INNER JOIN proovedores p ON p.idEntidadFiscal = ef.idEntidadFiscal
                          SET ef.nombre = :nombre, ef.calle = :calle, ef.num = :num, 
                              ef.pais = :pais, ef.colonia = :colonia, ef.contacto = :contacto
                          WHERE p.idProovedores = :id_proveedor";
            
            $stmtFiscal = $conexion->prepare($sqlFiscal);
            $stmtFiscal->execute([
                ':nombre' => $nombre,
                ':calle'  => $calle,
                ':num'    => $numero,
                ':pais'   => $pais,
                ':colonia'=> $colonia,
                ':contacto'=> $contacto,
                ':id_proveedor' => $id_editar
            ]);

            $conexion->commit();
            echo "<script>alert('Proveedor actualizado correctamente'); window.location.href=window.location.href;</script>";
            exit;

        } else {
            // ------------------S2: INSERTAR NUEVO PROVEEDOR------------------------------
            $sqlFiscal = "INSERT INTO entidad_fiscal (nombre, calle, num, pais, colonia, contacto) 
                          VALUES (:nombre, :calle, :num, :pais, :colonia, :contacto)";
            $stmtFiscal = $conexion->prepare($sqlFiscal); 
            $stmtFiscal->execute([
                ':nombre'  => $nombre,
                ':calle'   => $calle,
                ':num'     => $numero,
                ':pais'    => $pais,
                ':colonia' => $colonia,
                ':contacto'=> $contacto
            ]);
            
            // Obtener el ID generado de la ENTIDAD FISCAL
            $id_entidadFiscal = $conexion->lastInsertId();

            // INSERTAR EN PROOVEDORES (Respetando el doble 'o')
            $sqlProveedor = "INSERT INTO proovedores (idProovedores, idEntidadFiscal)
                             VALUES (:id_proveedor, :id_entidadFiscal)";
            $stmtProveedor = $conexion->prepare($sqlProveedor);
            $stmtProveedor->execute([
                ':id_proveedor'    => $id_entidadFiscal,
                ':id_entidadFiscal' => $id_entidadFiscal
            ]);

            $conexion->commit();
            echo "<script>alert('Proveedor agregado correctamente'); window.location.href=window.location.href;</script>";
            exit;
        }      

    } catch (PDOException $e) {
        $conexion->rollback();
        die("Error en la base de datos: " . $e->getMessage());
    }
}

//-------LECTURA PARA EL LISTADO CON INNER JOIN------
try {
    $sqlListado = "SELECT p.idProovedores, df.nombre, df.num, df.contacto, df.calle, df.colonia, df.pais 
                   FROM proovedores p
                   INNER JOIN entidad_fiscal df ON p.idEntidadFiscal = df.idEntidadFiscal";
    $stmtListado = $conexion->query($sqlListado);
    $proveedores = $stmtListado->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $proveedores = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores</title>
    <link rel="stylesheet" href="../Css/proveedor.css"> 
</head>
<body>
    <div class="main-container">
        <header class="logiflow-header">
            <span class="header-menu">MENU</span>
            <div class="header-logo">
                <span class="logo-icon">⬢</span> LOGIFLOW
            </div>
            <span class="header-status">PROVEEDORES</span> 
        </header>

        <main class="content">
            <h1 class="page-title">PROVEEDORES</h1> 

            <form id="proveedoresForm" class="form-card" action="" method="POST">
                <input type="hidden" id="id_proveedor_editar" name="id_proveedor_editar" value="">
                <div class="form-grid">
                    <div class="field">
                        <label>NOMBRE</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div class="field">
                        <label>NÚMERO</label>
                        <input type="text" name="numero" required>
                    </div>

                    <div class="field">
                        <label>CONTACTO</label>
                        <input type="text" name="contacto" required placeholder="**-***-***">
                    </div>
                    <div class="field">
                        <label>CALLE</label>
                        <input type="text" name="calle" required>
                    </div>
                    <div></div>

                    <div class="field">
                        <label>COLONIA</label>
                        <input type="text" name="colonia">
                    </div>
                    <div class="field">
                        <label>PAÍS</label>
                        <select id="pais" name="pais" required>
                            <option value="" disabled selected>Seleccione un país...</option>
                            <option value="USA">Estados Unidos</option>
                            <option value="MEX">México</option>
                            <option value="CHN">China</option>
                            <option value="CAN">Canadá</option>
                        </select>
                    </div>
                    <div></div>

                    <div></div>
                    <div class="form-footer">
                        <button type="submit" class="btn-submit">AGREGAR PROVEEDOR</button>
                    </div>
                    <div></div>
                </div>
            </form>
        </main>

        <section class="destinatarios-section">
            <h2 class="section-title">LISTADO DE PROVEEDORES</h2>
            <div class="destinatarios-controls">
                <input type="text" id="searchProveedor" placeholder="Buscar proveedor por ID...">
            </div>

            <table class="destinatarios-table">
                <thead>
                    <tr>
                        <th>ID PROVEEDOR</th>
                        <th>NOMBRE</th>
                        <th>NÚMERO</th>
                        <th>CONTACTO</th>
                        <th>CALLE</th>
                        <th>COLONIA</th>
                        <th>PAÍS</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody id="proveedoresTableBody">
                    <?php if (empty($proveedores)): ?>
                        <tr>
                            <td colspan="8" class="no-data">No hay proveedores registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($proveedores as $prov): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prov['idProovedores']); ?></td>
                                <td><?php echo htmlspecialchars($prov['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($prov['num']); ?></td>
                                <td><?php echo htmlspecialchars($prov['contacto']); ?></td>
                                <td><?php echo htmlspecialchars($prov['calle']); ?></td>
                                <td><?php echo htmlspecialchars($prov['colonia']); ?></td>
                                <td><?php echo htmlspecialchars($prov['pais']); ?></td>
                                <td>
                                    <button class="btn-edit" onclick="editProveedor(this, '<?php echo $prov['idProovedores']; ?>')" style="color: var(--primary-color); background: none; border: none; cursor: pointer; font-weight: bold;">✏️ Editar</button>
                                    <button class="btn-delete" onclick="eliminarProveedor('<?php echo $prov['idProovedores']; ?>')" style="color: #c12d2d; background: none; border: none; cursor: pointer; font-weight: bold;">🗑️ Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="destinatarios-summary">
                <div>Total Proveedores: <span id="totalProveedores"><?php echo count($proveedores); ?></span></div>
            </div>
        </section>
    </div>

    <script src="../JavaScript/proveedor.js"></script>
</body>
</html>
