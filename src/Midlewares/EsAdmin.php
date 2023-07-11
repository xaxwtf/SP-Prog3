<?php
namespace App\Midlewares;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response ;

use App\Models\AutentificadorJWT;

class EsAdmin{
    public function __invoke(Request $request, RequestHandler $handler):Response
    {
        try{
            

            $response= new Response();  
            $autorizacion = $request->getHeaderLine('Authorization');//recupero el token de autorizacion
            
            $data=AutentificadorJWT::ObtenerData($autorizacion);

            if($data->tipo=="admin"){

                $response=$handler->handle($request);//INVOCA AL SIGUIENTE MIDDLEWARE;
            }
            else{
                $aux=json_encode(array("mensaje"=>"error, no posee los permisos necesarios"));
                $response->getBody()->write($aux);
            }
            
            
        }
        catch(\Throwable $th){
            $response= new Response();
            $aux=json_encode(array("mensaje"=>"error,". $th->getMessage()));
            $response->getBody()->write($aux);
        }
        finally{
            return $response;
        }
        
        
        
    }
}