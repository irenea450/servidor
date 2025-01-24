//Mostrar unico producto con sus img
//Boton añadir producto--para añadir producto al carrito.._ANTES_Comprobar que ha echo login el us--redirigir a carrito.php;
//*Falta ajustar..

<section style="background-position:center; background-size:cover;">
    <?php
        //*Datos necesarios para hacer la conexion a la base de datos
        $conexion = "mysql:dbname=irjama;host:localhost";
        $usuario = "root";
        $contraseña = "root";
        try {
            //Hago la conexion a la base de datos
            $db= new PDO($conexion, $usuario, $contraseña);
            //!Sacamos la informacion de todos los productos de la categoria que se paso por el metodo get
            $sql = 'select * from producto where COD_CATEGORIA="'.$_GET['categoria'].'"';
            $info = $db->query($sql);
            echo '<div class="cartas" style="padding:150px;">';
            echo '<div class="d-flex flex-row justify-content-between align-items-center">';
            
            foreach ($info as $row){
            echo '<div class="card" style="width: 150px;">';
            //!Las imagenes tienen que tener el nombre(sustituit por id) que cada producto tiene en la base de datos y ser .png
            echo '<img src="../img/'.$row['NOMBRE'].'.png" class="card-img-top" alt="'.$row['NOMBRE'].'">';
            echo '<div class="card-body bg-light">';
            echo '<h5 class="card-title">'.$row['NOMBRE'].'</h5>';
            echo '<p class="card-text">'.$row['DESCRIPCION'].'</p>';

            // En los input usamos el min para que no pueda poner cantidades menor a 0
            // Y en el max ponemos siempre el stock
            // Pasamos a traves de input ocultos "hidden" el codigo del producto(ref) y el stock
            echo '<div class="inputs">
                    <form action="añadir.php" method="post" class="d-flex flex-row">
                        <input type="number" id="cantidad" name="cantidad" size="3px" min="0" max="'.$row['STOCK'].'">
                        <input type="submit" id="añadir" name="añadir" value="Añadir" class="btn btn-danger btn-sm">
                        <input type="hidden" name="codProd" id="codProd" value='.$row["CODIGO"].'>
                        <input type="hidden" name="stock" id="stock" value='.$row["STOCK"].'>
                    </form>
                    </div>';
            echo '</div>';
            echo '</div>';
            }

            echo '</div>';
            echo '</div>';
        } catch (PDOException $e) {
            echo "Error en la base de datos ".$e->getMessage();
        }
    ?>
</section>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
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
            <li></li>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="categorias.php">Categorias</a>
                <ul class="categorias">
                    <!-- Poner los enlaces que corresponden -->
                    <li><a href="#" data-category="1">Microcontroladores</a></li>
                    <li><a href="#" data-category="2">Sensores</a></li>
                    <li><a href="#" data-category="3">Servos</a></li>
                    <li><a href="#" data-category="4">Kits de Robots</a></li>
                    <li><a href="#" data-category="5">Libros</a></li>
                </ul>
            </li>
            <li><a href="#">Contacto</a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>
</body>
