//Mostrar unico producto con sus img
//Boton añadir producto--para añadir producto al carrito.._ANTES_Comprobar que ha echo login el us--redirigir a carrito.php;
//*Falta ajustar..


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="producto.css">-->
</head>
<style>
    /* Estilos personalizados para la página del producto */
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

/* Contenedor principal */
main {
    margin-top: 100px;
    padding: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Tarjeta del producto */
.product-card {
    background: #1e1e1e;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    max-width: 800px;
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
    color: #fff;
}

/* Imagen del producto */
.product-card img {
    flex: 1 1 300px;
    max-width: 300px;
    height: 300px;
    object-fit: cover;
    border-radius: 10px;
    background: #000;
}

/* Información del producto */
.product-info {
    flex: 2 1 400px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-info h1 {
    font-size: 2rem;
    color: #ff6700;
    margin-bottom: 10px;
}

.product-info p {
    font-size: 1rem;
    color: #bbb;
    margin-bottom: 15px;
    line-height: 1.5;
}

.product-info .product-price {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ff4500;
    margin-bottom: 20px;
}

.product-info .buttons {
    display: flex;
    gap: 10px;
}

.product-info .buttons button {
    background: #ff6700;
    border: none;
    padding: 10px 20px;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 1rem;
}

.product-info .buttons button:hover {
    background: #ff4500;
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
    .product-card {
        flex-direction: column;
        align-items: center;
    }

    .product-card img {
        width: 100%;
        max-width: none;
    }

    .product-info {
        text-align: center;
    }
}

</style>
<body>
    <header>
        <div class="logo">Irjama</div>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="categorias.php">Categorías</a>
            <a href="contacto.php">Contacto</a>
        </nav>
    </header>
    <main>
        <?php
        // Leer parámetros del producto desde la URL
        $categoria = $_GET['categoria'] ?? '';
        $productoRef = $_GET['producto'] ?? '';

        // Definir nombres de categorías
        $categories = [
            1 => 'microcontroladores',
            2 => 'sensores',
            3 => 'servos',
            4 => 'kits de robots',
            5 => 'libros'
        ];

        if (!isset($categories[$categoria])) {
            echo "<p>Categoría no encontrada.</p>";
            exit;
        }

        $categoriaNombre = $categories[$categoria];

        // Ruta de la imagen del producto
        $productPath = "categorias/$categoriaNombre/$productoRef";
        $imagePath = "$productPath/1.jpg";

        // Mostrar información del producto
        echo '<div class="product-card">';
        if (file_exists($imagePath)) {
            echo "<img src='$imagePath' alt='Producto $productoRef'>";
        } else {
            echo "<img src='img/default.jpg' alt='Imagen no disponible'>";
        }

        echo '<div class="product-info">';
        echo "<h1>Producto $productoRef</h1>";
        echo "<p>Este producto pertenece a la categoría: <strong>$categoriaNombre</strong>.</p>";
        echo "<p>Descripción detallada del producto y sus características específicas.</p>";
        echo "<p class='product-price'>Precio: $99.99</p>";
        echo '<div class="buttons">';
        echo '<button>Añadir al carrito</button>';
        echo '<button>Comprar ahora</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        ?>
    </main>
</body>
</html>



