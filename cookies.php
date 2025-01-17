<?php
require "funciones.php";
//?- una vez realizada la compra la cookie "carrito" debera ser destruida
//?- al arrancar la aplicacion se ha de comprobar si $_COOKIE["carrito"] esta inicializada en caso afirmativo 
//?- la enviaremos a la funcion desmontar1() y desmontar2() donde se transformara en una matriz
//?- Cada vez que añadamos un producto al carrito este se añadira a la mtriz y se sobreescribira la cookie

    //todo - FUNCION PARA CREAR O ACTUALIZAR $_COOKIE["carrito"]
    function cookieCarrito($matriz){
        //inicializamos variable con la duracion de la cookie
        $time = time() + (7 * 24 * 60 * 60);

        //variable vacia para generar el string del carrito
        $info = "";
        // Mandar la matriz a la cookie
        foreach ($matriz as $fila) {
            // Si $info no está vacío, añadimos un asterisco antes de concatenar para separar cada producto
            if (!empty($info)) {
                $info .= "*";
            }
            //concatenamos la referencia del producto + la cantidad de articulos como texto y separados por una coma
            $info .= $fila["ref"] . "," . $fila["cantidad"];
        }

        try{
            //creamos la cookie "carrito" con el valor de $info y una duracion de una semana
            //en caso de error se lanzara un error
            if(!setcookie("carrito", $info, $time)){
                throw new Exception("Error al establecer la cookie");
            }
        
        }  catch (Exception $e) {
            // Manejo del error
            echo "No ha sido posible guardar el carrito.";
        }
        
    }

    /*     //todo- prueba cookie carrito
    // Crear una matriz con 5 filas y 2 columnas
    $matriz = [
        ["ref" => 101, "cantidad" => 2],
        ["ref" => 102, "cantidad" => 5],
        ["ref" => 103, "cantidad" => 8],
        ["ref" => 104, "cantidad" => 3],
        ["ref" => 1030, "cantidad" => 80],
        ["ref" => 105, "cantidad" => 6]
    ];

    cookieCarrito($matriz);

    // El array cookies no se actualiza hasta la siguiente vez que corres el script 
    //por eso la primera vez no plotea el valor de la cookie y al actualizar va desfasado en uno

    // Verifica si la cookie existe
    if (isset($_COOKIE['carrito'])) {
        // Muestra el valor de la cookie
        echo "El valor de la cookie es: " . $_COOKIE['carrito'];
    } else {
        echo "La cookie no está definida.";
    }
 */
    //borrar cookie
    //setcookie("carrito", 1, time() -100);




    //todo - FUNCION PARA CREAR O ACTUALIZAR $_COOKIE["session_token"]
    function cookieSesion(){
        // La cookie esta formada por un string que contiene el id y el token separado por una coma ("id,token")
        /**El token generado en el código utiliza el formato hexadecimal porque se obtiene con la función bin2hex(random_bytes(32)). 
            Esto convierte los bytes generados por random_bytes (valores binarios) en una cadena de texto hexadecimal legible.

        Características del Token:
        Longitud: El token será de 64 caracteres (cada byte se convierte en dos caracteres hexadecimales).

        Formato: Una cadena alfanumérica en minúsculas, compuesta por los caracteres 0-9 y a-f.

        Ejemplo de Token:
        Si imprimes el token generado, podría verse así: e3c4f7d9a2b3c1d5e6f7a8b9c0d1e2f3c4f5b6a7c8d9e0f1b2c3a4d5e6f7a8b9 */

        // Inicia una sesión para el usuario
        session_start();

        //Parámetros de conexión a la base de datos
        $cadena_conexion = "mysql:dbname=irjama;host=127.0.0.1";
        $usuarioConex = "root";
        $claveConex = "";
        //para que en caso de error me devuelva un FALSE , ponerlo siempre
        $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];

        //Crear una instancia de la clase PDO para la conexión con la base de datos
        //El modo de error está configurado como ERRMODE_SILENT, lo que significa que no lanzará excepciones
        $db = new PDO($cadena_conexion, $usuarioConex, $claveConex, $errmode);
        //echo "conexion realizada con exito<br>";

        // Define la duración de la cookie en segundos (7 días)
        $cookie_duration = 7 * 24 * 60 * 60; 

        // Verifica si $_COOKIE['session_token'] ya está configurada
        if (isset($_COOKIE["session_token"])) {
            // Recupera el id y el token almacenado en la cookie session_token (id,token)
            $info = $_COOKIE["session_token"];

            //mandamos la info a desmontar3() para separar los datos por la coma devolviendo un array de dos posiciones clave-valor
            $session_token = desmontar3($info);

            // Guardamos $session_token en $id y $token mediante un foreach ya que es un array de arrays con una linea
            $id = '';
            $token = '';
            foreach ($session_token as $elemento) {
                $id = $elemento['id'];
                $token = $elemento['token'];
            }

            // rescatamos el valor almacenado en la base de datos gracias a la cookie[id]
            $preparada1 = $db ->prepare("SELECT token FROM cliente WHERE id = ?");
            $preparada1 -> execute(array($id));
            $datos = $preparada1->fetch();

            //comprobamos que coinciden ambos tokens
            if ($token === $datos["token"]) {
                

                
                // Si el token es válido, iniciamos las variables de sesión necesarias ($_SESSION['id']... etc)
                $preparada = $db ->prepare("SELECT * FROM cliente WHERE id = ?");
                $preparada -> execute(array($id));
                $datos = $preparada->fetch();
                //?- inicializar las variables de session necesarias
                $_SESSION["id"] = $datos["id"];
                $_SESSION["login"] = true; //?- OPCIONAL PARA QUE TODOS LOS SCRIPS SEPAN QUE ESTAS LOGUEADO

                echo "<h1>sigues logueado campeon</h1>";//! -- valores de prueba BORRAR
            }
        } else {
            //! -- llamariamos a la funcion login y despues generariamos el token e iniciariamos las cookies (session_token) BORRAR
                        echo "estas en el ELSE y vas al login <br><br>";    //! -- valores de prueba BORRAR
                        $email = "marcos@gmail.com";    //! -- valores de prueba BORRAR
                        $_SESSION["id"] = "1";    //! -- valores de prueba BORRAR
                        echo $_SESSION['id'];   //! -- valores de prueba BORRAR
                        echo "<br><br>";      //! -- valores de prueba BORRAR
                        $id = $_SESSION['id'];      //! -- valores de prueba BORRAR
                        echo "el login me devuelve: email = $email y id = $id <br><br>"; //! -- valores de prueba BORRAR

            // llamamos a la funcion login() la cual inicia las variables de sesion (copiar lineas 61-66) y nos devuelve un boolean
            //$control = login();//?- guardamos el resultado de la funcion login()

            //do{//?- si el resultado de la funcion login() es false se le redirige al login
                //$control = login();//?- o a una funcion especifica
            //}while(!$control);

            $control = true;  //! -- valores de prueba BORRAR

            if($control){
                // Genera un nuevo token único y seguro
                $session_token = bin2hex(random_bytes(32));

                // update en la bdd con el token correspondiente
                $preparada2 = $db ->prepare("UPDATE cliente SET token = ? WHERE id = ?");
                $preparada2 -> execute(array($session_token, $_SESSION["id"])); //?- Cambiar $id por $_SESSION['id']


                //concatenamos el id con el token separados por una coma para inicializar la cookie
                $token = $_SESSION["id"] . "," . $session_token;  //?- Cambiar $id por $_SESSION['id']

                // Configura la cookie para almacenar el token
                // - Nombre de la cookie: 'session_token'
                // - Valor: el token generado ($token)
                // - Tiempo de expiración: ahora + 7 días
                setcookie("session_token", $token, time() + $cookie_duration);
            }
        }
    }
?>