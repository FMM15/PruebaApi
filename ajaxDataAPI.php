<?php 
/*require("../includes/session.php");
require_once("../includes/conexion_db.php");
require_once("../includes/functions.php");*/

if(isset($_POST["pagRef"])){
	//Guardamos la pagina de referencia en la variable 
	$paginaRefe = $_POST["pagRef"];
	
	/*==========================================================================================
	================PROYECTOS
	===========================================================================================*/
	if($paginaRefe == "consultarAPI"){	
		
			$curl = curl_init();
		   //url: 'https://api.allorigins.win/get?url=https://api.hacienda.go.cr/fe/ae?identificacion='+cedula,
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://cedesvirtual.com/test_api/pacientes.php?page=1",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => false,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				$tipoMensaje = 0;
				$mensaje = "No pudimos comprobar la información Error: " .$err;		  
			} else {
				$tipoMensaje = 1;
				$dataArray = json_decode($response, true);
				print_r($dataArray);
				//$mensaje = $dataArray["nombre"];	
			}
			
	}//end si la cedula existe
	if($paginaRefe == "usarAPI"){	
		$nombre = (filter_var($_POST["nombre"], FILTER_SANITIZE_STRING));
		$correo = (filter_var($_POST["correo"], FILTER_SANITIZE_STRING));
		$dni = (filter_var($_POST["dni"], FILTER_SANITIZE_STRING));
		$token = (filter_var($_POST["token"], FILTER_SANITIZE_STRING));
		$data = array(
			'nombre' => $nombre,
			'dni' => $dni,
			'correo' => $correo,
			'token' => $token
		);
		$json_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://cedesvirtual.com/test_api/pacientes.php");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json_data))
		);

		$response = curl_exec($curl);

		if(curl_errno($curl)){
			echo 'Error: ' . curl_error($curl);
		}
		
		curl_close($curl);
		
		echo json_encode($response);
		
}			
		
} //end de pagRef

	 
?>