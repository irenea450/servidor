<?php
/* session_start(); // iniciar sesión */

    // Usamos el espacio de nombres PHPMailer\PHPMailer\PHPMailer para acceder a la clase PHPMailer.
    use PHPMailer\PHPMailer\PHPMailer;

    //! Pruebas
    /* echo $_SESSION['emailUsuario']; */

    //!- BORRAR CUANDO ESTE TESTEADO (VARIABLES DE PRUEBA)
/*     echo "Envio de correo en curso";
    $email = "culebras.jr@gmail.com";
    $pedido = "1289";
    mailPedido($email, $pedido); */


    function mailPedido($email, $numPedido){
        // Incluimos el autoloader de Composer para cargar automáticamente las clases necesarias.
        require "vendor/autoload.php";

        // Creamos una nueva instancia de PHPMailer.
        $mail = new PHPMailer();
        // Configuramos el uso de SMTP (Simple Mail Transfer Protocol) para enviar el correo.
        $mail->isSMTP();
        
        // Definimos el nivel de depuración (0 para no mostrar información de depuración).
        $mail->SMTPDebug = 0;
        // Activamos la autenticación SMTP para el envío.
        $mail->SMTPAuth = true;
        // Usamos la encriptación TLS (Transport Layer Security) para la conexión SMTP.
        $mail->SMTPSecure = "tls";
        // Definimos el servidor SMTP de Gmail.
        $mail->Host = "smtp.gmail.com";
        // Especificamos el puerto del servidor SMTP de Gmail (587 es el puerto estándar para TLS).
        $mail->Port = 587;

        // Configuramos las credenciales de Gmail para la autenticación.
        $mail->Username = "irenedelalamo.alumno@gmail.com"; // Dirección de correo desde donde se envía.
        $mail->Password = "uohl zjup gtxs ghup"; // Contraseña generada por la verificación en dos pasos (Google App Password).

        // Configuramos la dirección del remitente.
        // El segundo argumento es el nombre que aparecerá para el destinatario.
        $mail->setFrom($email, "Irjama"); 

        // Asignamos un asunto al correo.
        $mail->Subject = "Pedido: ".$numPedido;

        // Ruta de la imagen local
        $image_path = __DIR__ . "/../../img/LOGO 2.png"; // Cambia 'imagen.jpg' al nombre de tu archivo

        // Convertir la imagen a Base64
        $image_data = base64_encode(file_get_contents($image_path));
        $image_mime = mime_content_type($image_path); // Obtener el MIME type de la imagen

        // Crear la etiqueta <img> con la imagen incrustada
        $image_base64 = "data:$image_mime;base64,$image_data";

        // Definimos el cuerpo del mensaje en formato HTML.
        $mail->msgHTML("
                        <html>
                        <head>
                            <title>Correo con Imagen</title>
                        </head>
                        <body>
                            <div class='cuerpo'></div>
                            <h1>Pedido con referencia: $numPedido realizado con exito.</h1>
                            <p>No responda a este correo, desde Irjama le agradecemos su confianza.</p>
                            <img src='$image_base64' alt='Descripción de la imagen' style='max-width: 100%; height: auto;'>
                        </body>
                        </html>
                        ");

        // Adjuntamos un archivo al correo. Aquí se especifica la ruta completa del archivo adjunto.
        $mail->addAttachment("");//archivo adjunto que envias 
        
        // Definimos la dirección del destinatario.
        // El primer argumento es la dirección de correo del destinatario.
        // El segundo argumento puede ser el nombre del destinatario (en este caso está vacío).
        $mail->addAddress($email, ""); 

        // Enviamos el correo y guardamos el resultado (true o false).
        $resul = $mail->send();

        // Verificamos si hubo algún error en el envío.
        if(!$resul){
            // Si ocurre un error, se muestra un mensaje con la información del error
            echo "<br><br>Error: " . $mail->ErrorInfo . "<br><br>";
        }else{
            // Si el envío fue exitoso, se muestra un mensaje de éxito.
            /* //! mensaje enviado enviado 
            echo "Enviado"; */
        }
    }

?>