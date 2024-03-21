<?php 
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class pacientes extends conexion{
    private $table = "pacientes";
    private $pacienteId = "";
    private $dni = "";
    private $nombre = "";
    private $direccion = "";
    private $codigoPostal = "";
    private $genero = "";
    private $telefono = "";
    private $fechaNacimiento = "0000-00-00";
    private $correo = "";
    private $imagen ="";

    private $token =""; //d372fea29cd06e069cfa869f753d8c13
    public function listaPacientes($pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1))+1;
            $cantidad = $cantidad * $pagina;
        }

        $query = "SELECT PacienteID, Nombre, DNI, Telefono, Correo FROM ". $this->table ." limit $inicio, $cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }
    public function obtenerPaciente($id){
        $query = "SELECT * FROM ". $this->table ." WHERE PacienteId = '$id'";
        return parent::obtenerDatos($query);
    }
    public function post($json){
        $_respuestas = new respuestas; //Instanciamos la clase respuesta para poder usar los errores que codificamos
        $datos = json_decode($json, true); //Convertirmos la info recibida a un array asociativo
        //Se valida que se recibe el token
        if(!isset($datos['token'])){
            //Retorna el error en caso de que no sea autorizado
            return $_respuestas->error_401();  
        }
        else{
            $this->token = $datos['token']; //Igualamos el token recibido al atributo
            $arrayToken = $this->buscarToken(); //Buscamos el token en la BD
            if($arrayToken){
                if(!isset($datos['nombre']) || !isset($datos['dni']) || !isset($datos['correo'])){//Revisa que la solicitud venga con al menos uno de los siguientes: nombre, dni, correo
                    return $_respuestas->error_400(); //En caso de que no lo incluya devuelve el error
                }
                 else{ //Si existe el token
                    //Los siguientes parámetros son estrictamente necesarios para realizar la inserción
                    $this->nombre = $datos['nombre'];
                    $this->dni = $datos['dni'];
                    $this->correo = $datos['correo'];
                    //Los siguientes parámetros son opcionales
                    if(isset($datos['telefono'])){$this->telefono = $datos['telefono'];}
                    if(isset($datos['direccion'])){$this->direccion = $datos['direccion'];}
                    if(isset($datos['codigoPostal'])){$this->codigoPostal = $datos['codigoPostal'];}
                    if(isset($datos['genero'])){$this->genero = $datos['genero'];}
                    if(isset($datos['fechaNacimiento'])){$this->fechaNacimiento = $datos['fechaNacimiento'];}
                    //https://www.base64-image.de/
                    //
                    if(isset($datos['imagen'])){
                        $resp = $this->procesarImagen($datos['imagen']);
                        $this->imagen = $resp;
                    }

                    $respuesta = $this ->insertarPaciente();
                    if($respuesta){
                        $respuesta= $_respuestas->response;
                        $respuesta["result"] = array(
                            "pacienteId" => $respuesta
                        );
                        return $respuesta;
                    }
                    else{
                        return $_respuestas->error_500();
                    }
            }
        }
            else{
                return $_respuestas->error_401("El token enviado es inválido o ha caducado");  
            }
        }
        
    }
    private function procesarImagen($img){
        $direccion = dirname(__DIR__). "\public\imagenes\\";
        $partes = explode(";base64,",$img);
        $extencion = explode("/", mime_content_type($img)[1]);
        $imagen_base64 = base64_decode($partes[1]);
        $file = $direccion . uniqid() . "." . $extencion;
        file_put_contents($file, $imagen_base64);
        $nuevadireccion = str_replace('\\', '/', $file);


        return $nuevadireccion;

    }
    public function put($json){
        $_respuestas = new respuestas; //Instanciamos la clase respuesta para poder usar los errores que codificamos
        $datos = json_decode($json, true); //Convertirmos la info recibida a un array 
        //Se valida que se recibe el token
        if(!isset($datos['token'])){
            //Retorna el error en caso de que no sea autorizado
            return $_respuestas->error_401();  
        }
        else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['pacienteId'])){//Revisa que la solicitud incluya pacienteId
                    return $_respuestas->error_400(); //En caso de que no lo incluya devuelve el error
                }
                else{
                    $this->pacienteId = $datos['pacienteId'];
                    //En caso de recibir el parámetro en la solicitud, lo actualiza
                    if(isset($datos['nombre'])){$this->nombre = $datos['nombre'];}
                    if(isset($datos['dni'])){$this->dni = $datos['dni'];}
                    if(isset($datos['correo'])){$this->correo = $datos['correo'];}
                    if(isset($datos['telefono'])){$this->telefono = $datos['telefono'];}
                    if(isset($datos['direccion'])){$this->direccion = $datos['direccion'];}
                    if(isset($datos['codigoPostal'])){$this->codigoPostal = $datos['codigoPostal'];}
                    if(isset($datos['genero'])){$this->genero = $datos['genero'];}
                    if(isset($datos['fechaNacimiento'])){$this->fechaNacimiento = $datos['fechaNacimiento'];}
                    $respuesta = $this->modificarPaciente();
                    if($respuesta){
                        $respuesta= $_respuestas->response;
                        $respuesta["result"] = array(
                            "pacienteId" => $this->pacienteId
                        );
                        return $respuesta;
                    }
                    else{
                        return $_respuestas->error_500();
                    }
                }
            }
            else{
                return $_respuestas->error_401("El token enviado es inválido o ha caducado");  
            }
        }
        
    }
    public function delete($json){
        $_respuestas = new respuestas; //Instanciamos la clase respuesta para poder usar los errores que codificamos
        $datos = json_decode($json, true); //Convertirmos la info recibida a un array 
        //Se valida que se recibe el token
        if(!isset($datos['token'])){
            //Retorna el error en caso de que no sea autorizado
            return $_respuestas->error_401();  
        }
        else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['pacienteId'])){//Revisa que la solicitud incluya pacienteId
                    return $_respuestas->error_400(); //En caso de que no lo incluya devuelve el error
                }
                else{
                    $this->pacienteId = $datos['pacienteId'];
        
                    $respuesta = $this->eliminarPaciente();
                    if($respuesta){
                        $respuesta= $_respuestas->response;
                        $respuesta["result"] = array(
                            "pacienteId" => $this->pacienteId
                        );
                        return $respuesta;
                    }
                    else{
                        return $_respuestas->error_500();
                    }
                }
            }
            else{
                return $_respuestas->error_401("El token enviado es inválido o ha caducado");  
            }
        }
        
    }

    //Funciones CRUD de base de datos
    private function insertarPaciente(){
        $query = "INSERT INTO ".$this->table . " (DNI, Nombre, Direccion, CodigoPostal, Telefono, Genero, FechaNacimiento, Correo, Imagen)
        VALUES ('" . $this->dni . "', '" . $this->nombre . "', '" . $this->direccion . "', '" .
         $this->codigoPostal . "', '" . $this->telefono . "', '" . $this->genero . "', '" .
         $this->fechaNacimiento . "', '" . $this->correo . "', '" . $this->imagen . "')";
         $respuesta = parent::nonQueryId($query);
         if($respuesta){
            return $respuesta;
         }
         else{
            return 0;
         }
    }
    private function modificarPaciente(){
        
        $query = "UPDATE ".$this->table . " SET Nombre ='" . $this->nombre . " ',Direccion ='" . $this->direccion
        . "', CodigoPostal ='" . $this->codigoPostal . "', Telefono ='" . $this->telefono . "', Genero ='" . $this->genero
        . "', FechaNacimiento ='" . $this->fechaNacimiento . "', Correo ='" . $this->correo . "' WHERE PacienteId = '" 
        . $this->pacienteId . "'";
        $respuesta = parent::nonQuery($query);
        //var_dump($respuesta);
         if($respuesta >=1){
            return $respuesta;
         }
         else{
            return 0;
         }
    }
    private function eliminarPaciente(){
        $query = "DELETE FROM ". $this->table . " WHERE PacienteId= '" . $this->pacienteId . "'";
        $resp = parent::nonQuery($query);
        if($resp >=1){
            return $resp;
        }
        else{
            return 0;
        }
    }
    
    //Funciones para el token
    private function buscarToken(){ //No recibe parámetros porque vamos a pasarlo haciendo uso de las clases 
        $query = "SELECT TokenId, UsuarioId, Estado from usuarios_token WHERE Token = '".$this->token . "' AND Estado = 'Activo' ";
        $res = parent::obtenerDatos($query); 
        if($res){
            return $res;
        }
        else{
            return 0;
        }
    }
    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:I");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE TokenId = '$tokenid' ";
        $res = parent::nonQuery($query);
        if($res){
            return $res;
        }
        else{
            return 0;
        }
    }
}
?>