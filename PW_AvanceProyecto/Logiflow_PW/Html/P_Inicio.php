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

//Comprobar que el usuario envio el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //Recibir y limpiar los datos del formulario
    $user_input = trim($_POST['username']);
    $pass_input = trim($_POST['password']);


try{
    //CONSULTA SQL 
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username = :user AND password = :pass");

    //Ejecuta -> se pasan los datos reales
    $stmt->execute([
        ':user' => $user_input,
        ':pass' => $pass_input
    ]);

    //Guardamos el resultado
    $usuario_encontrado = $stmt->fetch(PDO::FETCH_ASSOC);

    if($usuario_encontrado){
        echo "<script>alert('¡Bienvenido al sistema, " . $usuario_encontrado['username'] . "!');</script>";
    } else{
        echo "<script>alert('Usuario o contraseña incorrecta');</script>";
    }
}catch (PDOException $e){
    //ERROR-> Se detiene pagina y se muestra mensaje
    die("¡Error de conexión!:" . $e->getMessage());
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logiflow</title>

    <!--ESTILO CSS-->
    <link rel="stylesheet" href="../Css/iniciosesion.css">

</head>

<body>
    <div class="contenedor">
        <form action="" method="POST">
            <h1>LogiFlow</h1>
            <div class="box">
                <input type="text" name="username" placeholder="Username" required>
                <i class="bx_user"></i>
            </div>

            <div class="box">
                <input type="password" name="password" placeholder="password" required>
                <i class="bx_pass"></i>
            </div>

            <div class="recuerdame">
                <label>
                    <input type="checkbox">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn_log">ACCEDER</button>
        </form>
    </div>

    <script src="../JavaScript/inicio.js"></script>
</body>
</html>