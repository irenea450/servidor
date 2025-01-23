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
     * ? Con los datos introducitos se va a addCliente() donde se realizara el insert
     * ?   */
    if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email']) && !empty($_POST['clave']) && !empty($_POST['nombre']) && !empty($_POST['apellidos']) && !empty($_POST['direccion']) && !empty($_POST['telefono']) && !empty($_POST['sexo']) && !empty($_POST['fechaNacimiento'])){
        addCliente();
    }else{
        //? En caso de no esten todos los campos rellenos se activa la variable de error 
        $_SESSION["error_registro"] = TRUE;
    }

    //?- conectamos con la base de datos y procedemos a realizar el insert del nuevo cliente 
    function addCliente(){
        //conexion con la base de datos
        $conexion = "mysql:dbname=irjama;host=127.0.0.1";
        $usuario_bd = "root";
        $clave_bd = "";
        $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
        $db = new PDO($conexion , $usuario_bd, $clave_bd, $errmode);

        //introduzco los valores de $_POST en variables para simplificar comillas en la query
        $clave = $_POST['clave'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['email'];
        $direccionEnvio = $_POST['direccion'];
        $direccionFacturacion = $_POST['direccion'];
        $tlf = $_POST['telefono'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $sexo = $_POST['sexo'];
        $tipo = "normal";

        //* La preparada da error y optamos por una query
        /* $preparada1 = $db ->prepare("INSERT INTO cliente (clave, nombre, apellidos, email, direccionEnvio, direccionFacturacion, tlf, fechaNacimiento, sexo, tipo) 
                                    VALUES ('?', '?', '?', '?', '?', '?', '?', '?', '?', '?')");
        $array = array($_POST['clave'], $_POST['nombre'], $_POST['apellidos'], $_POST['email'], $_POST['direccion'], $_POST['direccion'], $_POST['telefono'], $_POST['fechaNacimiento'], $_POST['sexo'], $tipo); */
        
        $ins1 =  "INSERT INTO cliente (clave, nombre, apellidos, email, direccionEnvio, direccionFacturacion, tlf, fechaNacimiento, sexo, tipo) 
                                    VALUES ('$clave', '$nombre', '$apellidos', '$email', '$direccionEnvio', '$direccionFacturacion', '$tlf', '$fechaNacimiento', '$sexo', '$tipo')";
        $resul = $db->query($ins1);

        //?- si resul es true, es decir el insert a sido correcto 
        //?- -sacamos el id del nuevo cliente
        //?- -inicializamos las variables de sesion
        //?- -redirigimos al index
        //?- -en caso de fallo lanzara un mensaje de aviso
        if($resul){
            //preparada para sacar el id del nuevo cliente
            $preparada1 = $db ->prepare("SELECT id FROM cliente WHERE email = ? AND clave = ?");
            $preparada1->execute(array($email, $clave));

            $usu = $preparada1->fetch();

            //inicio variables de sesion
            $_SESSION["id"] = $usu["id"];
            $_SESSION["usuario"] = $email;
            $_SESSION["logeado"] = true;

            //redirigimos al index para empezar a comprar
            header("Location: ../index.php");
        }else{
            echo "<h1>Fallo en el registro pruebe mas tarde</h1>";
        }
}

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