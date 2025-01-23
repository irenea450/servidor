<?php
require "funciones.php";
//* Inicio de sesión 
session_start();

//? Si la sesión esta iniciada extraer el id del usuario logueado
if (isset($_SESSION["id"])) {
    //? Obtenemos el nombre del usuario por el id guardad en la sesión
    $usuario = obtenerNombreUsuario($_SESSION["id"]);
} else {
    //? Si no hay sesión activa redirigimos al login
    header("Location:login.php");
}

//? Varible que guarda el nombre de usuario , usarlo más adelnate parav infromar que ese usuario ya esta logueado
$_SESSION["usuario"] = $usuario;

//~ Si se activa el botón de volver atras redirige a la pagina principal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atras'])) {
    header("Location: ../index.php");}

//~ Si se activa el boton log out(name="out") se cierra sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['out'])) {

    //! Destruyo cookies de sesión y carrito de compra
    //? Destruye cookie de sesión
    setcookie("session_token", 123, time() - 1000);
    //? Destruye cookie de carrito de compra
    setcookie("carrito", 123, time() - 1000);

    //Para cerrar la sesión es necesario borrar todas las variables de la sesión, para ello se inicializa el array $_SESSION:
    $_SESSION = array();

    //Además, se debe utilizar la función session_destroy():
    session_destroy();

    //Por último, se debe de eliminar la cookie:
    setcookie(session_name(), 123, time() - 1000); // session_name devuelve el nombre de la sesión actual.

    //Finalmente el script lleva de al inicio
    header("Location: ../index.php");}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Out</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_login.css">
</head>
<body>
    <div class="contenedor">
        <img id="icono-login" src="/img/login_icono.png">
        <!-- Mnesaje de que el usuario que se indica ya está logueado -->
        <h1>Tu sesión ya está iniciada, <?php echo htmlspecialchars($usuario); ?></h1>
        <!-- Formulario con boton de hacer logout -->
        <form id="logoutForm" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="post">
            <button type="submit" name="out" id="enviar" >Log Out</button>
        </form>
    </div>

    <div class="volverInicio">
        <form id="atrasForm" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Formulario con función de ir atrás -->
            <button type="submit" name="atras" class="flechaVolver"  >
                <img src="/img/flecha_atras.png">
            </button>
        </form>
    </div>
</body>
</html>

