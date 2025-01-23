<?php

        //todo - FUNCIONES PARA DESMONTAR STRINGS
    /**
     * Funciónes para procesar la cookie "carrito" (desmontar1 + desmontar2), caracteristicas de producto (desmontar1), etc.
     * string $info = Cadena de texto con los datos del carrito, carcteristicas, etc.
     * array Matriz $resultado de dos columnas con los datos procesados en caso del carrito.
     */
    function desmontar1($info){
        // Dividir la cadena en un array utilizando el asterisco (*) como separador.
        $elementos = explode('*', $info);

        // Retornar el array procesado.
        return $elementos;
    }

    function desmontar2($info){
        $resultado = []; // Inicializamos un array vacío para almacenar la matriz final.

        // Recorrer cada elemento del array dividido por asteriscos.
        foreach ($info as $elemento) {
            // Dividir el elemento actual en otro array utilizando la coma (,) como separador.
            $partes = explode(',', $elemento);

            // Asegurarnos de que cada elemento dividido tenga exactamente dos valores (ref y cantidad).
            if (count($partes) === 2) {
                // Agregar el par ref-cantidad al resultado, convirtiéndolos a enteros.
                $resultado[] = [
                    'ref' => intval($partes[0]),     // Convertir la primera parte (id) a entero.
                    'cantidad' => intval($partes[1]) // Convertir la segunda parte (cantidad) a entero.
                ];
            }
        }

        // Retornar la matriz procesada.
        return $resultado;
    }


    //? - ejemplos de uso de desmontando1() y desmontando2()
    /* //todo - prueba de desmontar1() para las caracteristicas del producto
    //variable de prueba
    $cadenaPrueba = "hola*eso*es*una*cadena"; 

    $prueba = desmontar1($cadenaPrueba);


    $num = 1;
    foreach($prueba as $posicionArray){
        echo $num . " - " . $posicionArray . "<br>";
        $num++;
    }

    echo "<br>";

    //todo - prueba de desmontar1() + desmontar2() para la cookie del carrito
    //variable de prueba
    $cadenaPrueba2 = "101,3*102,5*103,2*104,10*105,8";

    //troceo transformando el string en un array
    $prueba2 = desmontar1($cadenaPrueba2);
    //troceo transformando el array en una matriz con referencias (ref, cantidad)
    $matriz = desmontar2($prueba2);

    foreach($matriz as $linea){
        echo "Referencia: " . $linea['ref'] . " - Cantidad: " . $linea['cantidad'] . "<br>";
    } */

    function desmontar3($info){
        $resultado = []; // Inicializamos un array vacío para almacenar la matriz final.

        // Dividir el elemento actual en otro array utilizando la coma (,) como separador.
        $partes = explode(',', $info);

        // Asegurarnos de que el elemento dividido tenga exactamente dos valores (id y token).
        if (count($partes) === 2) {
            // Agregar el par ref-cantidad al resultado, convirtiéndolos a enteros.
            $resultado[] = [
                'id' => intval($partes[0]),     // Convertir la primera parte (id) a entero.
                'token' => $partes[1]
            ];
        }
        
        // Retornar la matriz procesada.
        return $resultado;
    }

/* ---------------- Función para saber el nombre del usuario ---------------- */
//TODO Función para obtener el nombre del usuario con base en el ID almacenado en la sesión
function obtenerNombreUsuario($id) {
    // Aquí debes conectar a tu base de datos y obtener el nombre del usuario con el ID
    $conexion = "mysql:dbname=irjama;host=127.0.0.1";
    $usuario_bd = "root";
    $clave_bd = "";
    $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
    $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);

    
    // Consulta para obtener el nombre del usuario con el ID
    $consulta = "SELECT nombre FROM cliente WHERE id = :id";
    $stmt = $bd->prepare($consulta);

    // Ejecutar la consulta pasando solo el parámetro id
    $stmt->execute(['id' => $id]);

    // Obtener el resultado de la consulta
    $resultado = $stmt->fetch();

    // Devolver el nombre del usuario si se encuentra, o "Usuario desconocido" si no
    return $resultado ? $resultado['nombre'] : "Usuario desconocido";

}

/* ---------- Función para obtener datos de facturación del usuario --------- */
//TODO: Función que va a sacar los datos de facturación el usaurio que este logueado mediante el id
function obtenerDirecciones($id){
    //? Consulta que extrae los datos del usuario
    $conexion = "mysql:dbname=irjama;host=127.0.0.1";
    $usuario_bd = "root";
    $clave_bd = "";
    $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
    $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);


    // Consulta para obtener el nombre del usuario con el ID
    $consulta = "SELECT direccionEnvio , direccionFacturacion FROM cliente WHERE id = :id";
    $ejecuto = $bd->prepare($consulta);

    // Ejecutar la consulta pasando solo el parámetro id
    $ejecuto->execute(['id' => $id]);

    // Obtener el resultado de la consulta
    $resultado = $ejecuto->fetch();

    if ($resultado) {
        $direccionEnvio = $resultado['direccionEnvio'];
        $direccionFacturacion = $resultado['direccionFacturacion'];
    }
    return $resultado; 
}
/* ------------------- Obtener saldo y puntos del usuario ------------------- */
//TODO: función que obtiene el saldo y puntos que tiene el usuario logueado
function obtenerSaldo($id){
    //? Consulta que extrae los datos del usuario
    $conexion = "mysql:dbname=irjama;host=127.0.0.1";
    $usuario_bd = "root";
    $clave_bd = "";
    $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
    $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);


    // Consulta para obtener el nombre del usuario con el ID
    $consulta = "SELECT saldo , puntos FROM cliente WHERE id = :id";
    $ejecuto = $bd->prepare($consulta);

    // Ejecutar la consulta pasando solo el parámetro id
    $ejecuto->execute(['id' => $id]);

    // Obtener el resultado de la consulta
    $resultado = $ejecuto->fetch();

    if ($resultado) {
        $saldo = $resultado['saldo'];
        $puntos = $resultado['puntos'];
    }
    return $resultado; 
}
//* preubas irene
function sacarIdImagen($categoria){
    //? Consulta que extrae los id de los productos para mostrarlos segun su categoria
    $conexion = "mysql:dbname=irjama;host=127.0.0.1";
    $usuario_bd = "root";
    $clave_bd = "";
    $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
    $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);


    // Consulta para obtener el nombre del usuario con el ID
    $consulta = "SELECT id  FROM productos WHERE categodia = :categodia";
    $ejecuto = $bd->prepare($consulta);

    // Ejecutar la consulta pasando solo el parámetro id
    $ejecuto->execute(['categodia' => $categoria]);

    // Obtener el resultado de la consulta
    $resultado = $ejecuto->fetch();

    if ($resultado) {
        $idProducto = $resultado['idProducto'];
    }
    return $resultado; 
}
?>