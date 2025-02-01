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
     * ? Se comprueba que se ha enviado el formulario de registro y si se han introducido todos los datos
     * ? Con los datos introducitos se va a recargarSaldo() donde se realizara el insert de recarga
     * ? En un futuro se cotejaran los datos de la tarjeta de credito
     * ! Actualmete la recarga es simulada
     *  */
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!empty($_POST['num']) && !empty($_POST['cvv']) && !empty($_POST['fecha']) && !empty($_POST['saldo'])){
            recargarSaldo();
            //aviso de recarga correcta
            echo "<script> alert('Recarga realizada con exito.'); </script>";
        }else{
            //? En caso de no esten todos los campos rellenos se activa la variable de error 
            $_SESSION["error_recarga"] = TRUE;
        }
    }


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
    <title>Recarga</title>
    <link rel="stylesheet" href="/css/estilos_principales.css">
    <link rel="stylesheet" href="/css/estilos_login.css">
</head>
<body>
    <div class="contenedor2">
        <img id="icono-login" src="/img/login_icono.png">
        <h2>Recarga de saldo</h2>
        <!-- Contenedor donde se van a mostrar los errores -->
        <div class="erroresContenedor">
        <?php
            //?En caso de que no esten todos los campos rellenos manda mensaje de error 
            if(isset($_SESSION["error_recarga"])){
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
                unset($_SESSION["error_recarga"]); 
            }
            //?En caso de que falle el update manda mensaje de error 
            if(isset($_SESSION["error_recarga"])){
                //* En javascript se inserta el mensaje de error
                echo '<!-- uso de js para introdcuir el mensaje donde queremos del login -->
                <script>
                    // Seleccionar elementos correctamente
                    let mensaje = "Fallo en la recarga, pruebe mas tarde";
                    let contenedor = document.querySelector(".erroresContenedor");

                    // Mostrar mensaja en el contenedor en caso de error
                    contenedor.innerHTML = mensaje;
                </script>';
                // Eliminar el error después de mostrarlo
                unset($_SESSION["error_recarga1"]); 
            }
        ?>

        </div>
        <!-- Formualario de registro -->
        <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="POST" >
        <!-- Para redirigir  -->
        <input type="hidden" name="redirigido" value="<?php echo isset($_GET['redirigido']) ? htmlspecialchars($_GET['redirigido']) : ''; ?>">

        
            <div class="inputs2">
                <label for="num">Numero tarjeta</label>
                <input id="num" name="num" placeholder="Numero de tarjeta" type="text">
            </div>

            <div class="inputs2">
                <label for="cvv">CVV</label>
                <input name="cvv" type="text" placeholder="cvv">
            </div>

            <div class="inputs2">
                <label for="fecha">Fecha caducidad</label>
                <input name="fecha" type="date" placeholder="Fecha de caducidad">
            </div>

            <div class="inputs2">
                <label for="saldo">Saldo a ingresar</label>
                <input name="saldo" type="text" placeholder="Saldo a ingresar">
            </div>

            <input type="submit" id="enviar" value="RECARGAR">
            
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