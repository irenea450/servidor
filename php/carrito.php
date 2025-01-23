<?php
require "funciones.php";
//*Inicia sesión
session_start();

/**
 * ?Voy a extraer los datos del usuario que está logueado para mostrar los datos de la compra
 * ? uso variables para luego mostralo en el html */
//~Variable de nombre del usuario
$nombreUsuario = obtenerNombreUsuario($_SESSION["id"]);

//? Para sacar los datos de facturación y envio se llama a la función pasando el id del usuario
$datosUsuario = obtenerDirecciones($_SESSION["id"]);
//? Para sacar los datos de saldo se llama a la función obtener saldo
$datosSaldo = obtenerSaldo($_SESSION["id"]);

//? las variables que van a guardar los datos extraidos en las consultas anteriores
//? se incluyen estas variables más abajo para que sean visibles
$direccionEnvio = $datosUsuario['direccionEnvio'];
$direccionFacturacion = $datosUsuario['direccionFacturacion'];
$saldo = $datosSaldo['saldo'];
$puntos = $datosSaldo['puntos'];






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
        <ul>
            <li><img src="/img/LOGO 2.png"></li>
            <li></li>
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
                <h4>Precio Total <!-- Poner precio total aquí --> €</h4>
            </div>
        </section>
    </main>

    <footer>
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
</body>
</html>


