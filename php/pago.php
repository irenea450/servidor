<?php 
//~ require dle correo madnadndole el email y nuemro de pedido
session_start(); // iniciar sesión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tokenRecibido = $_POST['token'] ?? null;
    $tokenSession = $_SESSION['tokenPedido'] ?? null;

    echo "Token en sesión: " . htmlspecialchars($tokenSession) . "<br>";
    echo "Token recibido en POST: " . htmlspecialchars($tokenRecibido) . "<br>";

    if (!$tokenRecibido || $tokenRecibido !== $tokenSession) {
        echo "⚠ Advertencia: Token no válido, pero continuamos.";
    } else {
        echo "✅ Token válido, procediendo con el pago...";
    }
}

//? Recuperar las variables de sesión
$idUsuario = $_SESSION["id"];
$nombreCompleto = $_SESSION['nombreCompleto'];
$direccionEnvio = $_SESSION['direccionEnvio'];
$pesoEnvio = $_SESSION['pesoEnvio'] ;
$sumaPrecioProductos = $_SESSION['sumaPrecioProductos'] ?? 0;
$gastosEnvio = $_SESSION['gastosEnvio'] ?? 4.5; // Valor por defecto si no está en la sesión
$precioTotal = $_SESSION['precioTotal'] ?? 0;

// Conectar a la base de datos
$conexion = "mysql:dbname=irjama;host=127.0.0.1";
$usuario_bd = "root";
$clave_bd = "";
$errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];


try {
    $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);

    //Se inicia la transacción, en caso de error se cancela el pedido
    $bd->beginTransaction();

    // Insertar un nuevo pedido
    $stmt = $bd->prepare("
        INSERT INTO pedido (idCliente, fechaEnvio, enviado, peso, gastosEnvio, pvpTotal) 
        VALUES (:idCliente, NOW(), :enviado, :peso, :gastosEnvio, :pvpTotal)
    ");

    $stmt->execute([
        'idCliente' => $idUsuario,  // id del cleinte que esta logueado
        'enviado' => "no" , // por defecto no, una vez la empresa de envio lo prepare cambiara su estado
        'peso' => $pesoEnvio,  //peso total dle paquete
        'gastosEnvio' => $gastosEnvio,  //gstos de envio
        'pvpTotal' => $precioTotal //precio total del producto
    ]);

    // Obtener el ID del último pedido insertado
    $ultimoIdPedido = $bd->lastInsertId();

    // Recuperar los datos del pedido insertado
    $stmt = $bd->prepare("SELECT * FROM pedido WHERE id = ?");
    $stmt->execute([$ultimoIdPedido]);
    $ultimoPedido = $stmt->fetch(PDO::FETCH_ASSOC);

    //confrimar transacción
    $bd->commit();

    //~ Generar un nuevo token solo después del pago exitoso
    /* $_SESSION['tokenPedido'] = bin2hex(random_bytes(32)); */

    // Extraer los valores de fecha y peso
    $fechaEnvio = $ultimoPedido['fechaEnvio'] ?? 'Fecha no disponible';
    $pesoTotal = $ultimoPedido['peso'] ?? 'Peso no disponible';


    // Mostrar el ID y los datos del pedido
    echo "📌 Pedido insertado con éxito. ID del pedido: " . $ultimoIdPedido . "<br>";
    echo "📌 Datos del pedido: <pre>" . print_r($ultimoPedido, true) . "</pre>";

} catch (Exception $e) {
    // En caso de error, deshacer la transacción
    if ($bd->inTransaction()) {
        $bd->rollBack();
    }
    echo "❌ Error con la base de datos: " . $e->getMessage();
}



/* -------------------------------------------------------------------------- */
/*                             mostrar infromación                            */
/* -------------------------------------------------------------------------- */


// Mostrar la información
echo "<h2>Tu pedido ha sido realizado: Nº $ultimoIdPedido</h2>";
echo "<p><strong>Nombre:</strong> $nombreCompleto</p>";
echo "<p><strong>Dirección de Envío:</strong> $direccionEnvio</p>";
echo "<p><strong>Suma de Productos:</strong> $sumaPrecioProductos €</p>";
echo "<p><strong>Gastos de Envío:</strong> $gastosEnvio €</p>";
echo "<p><strong>Total a Pagar:</strong> $precioTotal €</p>";
echo "<p><strong>Fecha de Envío:</strong> $fechaEnvio</p>";
echo "<p><strong>Peso total del paquete:</strong> $pesoTotal kg</p>";

?>