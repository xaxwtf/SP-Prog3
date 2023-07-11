<?php
namespace App\Controller;

use App\Models\Venta;
use App\Models\AutentificadorJWT;
use SplFileInfo;


class VentasController{
    public function AltaVenta($request, $response, array $args){
        $parametros=$request->getParsedBody();
        $cantidad=$parametros["cantidad"];
        $producto=$parametros["idProducto"];
        $cliente;
        $mensaje="Venta Realizada Con exito!";

        $header = $request->getHeaderLine('Authorization');
        if(!empty($header)){
            $info=AutentificadorJWT::ObtenerData($header);      
            $cliente=$info->id;
        }
    
        
        $nuevo= new Venta(1,date("Y-m-d H:i:s"),$cantidad,$producto,$cliente);
        if(!$nuevo->Alta()){
            $mensaje="Error, no se ha realizado la venta";
        }
        

        $payload = json_encode(array("Resultado" => $mensaje));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }
    public function VentasdeEEUUdeUnPeriodo($request, $response , array $args){

        $lista=Venta::TraerVentasDeUnPeriodoDeArmasDeUnPais('2023-11-13','2023-11-16',"EEUU");

        $payload = json_encode(array("listaDeArmas" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');

    }
    public function UsuariosQueCompraronCiertoProducto($request, $response , array $args){
        $producto=$args["nameProducto"];
        $lista=Venta::TraerUsuariosQueCompraronCiertoProducto($producto);

        $payload = json_encode(array("listaDeArmas" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VentasDelMesOrdenadas($request, $response, $args){
        $order=$args["orderAS"];
        var_dump($order);
        if($order=="1"){
            Venta::DescargarOrdenadosDatosEnArhivo("VentasDelMes.pdf",true);
        }
        else if($order=="2"){
            Venta::DescargarOrdenadosDatosEnArhivo("VentasDelMes.pdf");
        }
        
        
        $payload = json_encode(array("mensaje" => "testeando!"));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    

}