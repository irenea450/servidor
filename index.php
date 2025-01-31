<?php
require "php/funciones.php";
require 'php/cookies.php';
session_start();

/** 
 * ? si el usuario esta logeado se va a mostrar la opción de ajustar su perfil en el menu
 * ? Aparecerá su nombre arriba con un enlace a su area personal */
$nombreUsuario = ""; // Por defecto, vacío
if(isset($_SESSION["nombre"])){
    $nombreUsuario = $_SESSION["nombre"];
}else{
    // Dejamos "Área Personal" o vacío
    $nombreUsuario = "Personal"; // Puedes cambiar esto a "" si prefieres que esté en blanco
}


//?- Si la cookie de sesion esta activa 
if (isset($_COOKIE['session_token'])) {
    cookieSesion1();
}

//!- OPCION ANTERIOR
/* if (isset($_COOKIE['session_token'])) {
    // Si hay cookie -> verificamos si es válida y en caso afirmativo generamos las variables de sesion
    cookieSesion1();
    // Si la cookie es valida e inicia las variables de sesion, obtenemos el nombre del usuario
    if(isset($_SESSION["id"])){
        $nombreUsuario = obtenerNombreUsuario($_SESSION["id"]);
    }
} else {
    // Si la cookie de sesión no está iniciada
    // Si la variable $_SESSION["id"] esta inicializada, obtenemos el nombre del usuario
    if(isset($_SESSION["id"])){
        $nombreUsuario = obtenerNombreUsuario($_SESSION["id"]);
    }else{
        // Eliminamos todas las variables de sesión para que no generen datos erroneos
        session_unset();  
        // Dejamos "Área Personal" o vacío
        $nombreUsuario = "Personal"; // Puedes cambiar esto a "" si prefieres que esté en blanco
    }
} */

/* ------------------------- Sacar imagen aleatoria ------------------------- */
// Definir nombres de categorías según la base de datos (ahora como array asociativo) //?- para la etiqueta li del menu
$categories = [
    'microcontroladores' => 'microcontroladores',
    'sensores' => 'sensores',
    'servos' => 'servos',
    'kits de robots' => 'kits de robots',
    'libros' => 'libros'
];

//Categorias existentes
$categorias = [
    1 => 'microcontroladores',
    2 => 'sensores',
    3 => 'servos',
    4 => 'kits_de_robots',
    5 => 'libros'
];

// Genera un número aleatorio entre 1 y 5
$numeroAleatorio = rand(1, 5); 
$categoriaAleatoria = $categorias[$numeroAleatorio];

// Numero aleatorio para el producto de esa categoria
$numeroAleatorio2 = rand(1, 8); 

//? Ruta de l aimagen aleatoria
$rutaImagen = "categorias/$categoriaAleatoria/$numeroAleatorio2/1.png";
//echo "<p>Ruta generada: $rutaImagen</p>"; // imprimir ruta para probar que ruta esta sacando

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
                    <!-- Enlaces dinámicos basados en las categorías -->
                    <?php foreach ($categories as $key => $name): ?>
                        <li><a href="/php/categorias.php?category=<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($name) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li><a href="/php/areaPersonal.php">Área <?php echo $nombreUsuario ?></a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>

    <main class="contenedor-imagenes">
        <img src="<?php echo $rutaImagen; ?>">
    </main>

    <footer>
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
    
</body>
</html>
