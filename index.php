<?php
require "php/funciones.php";
session_start();

/** 
 * ? si el usuario esta logeado se va a mostrar la opción de ajustar su perfil en el menu
 * ? Aparecerá su nombre arriba con un enlace a su area personal */
$nombreUsuario = ""; // Por defecto, vacío

if (isset($_SESSION["id"])) {
    // Si la sesión está iniciada, obtenemos el nombre del usuario
    $nombreUsuario = obtenerNombreUsuario($_SESSION["id"]);
} else {
    // Si la sesión no está iniciada, dejamos "Área Personal" o vacío
    $nombreUsuario = "Personal"; // Puedes cambiar esto a "" si prefieres que esté en blanco
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
</head>
<body>
    <header>
        <img src="/img/LOGO 2.png">
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="/php/categorias.php">Categorias</a>
                <ul class="categorias">
                    <!-- Poner los enlaces que corresponden -->
                    <li><a href="#">Microcontroladores</a></li>
                    <li><a href="#">Sensores</a></li>
                    <li><a href="#">Servos</a></li>
                    <li><a href="#">Kits de Robots</a></li>
                    <li><a href="#">Libros</a></li>
                </ul>
            </li>
            <li><a href="/php/areaPersonal.php">Área <?php echo $nombreUsuario ?></a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>

    <main class="contenedor-imagenes">
        <img src="/categorias/<?php $categoria?>/<?php $idProductoImagen?>/1">
    </main>

    <footer>
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
    
</body>
</html>
