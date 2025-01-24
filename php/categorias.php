// al pinchar ver mas o en producto..;
//Mandamos a traves del metodo get en el href que categoria y producto se ha seleccionado, redirigir a --producto.php PARA VER PRODUCTO

//**********************Solucion */
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <!-- Estilos de categorias.php-->
    <style>
        body {
            margin: 0;
            font-family: 'Orbitron', sans-serif;
            background-color: #121212;
            color: #fff;
        }

        /* Encabezado */
        header {
            background: linear-gradient(90deg, #ff6700, #ff4500);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        header .logo {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        header nav {
            display: flex;
            gap: 20px;
        }

        header nav a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
        }

        /* Categorías desplegables */
        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #1e1e1e;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #333;
        }

        .dropdown-content a:hover {
            background-color: #333;
        }

        /* Sección de productos */
        main {
            margin-top: 80px;
            padding: 30px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .product {
            background: #1e1e1e;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s;
        }

        .product:hover {
            transform: translateY(-10px);
        }

        .product img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
        }

        .product-info h3 {
            margin: 0 0 10px;
            font-size: 1.2rem;
            color: #ff6700;
        }

        .product-info p {
            margin: 0;
            font-size: 0.9rem;
            color: #bbb;
        }

        .product-info button {
            margin-top: 10px;
            background: #ff6700;
            border: none;
            padding: 10px 15px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .product-info button:hover {
            background: #ff4500;
        }

        /* Animación al hacer scroll */
        .hidden {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s, transform 0.6s;
        }

        .show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Modal de imágenes */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.5s;
        }

        .modal.active {
            visibility: visible;
            opacity: 1;
        }

        .modal img {
            max-width: 80%;
            max-height: 80%;
        }

        .modal .close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2rem;
            color: #fff;
            cursor: pointer;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            header nav {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <ul>
            <li><img src="/img/LOGO 2.png"></li>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="categorias.php">Categorías</a>
                <ul class="categorias">
                    <!-- Aquí mostramos las categorías dinámicamente -->
                    <?php
                    foreach ($categories as $id => $name) {
                        echo "<li><a href='categorias.php?category=$id'>" . ucfirst($name) . "</a></li>";
                    }
                    ?>
                </ul>
            </li>
            <li><a href="#">Contacto</a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>

    <main class="contenedor-imagenes">
        <div class="product-grid" id="product-grid">
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

<!-- PHP para manejar la lógica del servidor -->
<?php
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
        $category = 1; // Por defecto, microcontroladores
    }

    // Consultar productos de la categoría seleccionada desde la base de datos
    $conexion = "mysql:dbname=irjama;host=localhost";
    $usuario = "root";
    $contraseña = "root";

    try {
        // Conexión a la base de datos
        $db = new PDO($conexion, $usuario, $contraseña);

        // Buscar productos por categoría en la base de datos
        $sql = "SELECT id FROM productos WHERE categoria = :categoria";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':categoria', $category, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener IDs de productos
        $products = $stmt->fetchAll(PDO::FETCH_COLUMN);

    } catch (PDOException $e) {
        // Manejo de errores de conexión
        echo "Error en la base de datos: " . $e->getMessage();
    }

    // Obtener imágenes desde el sistema de archivos según la estructura
    if (!empty($products)) {
        foreach ($products as $key => $product) {
            $productPath = "categorias/" . $categories[$category] . "/$product";
            if (is_dir($productPath)) {
                $products[$key] = $product; // Guardar ID del producto
            } else {
                unset($products[$key]); // Eliminar si no tiene carpeta
            }
        }
    }
?>