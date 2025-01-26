<!-- opcion de registro y check / isset del metodo post -->
<?php
//scripts que vamos a necesitar
require 'cookies.php';

/**
 *? comprueba si no hay una sesión activa y si no la hay la inicia
 *? session_status -> devuelve el estado actual de la sesión  */
if (session_status() == PHP_SESSION_NONE) {
    //? si se cumple la condición de no activa se iniciar la sesión
    session_start();
}

/**
 * ? 1º va a comprobar si la cookie sesion_token está activa
 * ? 2º Se llama a la función de cookie sesión
 * ? 3º Si la sesión ya esat inicaida en la cookie y se tiene el id y se ha hecho log in antes 
 * ? no hace falta hacer el log in de nuevo por lo que se dirige al log out  */ 

//* Comprobar si ya hay una sesión activa (basada en la cookie)
if (isset($_COOKIE['session_token'])) {
    //? Si hay cookie -> verificamos si es válida y redirigimos a logout
    cookieSesion1(); // Función para validar la cookie

if (isset($_SESSION["id"]) && $_SESSION["login"] === true) {
    //? Si la sesión de la cookie ya está activa se redirige al logout
    header("Location: /php/logout.php");
}
}   


    //* Variable que maneja error al intentar inicar sesión, inicialemnte su estado va a ser false
    $error = false;

    /**
     * ?Un vez se ha comprobado que la sesión no esta iniciada previamente y guardada en la cookie
     * ? Se comprueba que se ha enviado el formulario de log in y si se han introducido el email y la clave
     * ? Con los datos introducitos se va a llamar a la función comprobarUsuario que verifique si los datos son correctos
     * ?   */
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['clave'])){

        //* variable que comprueba si el inicio de sesión es correcto o no
        $comprobarDatos = comprobarUsuario( $_POST["email"] , $_POST["clave"]);

        if ($comprobarDatos == FALSE) {
            //? Si los datos de inicio de sesión no coinciden
            $usuario = $_POST["email"];
            //? En caso de no coincidir se activa la variable de error 
            $_SESSION["error_login"] = TRUE;
        }else{
            //* Inicio de sesion 
            session_start();

            //! VARIABLES DE SESIÓN AL HACER LOGIN
            $_SESSION["usuario"] = $comprobarDatos; // Guardar el email del usuario
            $_SESSION["id"] = obtenerIdUsuario($comprobarDatos); // Obtener el ID del usuario 
            $_SESSION["logueado"] = TRUE; //Guardar variable logueado como tu si ha podido hacer log
            //? Guardar la sesión en la cookie para poder iniciar sesión automaticamente más adelante
            cookieSesion2($_SESSION["id"]);

            //? Una vez el login es correcto va a redidirgir por defecto al index
            //? si llega redirigido de otra pagina se va a volver a esa pagina
            if (isset($_POST['redirigido']) && $_POST['redirigido'] === 'carrito.php') {
                $redirectUrl = 'carrito.php';
            } else {
                //? si no está redirigido por defecto va a index
                $redirectUrl = '../index.php';
            }
            //* Cambia la la pantalla con el url de index o redirigido
            header("Location: " . $redirectUrl);
    
        }
    }

    /* ---- Función de comprobar el usuario y contraseña en la base de datos ---- */
    function comprobarUsuario($email,$clave){
        //conexion con la base de datos
        $conexion = "mysql:dbname=irjama;host=127.0.0.1";
        $usuario_bd = "root";
        $clave_bd = "";
        $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
        $bd = new PDO($conexion , $usuario_bd, $clave_bd, $errmode);

            
        //consulta de email y clave del usuarios
        $consulta = "SELECT email , clave  FROM cliente WHERE email = :email AND clave = :clave"; 
        $comprobar = $bd->prepare($consulta);
        $comprobar->execute(['email' => $email, 'clave' => $clave]);
        $email = $comprobar->fetch();

        //? Si la clave coincide se confirma el inicio y se devuelve true
        if ($email && $email['clave'] === $clave){
            return $email['email'];
        }else return FALSE; //en caso de que no coincida se devuelve false
    }

    /* ---------- Función para obtener el id del usuario que se loguea ---------- */
    function obtenerIdUsuario($email) {
        $conexion = "mysql:dbname=irjama;host=127.0.0.1";
        $usuario_bd = "root";
        $clave_bd = "";
        $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
        $bd = new PDO($conexion, $usuario_bd, $clave_bd, $errmode);
    
        // Consulta para obtener el ID del usuario
        $consulta = "SELECT id FROM cliente WHERE email = :email"; 
        $ejecutamos = $bd->prepare($consulta);
        $ejecutamos->execute(['email' => $email]);
        $resultado = $ejecutamos->fetch();
    
        //? se devuelve el id o en caso de no encontrarlo se devuelve null
        return $resultado ? $resultado['id'] : null;
    }
?>
<!-- HTML-> Formulario y manejo de errores -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_login.css">
</head>
<body>
    <div class="contenedor">
        <img id="icono-login" src="/img/login_icono.png">
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor">
        <?php
            //?En caso de que la contarseña o el usuario no coincidan manda mensaje de error
            if(isset($_SESSION["error_login"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Revise usuario y contraseña";
                    let contenedor = document.querySelector(".erroresContenedor");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_login"]); 
            }
                
        ?>
        <div class="registrarse">
            <p><a href="registro.php">Registrase como nuevo Usuario</a></p>
        </div>
        </div>
        <!-- Formualario de inicio de sesión -->
        <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="POST" >
            <!-- Campo oculto para poder hacer la redirección -->
        <input type="hidden" name="redirigido" value="<?php echo htmlspecialchars($_GET['redirigido'] ?? ''); ?>">

            <div class="inputs">
                <label for="email"><img src="/img/usuario.png"></label>
                <input id="email" name="email" placeholder="email" type="text" value="<?php if(isset($usuario)) echo $usuario ?>" >
            </div>
            <div class="inputs">
                <label for="clave"><img src="/img/contraseña.png"></label>
                <input name="clave" type="password" placeholder="contraseña">
            </div>

            
            <div class="sesionIniciada">
                <label>Mantener sesión iniciada</label>
                <input type="radio" name="auth" >
            </div>
            

            <input type="submit" id="enviar" value="LOGIN">
            
        </form>
    </div>
</body>
</html>

