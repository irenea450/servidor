
<?php
    /*PHP para manejar la lógica del servidor */

    // Leer categoría seleccionada desde el método GET
    $category = isset($_GET['category']) ? intval($_GET['category']) : 1;

    // Definir nombres de categorías según la base de datos
    $categories = [
        1 => 'microcontroladores',
        2 => 'sensores',
        3 => 'servos',
        4 => 'kits de robots',
        5 => 'libros'
    ];

    // Verificar si la categoría es válida
    if (!array_key_exists($category, $categories)) {
        $category = 1; //*Por defecto, microcontroladores
    }

    //*Consultar productos de la categoría seleccionada desde la base de datos
    $conexion = "mysql:dbname=irjama;host=127.0.0.1";
    $usuario = "root";
    $contraseña = "";

    try {
        // Conexión a la base de datos
        $db = new PDO($conexion, $usuario, $contraseña);

        //!Buscar productos por categoría en la base de datos
        $sql = "SELECT ref FROM producto WHERE categoria = :categoria";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':categoria', $category, PDO::PARAM_INT);
        $stmt->execute();

        //! REVISAR PRODUCTO , no coge la referencia
        // Obtener ref s de productos
        $products = $stmt->fetchAll(PDO::FETCH_COLUMN);

    } catch (PDOException $e) {
        // Manejo de errores de conexión
        echo "Error en la base de datos: " . $e->getMessage();
    }

    //!Obtener imágenes desde el sistema de archivos según la estructura
    if (!empty($products)) {
        foreach ($products as $key => $product) {
            $productPath = "categorias/" . $categories[$category] . "/$product";
            if (is_dir($productPath)) {
                $products[$key] = $product; // Guardar ref del producto
            } else {
                unset($products[$key]); // Eliminar si no tiene carpeta
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
    --<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_categoria.css">
    <!-- Estilos de categorias.php-->
</head>
<body>
<!--reusado header-->
    <header>
        <ul>
            <li><img src="/img/LOGO 2.png"></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="categorias.php">Categorías</a>
                <ul class="categorias">
                    <li><a href="?category=1">Microcontroladores</a></li>
                    <li><a href="?category=2">Sensores</a></li>
                    <li><a href="?category=3">Servos</a></li>
                    <li><a href="?category=4">Kits de Robots</a></li>   
                    <li><a href="?category=5">Libros</a></li> 
                    <!-- Aquí mostramos las categorías dinámicamente todavia no -->
                </ul>
            </li>
            <li><a href="#">Contacto</a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>

    <main class="contenedor-imagenes">
        <div class="product-grid" id="product-grid">
            <!-- MAL A PARTIR DE AQUI -->
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <div class="product hidden">
                    <!-- Mostrar imagen del producto -->
                    <img src="<?= "categorias/" . $categories[$category] . "/$product/1.png" ?>" 
                         alt="<?= $categories[$category] ?>" 
                         onerror="this.onerror=null; this.src='/img/default.png';">
                    <!-- Mostrar descripción del producto -->
                    <div class="product-info">
                        <h3><?= ucfirst($categories[$category]) ?> - Producto <?= $product ?></h3>
                        <p>Producto ID: <?= $product ?></p>
                        <!-- Botón que redirige a producto.php con la categoría y producto seleccionados -->
                        <button onclick="window.location.href='producto.php?categoria=<?= $category ?>&producto=<?= $product ?>'">Ver más</button>
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

