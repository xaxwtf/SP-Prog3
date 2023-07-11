<?php
namespace App\Models;

use App\Db\AccesoDatos;
use PDO;

class Usuario {
    public $id;
    public $mail;
    public $tipo;
    public $clave;

    public function __construct($id=null,$mail = null, $tipo= null, $clave= null){
        if($id!=null){
            $this->id=$id;
        }
        if($mail!=null){
            $this->mail=$mail;
        }
        
        if($tipo!=null){
            $this->tipo=$tipo;
        }
        if($clave!=null){
            $this->clave = password_hash($clave, PASSWORD_DEFAULT);
        }
        
        
    }
    public function Save(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (mail, tipo, clave) VALUES (:mail, :tipo,  :clave)");
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo',$this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave',$this->clave, PDO::PARAM_STR);
        $consulta->execute();
        return "Creando Usuario ID: ". $objAccesoDatos->obtenerUltimoId();
    }  
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }
    public static function TraerUno($id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios  where id= :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Usuario");
        return $consulta->fetch();
    }
    public static function BuscarConcidenciaEnDB($mail,$pass){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios  where mail= :mail");
        $consulta->bindValue(':mail', $mail , PDO::PARAM_STR);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Usuario");
        $rec=$consulta->fetch();
        $r=null;
        if( password_verify( $pass, $rec->clave)){
            $r= $rec;
        }
        return $r;
    } 
    public static function VerificarEnDB($mail,$pass,$tipo){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios  where mail= :mail and tipo=:tipo" );
        $consulta->bindValue(':mail', $mail , PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo , PDO::PARAM_STR);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Usuario");
        $rec=$consulta->fetch();
        $r=null;
        if( password_verify( $pass, $rec->clave)){
            $r= $rec;
        }
        return $r;
    }
}
?>
