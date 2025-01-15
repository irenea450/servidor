<?php
//?- una vez realizada la compra la cookie "carrito" debera ser destruida
//?- al arrancar la aplicacion se ha de comprobar si $_COOKIE["carrito"] esta inicializada en caso afirmativo 
//?- la enviaremos a la funcion desmontar1() y desmontar2() donde se transformara en una matriz
//?- Cada vez que añadamos un producto al carrito este se añadira a la mtriz y se sobreescribira la cookie

    //todo - FUNCION PARA CREAR O ACTUALIZAR $_COOKIE["carrito"]
    function cookieCarrito($matriz){
        //inicializamos variable con la duracion de la cookie
        $time = time() + (7 * 24 * 60 * 60);

        //variable vacia para generar el string del carrito
        $info = "";
        // Mandar la matriz a la cookie
        foreach ($matriz as $fila) {
            // Si $info no está vacío, añadimos un asterisco antes de concatenar para separar cada producto
            if (!empty($info)) {
                $info .= "*";
            }
            //concatenamos la referencia del producto + la cantidad de articulos como texto y separados por una coma
            $info .= $fila["ref"] . "," . $fila["cantidad"];
        }

        try{
            //creamos la cookie "carrito" con el valor de $info y una duracion de una semana
            //en caso de error se lanzara un error
            if(!setcookie("carrito", $info, $time)){
                throw new Exception("Error al establecer la cookie");
            }
        
        }  catch (Exception $e) {
            // Manejo del error
            echo "No ha sido posible guardar el carrito.";
        }
        
    }

    /*     //todo- prueba cookie carrito
    // Crear una matriz con 5 filas y 2 columnas
    $matriz = [
        ["ref" => 101, "cantidad" => 2],
        ["ref" => 102, "cantidad" => 5],
        ["ref" => 103, "cantidad" => 8],
        ["ref" => 104, "cantidad" => 3],
        ["ref" => 1030, "cantidad" => 80],
        ["ref" => 105, "cantidad" => 6]
    ];

    cookieCarrito($matriz);

    // El array cookies no se actualiza hasta la siguiente vez que corres el script 
    //por eso la primera vez no plotea el valor de la cookie y al actualizar va desfasado en uno

    // Verifica si la cookie existe
    if (isset($_COOKIE['carrito'])) {
        // Muestra el valor de la cookie
        echo "El valor de la cookie es: " . $_COOKIE['carrito'];
    } else {
        echo "La cookie no está definida.";
    }
 */
    //borrar cookie
    //setcookie("carrito", 1, time() -100);
?>