<!-- opcion de registro y check / isset del metodo post -->
<?php
//scripts que vamos a necesitar
require 'cookies.php';
require 'funciones.php';

/**
 *? comprueba si no hay una sesión activa y si no la hay la inicia
 *? session_status -> devuelve el estado actual de la sesión  */
if (session_status() == PHP_SESSION_NONE) {
    //? si se cumple la condición de no activa se iniciar la sesión
    session_start();
}

//? en el caso de que la sesion este ya iniciada mediante la cookie o mediante un login
//? y vuelve a entrar a registro se renviara al cliente al logout para cerrar sesion
if (isset($_SESSION["id"]) && $_SESSION["login"] === true) {
    header("Location: /php/logout.php");
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
            //! VARIABLES DE SESIÓN AL HACER LOGIN
            $_SESSION["usuario"] = $comprobarDatos; // Guardar el email del usuario
            $_SESSION["id"] = obtenerIdUsuario($comprobarDatos); // Obtener el ID del usuario 
            $_SESSION["login"] = TRUE; //Guardar variable logueado como tu si ha podido hacer log
            $_SESSION["tipo"] = obtenerTipoUsuario($comprobarDatos);
            $_SESSION["nombre"]= obtenerNombreUsuario($_SESSION["id"]);

            //si la cookie carrito esta activa la vuelca en session[matriz] y genera session[numcarrito]
            if(!isset($_SESSION["matriz"])){
                //si exsite la cookie carrito y el usuario esta logueado
                if (isset($_COOKIE['carrito']) && isset($_SESSION['id'])){
                    $cookieCarrito = $_COOKIE['carrito'];
                    $arrayCookie = desmontar1($cookieCarrito);//pasamos de string a array
                    $_SESSION["numCarrito"] = count($arrayCookie);//sacamos las posiciones del array para poner la cantidad de productos en el carrito
                    $_SESSION["matriz"] = desmontar2($arrayCookie);//pasamos de array a matriz e inicializamos la variable de sesion
                }
            }

            //? Guardar la sesión en la cookie para poder iniciar sesión automaticamente más adelante
            //*Pero solo si se ha marcado la opción de recordar si no esta checked no se inicializa la variable POST
            if (isset($_POST["recordar_sesion"])) {
                if ($_POST["recordar_sesion"] === "on") { //on = checked
                    //generamos la cookie de sesion
                    cookieSesion2($_SESSION["id"]);
                }
            }

            //? Una vez el login es correcto va a redidirgir por defecto al index
            //? si llega redirigido de otra pagina se va a volver a esa pagina
            $redirectUrl = !empty($_POST['redirigido']) ? $_POST['redirigido'] : '../index.php';

            //* Redirigir al usuario a la desde la ha sido redirigido antes
            //Ejemplo: si viene desde carrito.php, va a volver a esa página
            header("Location: " . $redirectUrl);
        }
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
                <input type="checkbox" name="recordar_sesion" >
            </div>
            

            <input type="submit" id="enviar" value="LOGIN">
            
        </form>
    </div>
</body>
</html>

