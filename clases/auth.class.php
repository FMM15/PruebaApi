<?php

require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class auth extends conexion{ #Mediante el extends aplico la herencia, de manera que puedo usar todos los métodos de conexión en público

    public function login($json){
        $_respuestas = new respuestas; #Instanciamos la clase respuestas
        $datos = json_decode($json, true);#Convertirmos el json que recibimos como parámetro a un array
        if(!isset($datos['usuario']) || !isset($datos['password'])){ #Si encuentra un error con esos respectivos campos
            return $_respuestas->error_400();
        } 
        else{
            $usuario = $datos['usuario'];
            $password = $datos['password']; //Hay que encriptarla para poder compararla con la de la BD
            $password = parent::encriptar($password); //Cuando uso la palabra parent es porque la clase de la que hereda tiene ese método encriptar
            $datos = $this->obtenerDatosUsuario($usuario); 
            if($datos){ //Verificar si la contraseña es igual a la de la BD
                if($password == $datos[0]['Password']){
                    if($datos[0]['Estado']=="Activo"){ //Comprobar si el usuario está activo
                       $verificar = $this->insertarToken( $datos[0]['UsuarioId']);
                       if($verificar){ //Si se guardó
                        $result = $_respuestas->response;
                        $result["result"] = array(
                            '$token' => $verificar
                        );
                        return $result;
                       }
                       else{ //Error al guardar
                        return $_respuestas->error_500("Error interno, no hemos podido guardar");
                       }
                    }
                    else{ //El usuario está inactivo
                        return $_respuestas->error_200("El usuario est&aacute; inactivo");
                    }
                }
                else{
                    return $_respuestas->error_200("El password es incorrecto");
                }

            }
            else{ //No existe el usuario
                return $_respuestas->error_200("El usuario  $usuario no existe");

            }
        }
    }


    private function obtenerDatosUsuario($correo){
        $query = "SELECT UsuarioId, Password, Estado FROM usuarios WHERE Usuario = '$correo'";
        $datos = parent::obtenerDatos($query);
        if(isset($datos[0]["UsuarioId"])){
            return $datos;
        }
        else{
            return 0;
        }
    }

    private function insertarToken($usuarioId){
        $val = true;
        //bin2hex Nos devuelve un string hexadecimal 
        //openssl_random_pseudo_bytes Nos genera una cadena de bytes pseudoaleatoria, recibe la cantidad de bytes
        $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
        $date = date("Y-m-d H:i");
        $estado = "Activo";
        $query = "INSERT INTO usuarios_token (UsuarioId, Token, Estado, Fecha) VALUES ('$usuarioId', '$token', '$estado', '$date')";
        $verificar = parent::nonQuery($query);
        if ($verificar){
            return $token;
        }
        else{
            return 0;
        }
    }

}



?>