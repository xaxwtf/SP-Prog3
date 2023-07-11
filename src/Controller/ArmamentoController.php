<?php
namespace App\Controller;

use App\Models\Armamento;
use SplFileInfo;
use App\Models\Registro;

class ArmamentoController{

    public function AltaArmamento($request, $response, array $args){
        $accion;

        if(false){
            $accion=Usuario::CrearConArchivoCSV($_FILES["data"]["tmp_name"]);
        }
        else{
            $parametros = $request->getParsedBody();
            $precio = $parametros['precio'];
            $nombre=$parametros['nombre'];

            
            $info = new SplFileInfo($_FILES["foto"]["name"]);
            $destino = __DIR__."/../../FotosArma2023/" . $nombre.".". $info->getExtension();
            $resultado=move_uploaded_file($_FILES["foto"]["tmp_name"], $destino);
                
            

            $nacionalidad=$parametros['nacionalidad'];
            $nuevo = new Armamento(1,$precio, $nombre, $destino, $nacionalidad);
            $accion=$nuevo->SaveDb();
          }
          /*$header = $request->getHeaderLine('Authorization');
          if(!empty($header)){
            $data=AutentificadorJWT::ObtenerData($header);
            $registro=new Registro($data->id,$accion);
            $registro->GuardarEnDB();
          }
          */
      
          $payload = json_encode(array("mensaje" => "Armamento creado con exito"));
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerTodos($request, $response, array $args){
        $lista=Armamento::TraerTodos();
        $payload = json_encode(array("listaDeArmas" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerTodosDeNacionalidad($request, $response, array $args){
        
        $nacionalidad=$args['nacionalidad'];

        $lista=Armamento::TraerTodosDeNacionalidad($nacionalidad);
        $payload = json_encode(array("listaDeArmas" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $id=$args["id"];
        
        $arma = Armamento::TraerUno($id);
        $payload = json_encode($arma);



        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function BajaArmamento($request, $response, $args){
        $id=$args["id"];
        $accion=Armamento::EliminarArma($id);

        session_start();
        $_SESSION['id_arma'] = $id;
        $_SESSION['accion']=$accion;
        $payload = json_encode(array("mensaje" => "Borrando Arma!"));
        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
        
    }
    public function EditArmamento($request, $response, $args){

        $test=json_decode(file_get_contents('php://input'));
        
        
        if(isset($test->id)){
            $respuesta= Armamento::Editar($test->id,$test->precio,$test->nombre,null,$test->nacionalidad);
        }
        else{
            $respuesta="no se ha Ingresado el ID";
        }

        $payload = json_encode(array("resultado" => $respuesta ));
        
        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
    public function GenerarCSVArmamento($request, $response, $args){

        Armamento::DescargarDatosEnCSV("listaArmas.csv");
        $payload = json_encode(array("resultado" => "se ha descargado todas las armas en listaArmas.csv"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function GenerarCSVRegistro($request, $response , $args){
        Registro::DescargarDatosEnCSV("logs.csv");
        $payload = json_encode(array("resultado" => "se ha descargado todos los registros en logs.csv"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}