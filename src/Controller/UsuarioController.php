<?php namespace App\Controller;

use App\Models\Usuario;
use App\Models\AutentificadorJWT;

class UsuarioController{

  public function CrearUno($request, $response, array $args){
    $accion;
    
    if($_FILES["data"]!=null){
      $accion=Usuario::CrearConArchivoCSV($_FILES["data"]["tmp_name"]);
    }
    else{
      $parametros = $request->getParsedBody();
      $mail = $parametros['mail'];
      $tipo=$parametros['tipo'];
      $clave=$parametros['clave'];
      $usr = new Usuario($mail,$tipo, $clave);
      $accion=$usr->CreateInDB();
    }
    /*$header = $request->getHeaderLine('Authorization');
    if(!empty($header)){
      $data=AutentificadorJWT::ObtenerData($header);
      $registro=new Registro($data->id,$accion);
      $registro->GuardarEnDB();
    }
    */

    $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function LogearUsuario($request, $response, array $args){
    $parametros = $request->getParsedBody();
    $usr = $parametros['mail'];
    $pass=$parametros['clave'];
    $r = Usuario::BuscarConcidenciaEnDB($usr,$pass);
    
    if ($r!=null){
      $token=AutentificadorJWT::CrearToken($r);
      //$registro=new Registro($r["id"] ,"el usuario se ha logeado!");
      //$registro->GuardarEnDB();

      $payload = json_encode(array("jwt"=>$token));
    } else {

      $payload = json_encode(array("mensaje" => "ERROR! usuario no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function VerificarUsuario($request, $response, array $args){
    $parametros = $request->getParsedBody();
    $usr = $parametros['mail'];
    $pass=$parametros['clave'];
    $tipo=$parametros['tipo'];
    
    $r = Usuario::VerificarEnDB($usr,$pass,$tipo);
    if ($r!=null){

      $payload = json_encode(array("respuesta"=>"OK  --> es $tipo"));
    } else {

      $payload = json_encode(array("mensaje" => "ERROR! usuario no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }



  public function Test($request, $response, array $args){
    
    
    $payload = json_encode(array("mensaje" => "PROBANDO!!!!!"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  

};
