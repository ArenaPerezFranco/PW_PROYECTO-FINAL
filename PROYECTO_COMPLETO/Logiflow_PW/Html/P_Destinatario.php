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

// --- S3: SOLICITUD DE ELIMINAR DESTINATARIO ---
    if (isset($_POST['accion_eliminar']) && !empty($_POST['id_eliminar'])) {
        $id_eliminar = trim($_POST['id_eliminar']);
        try {
            $conexion->beginTransaction();
            
            //Primero lo eliminamos de DESTINATARIOS
            $sqlDelDest = "DELETE FROM destinatarios WHERE idDestinatarios = :id";
            $stmtDelDest = $conexion->prepare($sqlDelDest);
            $stmtDelDest->execute([':id' => $id_eliminar]);
            
            // Después eliminamos de ENTIDAD FISCAL
            $sqlDelFiscal = "DELETE FROM entidad_fiscal WHERE idEntidadFiscal = :id";
            $stmtDelFiscal = $conexion->prepare($sqlDelFiscal);
            $stmtDelFiscal->execute([':id' => $id_eliminar]);
            
            $conexion->commit();
            echo "<script>alert('Destinatario eliminado correctamente'); window.location.href=window.location.href;</script>";
            exit;
        } catch (PDOException $e) {
            $conexion->rollback();
            die("Error al eliminar: " . $e->getMessage());
        }
    }
    // Recibir datos del formulario
    $nombre = trim($_POST['nombre']);
    $numero = trim($_POST['numero']);
    $contacto = trim($_POST['contacto']);
    $calle = trim($_POST['calle']);
    $colonia = trim($_POST['colonia']);
    $pais = trim($_POST['pais']);
    $id_editar = isset($_POST['id_destinatario_editar']) && !empty($_POST['id_destinatario_editar']) ? trim($_POST['id_destinatario_editar']) : '';


    try {
        // Iniciamos transacción asegurando inserciones
        $conexion->beginTransaction();
 // ------------------S1: UPDATE, EDITAR DESTINATARIO------------------------------
        if (!empty($id_editar)) {
            $sqlFiscal = "UPDATE entidad_fiscal ef
                          INNER JOIN destinatarios d ON d.idEntidadFiscal = ef.idEntidadFiscal
                          SET ef.nombre = :nombre, ef.calle = :calle, ef.num = :num, 
                              ef.pais = :pais, ef.colonia = :colonia, ef.contacto = :contacto
                          WHERE d.idDestinatarios = :id_destinatario";
            
            $stmtFiscal = $conexion->prepare($sqlFiscal);
            $stmtFiscal->execute([
                ':nombre'=> $nombre,
                ':calle'=> $calle,
                ':num'=> $numero,
                ':pais'=> $pais,
                ':colonia'=> $colonia,
                ':contacto'=> $contacto,
                ':id_destinatario' => $id_editar
            ]);
            $conexion->commit();
            echo "<script>
                     alert('Destinatario actualizado');
                     window.location.href=window.location.href;
                 </script>";
            exit;
        }else{
  // ------------------S2: INSERTAR NUEVO DESTINATARIO------------------------------
        $sqlFiscal = "INSERT INTO entidad_fiscal (nombre, calle, num, pais, colonia, contacto) 
                      VALUES (:nombre, :calle, :num, :pais, :colonia, :contacto)";
        $stmtFiscal = $conexion->prepare($sqlFiscal); 
        $stmtFiscal->execute([
            ':nombre'=> $nombre,
            ':calle'=> $calle,
            ':num'=> $numero,
            ':pais'=> $pais,
            ':colonia' => $colonia,
            ':contacto' => $contacto
        ]);
        // Obtener el ID generado de la ENTIDAD FISCAL
        $id_entidadFiscal = $conexion->lastInsertId();

        // INSERTAR EN DESTINATARIOS
        $sqlDestinatario = "INSERT INTO destinatarios (idDestinatarios, idEntidadFiscal)
                            VALUES (:id_destinatario, :id_entidadFiscal)";

        $stmtDestinatario = $conexion->prepare($sqlDestinatario);
        
        $stmtDestinatario->execute([
            ':id_destinatario'  => $id_entidadFiscal,
            ':id_entidadFiscal' => $id_entidadFiscal
        ]);

        // Si todo sale bien -> Se confirman los cambios en BD
        $conexion->commit();
        echo "<script>alert('Destinatario agregado correctamente');</script>";

        }      

    } catch (PDOException $e) {
        $conexion->rollback();
        die("Error al insertar: " . $e->getMessage());
    }
}

//-------LECTURA PARA EL LISTADO------
try {
    $sqlListado = "SELECT d.idDestinatarios, df.nombre, df.num, df.contacto, df.calle, df.colonia, df.pais 
                   FROM destinatarios d
                   INNER JOIN entidad_fiscal df ON d.idEntidadFiscal = df.idEntidadFiscal";
    $stmtListado = $conexion->query($sqlListado);
    $destinatarios = $stmtListado->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si falla la consulta por alguna razón, destinatarios queda vacio
    $destinatarios = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinatarios</title>
    <link rel="stylesheet" href="../Css/destinatario.css"> 
</head>
<body>
    <div class="main-container">
        <header class="logiflow-header">
            <span class="header-menu">MENU</span>
            <div class="header-logo">
                <span class="logo-icon">⬢</span> LOGIFLOW
            </div>
            <span class="header-status">DESTINATARIOS</span> 
        </header>

        <main class="content">
            <h1 class="page-title">DESTINATARIOS</h1> 

            <form id="destinatariosForm" class="form-card" action="" method="POST">
                <input type="hidden" id="id_destinatario_editar" name="id_destinatario_editar" value="">
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
                        <button type="submit" class="btn-submit">AGREGAR DESTINATARIO</button>
                    </div>
                    <div></div>
                </div>
            </form>
        </main>

        <section class="destinatarios-section">
            <h2 class="section-title">LISTADO DE DESTINATARIOS</h2>
            <div class="destinatarios-controls">
                <input type="text" id="searchDestinatario" placeholder="Buscar destinatario...">
                
            </div>

            <table class="destinatarios-table">
                <thead>
                    <tr>
                        <th>ID DESTINATARIO</th>
                        <th>NOMBRE</th>
                        <th>NÚMERO</th>
                        <th>CONTACTO</th>
                        <th>CALLE</th>
                        <th>COLONIA</th>
                        <th>PAÍS</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody id="destinatariosTableBody">
                    <?php if (empty($destinatarios)): ?>
                        <tr>
                            <td colspan="8" class="no-data">No hay destinatarios registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($destinatarios as $dest): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($dest['idDestinatarios']); ?></td>
                                <td><?php echo htmlspecialchars($dest['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($dest['num']); ?></td>
                                <td><?php echo htmlspecialchars($dest['contacto']); ?></td>
                                <td><?php echo htmlspecialchars($dest['calle']); ?></td>
                                <td><?php echo htmlspecialchars($dest['colonia']); ?></td>
                                <td><?php echo htmlspecialchars($dest['pais']); ?></td>
                                <td>
                                    <button class="btn-edit" onclick="editDestinatario(this, '<?php echo $dest['idDestinatarios']; ?>')" style="color: var(--primary-color); background: 
                                       none; border: none; cursor: pointer; font-weight: bold;">✏️ Editar</button>
                                    <button class="btn-delete" onclick="eliminarDestinatario('<?php echo $dest['idDestinatarios']; ?>')" style="color: #c12d2d; background: none; border: none; cursor: pointer; font-weight: bold;">🗑️ Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="destinatarios-summary">
                <div>Total Destinatarios: <span id="totalDestinatarios"><?php echo count($destinatarios); ?></span></div>
            </div>
        </section>
    </div>

    <script src="../JavaScript/destinatario.js"></script>
</body>
</html>