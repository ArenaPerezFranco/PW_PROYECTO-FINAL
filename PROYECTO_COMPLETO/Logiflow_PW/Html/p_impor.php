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
        echo "
        <script>
        alert('Importación guardada con éxito'); 
        window.location.href='importacion.html';
        </script>
        ";
    } else {
        echo "Error al guardar el registro: " . pg_last_error($dbconn);
    }

    // Cerrar la conexión
    pg_close($dbconn);
} else {
    echo "Acceso no autorizado.";
}

//ARRAY DE UNIDADES
    $unidades = [
        "Kg" => "Kilogramos", 
        "L" => "Litros",
         "Pz" => "Piezas", 
         "M" => "Metros"
         ];
    
?>

<!DOCTYPE html>
<html lang="es">
<div class="main-container">
    <header class="logiflow-header">
        <span class="header-menu">MENU</span>
        
        <meta charset="UTF-8">
        <span class="header-status">IMPORTACIÓN</span>

        <!--ESTILO CSS-->>
        <link rel="stylesheet" href="../Css/form_ex-im.css">
    </header>

    <main class="content">

        <h1 class="page-title">IMPORTACIÓN</h1>

        <form class="form-card" method="POST" action="">
            <div class="form-grid">

                <div class="field">
                    <label for="fecha">FECHA</label>
                    <input type="date" id="fecha" placeholder="DD/MM/YYYY">   
                </div>
                <div class="field">
                    <label for="cantidad">CANTIDAD</label>
                    <input type="number" name="cantidad" required placeholder="0">
                </div>
                <div class="field">
                    <label for="unidad_medida">UNIDAD DE MEDIDA</label>
                    <select name="unidad_medida" id="unidad_medida" required>
                        <option value="" disabled selected>Seleccione medida...</option>
                        <?php 
                          foreach($unidades as $valor => $texto):
                        ?>
                        <option value ="<?php echo $valor; ?>">
                            <?php echo $texto; ?> (<?php echo $valor; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="pais">PAÍS</label>
                    <select id="pais" name="pais" required>
                        <option value="" disabled selected>Seleccione un país...</option>
                        <option value="USA">Estados Unidos</option>
                        <option value="MEX">México</option>
                        <option value="CHN">China</option>
                        <option value="CAN">Canadá</option>
                    </select>
                </div>

                <!-- Fila 2 -->
                <div class="field">
                    <label for="tipo_cambio">TIPO DE CAMBIO</label>
                    <input type="number" required placeholder="0.00">
                </div>

                <div class="field">
                    <label for="valor_dolares">VALOR EN DOLARES</label> <!--AQUI TRIGGER PARA CALCULAR EL VALOR EN DOLARES DE PESOS A DOLARES-->
                    <input type="number" step="0.01" id="valor_dolares" name="valor_dolares" placeholder="0.00">
                </div>
                <div class="field">
                    <label>COSTO UNITARIO</label>
                    <input type="number" required placeholder="0.00">
                </div>
                <div class="field">
    
                    <label>TIPO DE IMPO</label>
                    <select name="" id="">
                        <option value="" disabled selected>Seleccione tipo de importacion...</option>
                        <option value="Materia Prima Temporal">MP temporal</option>
                        <option value="Materia Prima Definitiva">MP Definitiva</option>
                    </select>
                </div>

                <div class="field">
                    <label>PESO NETO</label>
                    <input type="number" required placeholder="0">
                </div>
                
                <div class="field description-field">
                    <label>DESCRIPCIÓN</label>
                    <textarea rows="5" required placeholder="Breve descripcion..."></textarea>
                </div>

                <div class="field">
                    <label>TOTAL</label> <!--TRIGGER PARA CALCULAR TOTAL= cantidad*costo_unitario-->
                    <input type="text" placeholder="0">
                </div>

                <div class="field">
                    <label>PESO BRUTO</label>
                    <input type="text" required placeholder="0">
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-submit">SUBMITT</button>
            </div>
        </form>
    </main>

    <!--------------------TABLA DE IMPORTACIONES-------------------------------------------->

    <section class="importacion-section">
            <h2 class="section-title">LISTADO DE IMPORTACIONES</h2>
            <div class="impo-controls">
                <input type="number" id="searchImpo" placeholder="Buscar proveedor...">
            </div>

            <table class="impo-table">
                <thead>
                    <tr>
                        <th>ID_FACTURA</th>
                        <th>FECHA</th>
                        <th>DESCRIPCIÓN</th>
                        <th>TIPO DE IMPORTACIÓN</th>
                        <th>VALOR EN DOLARES</th>
                        <th>TOTAL</th>
                        <th>ADUANERO</th> <!--USUARIO QUIEN REALIZO LA IMPO -> quien agrego datos de impo-->
                    </tr>
                </thead>
                <tbody id="importacionTableBody">
                    <tr>
                        <td colspan="8" class="no-data">No hay importaciones registradas</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary">
                <!--Importaciones realizadas-->
                <div>Total de importaciones: <span id="totalImpo">0</span></div> 
                <!--Tipo de importaciones
                MATERIA PRIMA TEMPORAL-->
                <div>Materia prima temporal: <span id="mpTemp">0</span></div> 
                <!--Tipo de importaciones 
                MATERIA PRIMA DEFINITICA-->
                <div>Materia definitiva: <span id="mpDef">0</span></div> 
            </div>
        </section>
    </div>
</div>

<script src="../JavaScript/factura.js"></script>
</html>