<?php 
//~ require dle correo madnadndole el email y nuemro de pedido
require 'funcionesInsUpdDel.php';

session_start(); // iniciar sesión

//? Verificar si el token no existe, en caso de que ya haya sido eliminado(se ha reliazado el pedido)
//? aparecerá pantalla en blanco y no se ejecutará una nueva transacción
if (!isset($_SESSION['tokenPedido'])) {
    //? Si el token no existe va a lanzar mensaje de error, pues es que el pedido ya ha sido realizado y
    //? no se puede volver a hacer sin pasar por la cesta
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            document.body.innerHTML = "<h2>⚠ ERROR: Para volver a realizar el pedido tienes que pasar por la cesta</h2>";
            document.body.style.textAlign = "center";
            document.body.style.paddingTop = "50px";
            document.body.style.color = "red";
        });
    </script>'
    ;
    exit(); //* Detiene el codigo aqui para que no ejecute la transacción de nuevo
}

//? Recuperar las variables de sesión
$idUsuario = $_SESSION["id"];
$nombreCompleto = $_SESSION['nombreCompleto'];
$direccionEnvio = $_SESSION['direccionEnvio'];
$pesoEnvio = $_SESSION['pesoEnvio'] ;
$gastosEnvio = $_SESSION['gastosEnvio'] ; 
$precioTotal = $_SESSION['precioTotal'] ;
$precioProductos = $_SESSION['sumaPrecioProductos']; //precio total de productos que va a ser los puntos que se sume al usuario



// Conectar a la base de datos
$conexion = "mysql:dbname=irjama;host=127.0.0.1";
$usuario_bd = "root";
$clave_bd = "";
$errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];


try {
    $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);

    //Se inicia la transacción, en caso de error se cancela el pedido
    $bd->beginTransaction();

    //? Preparada para insertar un nuevo pedido
    $preparada1 = $bd->prepare("
        INSERT INTO pedido (idCliente, fechaEnvio, enviado, peso, gastosEnvio, pvpTotal) 
        VALUES (:idCliente, NOW(), :enviado, :peso, :gastosEnvio, :pvpTotal)
    ");

    //? Se insertan los datos del usuario y de envio en la preparada de la insercción
    $preparada1->execute([
        'idCliente' => $idUsuario,  // id del cleinte que esta logueado
        'enviado' => "no" , // por defecto no, una vez la empresa de envio lo prepare cambiara su estado
        'peso' => $pesoEnvio,  //peso total dle paquete
        'gastosEnvio' => $gastosEnvio,  //gstos de envio
        'pvpTotal' => $precioTotal //precio total del producto
    ]);

    //? Sacar el ID del último pedido insertado
    $ultimoIdPedido = $bd->lastInsertId();

    //? Recuperar los datos del pedido insertado para sacar el id 
    $preparada2 = $bd->prepare("SELECT * FROM pedido WHERE id = ?");
    $preparada2->execute([$ultimoIdPedido]);
    $ultimoPedido = $preparada2->fetch(PDO::FETCH_ASSOC);


    //? Saldo del cliente, se va a consultar la base de datos cual es el saldo actual
    $preparada3 = $bd->prepare("SELECT saldo, puntos FROM cliente WHERE id = ?");
    $preparada3->execute([$_SESSION['id']]);
    $cliente = $preparada3-> fetch();
    // saca el saldo y puntos y se guarda
    $saldoActual = $cliente['saldo'];
    $puntosActual = $cliente['puntos'];


    //? Al saldo actual se le resta el precio del pedido
    $saldoResta = $saldoActual - $precioTotal;
    //? a los puntos actuales se suma los puntos nuevos de la compra(precioProductos)
    $sumaPuntos = $puntosActual + $precioProductos ;


    //? Hace update del saldo del cliente en la base de datos, introduciendo el saldo ya restado (saldoResta)
    $updateSaldo = $bd ->prepare("UPDATE cliente SET saldo = ?  WHERE id = ?");
    // saldoResta es el saldo descontando el total del pedido
    // $_SESSION['sumaPrecioProductos'] suma del precio de todos los productos, se va a sumar un punto por €
    $resul = $updateSaldo->execute(array($saldoResta, $_SESSION['id']));


    //* CONFIRMAR TRANSACCIÓN
    $bd->commit();

    //? Una vez la transacción se ha realizado con exito, se elimina el token , de esta froma no puedes
    //?  volver a pedir lo mismo por duplicado sin pasar por la cesta
    unset($_SESSION['tokenPedido']);

    //*Extraer los valores de fecha y peso para despues mostrar
    $fechaEnvio = $ultimoPedido['fechaEnvio'] ?? 'Fecha no disponible';
    $pesoTotal = $ultimoPedido['peso'] ?? 'Peso no disponible';


/*     // Mostrar el ID y los datos del pedido
    echo "📌 Pedido insertado con éxito. ID del pedido: " . $ultimoIdPedido . "<br>";
    echo "📌 Datos del pedido: <pre>" . print_r($ultimoPedido, true) . "</pre>"; */

} catch (Exception $e) {
    // En caso de error, deshacer la transacción
    if ($bd->inTransaction()) {
        $bd->rollBack();
    }
    echo "Error en la base de datos: " . $e->getMessage();
}


puntosTipo($sumaPuntos);

/* -------------------------------------------------------------------------- */
/*                             mostrar infromación                            */
/* -------------------------------------------------------------------------- */


// Mostrar la información
/* echo "<h2>Tu pedido ha sido realizado: Nº $ultimoIdPedido</h2>"; */
/* echo "<p><strong>Nombre:</strong> $nombreCompleto</p>"; */
/* echo "<p><strong>Dirección de Envío:</strong> $direccionEnvio</p>"; */
/* echo "<p><strong>Suma de Productos:</strong> $sumaPrecioProductos €</p>"; */
/* echo "<p><strong>Gastos de Envío:</strong> $gastosEnvio €</p>"; */
/* echo "<p><strong>Total a Pagar:</strong> $precioTotal €</p>"; */
/* echo "<p><strong>Fecha de Envío:</strong> $fechaEnvio</p>"; */
/* echo "<p><strong>Peso total del paquete:</strong> $pesoTotal kg</p>"; */
/* echo "<h1><strong>Saldo resta</strong> $saldoResta €</h1>"; */

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos Pedido</title>
    <link rel="stylesheet" href="/css/estilos_compra.css">
</head>
<body id="contenedor-pago">
    <h1>Tu pedido ha sido realizado: Nº <?php echo $ultimoIdPedido ?></h1>

    <div class="datosResumenEnvio">
        <h3>Datos de Envío</h3>
        <p><strong>Dirección de Envio:</strong> <?php echo $direccionEnvio ?></p>
        <p><strong>Fecha de Facturación:</strong> <?php echo $fechaEnvio ?></p>
        <p><strong>Peso del pedido:</strong> <?php echo $pesoTotal ?></p>
        <p><strong>Gastos de envio:</strong> <?php echo $gastosEnvio ?></p>
        <p><strong>Precio total del pedido:</strong> <?php echo $precioTotal ?></p>
    </div>
    <div class="datosResumenPersonales">
        <h3>Datos Personales</h3>
        <p><strong>Nombre:</strong> <?php echo $nombreCompleto ?></p>
        <p><strong>Saldo:</strong> <?php echo $saldoResta ?></p>
    </div>
    
    

</body>
</html>