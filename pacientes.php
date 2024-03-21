

<?php 
require_once 'clases/pacientes.class.php';
require_once 'clases/respuestas.class.php';

$_respuestas = new respuestas;
$_pacientes = new pacientes;

//Obtiene la informacion de los pacientes
if($_SERVER['REQUEST_METHOD'] == "GET"){
    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaPacientes = $_pacientes->listaPacientes($pagina);
        echo json_encode($listaPacientes);
    }
    else if(isset($_GET['id'])){
        $pacienteId = $_GET['id'];
        $datosPaciente = $_pacientes->obtenerPaciente($pacienteId);
        echo json_encode($datosPaciente);
    }
    $_pacientes->listaPacientes(2);
}
else if($_SERVER['REQUEST_METHOD'] == "POST"){
    
    //Recibimos los datos enviados del cuerpo HTTP
   $postBody = file_get_contents("php://input");
    //Enviamos los datos al manejador
    $resp = $_pacientes->post($postBody);
    //Devolvemos una respuesta
    header('Content-Type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    } else{
        http_response_code(200);
    }
    //echo json_encode($datosArray);
    print_r($resp);
}
else if($_SERVER['REQUEST_METHOD'] == "PUT"){
    //Recibimos los datos enviados
   $postBody = file_get_contents("php://input");
   //Enviamos los datos al manejador
   $resp = $_pacientes->put($postBody);
   //Devolvemos una respuesta
   header('Content-Type: application/json');
   if(isset($datosArray["result"]["error_id"])){
       $responseCode = $datosArray["result"]["error_id"];
       http_response_code($responseCode);
   } else{
       http_response_code(200);
   }
   //echo json_encode($datosArray);
   print_r($resp);
    
}
else if($_SERVER['REQUEST_METHOD'] == "DELETE"){

    //Acá se pueden obtener tanto los datos por el header como por el body
    $headers = getallheaders();
    if(isset($headers["token"]) && isset($headers["pacienteId"])){
        //Recibimos los datos enviados por el header
        $send = [
            "token" => $headers["token"],
            "pacienteId" => $headers["pacienteId"]
        ];
        $postBody = json_encode($send);
    }else{
        //Recibimos los datos enviados
        $postBody = file_get_contents("php://input");
    }
    
   //Enviamos los datos al manejador
   $resp = $_pacientes->delete($postBody);
   //Devolvemos una respuesta
   header('Content-Type: application/json');
   if(isset($datosArray["result"]["error_id"])){
       $responseCode = $datosArray["result"]["error_id"];
       http_response_code($responseCode);
   } else{
       http_response_code(200);
   }
   //echo json_encode($datosArray);
   print_r($resp);
}
else{
    header('Content-type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
?>