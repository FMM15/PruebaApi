<?php
class conexion {
    #Atributos de la clase, son los mismos del documento config
   private $server;
   private $user;
   private $password;
   private $database;
   private $port;
   private $conexion;

   #Primera función que se ejecuta por defecto, 
   function __construct(){
        $listadatos = $this->datosConexion();
        #Recorremos el array para asignarle los valores correspondientes de config a los atributos de la clase
        foreach ($listadatos as $key => $value){
            $this->server = $value['server'];
            $this->user = $value['user'];
            $this->password = $value['password'];
            $this->database = $value['database'];
            $this->port = $value['port'];
        }
        #Establece la conexión
        $this->conexion = new mysqli($this->server, $this->user, $this->password, $this->database, $this->port);
        #En caso de que al intentar conectar, encuentre un error
        if($this->conexion->connect_errno){
            echo "Hubo un error al establecer la conexión";
            die();
        }
    }

    #Función para obtener los datos de los atributos de la clase 
   private function datosConexion(){
        $direccion = dirname(__FILE__); #Almacena la dirreción del archivo "config"
        $jsondata = file_get_contents($direccion . "/". "config"); #Como lo que está dentro del archivo "config" es un json, es lo que vamos a guardar aquí
        return json_decode($jsondata, true); #Devolvemos el json pero queremos convertirlo en un array asociativo
   }

    #Convertir a utf8 los registros que van a pasarse por el API, quitando problemas de legibilidad que generen las tildes o las ñ
    #UTF-8 es un formato de codificación de caracteres Unicode, que le asigna una cadena de bits a cada caracter, puede leerse como un binario
    #Unicode es un estándar de codificación de caracteres diseñado para facilitar la visualización de textos
   private function convertirUTF8($array){
    array_walk_recursive($array, function(&$item, $key){ #Método de php que recibe un array y un trigger que también recibe un puntero (cuando pasamos un parametro por referencia) siempre llevan un &  
        if(!mb_detect_encoding($item, 'utf-8', true)){ #Si encuentra algún carácter problemático lo va a convertir a utf8
            //$item = utf8_encode($item); Esta es la línea que viene en el tutorial, sin embargo, el IDE me dice que utf8_encode está depreciado 
            $item = mb_convert_encoding($item, 'UTF-8', 'OLD_ENCODING'); //Según investigué está linea podría ser conveniente para sustituir la anterior, Donde OLD_ENCODING es la codificación de caracteres original de tus datos.

        }
    });
    return $array;
   }
   #Esta función es pública porque se va a querer utilizar fuera de la función, va a recibir un query como parámetro
   public function obtenerDatos($sqlstr){
    $results = $this->conexion->query($sqlstr); //Resultados del query Select * from pacientes
    $resultArray  = array(); //Para poder manejar el resultado, vamos a pasar los resultados a un array 
    foreach ($results as $key){ 
        $resultArray[] = $key;
    }
    return $this->convertirUTF8($resultArray); //Usamos la función que creamos para codificar en UTF-8
   }
   #Función para las consultas Guardar, Eliminar, Editar
   public function nonQuery($sqlstr){
    $results = $this->conexion->query($sqlstr);
    return $this->conexion->affected_rows;
   }

    #Función para consultas Guardar, Eliminar, Editar pero con la condición de verficar si la consulta tiene respuesta
   public function nonQueryId($sqlstr){
    $results = $this->conexion->query($sqlstr);
    $filas = $this->conexion->affected_rows;
    if ($filas >= 1){ #Si hay una fila afectada se retorna el último id que devuelve la consulta
        return $this->conexion->insert_id;
    }
    else{
        return 0;
    }
   }

   //Encriptar contraseña para comparar con la de la BD
   protected function encriptar($string){
    return md5($string);
   }

   //Los métodos publicos se pueden utilizar si la clase es instanciada
   //Los métodos privados  no se pueden utilizar de otra clase externa o fuera de la clase
   //Los métodos protected se pueden utilizar siempre y cuando la clase herede el método







}

?>