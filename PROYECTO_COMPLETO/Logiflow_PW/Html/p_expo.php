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
    $dbname = "BD_Aduanas";
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

<!DOCTYPE html>
<html lang="es">
<div class="main-container">
    <header class="logiflow-header">
        
        <meta charset="UTF-8">
        <span class="header-status">EXPORTACIÓN</span> 
        <link rel="stylesheet" href="../Css/form_ex-im.css">
    </header>

    <main class="content">
        <h1 class="page-title">EXPORTACIÓN</h1> 

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
                        <option value="Ex_temporal">Exportacion Temporal</option>
                        <option value="Ex_definitiva">Exportacion Definitiva</option>
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
                    <label>TOTAL</label>
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

    <section class="expo-section">
            <h2 class="section-title">LISTADO DE EXPORTACIONES</h2>
            <div class="controls">
                <input type="number" id="searchExpo" placeholder="Buscar exportación...">
            </div>

            <table class="table">
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
                <tbody id="TableBody">
                    <tr>
                        <td colspan="8" class="no-data">No hay exportaciones registradas</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary">
                <!--Importaciones realizadas-->
                <div>Total de exportaciones: <span id="totalExpo">0</span></div> 
                <!--Tipo de importaciones
                MATERIA PRIMA TEMPORAL-->
                <div>Exportacion temporal: <span id="Ex-tem">0</span></div> 
                <!--Tipo de importaciones 
                MATERIA PRIMA DEFINITICA-->
                <div>Exportacion definitiva: <span id="Ex-def">0</span></div> 
            </div>
        </section>
    </div>
</div>

<script src="../JavaScript/factura.js"></script>
</html>