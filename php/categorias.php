<?php
/*require "php/funciones.php";*/
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

<!-- PHP para manejar la lógica del servidor -->
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
    $category = 1; // Por defecto, microcontroladores
}

// Consultar productos de la categoría seleccionada desde la base de datos
$conexion = "mysql:dbname=irjama;host=127.0.0.1";
$usuario = "root";
$contraseña = "";

try {
    // Conexión a la base de datos
    $db = new PDO($conexion, $usuario, $contraseña);

    // Buscar productos por categoría en la base de datos
    $sql = "SELECT ref FROM producto WHERE categoria = :categoria";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':categoria', $category, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener IDs de productos
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

    /* -------------------------- Ajuste menu variables ------------------------- */
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
    <title>Categorias</title>
    --<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_categoria.css">
    <!-- Estilos de categorias.php-->
</head>
<body>
<!--reusado header-->
    <header>
        <img id="logo" src="/img/LOGO 2.png">
        <ul>
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="categorias.php">Categorías</a>
                <ul class="categorias">
                    <li><a href="#" data-category="1">Microcontroladores</a></li>
                    <li><a href="#" data-category="2">Sensores</a></li>
                    <li><a href="#" data-category="3">Servos</a></li>
                    <li><a href="#" data-category="4">Kits de Robots</a></li>   
                    <li><a href="#" data-category="5">Libros</a></li> 
                    <!-- Aquí mostramos las categorías dinámicamente todavia no -->
                </ul>
            </li>
            <li><a href="/php/areaPersonal.php">Área <?php echo $nombreUsuario ?></a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>
    <!--Mostrar productos-->
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
    <!--//Modal de imágenes-->
    <div class="modal" id="product-modal">
        <span class="close">&times;</span>
        <img src="" alt="Producto">
    </div>

<script>
    const productGrid = document.getElementById('product-grid');

    // Configuración para cargar productos desde carpetas locales
    const categories = {
        1: 'microcontroladores',
        2: 'sensores',
        3: 'servos',
        4: 'kits de robots',
        5: 'libros'
    };

    function loadProducts(category) {
        productGrid.innerHTML = ''; // Limpiar los productos actuales

        for (let i = 1; i <= 10; i++) {
            const product = document.createElement('div');
            product.className = 'product hidden';

            const imgSrc = `../categorias/${category}/${i}.png`;

            product.innerHTML = `
                <img src="${imgSrc}" alt="${categories[category]} ${i}" onerror="this.onerror=null; this.src='placeholder.png';">
                <div class="product-info">
                    <h3>${categories[category]} ${i}</h3>
                    <p>Caracteristicas del producto ${i}.</p>
                    <button>Ver más</button>
                </div>
            `;

            productGrid.appendChild(product);
        }

        // Animación al hacer scroll
        const products = document.querySelectorAll('.product');

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                } else {
                    entry.target.classList.remove('show');
                }
            });
        });

        products.forEach(product => observer.observe(product));
    }

    // Evento para el menú de categorías
    document.querySelectorAll('.dropdown-content a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const category = link.getAttribute('data-category');
            loadProducts(category);
        });
    });

    // Modal de imágenes
    const modal = document.getElementById('product-modal');
    const modalImage = modal.querySelector('img');
    const closeModal = modal.querySelector('.close');

    productGrid.addEventListener('click', (e) => {
        if (e.target.tagName === 'IMG') {
            modalImage.src = e.target.src;
            modal.classList.add('active');
        }
    });

    closeModal.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Cargar productos de la primera categoría por defecto
    loadProducts(1);
</script>

</body>
</html>



