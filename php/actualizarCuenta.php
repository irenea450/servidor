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




/**
     * ? Se comprueba que se ha enviado el formulario de registro y quedatos se han introducido
     * ? Con los datos introducidos se va a updateCliente() donde se realizara los apdates necesarios
     * ?   */
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //array_filter($_POST): Filtra los valores vacíos ("", NULL, false) y devuelve un array con los que tienen datos.
        if (!empty(array_filter($_POST))) {
            updateCliente();
            //aviso de actualizado correcto
            echo "<script> alert('Datos actualizados correctamente.'); </script>";
        } else {
            //? En caso de no esten todos los campos rellenos se activa la variable de error 
        $_SESSION["error_update6"] = TRUE;
        }
    }

    //?- Leyenda de errores de update
        /* 
        form1 - 1 <h1>Fallo al actualizar pruebe mas tarde</h1>
        form2 - 2 <h1>Fallo al actualizar pruebe mas tarde</h1>
                3 <h1>Email no valido</h1>
        form3 - 4 <h1>Clave actual erronea</h1>
                5 <h1>Las nuevas claves no coinciden</h1>
        html  - 6 <h1>Fallo al actualizar pruebe mas tarde</h1>
        */

    //? Botón retorno al área personal
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atras'])) {
        //* Si se pulsa volver atrás, te llevará al area personal del usuario
        header("Location: areaPersonal.php");
    }
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
</head>
<body>
    <div class="erroresContenedor0">
    <?php
        //?En caso de que haya un error en la lectura del input tipo hidden 
        if(isset($_SESSION["error_update6"])){
            //* En javascript se inserta el mensaje de error
            echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
            <script>
                // Seleccionar elementos correctamente
                let mensaje = "Fallo al actualizar, pruebe mas tarde";
                let contenedor = document.querySelector(".erroresContenedor0");

                // Mostrar mensaja en el contenedor en caso de error
                contenedor.innerHTML = mensaje;
            </script>';
            // Eliminar el error después de mostrarlo
            unset($_SESSION["error_update6"]); 
        }
    ?>
    </div>

    <!-- FORMULARIO1--------------------------------------------------------------- para el cambio de direcciones -->
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <h2>Direcciones</h2>
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor1">
        <?php
            //?En caso de que haya un error en el update 
            if(isset($_SESSION["error_update1"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Fallo al actualizar, pruebe mas tarde";
                    let contenedor = document.querySelector(".erroresContenedor1");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_update1"]); 
            }
        ?>

        </div>
        <!-- Formualario de actualizado -->
        <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="POST" >
        
            <!-- input invisible con el que se reconoce el formulario que envia la info -->
            <input type="hidden" name="formulario" value="formulario1">

            <div class="inputs2">
                <label for="direccion">Envio</label>
                <textarea name="direccion" placeholder="C/falsa, 123, madrid, madrid, 28001" maxlength="100"></textarea>
            </div>

            <div class="inputs2">
                <label for="direccionFac">Facturacion</label>
                <textarea name="direccionFac" placeholder="C/falsa, 123, madrid, madrid, 28001" maxlength="100"></textarea>
            </div>

            <input type="submit" id="enviar" value="ACTUALIZAR">
            
        </form>
    </div>



    <!-- FORMULARIO2--------------------------------------------------------------- para el cambio de datos -->
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <h2>Datos personales</h2>
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor2">
        <?php
            //?En caso de que haya un error en el update 
            if(isset($_SESSION["error_update2"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Fallo al actualizar, pruebe mas tarde";
                    let contenedor = document.querySelector(".erroresContenedor2");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_update2"]); 
            }
            //?En caso de que el email nuevo no sea valido 
            if(isset($_SESSION["error_update3"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Email no valido";
                    let contenedor = document.querySelector(".erroresContenedor2");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_update3"]); 
            }
        ?>

        </div>
        <!-- Formualario de actualizado -->
        <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="POST" >
        
            <!-- input invisible con el que se reconoce el formulario que envia la info -->
            <input type="hidden" name="formulario" value="formulario2">

            <div class="inputs2">
                <label for="email">Email</label>
                <input id="email" name="email" placeholder="email" type="text" value="<?php if(isset($usuario)) echo $usuario ?>" >
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
                <label for="telefono">Telefono</label>
                <input name="telefono" type="text" placeholder="telefono">
            </div>

                <label class="labelRegistro" for="sexo">Sexo:</label>
                <select class="selectRegistro" name="sexo">
                    <option selected='selected' value=>Sexo</option>
                    <option value="hombre">Hombre</option>
                    <option value="mujer">Mujer</option>
                </select>
            
            <div class="inputs2">
                <label for="fechaNacimiento">fecha nacimiento</label>
                <input name="fechaNacimiento" type="date" placeholder="fecha nacimiento">
            </div>

            <input type="submit" id="enviar" value="ACTUALIZAR">
            
        </form>
    </div>




    <!-- FORMULARIO3--------------------------------------------------------------- para el cambio de contraseña -->
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <h2>Cambio de contraseña</h2>
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor3">
        <?php
            //?En caso de que no esten todos los campos rellenos manda mensaje de error 
            if(isset($_SESSION["error_update4"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Clave actual erronea";
                    let contenedor = document.querySelector(".erroresContenedor3");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_update4"]); 
            }
            //?En caso de que no esten todos los campos rellenos manda mensaje de error 
            if(isset($_SESSION["error_update5"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Las nuevas claves no coinciden";
                    let contenedor = document.querySelector(".erroresContenedor3");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_update5"]); 
            }
        ?>

        </div>
        <!-- Formualario de actualizado -->
        <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="POST" >

            <!-- input invisible con el que se reconoce el formulario que envia la info -->
            <input type="hidden" name="formulario" value="formulario3">

            <div class="inputs2">
                <label for="clave">Contraseña</label>
                <input name="clave" type="password" placeholder="contraseña actual">
            </div>

            <div class="inputs2">
                <label for="claveNueva1">Contraseña</label>
                <input name="claveNueva1" type="password" placeholder="nueva contraseña">
            </div>

            <div class="inputs2">
                <label for="claveNueva2">Contraseña</label>
                <input name="claveNueva2" type="password" placeholder="repita nueva contraseña">
            </div>

            <input type="submit" id="enviar" value="ACTUALIZAR">
            
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