<?php
require "php/funciones.php";
require 'php/cookies.php';
session_start();

//?- Si la cookie de sesion esta activa 
if (isset($_COOKIE['session_token'])) {
    cookieSesion1();
}

//si la cookie carrito esta activa la vuelca en session[matriz] y genera session[numcarrito]
if(!isset($_SESSION["matriz"])){
    //si exsite la cookie carrito y el usuario esta logueado
    if (isset($_COOKIE['carrito']) && isset($_SESSION['id'])){
        $cookieCarrito = $_COOKIE['carrito'];
        $arrayCookie = desmontar1($cookieCarrito);//pasamos de string a array
        $_SESSION["numCarrito"] = count($arrayCookie);//sacamos las posiciones del array para poner la cantidad de productos en el carrito
        $_SESSION["matriz"] = desmontar2($arrayCookie);//pasamos de array a matriz e inicializamos la variable de sesion
    }
}

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

/* ------------------------- Sacar imagen aleatoria ------------------------- */
// Definir nombres de categorías según la base de datos (ahora como array asociativo) //?- para la etiqueta li del menu
$categories = [
    'microcontroladores' => 'microcontroladores',
    'sensores' => 'sensores',
    'servos' => 'servos',
    'kits de robots' => 'kits de robots',
    'libros' => 'libros'
];

// conectamos a la base de datos y obtenemos la info del producto aleatorio
$conexion = "mysql:dbname=irjama;host=127.0.0.1";
$usuario_bd = "root";
$clave_bd = "";
$errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
$bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);

// Consulta para obtener un producto aleatorio
$sql = "SELECT * FROM producto ORDER BY RAND() LIMIT 1";
$stmt = $bd->query($sql);

// Obtener el producto
$producto = $stmt->fetch();
$rutaImagen = "categorias/".$producto['categoria']."/".$producto['ref']."/1.png";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal</title>
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
            <li class="carrito"><?php 
                    if (isset($_SESSION['numCarrito'])){
                        echo "<div class='num'><p>{$_SESSION['numCarrito']}</p></div>";
                    } ?>
                <a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a>
            </li>
        </ul>
    </header>

    <main class="contenedor-imagenes">
        <div class="contenedor-img">
            <img src="<?php echo $rutaImagen; ?>">
        </div>
        
        <div class="contenedor-info">
                    
                        <?php
                            echo "<h1>{$producto['nombre']}</h1>";
                            echo "<p>{$producto['descripcion']}</p>";
                        ?>
                        <button class="botonVerMas" onclick="window.location.href='./php/producto.php?categoria=<?= htmlspecialchars($producto['categoria']) ?>&producto=<?= htmlspecialchars($producto['ref']) ?>'">
                                    Ver más
                        </button>
                    
                    
        </div>
    </main>

    <footer class="footerIndex">
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
    
</body>
</html>
