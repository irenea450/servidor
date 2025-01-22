<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
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
    <!--Insertar header de irene-->
    <header>
        <div class="logo" src="G:\Mi unidad\D.A.W01.02\D.A.W02\Desarrollo Web Entorno Servidor\ProyectoDwes-Irjama\servidor\img\LOGO 2.png">Irjama</div>
        <nav>
            <a href="#">Inicio</a>
            <div class="dropdown">
                <a href="#">Categorías</a>
                <div class="dropdown-content">
                    <a href="#" data-category="1">Microcontroladores</a>
                    <a href="#" data-category="2">Sensores</a>
                    <a href="#" data-category="3">Servos</a>
                    <a href="#" data-category="4">Kits de Robots</a>
                    <a href="#" data-category="5">Libros</a>
                </div>
            </div>
            <a href="#">Contacto</a>
        </nav>
    </header>

    <!--Panel visualización del producto-->
    <main>
        <div class="product-grid" id="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $index => $product): ?>
                    <div class="product hidden">
                        <img src="<?= $path . $product ?>" alt="<?= $categories[$category] . ' ' . ($index + 1) ?>" 
                            onerror="this.onerror=null; this.src='./pr';">
                        <div class="product-info">
                            <h3><?= $categories[$category] . ' ' . ($index + 1) ?></h3>
                            <p>Descripción del producto <?= ($index + 1) ?>.</p>
                            <button>Ver más</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay productos disponibles en esta categoría.</p>
            <?php endif; ?>
        </div>
    </main>

    <div class="modal" id="product-modal">
        <span class="close">&times;</span>
        <img src="" alt="Producto">
    </div>

</body>
</html>

<?php
    //Todo-->Leer categoría desde la solicitud GET
    $category = isset($_GET['category']) ? intval($_GET['category']) : 1;

    // Definir nombres de categorías
    $categories = [
        1 => 'microcontroladores',
        2 => 'sensores',
        3 => 'servos',
        4 => 'kits de robots',
        5 => 'libros'
    ];

    // Verificar si la categoría es válida
    if (!array_key_exists($category, $categories)) {
        $category = 1;
    }

    // Ruta a las imágenes de productos
    $path = "categorias/$category/";
    $products = [];

    //!Escanear carpeta de la categoría
    //**Buscar categoria por nombre 
    //**Buscar producto por id mejor..

    if (is_dir($path)) {
        foreach (scandir($path) as $file) {
            if (preg_match('/\.(jpg|png|jpeg|gif)$/i', $file)) {
                $products[] = $file;
            }
        }
    }
?>