<?php
namespace App\Models;

use App\Models\Armamento;
use App\Db\AccesoDatos;

use SplFileInfo;
use PDO;


class Venta{
    public $id;
    public $fecha;
    public $cantidad;
    public $producto;
    public $imagen;
    public $total;
    public $cliente;

    public function __construct($id=null,$fecha=null,$cantidad=null, $idProducto=null, $cliente=null){
        if($id!=null){
            $this->id=$id;
        }
        if($fecha!=null){
            $this->fecha= $fecha;
        }
        
        if($cantidad!=null){
            $this->cantidad=$cantidad;
        }
        if($idProducto!=null){
            $this->producto=$idProducto;
        }
        if($cliente!=null){
            $this->cliente=$cliente;
        }
    }
    public function Alta(){
        $r=true;
        $resultado=Armamento::traerUno($this->producto);
        $cte=Usuario::TraerUno($this->cliente);

        if($resultado==false|| $cte==false){
            $r=false;
        }
        else{
            $info = new SplFileInfo($resultado->foto);
            $destino = __DIR__."/../../FotosVentaArma2023/" . $resultado->nombre ."-". Venta::ObtenerMailSinArroba2($cte->mail) .".". $info->getExtension();
            copy($resultado->foto, $destino);
            $this->imagen=$destino;
            
            $this->total= (float)$resultado->precio * $this->cantidad;

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Ventas (fecha, cantidad, producto, total, imagen,cliente) VALUES (:fecha, :cantidad,  :producto,:total, :imagen, :cliente)");
            $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
            $consulta->bindValue(':cantidad',$this->cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':producto',$this->producto, PDO::PARAM_STR);
            $consulta->bindValue(':total',$this->total, PDO::PARAM_STR);
            $consulta->bindValue(':imagen',$this->imagen, PDO::PARAM_STR);
            $consulta->bindValue(':cliente',$this->cliente, PDO::PARAM_STR);
            $consulta->execute();
            $r= "Creando Venta ID: ". $objAccesoDatos->obtenerUltimoId();
            
        }
        return $r;
    }
    public function AgregarImagen($referencia){
        $info = new SplFileInfo($_FILES[$referencia]["name"]);
        $destino = "FotosArma2023/" . $this->producto->nombre ."_". $this->ObtenerMailSinArroba2($this->mail) ."_". $this->fecha.".". $info->getExtension();
        $resultado=move_uploaded_file($_FILES[$referencia]["tmp_name"], $destino);
        $this->imagen=$destino;
    }
    private static function ObtenerMailSinArroba2($mail){
        $resultado="";
        for($i=0;$i<strlen($mail);$i++){
            if($mail[$i]=='@'){
                $resultado=substr($mail,0,$i);
                break;
            }
        }
        return $resultado;
    }
    public static function TraerVentasDeUnPeriodoDeArmasDeUnPais($f1, $f2,$pais){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ventas.id, ventas.fecha ,ventas.producto, ventas.cantidad, ventas.total, ventas.imagen, ventas.cliente FROM ventas 
        inner join armas on ventas.producto= armas.id
        WHERE ventas.fecha >= :fecha1 AND ventas.fecha <= :fecha2 AND armas.nacionalidad=:nacionalidad
        " );
        $consulta->bindValue(':fecha1',$f1, PDO::PARAM_STR);
        $consulta->bindValue(':fecha2',$f2, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad',$pais, PDO::PARAM_STR);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Venta");
        return $consulta->fetchAll();
    }  
    public static function TraerUsuariosQueCompraronCiertoProducto($productoString){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT  usuarios.id, usuarios.mail , usuarios.tipo FROM ventas 
        inner join armas on ventas.producto= armas.id
        inner join usuarios on usuarios.id=ventas.cliente
        WHERE armas.nombre=:producto
        group by usuarios.mail" );
        $consulta->bindValue(':producto',$productoString, PDO::PARAM_STR);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Usuario");
        return $consulta->fetchAll();
    }
    public static function TraerVentasDelUltimoMes(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT *
        FROM ventas
        WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)" );
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Venta");
        return $consulta->fetchAll();
    }
   
   

    public static function DescargarOrdenadosDatosEnArhivo($namefile,bool $acendente=false){
        $lista=Venta::TraerVentasDelUltimoMes();
        if($acendente==true){
            usort($lista, 'App\Models\compararTotales');
        }
        else{
            usort($lista, 'App\Models\compararTotales2');
        }
        
        $contenidoCSV="";
        for($i=0;$i<count($lista);$i++){
            $contenidoCSV= $contenidoCSV . $lista[$i]->id. ", ".$lista[$i]->fecha .",". $lista[$i]->cantidad .",". $lista[$i]->producto .",". $lista[$i]->imagen .",". $lista[$i]->total .",". $lista[$i]->cliente ."\n";
        }
        ArchivosCSV::EscribirArchivo($namefile,$contenidoCSV);
    }
}
function compararTotales($a, $b) {
    
   
    if ($a->total == $b->total) {
            return 0;
        }
     
    return ($a->total > $b->total) ? -1 : 1;
}
function compararTotales2($a, $b) {
    
   
    if ($a->total == $b->total) {
            return 0;
        }
     
    return ($a->total < $b->total) ? -1 : 1;
}

?>