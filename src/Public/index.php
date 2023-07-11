<?php

error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

use App\Midlewares;

require __DIR__ . '/../../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
//$app->addErrorMiddleware(true, true, true);



$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("ESTE SERIA MI Segundo PARCIAL!");
    return $response;
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    //$group->get('[/]', App\Controller\UsuarioController::class . ':TraerTodos'  )->add(new EsAdmin_Socio)->add(new EstaLogeado);  // socios y admin
    //$group->get('/{usuario}', App\Controller\UsuarioController::class . ':TraerUno')->add(new EsAdmin_Socio)->add(new EstaLogeado);//socios y addmin
    //$group->post('/perfil', App\Controller\UsuarioController::class . ':Perfil')->add(new EstaLogeado);///solo si hay alguien logeado
    //$group->post('[/add]' , App\Controller\UsuarioController::class . ':cargarUno')->add(new EsAdmin_Socio)->add(new EstaLogeado);//socios y admin
    $group->post('/login', App\Controller\UsuarioController::class . ':LogearUsuario'); //todos
    $group->post('/VerificarUsuario', App\Controller\UsuarioController::class . ':VerificarUsuario'); //todos
    //$group->post('/test', App\Controller\UsuarioController::class .':Test');
  });
  $app->group('/armamento',function (RouteCollectorProxy $group){
      $group->get('[/]', App\Controller\ArmamentoController::class . ':TraerTodos'  );
      $group->get('/Nacionalidad/{nacionalidad}', App\Controller\ArmamentoController::class . ':TraerTodosDeNacionalidad');
      $group->get('/Arma/{id}', App\Controller\ArmamentoController::class . ':TraerUno')->add(new Midlewares\EstaLogeado);
      $group->post('/Alta', App\Controller\ArmamentoController::class . ':AltaArmamento')->add(new Midlewares\EsAdmin)->add(new Midlewares\EstaLogeado);
      $group->delete('/{id}', App\Controller\ArmamentoController::class . ':BajaArmamento')->add(new Midlewares\GuardarRegistro)->add(new Midlewares\EsAdmin)->add(new Midlewares\EstaLogeado);
      $group->put('/edit', App\Controller\ArmamentoController::class . ':EditArmamento')->add(new Midlewares\EsAdmin)->add(new Midlewares\EstaLogeado);
      $group->get('/listaCSV', App\Controller\ArmamentoController::class . ':GenerarCSVArmamento');
      $group->get('/logs', App\Controller\ArmamentoController::class . ':GenerarCSVRegistro' ); 
  });
  $app->group('/ventas',function (RouteCollectorProxy $group){
    //$group->get('[/]', App\Controller\ArmamentoController::class . ':TraerTodos'  );
    //$group->get('/Nacionalidad/{nacionalidad}', App\Controller\ArmamentoController::class . ':TraerTodosDeNacionalidad');
    //$group->get('/Arma/{id}', App\Controller\ArmamentoController::class . ':TraerUno');
    $group->post('/Alta', App\Controller\VentasController::class . ':AltaVenta')->add(new Midlewares\EstaLogeado);
    $group->get('/p1',App\Controller\VentasController::class . ':VentasdeEEUUdeUnPeriodo')->add(new Midlewares\EsAdmin)->add(new Midlewares\EstaLogeado);
    $group->get('/Compradores/{nameProducto}', App\Controller\VentasController::class . ':UsuariosQueCompraronCiertoProducto' )->add(new Midlewares\EsAdmin)->add(new Midlewares\EstaLogeado);
    $group->get('/VentasMes/{orderAS}', App\Controller\VentasController::class . ':VentasDelMesOrdenadas');
});

$app->run();

?>