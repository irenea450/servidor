<?php
    /**
     *? comprueba si no hay una sesión activa y si no la hay la inicia
    *? session_status -> devuelve el estado actual de la sesión  */
    if (session_status() == PHP_SESSION_NONE) {
        //? si se cumple la condición de no activa se iniciar la sesión
        session_start();
    }

    //? Botón retorno al área personal
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atras'])) {
        //* Si se pulsa volver atrás, te llevará al area personal del usuario
        header("Location: areaPersonal.php");
    }


    // Aquí debes conectar a la base de datos y obtener la info del producto con la referencia
    $conexion = "mysql:dbname=irjama;host=127.0.0.1";
    $usuario_bd = "root";
    $clave_bd = "";
    $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
    $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);


    $preparada = "SELECT p.id, p.fechaCompra, p.estado, p.pvpTotal, 
                    c.idProducto, c.cantidad 
                    FROM pedido p
                    LEFT JOIN composicion_envio c ON p.id = c.idPedido
                    WHERE p.idCliente = :id
                    ORDER BY p.id";
    $stmt = $bd->prepare($preparada);

    // Ejecutar la consulta pasando solo el parámetro ref
    $stmt->execute(['id' => $_SESSION["id"]]);
    

    // Obtener el resultado de la consulta
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!-- HTML-> Formulario y manejo de errores -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizado</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_login.css">
    <link rel="stylesheet" href="/css/estilos_pedido.css">
</head>
<body>
    <h1>Pedidos</h1>
    <?php
        foreach($pedidos as $pedido){
            //sacamos el nombre y la categoria del producto para montar la ruta de la imagen del producto
            $idProd = $pedido['idProducto'];
            $sql = "SELECT categoria, nombre FROM producto WHERE ref = $idProd";
            $result = $bd->query($sql);
            $prod = $result->fetch();
            $cat = $prod['categoria'];
            $nom = $prod['nombre'];
            $productPath = "/categorias/$cat/$idProd/1.png";

            echo "<div class='contenedorPedido'>
                <h2>Pedido: {$pedido['id']}</h2>
                <img src='{$productPath}'>
                <h3 class='nombre'>{$nom}</h3>
                <p>Cantidad: {$pedido['cantidad']}</p>
                <p>Fecha compra: {$pedido['fechaCompra']}</p>
                <p>Estado: {$pedido['estado']}</p>
                <a href='producto.php?categoria={$cat}&producto={$idProd}'>Comprar</a>
            </div>";
            
        }
    ?>

    <!-- flecha volver atras -->
    <div class="volverInicio">
        <form id="atrasForm" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Formulario con función de ir atrás -->
            <button type="submit" name="atras" class="flechaVolver"  >
                <img src="/img/flecha_atras.png">
            </button>
        </form>
    </div>
</body>
</html>

