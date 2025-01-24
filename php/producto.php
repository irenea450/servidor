//Mostrar unico producto con sus img
//Boton añadir producto--para añadir producto al carrito.._ANTES_Comprobar que ha echo login el us--redirigir a carrito.php;
//*Falta ajustar..

<section style="background-position:center; background-size:cover;">
    <?php
        //*Datos necesarios para hacer la conexion a la base de datos
        $conexion = "mysql:dbname=irjama;host=127.0.0.1";
        $usuario = "root";
        $contraseña = "";
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
    <title>Producto</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_producto.css">
    <!-- Estilos de categorias.php-->
    <style>

    </style>
</head>

<body>
    <header>
        <ul>
            <li><img src="/img/LOGO 2.png"></li>
            <li></li>
            <li><a href="../index.php">Inicio</a></li>
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
