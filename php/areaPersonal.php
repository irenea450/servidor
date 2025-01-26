<?php
require "funciones.php";
session_start();
    //*Variable de nombre del usuario
    $nombreUsuario = obtenerNombreUsuario($_SESSION["id"]);

    //? Si se activa el botón de actualizar datos se va a redirigir a la pagina de 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    header("Location: actualizarCuenta.php");}

    //? Si se activa el botón de recargar saldo datos se va a redirigir a la pagina de 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recargar'])) {
    header("Location: recargar.php");}

    //? Si se activa el botón de cancelar datos se va a redirigir a la pagina de 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancelar'])) {
    header("Location: cancelarCuenta.php");}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Personal Cliente</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_compra.css">

</head>
<body>
<header>
    <img src="/img/LOGO 2.png">
        <ul>
            <li><a href="../index.php">Volver al inicio</a></li>
        </ul>
</header>

    <h1>Área Personal de <?php echo $nombreUsuario; ?></h1>

    <div class="opcionesArea">
        <!-- Botón de actualizar datos -->
        <form id="areaPersonalActualizar" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Formulario con función de ir atrás -->
            <button type="submit" name="actualizar" class="botonActualizarDatos"  >
                Actualizar Datos
            </button>
        </form>
        <!-- Botón de recargar saldo -->
        <form id="areaPersonalRecargar" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Formulario con función de ir atrás -->
            <button type="submit" name="recargar" class="botonRecargarSaldo"  >
                Recargar Saldo
            </button>
        </form>
        <!-- Botón de cancelar cuenta -->
        <form id="areaPersonalCancelar" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Formulario con función de ir atrás -->
            <button type="submit" name="cancelar" class="botonCancelar"  >
                Cancelar Cuenta
            </button>
        </form>
    </div>
</body>
</html>