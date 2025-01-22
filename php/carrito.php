<?php


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
        </ul>
    </header>
    <h1>Hola (nombre), este es tu carrito de la compra</h1>
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
                        <td><img src="/Productos/Categorias"></td>
                        <td>Arduino Microcontrolador USB Uno R3</td>
                        <td>23 €</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td><img src=""></td>
                        <td>Arduino Microcontrolador USB Uno R3 </td>
                        <td>1536495 €</td>
                        <td>1</td>
                    </tr>

                </tbody>
            </table>
        </section>
        <!-- En esta sección se van a mostrar los datos de envio -->
        <section  id="direcciones">
            <div class="dir-envio">
                <!-- Dirrección de envio -->
                <h3>Dirección de Envio</h3>
                <p>Calle: Tajo , Nº 5</p>
                <p>Ciudad: Toledo , CP: 45007</p>
                
            </div>
            <div class="dir-facturación">
                <!-- Dirreciión de facturación -->
            </div>
        </section>
    </main>

    <footer>
        <p>Calle Instituto, 7, 45593 Bargas, Toledo</p>
        <p>Tlf: 653 985 395</p>
    </footer>
</body>
</html>