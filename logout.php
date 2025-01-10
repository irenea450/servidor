<?php
    session_start(); // El script se une a la sesión

    /* Para cerrar la sesión es necesario borrar todas las variables de la sesión, para ello se inicializa el array $_SESSION: */
    $_SESSION = array();

    /* Además, se debe utilizar la función session_destroy(): */
    session_destroy();

    /* Por último, se debe de eliminar la cookie: */
    setcookie(session_name(), 123, time() - 1000); // session_name devuelve el nombre de la sesión actual.

    /* Finalmente el script lleva de al login */
    header("Location: ../login.php");
?>