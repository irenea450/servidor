<?php
require "funciones.php";
require "cookies.php";

//?- Mostrar unico producto con sus img


    /**
     *? comprueba si no hay una sesión activa y si no la hay la inicia
    *? session_status -> devuelve el estado actual de la sesión  */
    if (session_status() == PHP_SESSION_NONE) {
        //? si se cumple la condición de no activa se iniciar la sesión
        session_start();
    }

    $nombreUsuario = ""; // Por defecto, vacío

    // Leer parámetros del producto desde la URL e inicializar variables
    if(isset($_GET['categoria'])){
        $categoria = $_GET['categoria'];
    }else{
        $categoria = 'Sin categoría';
    }

    if(isset($_GET['producto'])){
        $ref = $_GET['producto'];

        // Aquí debes conectar a la base de product y obtener la info del producto con la referencia
        $conexion = "mysql:dbname=irjama;host=127.0.0.1";
        $usuario_bd = "root";
        $clave_bd = "";
        $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
        $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);

        // Consulta para obtener todos los datos del producto con la ref
        $preparada = "SELECT * FROM producto WHERE ref = :ref";
        $stmt = $bd->prepare($preparada);

        // Ejecutar la consulta pasando solo el parámetro ref
        $stmt->execute(['ref' => $ref]);

        // Obtener el resultado de la consulta
        $product = $stmt->fetch();
    }else{
        $ref = 'Sin producto';
    }

    // Definir nombres de categorías para generar el submenu de categorias
    $categories = [
        'microcontroladores' => 'microcontroladores',
        'sensores' => 'sensores',
        'servos' => 'servos',
        'kits de robots' => 'kits de robots',
        'libros' => 'libros'
    ];

    // Leer categoría seleccionada desde el método GET
    $category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'microcontroladores';

    // Verificar si la categoría existe en el array asociativo, si no, usar la predeterminada
    if (!array_key_exists($category, $categories)) {
        $category = 'microcontroladores'; // Por defecto, microcontroladores
    }
    
    
    
    //? Se comprueba que se ha enviado el formulario de registro y que datos se han introducido
    if($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST['cantidad'] > 0)){
        // si no estan vacios $_POST['cantidad'] y $product['ref'] se procedera a introducir los datos en: $_COOKIE["carrito"]     
        if (!empty($_POST['cantidad']) && !empty($product['ref'])){
            // Agregar una fila con dos columnas "ref" y "cantidad"
            $_SESSION["matriz"][] = ["ref" => $product['ref'], "cantidad" => $_POST['cantidad']];
            //darle a la cookie "carrito" el valor de $_SESSION["matriz"]
            cookieCarrito($_SESSION["matriz"]);

            //* guarda la url en la que se encuentra actualmente y redirigir a redirigido.php donde nos devolvera a la pagina 
            //*y asi evitar errores del form al refrescar la pagina
            $url_actual = urlencode($_SERVER["REQUEST_URI"]);  //para que PHP interprete el & como parte del código en lugar de como un separador de parámetros en la URL
            //tedirige al redirigido 
            header("Location: ./redirigido.php?redirigido=$url_actual");
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto</title>
    <!-- <link rel="stylesheet" href="estilos_producto.css"> -->
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_categoria.css">
</head>
<body>
    <header>
    <img src="/img/LOGO 3.png">
        <ul>
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="categorias.php">Categorías</a>
                <ul class="categorias">
                    <!-- Enlaces dinámicos basados en las categorías -->
                    <?php foreach ($categories as $key => $name): ?>
                        <li><a href="./categorias.php?= htmlspecialchars($key) ?>"><?= htmlspecialchars($name) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li><a href="/php/areaPersonal.php">Área <?php echo $nombreUsuario ?></a></li>
            <li><a href="/php/login.php">Registrarse</a></li>
            <li><a href="/php/carrito.php"><img src="/img/icono_carrito.png"></a></li>
        </ul>
    </header>
    <main>
        <div class="contProd">
            <div class="imgprod">
                <?php
                    //bucle for para sacar las imagenes del producto
                    for($i = 1; $i < 5; $i++){
                        // Ruta de la imagen del producto al que habra que sumar la ultimaparte -$imagePath = "$productPath/1.png";
                        $productPath = "/categorias/$categoria/$ref";
                        // concatenamos el final de la ruta + la extension
                        $productPath = $productPath . "/" . $i . ".png";
                        //imprimimos la imagen
                        echo "<img src='{$productPath}'>";
                    }
                ?>
            </div>
            <div class="nomProd">
                <?php echo "<h2>{$product['nombre']}</h2>"; ?>
            </div>
            <div class="datosProd">
                <div class="datosProd1">
                    <h3>Descripcion:</h3>
                    <?php echo "<p>{$product['descripcion']}</p>"; ?>
                </div>
                <div class="datosProd1">
                    <h3>Caracteristicas:</h3>
                    <?php
                        //troceo transformando el string de caracteristicas en un array
                        $array = desmontar1($product['caracteristicas']);
                        //muestro una por una las categorias
                        foreach($array as $posicionArray){
                            echo "<p>- {$posicionArray}</p>";
                            echo "<br>";
                        }
                    ?>
                </div>
                <div class="datosProd1">
                    <h3>Comprame:</h3>
                    <?php
                        //si el producto tiene descuento y $_SESSION["tipo"] esta inicializado
                        if($product['descuento'] === "si"){
                            if(isset($_SESSION["tipo"])){
                                //imprimimos el pvp en gris class(pvpAfter)
                                echo "<p class='pvpAfter'>{$product['pvp']}€</p>";
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
                            }else{ // si $_SESSION["tipo"] no esta inicializado imprimimos el pvp sin descuento
                                echo "<p>{$product['pvp']}€</p>";
                            }
                            
                        }else{ // si no tiene descuento imprimimos el pvp
                            echo "<p>{$product['pvp']}€</p>";
                        }
                    ?>
                    <!-- formulario para elegir la cantidad y comprar -->
                    <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]. '?categoria='.$categoria.'&producto='.$ref); ?>" method="POST">
                        <label for="cantidad">Cantidad:</label>
                        <?php
                            // si tiene stock de sobra se podran elegir hasta 10 unidades
                            if($product['stock'] >= 10){
                                echo "<select id='cantidad' name='cantidad' required>";
                                // Opcion "0" preseleccionada
                                echo "<option selected='selected' value=0>0</option>"; 
                                for ($i = 1; $i <= 10; $i++) {
                                    echo "<option value=$i>$i</option>";
                                }
                            echo "</select>";
                            // si tiene menos de 10 de stock se podra elegir como maximo el stock actual 
                            }else{
                                echo "<select id='cantidad' name='cantidad' required>";
                                // Opcion "0" preseleccionada
                                echo "<option selected='selected' value=0>0</option>";
                                for ($i = 1; $i <= $product['stock']; $i++) {
                                    echo "<option value=$i>$i</option>";
                                }
                            echo "</select>";
                            }
                        
                            //?- si el usuario no esta logueado se le reenvia a login.php
                            if(!isset($_SESSION['id']) || !isset($_SESSION['login'])){
                                //* guarda la url actual
                                $url_actual = $_SERVER['REQUEST_URI'];

                                //* En javascript generamos el boton
                                echo '<input id="botonLogueate" type="button" value="Logueate" onclick="window.location.href=\'login.php?redirigido='.$url_actual = urlencode($_SERVER["REQUEST_URI"]).'\'">';
                                
                            }else{
                                echo '<input class="comprar" type="submit" id="enviar" value="COMPRAR">';
                            }
                        ?>
                    </form>
                </div>
            </div>
            
        </div>
    </main>

    <footer>
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
</body>
</html>



