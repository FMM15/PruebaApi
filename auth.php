<?php
require_once 'clases/auth.class.php';
require_once 'clases/respuestas.class.php';

$_auth = new auth;
$_respuestas = new respuestas; #El guion bajo indica lanomenclatura de que es una instancia de una clase 

//El método POST es más seguro que el GET al no enviar la contraseña ni usuario porque sería más fácil que sea expuesto

if($_SERVER['REQUEST_METHOD']== "POST"){
    //Recibir datos
    $postBody = file_get_contents("php://input");
    //Enviamos los datos al manejador
    $datosArray = $_auth->login($postBody);
    //Devolvemos una respuesta 
    header('Content-type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code( $responseCode );
    }
    else{
        http_response_code( 200 );
    }
    echo json_encode($datosArray);
    //print_r(json_encode($datosArray));
}
else{
    header('Content-type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);


}





?>