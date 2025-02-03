<?php
require "funciones.php";
session_start();
    //? Si no estas logeuado te redirige a iniciar sesión en en login y luego te volverá a esta página
    if (!isset($_SESSION["login"]) || $_SESSION["login"] === FALSE) {
        //* guarda la url en la que se encuentraa actualmente
        $url_actual = $_SERVER['REQUEST_URI'];
        //tedirige al login
        header("Location: login.php?redirigido=$url_actual");
    }


    //? Sacamos datos del cliente para mostarlos llamando la función obtenerDatosCliente
    $datosUsuario = obtenerDatosCliente($_SESSION["id"]);

    //? las variables que van a guardar los datos extraidos en las consultas anteriores
    //? se incluyen estas variables más abajo para que sean visibles en el html
    $nombre = $datosUsuario['nombre'];
    $apellidos = $datosUsuario['apellidos'];
    $email = $datosUsuario['email'];
    $direccionEnvio = $datosUsuario['direccionEnvio'];
    $direccionFacturacion = $datosUsuario['direccionFacturacion'];
    $tlf = $datosUsuario['tlf'];
    $fechaNacimiento = $datosUsuario['fechaNacimiento'];
    $sexo = $datosUsuario['sexo'];
    $saldo = $datosUsuario['saldo'];
    $puntos = $datosUsuario['puntos'];
    $tipo = $datosUsuario['tipo'];

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
    <img src="/img/LOGO 3.png">
        <ul>
            <li><a href="../index.php">Volver al inicio</a></li>
        </ul>
</header>
<main id="mainAreaPersonal">
    <h1>Área Personal de <?php echo $nombre; ?></h1>

    <div class="mostrarInformacionUsuario">
        <!-- Información Personal -->
        <div class="infoPersonal">
            <p><b>Nombre completo:</b> <?php echo $nombre , " " , $apellidos; ?> </p>
            <p><b>Fecha Nacimiento:</b> <?php echo $fechaNacimiento ?> </p>
            <p><b>Sexo:</b> <?php echo $sexo ?> </p>
        </div>
        <!-- Infromación de contacto del usuario y direcciones de envio-->
        <div class="infoContactoUsuario">
            <p><b>Email:</b> <?php echo $email?> </p>
            <p><b>Teléfono:</b> <?php echo $tlf?> </p>
            <p><b>Dirección de Envio:</b> <?php echo $direccionEnvio?> </p>
            <p><b>Dirección de Facturación:</b> <?php echo $direccionFacturacion?> </p>
        </div>

        <!-- Datos del usuario en l apalicación, tipo de cliente, saldo y puntos -->
        <div class="infoUsuarioAplicacion">
            <p><b>Tipo de Cliente:</b> <?php echo $tipo?> </p>
            <p><b>Saldo:</b> <?php echo $saldo?> </p>
            <p><b>Puntos:</b> <?php echo $puntos?> </p>
        </div>

    </div>

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
</main> 
</body>
</html>