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
     * ? Se comprueba que se ha enviado el formulario de registro y si se han introducido todos los datos
     * ? Con los datos introducitos se va a .............................
     * ?   */
    if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email']) && !empty($_POST['clave']) && !empty($_POST['nombre']) && !empty($_POST['apellidos']) && !empty($_POST['direccion']) && !empty($_POST['telefono']) && !empty($_POST['sexo']) && !empty($_POST['fechaNacimiento'])){

        echo $_POST['email'];
        echo $_POST['clave'];
        echo $_POST['nombre'];
        echo $_POST['apellidos'];
        echo $_POST['direccion'];
        echo $_POST['telefono'];
        echo $_POST['sexo'];
        echo $_POST['fechaNacimiento'];

    }else{
        //? En caso de no esten todos los campos rellenos se activa la variable de error 
        $_SESSION["error_registro"] = TRUE;
    }

$clave = "";
$nombre = "";
$apellidos = "";
$email = "";
$direccion = "";
$direccionEnvio = "";
$direccionFacturacion = "";
$telefono = "";
$fechaNacimiento = "";
$sexo = "";
$tipo = "normal";




?>

<!-- HTML-> Formulario y manejo de errores -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_login.css">
</head>
<body>
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor">
        <?php
            //!rectificar la parte del error
            //?En caso de que no esten todos los campos rellenos manda mensaje de error 
            if(isset($_SESSION["error_registro"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "¡¡Debe rellenar todos los campos!!";
                    let contenedor = document.querySelector(".erroresContenedor");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_registro"]); 
            }
                
        ?>

        </div>
        <!-- Formualario de inicio de sesión -->
        <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="POST" >
        
            <div class="inputs2">
                <label for="email">Email</label>
                <input id="email" name="email" placeholder="email" type="text" value="<?php if(isset($usuario)) echo $usuario ?>" >
            </div>

            <div class="inputs2">
                <label for="clave">Contraseña</label>
                <input name="clave" type="password" placeholder="contraseña">
            </div>

            <div class="inputs2">
                <label for="nombre">Nombre</label>
                <input name="nombre" type="text" placeholder="nombre">
            </div>

            <div class="inputs2">
                <label for="apellidos">Apellidos</label>
                <input name="apellidos" type="text" placeholder="apellidos">
            </div>

            <div class="inputs2">
                <label for="direccion">Direccion</label>
                <textarea name="direccion" placeholder="C/falsa, 123, madrid, madrid, 28001" maxlength="100"></textarea>
            </div>

            <div class="inputs2">
                <label for="telefono">Telefono</label>
                <input name="telefono" type="text" placeholder="telefono">
            </div>

            
                <label class="labelRegistro" for="sexo">Sexo:</label>
                <select class="selectRegistro" name="sexo">
                    <option value="hombre">Hombre</option>
                    <option value="mujer">Mujer</option>
                </select>
            
            
            <div class="inputs2">
                <label for="fechaNacimiento">fecha nacimiento</label>
                <input name="fechaNacimiento" type="date" placeholder="fecha nacimiento">
            </div>

            <input type="submit" id="enviar" value="REGISTRO">
            
        </form>
    </div>
</body>
</html>