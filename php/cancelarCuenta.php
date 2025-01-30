<?php
//scripts que vamos a necesitar
require 'funcionesInsUpdDel.php';

/**
 *? comprueba si no hay una sesión activa y si no la hay la inicia
 *? session_status -> devuelve el estado actual de la sesión  */
if (session_status() == PHP_SESSION_NONE) {
    //? si se cumple la condición de no activa se iniciar la sesión
    session_start();
}

//$_SESSION['usuario'] = "ana@gmail.com"; //!- necesario $_SESSION['usuario'] inicializado


/**
     * ? Se comprueba que se ha enviado el formulario de borrado
     * ? Se comprueba si se han introducido todos los datos
     * ? Se comprueba que las contraseñas no coincidan
     * ? ? Se comprueba si $_SESSION['usuario'] coincide o no con el email del post
     * ? Con los datos introducidos se va a deleteCliente() donde se realizara el delete
     * ?   */
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!empty($_POST['email']) && ($_POST['email'] === $_SESSION['usuario']) && !empty($_POST['clave']) && !empty($_POST['clave2']) && ($_POST['clave'] === $_POST['clave2'])){
            deleteCliente();
        }else{
            //? En caso de no esten todos los campos rellenos se activa la variable de error 
            $_SESSION["error_deleteCuenta"] = TRUE;
        }
    }

    //? Botón retorno al área personal
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atras'])) {
        //* Si se pulsa volver atrás, te llevará al area personal del usuario
        header("Location: ../index.php");
    }
?>


<!-- HTML-> Formulario y manejo de errores -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar cuenta</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_login.css">
</head>
<body>
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <h2>Cancelar cuenta</h2>
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor">
        <?php
            //?En caso de que la contarseña o el usuario no coincidan manda mensaje de error
            if(isset($_SESSION["error_deleteCuenta"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Usuario o contraseña incorrecto";
                    let contenedor = document.querySelector(".erroresContenedor");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_deleteCuenta"]); 
            }
                
        ?>

        </div>
        <!-- Formualario para cancelar la cuenta -->
        <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="POST" >
            <!-- Campo oculto para poder hacer la redirección -->
        <input type="hidden" name="redirigido" value="<?php echo htmlspecialchars($_GET['redirigido'] ?? ''); ?>">

            <div class="inputs2">
                <label for="email"><img src="/img/usuario.png"></label>
                <input id="email" name="email" placeholder="email" type="text" value="<?php if(isset($usuario)) echo $usuario ?>" >
            </div>
            <div class="inputs2">
                <label for="clave"><img src="/img/contraseña.png"></label>
                <input name="clave" type="password" placeholder="contraseña">
            </div>
            <div class="inputs2">
                <label for="clave2"><img src="/img/contraseña.png"></label>
                <input name="clave2" type="password" placeholder="repita la contraseña">
            </div>

            <input type="submit" id="enviar" value="ACEPTAR">
            
        </form>
    </div>


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

