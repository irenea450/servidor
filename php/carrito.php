<?php
require "funciones.php";
//*Inicia sesión
session_start();

//? Si la sesión no está iniciada, se redirige al login pasando la URL actual
if (!isset($_SESSION["login"]) || $_SESSION["login"] === FALSE) {
    //* guarda la url en la que se encuentraa actualmente
    $url_actual = $_SERVER['REQUEST_URI'];
    //te dirige al login
    header("Location: login.php?redirigido=$url_actual");
}

/**
 * ?Voy a extraer los datos del usuario que está logueado para mostrar los datos de la compra
 * ? uso variables para luego mostralo en el html */

//? se llama a la función que saca los datos del usaurio
$datosUsuario = obtenerDatosCliente($_SESSION["id"]);

//? las variables que van a guardar los datos extraidos en las consultas anteriores
//? se incluyen estas variables más abajo para que sean visibles en el html
$nombreUsuario = $datosUsuario['nombre'];
$apellidoUsuario = $datosUsuario['apellidos'];
$emailUsuario = $datosUsuario['email'];
$nombreCompleto = $nombreUsuario . " " . $apellidoUsuario;
$direccionEnvio = $datosUsuario['direccionEnvio'];
$direccionFacturacion = $datosUsuario['direccionFacturacion'];
$saldo = $datosUsuario['saldo'];
$puntos = $datosUsuario['puntos'];

//? extraemos datos de la cookie carrito
$productosCarrito = [];

if (isset($_COOKIE["carrito"]) && !empty($_COOKIE["carrito"])) {
    // Conexion a la base de datos
    $conexion = "mysql:dbname=irjama;host=127.0.0.1";
    $usuario_bd = "root";
    $clave_bd = "";

    try {
        $bd = new PDO($conexion, $usuario_bd, $clave_bd, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $items = explode("*", $_COOKIE["carrito"]); // Separar productos por "*"

        foreach ($items as $item) {
            list($ref, $cantidad) = explode(",", $item); // Separar referencia y cantidad

            // Consultar los datos del producto
            $stmt = $bd->prepare("SELECT nombre, categoria,neto, iva, pvp, peso, descuento FROM producto WHERE ref = :ref");
            $stmt->execute(['ref' => $ref]);
            $producto = $stmt->fetch();

            if ($producto) {
                $productosCarrito[] = [
                    "ref" => $ref,
                    "nombre" => $producto["nombre"],
                    "categoria" => $producto["categoria"],
                    "neto" => $producto["neto"],
                    "iva" => $producto["iva"],
                    "pvp" => $producto["pvp"], 
                    "peso" => $producto["peso"], 
                    "descuento" => $producto["descuento"], 
                    "cantidad" => $cantidad
                ];
            }
        }
    } catch (PDOException $e) {
        echo "Error en la conexión: " . $e->getMessage();
    }
} else {
    echo 'Carrito Vacio - añadir a la cesta ';
    //! añadir mensaje de error de carrito vacio
/*     echo '<script>
    alert("carrito vacio");
    //coger contenedor #productos-carrito y meter este parrafo dentro
    contenedor =document.querySelector("#productos-carrito");
    contenedor = " ";
    contenedor.innerHTML = "<p class="carritoVacio">No hay productos en el carrito.</p>";

    </script>'; */
}



/* ----------------------------- peso del pedido ---------------------------- */
//? El peso del pedido se pasa a la empresa de envio que es la que gestiona posibles aumentos de coste
//? en nuestro caso solo vamos a mostrar el peso total
$sumaPesoProductos = 0.00; //tipo float tener en cuanta para la suma

foreach ($productosCarrito as $producto) {
    //? multiplicar el peso por la cantidad pedida
    $sumaPesoProductos += floatval($producto['peso']) * intval($producto['cantidad']);
}


/* --------------------------- precios del pedido --------------------------- */
// Poner la suma de precios de los productos del carrito
// hacer consulta a los datos de ese producto, si tiene descuento descontar y sumar todo a esta variable

$sumaPrecioProductos = 0.00; 

foreach ($productosCarrito as $producto) {
    //? Sacamos el precio del producto
    //precio sin descuento
    $precioFinal = floatval($producto['pvp']);
    $descuentoAplicado = FALSE;


    // Precio con descuento ya aplicado
    if ($producto['descuento'] === "si" && isset($_SESSION["tipo"])) {
        switch ($_SESSION["tipo"]) {
            case "normal": // usuarios normales no tienen descuento
                break;
            case "bronce":
                $precioFinal = $producto['neto'] - ($producto['neto'] * 0.05);
                $descuentoAplicado = true;
                break;
            case "plata":
                $precioFinal = $producto['neto'] - ($producto['neto'] * 0.08);
                $descuentoAplicado = true;
                break;
            case "oro":
                $precioFinal = $producto['neto'] - ($producto['neto'] * 0.11);
                $descuentoAplicado = true;
                break;
            case "platino":
                $precioFinal = $producto['neto'] - ($producto['neto'] * 0.15);
                $descuentoAplicado = true;
                break;
        }
    }

// Aplicar IVA al precio final
$precioFinalConIva = $precioFinal + ($precioFinal * $producto['iva'] / 100);

// Multiplicar por la cantidad
$subtotal = $precioFinalConIva * intval($producto['cantidad']);

// Sumar al total
$sumaPrecioProductos += $subtotal;
}



//El gasto de envio va a tener un precio fijo de 4.5 manejado por la empresa de reparto, 
//en caso de que el pedido supere los 50, el envio será gratuito
if($sumaPrecioProductos > 50){
    $gastosEnvio = 0;
}else{
    $gastosEnvio = 4.5;
}

//? Precio total del pedido
$precioTotal = $sumaPrecioProductos + $gastosEnvio;

/* --------------- Carrito actualizaciones(eliminar producto) --------------- */
//! TERMINAR ELIMINAR CARRITO
//? ESTO ES COPIADO QUEDA MODIFICAR 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_producto'])) {
    $refEliminar = $_POST['eliminar_producto'];
    
    // Obtener la cookie del carrito
    if (isset($_COOKIE["carrito"])) {
        $items = explode("*", $_COOKIE["carrito"]);
        $nuevoCarrito = [];

        foreach ($items as $item) {
            list($ref, $cantidad) = explode(",", $item);
            if ($ref !== $refEliminar) {
                $nuevoCarrito[] = $item; // Guardamos los que no se eliminan
            }
        }

        // Actualizar la cookie del carrito sin el producto eliminado
        setcookie("carrito", implode("*", $nuevoCarrito), time() + 3600, "/");

        // Recargar la página para reflejar los cambios
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    }
}


/* ---------------------------- Proceder al pago ---------------------------- */
//? Si se pulsa el botón de tramitar, se va a didigir a la pagina de pago.php 
//? siempre y cuando el saldo sea mayor al precio total del pedido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tramitar']) && $saldo > $precioTotal) {
    //* si se ha enviado el formulario de tramitar y el saldo es mayor al precio total, el usuario se va a dirigiar pago
    header("Location: pago.php");
}if($saldo < $precioTotal){
    //* Inicializa variable de error de saldo cuando el precio total del pedido sea mayor al saldo del usuario logueado
    $_SESSION["error_saldo"] = TRUE;
}


//? Guardamos variables necesarias para el pago en variables de sesión
$_SESSION['emailUsuario'] = $emailUsuario;
$_SESSION['nombreCompleto'] = $nombreCompleto;
$_SESSION['direccionEnvio'] = $direccionEnvio;
$_SESSION['sumaPrecioProductos'] = $sumaPrecioProductos;
$_SESSION['pesoEnvio'] = $sumaPesoProductos;
$_SESSION['gastosEnvio'] = $gastosEnvio;
$_SESSION['precioTotal'] = $precioTotal;
$_SESSION['saldoUsuario'] = $saldo;
$_SESSION['sumaPrecioProductos'] = $sumaPrecioProductos; //para sumar los puntos al cliente (sin los gatos adicionales)


//~ token al relaizar el pedido
if (!isset($_SESSION['tokenPedido'])) {
    $_SESSION['tokenPedido'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_compra.css">
</head>
<body>
    <header>
        <img src="/img/LOGO 3.png">
        <ul>
            <li><a href="../index.php">Volver al inicio</a></li>
        </ul>
    </header>
    <h1>Hola <?php echo $nombreUsuario; ?>, este es tu carrito de la compra</h1>
    <main id="carrito">
        <!-- Va a tener la sección donde se muestren los productos que se ha guardado en el carrito y las dirreciones
        de envio y facturación -->

        <section  id="productos-carrito">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th><!-- vacio este hueco --></th>
                        <th>Precio</th>
                        <th>Catidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productosCarrito as $producto): ?>
                        <tr>
                            <?php
                                // Construcción de la ruta de imagen
                                $imagePath = "../categorias/" . htmlspecialchars($producto["categoria"]) . "/" . htmlspecialchars($producto["ref"]) . "/1.png";
                            ?>
                                
                            <td><img src="<?= $imagePath ?>" width="80" ></td>
                            <td> <?= $producto['nombre'] ?></td>

                            <td> <?=
                                //? Sacamos el precio del producto
                                //precio sin descuento
                                $descuentoAplicado = FALSE;

                                // Precio con descuento ya aplicado
                                if ($producto['descuento'] === "si" && isset($_SESSION["tipo"])) {
                                    switch ($_SESSION["tipo"]) {
                                        case "normal": // usuarios normales no tienen descuento
                                            break;
                                        case "bronce":
                                            $precioFinal = $producto['neto'] - ($producto['neto'] * 0.05);
                                            $descuentoAplicado = true;
                                            break;
                                        case "plata":
                                            $precioFinal = $producto['neto'] - ($producto['neto'] * 0.08);
                                            $descuentoAplicado = true;
                                            break;
                                        case "oro":
                                            $precioFinal = $producto['neto'] - ($producto['neto'] * 0.11);
                                            $descuentoAplicado = true;
                                            break;
                                        case "platino":
                                            $precioFinal = $producto['neto'] - ($producto['neto'] * 0.15);
                                            $descuentoAplicado = true;
                                            break;
                                    }
                                }

                                // Aplicar IVA al precio final
                                $precioFinalConIva = $precioFinal + ($precioFinal * $producto['iva'] / 100);

                                // Multiplicar por la cantidad
                                $subtotal = $precioFinalConIva * intval($producto['cantidad']);

                                // Sumar al total
                                $sumaPrecioProductos += $subtotal;

                                // Mostrar precio con IVA
                                if($descuentoAplicado){
                                    echo '<div class="descuentoAplicado">' . number_format($precioFinalConIva, 2) . ' € (con IVA)</div>';
                                }else{
                                    echo  number_format($precioFinalConIva, 2) . ' € (con IVA)</div>';

                                }
                                ?>
                                </td>
                            
                            <td> <?= $producto['cantidad'] ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="eliminar_producto" value="<?= $producto['ref'] ?>">
                                    <button type="submit">❌</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </section>
        <!-- En esta sección se van a mostrar los datos de envio -->
        <section  id="infoEnvio">
            <div class="dir-envio">
                <!-- Dirrección de envio -->
                <h3>Dirección de Envio</h3>
                <p><?php echo $direccionEnvio; ?></p>
            
            </div>
            <div class="dir-facturacion">
                <!-- Dirreciión de facturación -->
                <h3>Dirección de Facturación</h3>
                <p><?php echo $direccionEnvio; ?></p>
            </div>
            <div class="saldo">
                <h4>Saldo <?php echo $saldo ?>
                <h4>Puntos <?php echo $puntos ?>
            </div>
            <div class="precioTotal">
            <h4>Peso del pedido: <?php echo number_format(floatval($sumaPesoProductos), 2, '.', '') ?> kg</h4>
            <h4>Gastos de Envio: <?php echo $gastosEnvio ?>€</h4>
                <h4>Precio Total <?php echo $precioTotal ?><!-- Poner precio total aquí --> €</h4>
            </div>
            
            <!-- aqui mostrara los errores al tramitar el pedido -->
            <div class="contenedorErrorSaldo">
                <?php
                    //?En caso de que tengamos un error de saldo( se da en caso de que el precio total del pedido sea mayor al saldo disponible)
                    if(isset($_SESSION["error_saldo"]) && $_SESSION["error_saldo"] = TRUE){

                        //* guarda la url actual
                        $url_actual = $_SERVER['REQUEST_URI'];

                        //* En javascript se inserta el mensaje de error
                        echo '
                        <script>
                            // Seleccionar elementos correctamente
                            let mensaje = "Saldo insuficiente";
                            let contenedor = document.querySelector(".contenedorErrorSaldo");

                            // Mostrar mensaja en el contenedor en caso de error
                            contenedor.innerHTML = mensaje;

                            // Crear el botón
                            let botonRecarga = document.createElement("button"); // Se usa "createElement" en lugar de "create"
                            botonRecarga.textContent = "Recargar Saldo"; // Texto del botón
                            botonRecarga.id = "botonRecargar";

                            // Agregar evento al botón para redirigir a otro script (ejemplo: recarga.php)
                            botonRecarga.addEventListener("click", function() {
                                //* te dirige a cargar saldo
                                window.location.href = "recargar.php?redirigido=' . $url_actual . '"; // Cambia "recarga.php" por la URL del script al que quieres ir
                            });

                            // Agregar el botón al contenedor
                            contenedor.appendChild(botonRecarga);
                        </script>';
                        
                        
                        /* //* te dirige a cargar saldo
                        header("Location: saldo.php?redirigido=$url_actual"); */
                        
                        //? Una vez se muestre el error se elimina
                        unset($_SESSION["error_saldo"]); 
                    }else{
                        
                        echo '
                            <!-- Formulario para tramitar pedido -->
                            <form id="tramitar" action="pago.php" method="post">
                                <input type="hidden" name="token" value=" $_SESSION["tokenPedido"];">
                                <button type="submit" name="tramitar" class="botonTramitar">TRAMITAR PEDIDO</button>
                            </form>

                        ';
                    }
                ?>
            </div>
            

        </section>
    </main>

    <footer>
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
</body>
</html>


