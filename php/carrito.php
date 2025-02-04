<?php
require "funciones.php";
require "cookies.php";
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
//? Si la cookie de carrito esta iniciada y tiene productos se extraen datos y se consulta a la base de datos
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

        $productos = explode("*", $_COOKIE["carrito"]); // Separar productos por "*"

        foreach ($productos as $producto) {
            list($ref, $cantidad) = explode(",", $producto); // Separar referencia y cantidad

            //Consultar datos del producto segun su referencia
            $consultaProducto = $bd->prepare("SELECT nombre, categoria,neto, iva, pvp, peso, descuento FROM producto WHERE ref = :ref");
            $consultaProducto->execute(['ref' => $ref]);
            $producto = $consultaProducto->fetch();

            //se guardan los datos del producto
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
    //? Si el carrito esta vacio, manda mensaje de error y muestra foto del carrito vacio (con js)
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            // Crear el contenedor del mensaje
            let mensajeCarrito = document.createElement("div");
            mensajeCarrito.textContent = "Carrito esta vacío, añade productos";

            // Crear la imagen
            let imagenCarrito = document.createElement("img");
            imagenCarrito.src = "../img/carro-vacio.png"; // Reemplaza con el enlace de tu imagen
            imagenCarrito.alt = "Carrito vacío";
            imagenCarrito.style.width = "200px"; // Ajusta el tamaño si es necesario
            imagenCarrito.style.position = "absolute";
            imagenCarrito.style.top = "calc(40% + 60px)"; // Lo coloca debajo del mensaje
            imagenCarrito.style.left = "30%";
            imagenCarrito.style.transform = "translate(-50%, 0)";
            
            // Estilos para que el cuadro flote sobre la tabla
            mensajeCarrito.style.position = "absolute"; // O "fixed" si quieres que siempre sea visible
            mensajeCarrito.style.top = "40%";
            mensajeCarrito.style.left = "30%";
            mensajeCarrito.style.transform = "translate(-50%, -50%)"; // Centrarlo
            mensajeCarrito.style.backgroundColor = "rgba(0, 0, 0, 0.8)";
            mensajeCarrito.style.color = "white";
            mensajeCarrito.style.padding = "20px";
            mensajeCarrito.style.borderRadius = "10px";
            mensajeCarrito.style.fontSize = "18px";
            mensajeCarrito.style.zIndex = "1000";
            mensajeCarrito.style.textAlign = "center";

        // Insertar en el body
        document.body.appendChild(mensajeCarrito);
        document.body.appendChild(imagenCarrito);
        });
    </script>';
}


/* ----------------------------- Peso del pedido ---------------------------- */
//? El peso del pedido se pasa a la empresa de envio que es la que gestiona posibles aumentos de coste
//? en nuestro caso solo vamos a mostrar el peso total
$sumaPesoProductos = 0.00; //tipo float tener en cuanta para la suma

foreach ($productosCarrito as $producto) {
    //? multiplicar el peso por la cantidad pedida
    $sumaPesoProductos += floatval($producto['peso']) * intval($producto['cantidad']);
}


/* --------------------------- Precios del pedido --------------------------- */
//? Se suman los precio de los productos a la variable de sumaPrecioProductos
//? se aplican los descuentos , se suma el iva y se multiplica po la cantidad antes de añadirlo

$sumaPrecioProductos = 0.00; 

foreach ($productosCarrito as $producto) {
    //? Sacamos el precio del producto
    // inicializamos descuentos aplicados y precio final
    $descuentoAplicado = false;
    $precioFinal = $producto['neto']; // Precio base

    //? aplicamos descuento segun el tipo de cliente 
    if ($producto['descuento'] === "si" && isset($_SESSION["tipo"])) {
        switch ($_SESSION["tipo"]) {
            //tipo normal no tiene ningun descuento
            case "bronce":
                $precioFinal -= ($producto['neto'] * 0.05);
                $descuentoAplicado = true;
                break;
            case "plata":
                $precioFinal -= ($producto['neto'] * 0.08);
                $descuentoAplicado = true;
                break;
            case "oro":
                $precioFinal -= ($producto['neto'] * 0.11);
                $descuentoAplicado = true;
                break;
            case "platino":
                $precioFinal -= ($producto['neto'] * 0.15);
                $descuentoAplicado = true;
                break;
        }
    }

    //? Aplicar IVA al precio final
    $precioFinalConIva = $precioFinal + ($precioFinal * $producto['iva'] / 100);

    //? Calcular subtotal(precio con iva por la cantidad pedida)
    $subtotal = $precioFinalConIva * intval($producto['cantidad']);

    //? Sumar al pecio total de todos los productosy se guarda (para mostrar abajo)
    $sumaPrecioProductos += $subtotal;
}


/* ----------------------------- Gastos de envio ---------------------------- */
//?El gasto de envio va a tener un precio fijo de 4.5 manejado por la empresa de reparto, 
//? en caso de que el pedido supere los 50, el envio será gratuito
if($sumaPrecioProductos > 50){
    $gastosEnvio = 0;
}else if($sumaPrecioProductos == 0){
    //si no hay productos añadidos
    $gastosEnvio = 0;
}else{
    $gastosEnvio = 4.5;
}

//? Precio total del pedido (sumando el precio de productos + gastos de envio)
$precioTotal = $sumaPrecioProductos + $gastosEnvio;

/* --------------- Carrito actualizaciones(eliminar producto) --------------- */
//? Si se pulsa eliminar un producto y se ha enviado la referencia de este, se va a eliminar del carrito
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_producto'])) {
    // Guardamos la referencia del producto a eliminar
    $refEliminar = $_POST['eliminar_producto'];

    // Comprobamos si existe la cookie "carrito"
    if (isset($_COOKIE["carrito"])) {
        //? Usamos la función desmontar1 para obtener los productos en un array simple
        $productos = desmontar1($_COOKIE["carrito"]);

        //? Usamos la función desmontar2 para convertirlo en una matriz con "ref" y "cantidad"
        $matrizProductos = desmontar2($productos);

        $nuevoCarrito = []; // Creamos un array para almacenar los productos que no se eliminen

        // Recorremos la matriz de productos
        foreach ($matrizProductos as $producto) {
            if ($producto["ref"] !== intval($refEliminar)) {
                // Si la referencia no coincide con la eliminada, mantenemos el producto en el nuevo carrito
                $nuevoCarrito[] = $producto;
            }
        }

        //? Actualizar la variable de sesión matriz con el nuevo carrito para que no vuelvan a aparecer los elementos eliminados
        $_SESSION['matriz'] = $nuevoCarrito;
        $_SESSION["numCarrito"] --;

        //? Llamamos a la función cookieCarrito para actualizar la cookie con los elementos nuevos de la matriz
        cookieCarrito($_SESSION["matriz"]);
        

        //? Recargamos la página para que se vean los cambios
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit; // Detenemos la ejecución del script después de redirigir
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


//~ token al realizar el pedido
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
                <?php 
                    foreach ($productosCarrito as $producto) {
                        //* Ruta de la imagen
                        $imagePath = "../categorias/" . htmlspecialchars($producto["categoria"]) . "/" . htmlspecialchars($producto["ref"]) . "/1.png";

                        // inicializamos descuentos aplicados y precio final
                        $descuentoAplicado = false;
                        $precioFinal = $producto['neto']; // Precio base

                        //? aplicamos decuento segun el tipo de cliente 
                        if ($producto['descuento'] === "si" && isset($_SESSION["tipo"])) {
                            switch ($_SESSION["tipo"]) {
                                //tipo normal no tiene ningun descuento
                                case "bronce":
                                    $precioFinal -= ($producto['neto'] * 0.05);
                                    $descuentoAplicado = true;
                                    break;
                                case "plata":
                                    $precioFinal -= ($producto['neto'] * 0.08);
                                    $descuentoAplicado = true;
                                    break;
                                case "oro":
                                    $precioFinal -= ($producto['neto'] * 0.11);
                                    $descuentoAplicado = true;
                                    break;
                                case "platino":
                                    $precioFinal -= ($producto['neto'] * 0.15);
                                    $descuentoAplicado = true;
                                    break;
                            }
                        }

                        //? Aplicar IVA al precio final para mostrarlo
                        $precioFinalConIva = $precioFinal + ($precioFinal * $producto['iva'] / 100);


                        // Mostramos en la tabla
                        echo "<tr>
                                <td><img src='{$imagePath}' width='80'></td>
                                <td>{$producto['nombre']}</td>
                                <td>";

                        //Si tiene descuento mostrar en verde, si no normal
                        if ($descuentoAplicado) {
                            echo "<div class='descuentoAplicado'>" . number_format($precioFinalConIva, 2) . " € (con IVA)</div>";
                        } else {
                            echo number_format($precioFinalConIva, 2) . " € (con IVA)</div>";
                        }

                        echo "  </td>
                                <td>{$producto['cantidad']}</td>
                                <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='eliminar_producto' value='{$producto['ref']}'>
                                        <button type='submit'>❌</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                    ?>

                </tbody>

            </table>
        </section>
        <!-- En esta sección se van a mostrar los datos de envio y botones para tramitar pedido o rellenar saldo -->
        <section  id="infoEnvio">
            <div class="dir-envio">
                <!-- Dirrección de envio -->
                <h3>Dirección de Envio</h3>
                <p><?php echo $direccionEnvio; ?></p>
            
            </div>
            <div class="dir-facturacion">
                <!-- Dirreción de facturación -->
                <h3>Dirección de Facturación</h3>
                <p><?php echo $direccionEnvio; ?></p>
            </div>
            <div class="saldo">
                <!-- Saldo y puntos del cliente -->
                <h4>Saldo <?php echo $saldo ?>
                <h4>Puntos <?php echo $puntos ?>
            </div>
            <div class="precioTotal">
                <!-- Datos del pedido(peso, gasto de envio y precio total) -->
                <h4>Peso del pedido: <?php echo number_format(floatval($sumaPesoProductos), 2, '.', '') ?> kg</h4>
                <h4>Gastos de Envio: <?php echo $gastosEnvio ?>€</h4>
                <h4>Precio Total <?php echo number_format($precioTotal, 2) ?> €</h4>
            </div>
            
            <!-- muestra botones, tramitar pedido, o recargar slado si no hay suficiente -->
            <div class="contenedorErrorSaldo">
                <?php
                    //?En caso de que tengamos un error de saldo( se da en caso de que el precio total del pedido sea mayor al saldo disponible)
                    if(isset($_SESSION["error_saldo"]) && $_SESSION["error_saldo"] = TRUE){

                        //? guarda la url actual
                        $url_actual = $_SERVER['REQUEST_URI'];

                        //* En javascript se inserta el mensaje de error
                        echo '
                        <script>
                            //~ Seleccionar elementos correctamente
                            let mensaje = "Saldo insuficiente";
                            let contenedor = document.querySelector(".contenedorErrorSaldo");

                            //~ Mostrar mensaja en el contenedor en caso de error
                            contenedor.innerHTML = mensaje;

                            //~ Crear el botón
                            let botonRecarga = document.createElement("button"); // Se usa "createElement" en lugar de "create"
                            botonRecarga.textContent = "Recargar Saldo"; // Texto del botón
                            botonRecarga.id = "botonRecargar";

                            //~ Agregar evento al botón para redirigir a otro script (ejemplo: recarga.php)
                            botonRecarga.addEventListener("click", function() {
                                //* te dirige a cargar saldo
                                window.location.href = "recargar.php?redirigido=' . $url_actual . '";
                            });

                            //~ Agregar el botón al contenedor
                            contenedor.appendChild(botonRecarga);
                        </script>';
                        
                        
                        //? Una vez se muestre el error se elimina de la variable
                        unset($_SESSION["error_saldo"]); 
                    
                    //? En caso de que si tenga saldo suficiente para pagar el pedido se muestar boton de tramitar
                    }else{
                        //? Si hay productos en el carrito va a salir el boton de tramitar
                        if (isset($_COOKIE["carrito"]) && !empty($_COOKIE["carrito"])) {
                        echo '
                            <!-- Formulario para tramitar pedido -->
                            <form id="tramitar" action="pago.php" method="post">
                                <input type="hidden" name="token" value=" $_SESSION["tokenPedido"];">
                                <button type="submit" name="tramitar" class="botonTramitar">TRAMITAR PEDIDO</button>
                            </form>

                        ';
                        }
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


