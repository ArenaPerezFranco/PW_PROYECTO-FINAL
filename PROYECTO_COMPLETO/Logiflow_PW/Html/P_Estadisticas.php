<?php
  session_start();

  $nombre_usuario = isset($_SESSION['username']) ? $_SESSION['username'] : 'invitado';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadisticas</title>

    <!--ESTILO CSS-->
    <link rel="stylesheet" href="../Css/estadisticas.css">

    <!--CDN CONTENIDO DE NETWORK CHART-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>

</head>
<body>
    <header>
        ESTADISTICAS 
    </header>

    <div class="Welcome-container">
    <h1>
        ¡BIENVENIDO "<?php echo htmlspecialchars($nombre_usuario); ?>"!
    </h1>
    </div>

    <div class="chart-container">
        <canvas id="chart"></canvas>
    </div>

    <div class="resumen">
        <div class="resumen-card">
            <span class="resumen-label">Importación en total:</span>
            <span class="resumen-value" id="total">0</span>
        </div>
        <div class="resumen-card export">
            <span class="resumen-label">Exportación en total:</span>
            <span class="resumen-value" id="total">0</span>
        </div>
    </div>
   
    <script src="../JavaScript/estadisticas.js"></script>

</body>
</html>