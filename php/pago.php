<?php 
//~ require dle correo madnadndole el email y nuemro de pedido
require 'funcionesInsUpdDel.php';
require 'funciones.php';
require 'email/emailConfirmacion.php';
require "cookies.php";

session_start(); // iniciar sesión

//? Si se activa el botón de volver atras redirige a la pagina principal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atras'])) {
    header("Location: ../index.php");
}

//? Verificar si el token no existe, en caso de que ya haya sido eliminado(se ha reliazado el pedido)
//? aparecerá pantalla en blanco y no se ejecutará una nueva transacción, de esat forma bloqueamos pedir varias veces lo mismo al refresacar
if (!isset($_SESSION['tokenPedido'])) {
    //? Si el token no existe, muestra un mensaje de error y un botón para volver al inicio
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            document.body.innerHTML = `
                <h2>⚠ ERROR: Para volver a realizar el pedido tienes que pasar por la cesta</h2>
                <div class="volverInicio">
                    <form id="atrasForm" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">
                        <button type="submit" name="atras" class="flechaVolver">
                            <img src="/img/flecha_atras.png">
                        </button>
                    </form>
                </div>
            `;
            document.body.style.textAlign = "center";
            document.body.style.paddingTop = "50px";
            document.body.style.color = "red";
        });
    </script>';
    exit(); //* Detiene la ejecución para evitar que continúe el proceso del pedido
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

    //*Se inicia la transacción, en caso de error se cancela el pedido
    $bd->beginTransaction();

    //? Preparada para insertar un nuevo pedido
    $preparada1 = $bd->prepare("
        INSERT INTO pedido (idCliente, fechaCompra, estado, peso, gastosEnvio, pvpTotal) 
        VALUES (:idCliente, NOW(), :estado, :peso, :gastosEnvio, :pvpTotal)
    ");

    //? Se insertan los datos del usuario y de envio en la preparada de la insercción
    $preparada1->execute([
        'idCliente' => $idUsuario,  // id del cleinte que esta logueado
        'estado' => "preparacion" , // por defecto no, una vez la empresa de envio lo prepare cambiara su estado
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
    //? a los puntos actuales se suma los puntos nuevos de la compra(precioProductos pues se suma 1 punto por €)
    $sumaPuntos = $puntosActual + $precioProductos ;


    //? Hace update del saldo del cliente en la base de datos, introduciendo el saldo ya restado (saldoResta)
    $updateSaldo = $bd ->prepare("UPDATE cliente SET saldo = ?  WHERE id = ?");
    // saldoResta es el saldo descontando el total del pedido
    // $_SESSION['sumaPrecioProductos'] suma del precio de todos los productos, se va a sumar un punto por €
    $resul = $updateSaldo->execute(array($saldoResta, $_SESSION['id']));


    //? Insertar en la tabla composicion_envio el nº pedido y el id del producto y cantidad
    //? Preparada para insertar los datos del pedido
    $preparada4 = $bd->prepare("
        INSERT INTO composicion_envio (idPedido, idProducto, cantidad) 
        VALUES (:idPedido, :idProducto, :cantidad)
    ");

    //? Insertar los datos de la matriz con los pedidos en la tabla composicion_envio
    foreach ($_SESSION["matriz"]  as $producto) {
        $preparada4->execute([
            'idPedido'   => $ultimoIdPedido,  // Id del pedido que se ha realizado
            'idProducto' => $producto['ref'], // Id del producto
            'cantidad'   => $producto['cantidad'] // Cantidad pedida del producto
        ]);
    }


    //* CONFIRMAR TRANSACCIÓN
    $bd->commit();

    //? Una vez la transacción se ha realizado con exito, se elimina el token , de esta forma no puedes
    //?  volver a pedir lo mismo por duplicado sin pasar por la cesta
    unset($_SESSION['tokenPedido']);

    //*Extraer los valores de fecha y peso para despues mostrar en datos del pedido
    $fechaCompra = $ultimoPedido['fechaCompra'] ?? 'Fecha no disponible';
    $pesoTotal = $ultimoPedido['peso'] ?? 'Peso no disponible';


} catch (Exception $e) {
    //? En caso de error, deshacer la transacción
    if ($bd->inTransaction()) {
        $bd->rollBack();
    }
    echo "Error en la base de datos: " . $e->getMessage();
}
//? Una vez se ha realizado la transacción ya se envia el correo de confirmación, se actualizan los puntos y se borra el carrito
//? se añaden los puntos al usuario
puntosTipo($sumaPuntos);
//? Mandar email de confrimación de pedido
/* mailPedido($_SESSION['emailUsuario'], $ultimoIdPedido); */
$mensajeCorreo = mailPedido("irenedelalamo.alumno@gmail.com", $ultimoIdPedido);

//? eliminamos sesión matriz y el numero de carrito
$_SESSION['matriz'] = [];

//? guartdamos la matriz vaciua en la cookie carrito
cookieCarrito($_SESSION["matriz"]);

//? elimina cookie carrito, para vaciar todos los productos que ya han sido comprados
setcookie("carrito", 123, time() - 1000, "/"); // "/" para destruir en todo el proyecto
unset($_SESSION["numCarrito"]);//destruimos numero de carrito


/* -------------------------------------------------------------------------- */
/*                             mostrar información                            */
/* -------------------------------------------------------------------------- */
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
        <p><strong>Fecha de Compra:</strong> <?php echo $fechaCompra ?></p>
        <p><strong>Peso del pedido:</strong> <?php echo $pesoTotal ?></p>
        <p><strong>Gastos de envio:</strong> <?php echo $gastosEnvio ?></p>
        <p><strong>Precio total del pedido:</strong> <?php echo number_format($precioTotal, 2) ?>€</p>
    </div>
    <div class="datosResumenPersonales">
        <h3>Datos Personales</h3>
        <p><strong>Nombre:</strong> <?php echo $nombreCompleto ?></p>
        <p><strong>Saldo:</strong> <?php echo number_format($saldoResta, 2) ?></p>
    </div>

    <div class="volverInicio">
        <form id="atrasForm" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Formulario con función de ir atrás -->
            <button type="submit" name="atras" class="flechaVolver"  >
                <img src="/img/flecha_atras.png">
            </button>
        </form>
    </div>

    <!-- Mostrar si se ha enviado bien el correo de confrimación o no -->
    <?php if ($mensajeCorreo){ 
            echo '<div style="color: green; font-size: 18px; text-align: center; margin-top: 20px;">
                ✅ Se ha enviado un correo de confirmación a '. $_SESSION["emailUsuario"] .'
            </div>';
        }else{
            echo '<div style="color: red; font-size: 18px; text-align: center; margin-top: 20px;">
            ❌ Hubo un problema al enviar el correo.
            </div>';
        }
    ?>
    

</body>
</html>