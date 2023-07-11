<?php
namespace App\Models;


use App\Db\AccesoDatos;
use App\Models\ArchivosCSV;
use PDO;

class Armamento{
    public $id;
    public $precio;
    public $nombre;
    public $foto;
    public $nacionalidad;
    public function __construct($id=null,$precio=null, $nombre=null, $foto=null, $nacionalidad=null){
        if($id!=null){
            $this->id=$id;
        }
        if($precio!=null){
            $this->precio=$precio;
        }
        if($nombre!=null){
            $this->nombre=$nombre;
        }
        if($foto!=null){
            $this->foto=$foto;
        }
        if($nacionalidad!=null){
            $this->nacionalidad=$nacionalidad;
        }
    }

    public function SaveDb(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO armas (precio, nombre, foto, nacionalidad) VALUES (:precio, :nombre,  :foto, :nacionalidad)");
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':nombre',$this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':foto',$this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad',$this->nacionalidad, PDO::PARAM_STR);
        $consulta->execute();
        return "Creando Usuario ID: ". $objAccesoDatos->obtenerUltimoId();
    }
    
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio, nombre, foto, nacionalidad FROM armas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }
    public static function TraerUno($id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio, nombre, foto, nacionalidad FROM armas  where id= :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Armamento");
        return $consulta->fetch();
    }

    public static function TraerTodosDeNacionalidad($nacionalidad){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio, nombre, foto, nacionalidad FROM armas where nacionalidad=:nacionalidad");
        $consulta->bindValue(':nacionalidad',$nacionalidad, PDO::PARAM_STR);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Armamento");
        return $consulta->fetchAll();
    }
    public static function EliminarArma($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM armas WHERE (`id` = :id);");
        $consulta->bindValue(':id',$id, PDO::PARAM_STR);
        $r=$consulta->execute();
        return "Eliminando Arma!";
    }
    /*public static function CambiarEstadoUsuario($id , $estado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE usuarios SET estado = :estado WHERE (id = :id)");
        $consulta->bindValue(':estado',$estado, PDO::PARAM_STR);
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        return $consulta->execute();
    }*/
    public static function Editar($id, $precio=null, $nombre=null, $foto=null, $nacionalidad=null){

        $consultaStr="UPDATE armas SET ";
        $cambios=false;
        
        if($precio!=null){
            $consultaStr=$consultaStr . " precio = :precio ";
            $cambios=true;
        }
        if($nombre!=null){
            $consultaStr=$consultaStr . ", nombre = :nombre ";
            $cambios=true;
        }
        if($foto!=null){
            $consultaStr=$consultaStr . ", foto = :foto ";
            $cambios=true;
        }
        if($nacionalidad!=null){
            $consultaStr=$consultaStr . ", nacionalidad = :nacionalidad ";
            $cambios=true;
        }
        if($cambios){
            $consultaStr =$consultaStr . " WHERE (id = :id)";
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta($consultaStr);
            $consulta->bindValue(':id',$id, PDO::PARAM_INT);
            if($precio!=null){
                $consulta->bindValue(':precio',$precio, PDO::PARAM_STR);
            }
            if($nombre!=null){
                $consulta->bindValue(':nombre',$nombre, PDO::PARAM_STR);
            }
            /*if($foto!=null){
                $consulta->bindValue(':foto',$foto, PDO::PARAM_STR);
            }*/
            if($nacionalidad!=null){
                $consulta->bindValue(':nacionalidad',$nacionalidad, PDO::PARAM_STR);
            }

            $consulta->execute();
        }
        else{
            $consultaStr=null;
        }


        return "se ha realizado:". $consultaStr;
    }

    public static function DescargarDatosEnCSV($namefile){
        $lista=Armamento::TraerTodos();
        $contenidoCSV="";
        for($i=0;$i<count($lista);$i++){
            $contenidoCSV= $contenidoCSV . $lista[$i]->id .",". $lista[$i]->precio .",". $lista[$i]->nombre .",". $lista[$i]->foto .",". $lista[$i]->nacionalidad ."\n";
        }
        ArchivosCSV::EscribirArchivo($namefile,$contenidoCSV);
    }
  
}
?>