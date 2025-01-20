<?php
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
        header("Location:index.php");
}

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

    //Finalmente el script lleva de al login
    header("Location: index.php");
}

/* ---------------- Función para saber el nombre del usuario ---------------- */
//? Función para obtener el nombre del usuario con base en el ID almacenado en la sesión
function obtenerNombreUsuario($id) {
    // Aquí debes conectar a tu base de datos y obtener el nombre del usuario con el ID
    $conexion = "mysql:dbname=irjama;host=127.0.0.1";
    $usuario_bd = "root";
    $clave_bd = "";
    $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
    $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);

    
    // Consulta para obtener el nombre del usuario con el ID
    $consulta = "SELECT nombre FROM cliente WHERE id = :id";
    $stmt = $bd->prepare($consulta);

    // Ejecutar la consulta pasando solo el parámetro id
    $stmt->execute(['id' => $id]);

    // Obtener el resultado de la consulta
    $resultado = $stmt->fetch();

    // Devolver el nombre del usuario si se encuentra, o "Usuario desconocido" si no
    return $resultado ? $resultado['nombre'] : "Usuario desconocido";

}
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
        <form id="logoutForm" action="logout.php" method="post">
            <button type="submit" name="out" id="enviar" >Log Out</button>
        </form>
    </div>

    <div class="volverInicio">
        <form id="atrasForm" action="logout.php" method="post">
            <!-- Formulario con función de ir atrás -->
            <button type="submit" name="atras" class="flechaVolver"  >
                <img src="/img/flecha_atras.png">
            </button>
        </form>
    </div>
</body>
</html>

