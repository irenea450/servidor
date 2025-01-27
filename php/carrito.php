<?php
require "funciones.php";
//*Inicia sesión
session_start();

//? Si no esta inicaida la variable de sesión logeado, será necesario loguearse
if (!isset($_SESSION["logueado"]) || $_SESSION["logueado"] === FALSE) {
    //* Usando GET se va a redirigir al login
    header("Location: login.php?redirigido=carrito.php");
}

/**
 * ?Voy a extraer los datos del usuario que está logueado para mostrar los datos de la compra
 * ? uso variables para luego mostralo en el html */
//*Variable de nombre del usuario
$nombreUsuario = obtenerNombreUsuario($_SESSION["id"]);

//? Para sacar los datos de facturación y envio se llama a la función pasando el id del usuario
$datosUsuario = obtenerDirecciones($_SESSION["id"]);
//? Para sacar los datos de saldo se llama a la función obtener saldo
$datosSaldo = obtenerSaldo($_SESSION["id"]);

//? las variables que van a guardar los datos extraidos en las consultas anteriores
//? se incluyen estas variables más abajo para que sean visibles en el html
$direccionEnvio = $datosUsuario['direccionEnvio'];
$direccionFacturacion = $datosUsuario['direccionFacturacion'];
$saldo = $datosSaldo['saldo'];
$puntos = $datosSaldo['puntos'];

$precioTotal = 100;

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
        <img src="/img/LOGO 2.png">
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
                    <tr>
                        <td><img src="/categorias/1.Microcontroladores/1/1.png" id="imagenProductoCarrito"></td>
                        <td>Arduino Microcontrolador USB Uno R3</td>
                        <td>23 €</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td><img src=""></td>
                        <td>Arduino Microcontrolador USB Uno R3 </td>
                        <td>153 €</td>
                        <td>1</td>
                    </tr>

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
                <h4>Precio Total <?php echo $precioTotal ?><!-- Poner precio total aquí --> €</h4>
            </div>
            
            <!-- aqui mostrara los errores al tramitar el pedido -->
            <div class="contenedorErrorSaldo">
                <?php
                    //?En caso de que tengamos un error de saldo( se da en caso de que el precio total del pedido sea mayor al saldo disponible)
                    if(isset($_SESSION["error_saldo"]) && $_SESSION["error_saldo"] = TRUE){
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
                                window.location.href = "recargar.php"; // Cambia "recarga.php" por la URL del script al que quieres ir
                            });

                            // Agregar el botón al contenedor
                            contenedor.appendChild(botonRecarga);
                        </script>';
                        //? Una vez se muestre el error se elimina
                        unset($_SESSION["error_saldo"]); 
                    }
                ?>
            </div>
            
            <!-- Formulario para tramitar pedido -->
            <form id="tramitar" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Formulario para comenzar proceso de pago-->
            <button type="submit" name="tramitar" class="botonTramitar" >
                TRAMITAR PEDIDO
            </button>
        </form>
        </section>
    </main>

    <footer>
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
</body>
</html>


