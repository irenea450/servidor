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
        <form action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"]); ?>" method="POST" >
        
            <div class="inputs">
                <label for="usuario"><img src="/img/usuario.png"></label>
                <input id="usuario" name="usuario" placeholder="usuario" type="text" value="<?php if(isset($usuario)) echo $usuario ?>" >
            </div>
            <div class="inputs">
                <label for="clave"><img src="/img/contraseña.png"></label>
                <input name="clave" type="password" placeholder="contraseña">
            </div>

            <input type="submit" id="enviar" value="LOGIN">
        </form>
    </div>
</body>
</html>