<?php
//?- Script de apoyo para las funciones de: 
        //?. registro.php
        //?. cancelarCuenta.php
        //?. actualizarCuenta.php
        //?. recarga.php

/**
 *? comprueba si no hay una sesión activa y si no la hay la inicia
 *? session_status -> devuelve el estado actual de la sesión  */
if (session_status() == PHP_SESSION_NONE) {
    //? si se cumple la condición de no activa se iniciar la sesión
    session_start();
}

//TODO- :::::::::::::::::::::::::::::::::::::: INSERT ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
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
        //?- -en caso de fallo comprobara que el email no es repetido y lanzara el mensaje de error correspondiente
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
            //preparada para comprobar si el email es repetido
            $preparada2 = $db -> prepare("SELECT email, COUNT(*) AS cantidad FROM cliente WHERE email = ?"); 
            $preparada2 -> execute(array($email));
            $datos = $preparada2->fetch();

            if($datos['cantidad'] > 0){
                //en este caso se lanzara el aviso <h1>Email no valido</h1>
                $_SESSION["error_registro2"] = TRUE;
            }else{
                //en este caso se lanzara el aviso <h1>Fallo en el registro pruebe mas tarde</h1>
                $_SESSION["error_registro3"] = TRUE;
            }
        }
    }



//TODO- :::::::::::::::::::::::::::::::::::::: UPDATE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //?- No se usan transacciones porque es preferible que se actualizen todos los datos posibles y que no falle todo por un update
    //?- Identificamos de que formulario vienen los datos mediante una etiqueta oculta en ellos
    //?- Comprobamos que variables no estan vacias y realizamos el update de ellas
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
                                $_SESSION["error_update3"] = TRUE;
                            }else{
                                //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                                $_SESSION["error_update2"] = TRUE;
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
                            $_SESSION["error_update2"] = TRUE;
                        }
                    }
                    
                    if(!empty($_POST['apellidos'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET apellidos = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['apellidos'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update2"] = TRUE;
                        }
                    }
                    
                    if(!empty($_POST['telefono'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET tlf = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['telefono'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update2"] = TRUE;
                        }
                    }
                    
                    if(!empty($_POST['sexo'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET sexo = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['sexo'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update2"] = TRUE;
                        }
                    }

                    if(!empty($_POST['fechaNacimiento'])){
                        //preparada para update del cliente con los datos del post previamente comprobados
                        $preparada1 = $db ->prepare("UPDATE cliente SET fechaNacimiento = ? WHERE id = ?");
                        $resul = $preparada1->execute(array($_POST['fechaNacimiento'], $_SESSION['id']));

                        //si la preparada falla
                        if(!$resul){
                            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                            $_SESSION["error_update2"] = TRUE;
                        }
                    }
                    break;

                    

                // Procesar datos del Formulario 3
                case 'formulario3':
                    // comprobamos que los campos no esten vacios y que $_POST['claveNueva1'] y $_POST['claveNueva2'] sean iguales
                    if(!empty($_POST['clave']) && !empty($_POST['claveNueva1']) && !empty($_POST['claveNueva2']) && ($_POST['claveNueva1'] === $_POST['claveNueva2'])){
                        // preparada para sacar la clave actual del cliente y cotejarla antes del update
                        $preparada2 = $db -> prepare("SELECT clave FROM cliente WHERE id = ?"); 
                        $preparada2 -> execute(array($_SESSION['id']));
                        $datos = $preparada2->fetch();

                        //cotejamos la clave actual
                        if($datos['clave'] === $_POST['clave']){
                            //preparada para update del cliente con los datos del post previamente comprobados
                            $preparada1 = $db ->prepare("UPDATE cliente SET clave = ? WHERE id = ?");
                            $resul = $preparada1->execute(array($_POST['claveNueva1'], $_SESSION['id']));
                        }else{
                            //en este caso se lanzara el aviso <h1>Clave actual erronea</h1>
                            $_SESSION["error_update4"] = TRUE;
                        }
                    }else{
                        //en este caso se lanzara el aviso <h1>Las nuevas claves no coinciden</h1>
                        $_SESSION["error_update5"] = TRUE;
                    }
                    break;

                default:
                //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
                $_SESSION["error_update6"] = TRUE;
            }
        } else {
            //en este caso se lanzara el aviso <h1>Fallo al actualizar pruebe mas tarde</h1>
            $_SESSION["error_update6"] = TRUE;
        }
    }


//TODO- :::::::::::::::::::::::::::::::::::::: DELETE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //?- Se borra tanto la cuenta como el array SESSION y $_COOKIE["session_token"]
    function deleteCliente (){
        //conexion con la base de datos
        $conexion = "mysql:dbname=irjama;host=127.0.0.1";
        $usuario_bd = "root";
        $clave_bd = "";
        $errmode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];
        $db = new PDO($conexion , $usuario_bd, $clave_bd, $errmode);

        //preparada para borrar el cliente con los datos del post previamente comprobados
        $preparada1 = $db ->prepare("DELETE FROM cliente WHERE email = ? AND clave = ?");
        $resul = $preparada1->execute(array($_POST['email'], $_POST['clave']));

        //en caso de borrado exitoso borramos tambien los datos de session existentes o las cookies , mantendremos $_COOKIE["carrito"] por si acaso
        if($resul){
            $_SESSION = []; // Vacia el array $_SESSION asignando un array vacío

            if(isset($_COOKIE["session_token"])){
                setcookie("session_token", 0 , time() - 100);// elimina $_COOKIE["session_token"]
            }
        }
    }

?>