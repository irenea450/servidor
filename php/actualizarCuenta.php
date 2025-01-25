<?php
//!- FALTA EL BOTON DE RETORNO
//!- VOY POR la linea 157

/**
 *? comprueba si no hay una sesión activa y si no la hay la inicia
 *? session_status -> devuelve el estado actual de la sesión  */
if (session_status() == PHP_SESSION_NONE) {
    //? si se cumple la condición de no activa se iniciar la sesión
    session_start();
}

$_SESSION['id'] = 1; //!- variable de prueba para pasar los controles----BORRAR

/**
     * ? Se comprueba que se ha enviado el formulario de registro y quedatos se han introducido
     * ? Con los datos introducidos se va a updateCliente() donde se realizara los apdates necesarios
     * ?   */
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        updateCliente();
    }else{
        //? En caso de no esten todos los campos rellenos se activa la variable de error 
        $_SESSION["error_registro1"] = TRUE;
    }


    function updateCliente(){
        //conexion con la base de datos
        $conexion = "mysql:dbname=irjama;host=127.0.0.1";
        $usuario_bd = "root";
        $clave_bd = "";
        $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
        $db = new PDO($conexion , $usuario_bd, $clave_bd, $errmode);

        //variable de control
        $resul = false;

        // Identificar de qué formulario proviene la información
        if (isset($_POST['formulario'])) {
            switch ($_POST['formulario']) {
                // Procesar datos del Formulario 1
                case 'formulario1':
                    // Buscamos las variables post que no esten vacias y realizamos los updates
                    if(!empty($_POST['direccion'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET direccionEnvio = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['direccion'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update1"] = TRUE;
                        }
                    }

                    if(!empty($_POST['direccionFac'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET direccionFacturacion = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['direccionFac'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update1"] = TRUE;
                        }
                    }
                    break;

                // Procesar datos del Formulario 2
                case 'formulario2':
                    // Buscamos las variables post que no esten vacias y realizamos los updates
                    if(!empty($_POST['email'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET email = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['email'], $_SESSION['id']));

                        //si el update es correcto se actualiza la variable de sesion
                        if($resul){
                            $_SESSION['usuario'] = $_POST['email'];
                        }else{
                            //preparada para comprobar si el email es repetido
                            $preparada2 = $db -> prepare("SELECT email, COUNT(*) AS cantidad FROM cliente WHERE email = ?"); 
                            $preparada2 -> execute(array($_POST['email']));
                            $datos = $preparada2->fetch();

                            //si el email ya esta en la base de datos
                            if($datos['cantidad'] > 0){
                                //en este caso se lanzara el aviso <h1>Email no valido</h1>
                                $_SESSION["error_update2"] = TRUE;
                            }else{
                                //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                                $_SESSION["error_update1"] = TRUE;
                            }
                        }
                    }

                    if(!empty($_POST['nombre'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET nombre = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['nombre'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update1"] = TRUE;
                        }
                    }
                    
                    if(!empty($_POST['apellidos'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET apellidos = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['apellidos'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update1"] = TRUE;
                        }
                    }
                    
                    if(!empty($_POST['telefono'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET tlf = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['telefono'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update1"] = TRUE;
                        }
                    }
                    
                    if(!empty($_POST['sexo'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET sexo = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['sexo'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update1"] = TRUE;
                        }
                    }

                    if(!empty($_POST['fechaNacimiento'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET fechaNacimiento = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['fechaNacimiento'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update1"] = TRUE;
                        }
                    }
                    break;

                    //!- VOY POR AQUI HAY QUE RECTIFICAR E INTRODUCIR LOS DIVS DE ERROR EN EL HTML
                    /* //en este caso se lanzara el aviso <h1>Email no valido</h1>
                    $_SESSION["error_update2"] = TRUE; formulario 2
                    //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                    $_SESSION["error_update1"] = TRUE; formulario 1,2 y 3 */ 
                    //!

                // Procesar datos del Formulario 3
                case 'formulario3':
                    // Buscamos las variables post que no esten vacias y realizamos los updates
                    if(!empty($_POST['direccion'])){
                        //guardo la info
                        $dir = $_POST['direccion']; //guardo la info

                        $preparada1 = $db ->prepare("UPDATE cliente SET direccionEnvio = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($dir, $_SESSION['id']));
                    }

                    if(!empty($_POST['direccionFac'])){
                        //guardo la info
                        $dir = $_POST['direccionFac']; //guardo la info

                        $preparada1 = $db ->prepare("UPDATE cliente SET direccionFacturacion = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($dir, $_SESSION['id']));
                    }
                    echo "Datos del Formulario 3";
                    break;

                default:
                    echo "Formulario no identificado.";
            }
        } else {
            echo "No se envió información del formulario.";
        }

        echo $resul;
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
    <!-- formulario para el cambio de direcciones -->
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <h2>Direcciones</h2>
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor">
        <?php
            //?En caso de que no esten todos los campos rellenos manda mensaje de error 
            if(isset($_SESSION["error_registro1"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Fallo al actualizar, pruebe mas tarde";
                    let contenedor = document.querySelector(".erroresContenedor");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_registro1"]); 
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



    <!-- formulario para el cambio de datos -->
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <h2>Datos personales</h2>
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor">
        <?php
            //?En caso de que no esten todos los campos rellenos manda mensaje de error 
            if(isset($_SESSION["error_registro1"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Fallo al actualizar, pruebe mas tarde";
                    let contenedor = document.querySelector(".erroresContenedor");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_registro1"]); 
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




    <!-- formulario para el cambio de contraseña -->
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <h2>Cambio de contraseña</h2>
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor">
        <?php
            //?En caso de que no esten todos los campos rellenos manda mensaje de error 
            if(isset($_SESSION["error_registro1"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Fallo al actualizar, pruebe mas tarde";
                    let contenedor = document.querySelector(".erroresContenedor");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_registro1"]); 
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
</body>
</html>