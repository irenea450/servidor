<?php
// Leer categoría seleccionada desde el método GET
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'microcontroladores';

// Definir nombres de categorías según la base de datos
$categories = [
    'microcontroladores',
    'sensores',
    'servos',
    'kits de robots',
    'libros'
];

// Verificar si la categoría es válida
if (!in_array($category, $categories)) {
    $category = 'microcontroladores'; // Por defecto, microcontroladores
}

// Conexión a la base de datos
$conexion = "mysql:dbname=irjama;host=127.0.0.1";
$usuario = "root";
$contraseña = "";

try {
    $db = new PDO($conexion, $usuario, $contraseña);

    // Buscar productos de la categoría en la base de datos
    $sql = "SELECT ref, nombre, caracteristicas FROM producto WHERE categoria = :categoria";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':categoria', $category, PDO::PARAM_STR);
    $stmt->execute();

    // Obtener productos
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo de errores
    echo "Error en la base de datos: " . $e->getMessage();
    $products = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
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
                    <li><a href="?category=microcontroladores">Microcontroladores</a></li>
                    <li><a href="?category=sensores">Sensores</a></li>
                    <li><a href="?category=servos">Servos</a></li>
                    <li><a href="?category=kits de robots">Kits de Robots</a></li>
                    <li><a href="?category=libros">Libros</a></li>
                </ul>
            </li>
            <li><a href="#">Contacto</a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>

    <main>
    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <?php
                        // Construir la ruta de la imagen
                        $imagePath = "/categorias/$category/{$product['ref']}/1.png";
                        ?>
                    <img src="<?= $imagePath ?>" alt="<?= $product['nombre'] ?>" onerror="this.src='/img/default.png';">
                    <div class="product-info">
                        <h3><?= $product['nombre'] ?></h3>
                        <p><?= $product['caracteristicas'] ?></p>
                        <button onclick="window.location.href='producto.php?categoria=<?= $category ?>&producto=<?= $product['ref'] ?>'">
                            Ver más
                        </button>
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

