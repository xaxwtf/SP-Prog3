<?php
namespace App\Midlewares;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response ;

use App\Models\Registro;
use App\Models\AutentificadorJWT;

class GuardarRegistro{
    public function __invoke(Request $request, RequestHandler $handler):Response
    {
        $autorizacion=null;
        try{
            $response = $handler->handle($request);
            $id_arma = $_SESSION['id_arma'];
            $accion= $_SESSION['accion'];
            $dato = $request->getAttribute('dato');        

            $autorizacion = $request->getHeaderLine('Authorization');//recupero el token de autorizacion
            
            $usuario=AutentificadorJWT::ObtenerData($autorizacion);

            $nuevo=new Registro($usuario->id, $id_arma, $accion, date("Y-m-d H:i:s"));
            $nuevo->GuardarEnDB();
             

            $response->getBody()->write("Save Registro");


            return $response;
            
        }
        catch(\Throwable $th){
            $response= new Response();
            $aux=json_encode(array("mensaje"=>"Error, ". $th->getMessage()));
            $response->getBody()->write($aux);
        }
        finally{
            return $response;
        }
        
        
        
    }
}



?>