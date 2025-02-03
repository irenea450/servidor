<?php
//?- Esta pagina sirve para evitar problemas al refrescar paginas con formularios , que no se vuelva a reenviar al refrescar

            //? si llega redirigido de otra pagina se va a volver a esa pagina
            $redirectUrl = !empty($_GET['redirigido']) ? $_GET['redirigido'] : '../index.php';

            //* Redirigir al usuario a la desde la ha sido redirigido antes
            //Ejemplo: si viene desde carrito.php, va a volver a esa página
            header("Location: " . $redirectUrl);
            //echo $redirectUrl; //!borrar


?>