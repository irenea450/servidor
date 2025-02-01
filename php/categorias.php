<?php
/**
 *? comprueba si no hay una sesión activa y si no la hay la inicia
 *? session_status -> devuelve el estado actual de la sesión  */
if (session_status() == PHP_SESSION_NONE) {
    //? si se cumple la condición de no activa se iniciar la sesión
    session_start();
}

//?- descuentos: normal 0%, bronce 5%, plata 8%, oro 11%, platino 15%
$_SESSION["tipo"] = "bronce"; //!BORRAR
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

// Leer categoría seleccionada desde el método GET
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'microcontroladores';

// Definir nombres de categorías según la base de datos (ahora como array asociativo)
$categories = [
    'microcontroladores' => 'microcontroladores',
    'sensores' => 'sensores',
    'servos' => 'servos',
    'kits de robots' => 'kits de robots',
    'libros' => 'libros'
];

// Verificar si la categoría existe en el array asociativo, si no, usar la predeterminada
if (!array_key_exists($category, $categories)) {
    $category = 'microcontroladores'; // Por defecto, microcontroladores
}

// Conexión a la base de datos
$conexion = "mysql:dbname=irjama;host=127.0.0.1";
$usuario = "root";
$contraseña = "";

try {
    // Crear conexión PDO
    $db = new PDO($conexion, $usuario, $contraseña, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Activar excepciones en caso de error
    ]);

    // Buscar productos de la categoría en la base de datos
    $sql = "SELECT ref, nombre, neto, iva, pvp, stock, descuento FROM producto WHERE categoria = :categoria";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':categoria', $category, PDO::PARAM_STR); // Pasar como string
    $stmt->execute();

    // Obtener productos
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo de errores: mostrar mensaje y asignar un array vacío
    echo "Error en la base de datos: " . $e->getMessage();
    $products = [];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías(prueba)</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_categoria.css">
</head>
<body>
<!-- Reusado header -->
    <header>
    <img src="/img/LOGO 3.png">
        <ul>
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="categorias.php">Categorías</a>
                <ul class="categorias">
                    <!-- Enlaces dinámicos basados en las categorías -->
                    <?php foreach ($categories as $key => $name): ?>
                        <li><a href="?category=<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($name) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li><a href="/php/areaPersonal.php">Área <?php echo $nombreUsuario ?></a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>

    <main>
        <div class="tituloCat">
        <h1><?php echo strtoupper($category); ?></h1>
        </div>
        <div class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <?php
                        // Construir la ruta de la imagen del producto
                        $imagePath = "/categorias/$category/{$product['ref']}/1.png";
                        ?>
                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($product['nombre']) ?>" 
                            onerror="this.src='/img/default.png';">
                        <div class="product-info">
                            <h3><?= htmlspecialchars($product['nombre']) ?></h3>
                            <div>
                                <?php
                                    //si el producto tiene descuento
                                    if($product['descuento'] === "si"){
                                        //imprimimos el pvp en gris class(pvpAfter)
                                        echo "<p class='pvpAfter'>{$product['pvp']}€</p>";
                                        if(isset($_SESSION["tipo"])){
                                            switch($_SESSION["tipo"]){
                                                case "normal": //usuarios normales no tienen descuento
                                                    break;
                                                case "bronce":
                                                    $pvpBefore = $product['neto'] - ($product['neto'] * 0.05); //quitamos descuento 
                                                    $pvpBefore = $pvpBefore + ($pvpBefore * $product['iva'] / 100); //sumamos iva
                                                    $pvpBefore = number_format($pvpBefore, 2, '.', ''); //truncamos a dos decimales
                                                    echo "<p class='pvpBefore'>{$pvpBefore}€ -Desc</p>"; //imprimimos el pvp en verde class(pvpBefore)
                                                    break;
                                                case "plata":
                                                    $pvpBefore = $product['neto'] - ($product['neto'] * 0.08); //quitamos descuento 
                                                    $pvpBefore = $pvpBefore + ($pvpBefore * $product['iva'] / 100); //sumamos iva
                                                    echo "<p class='pvpBefore'>{$pvpBefore}€ -Desc</p>"; //imprimimos el pvp en verde class(pvpBefore)
                                                    break;
                                                case "oro":
                                                    $pvpBefore = $product['neto'] - ($product['neto'] * 0.11); //quitamos descuento 
                                                    $pvpBefore = $pvpBefore + ($pvpBefore * $product['iva'] / 100); //sumamos iva
                                                    echo "<p class='pvpBefore'>{$pvpBefore}€ -Desc</p>"; //imprimimos el pvp en verde class(pvpBefore)
                                                    break;
                                                case "platino":
                                                    $pvpBefore = $product['neto'] - ($product['neto'] * 0.15); //quitamos descuento 
                                                    $pvpBefore = $pvpBefore + ($pvpBefore * $product['iva'] / 100); //sumamos iva
                                                    echo "<p class='pvpBefore'>{$pvpBefore}€ -Desc</p>"; //imprimimos el pvp en verde class(pvpBefore)
                                                    break;
                                                default:
                                                    echo "<p class='pvpAfter'>{$product['pvp']}€</p>"; //imprimimos precio normal en caso de fallo
                                                    break;
                                            }
                                        }
                                        
                                    }else{
                                        echo "<p>{$product['pvp']}€</p>";
                                    }
                                ?>
                                <button onclick="window.location.href='producto.php?categoria=<?= htmlspecialchars($category) ?>&producto=<?= htmlspecialchars($product['ref']) ?>'">
                                    Ver más
                                </button>
                            </div>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay productos disponibles en esta categoría.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
</body>
</html>
