<?php
namespace App\Models;
use App\Db\AccesoDatos;
use PDO;

class Registro{
    public $id_usuario;
    public $id_arma;
    public $accion;
    public $fecha;

    public function __construct($id_usuario=null, $id_arma=null, $accion=null, $fecha=null){
        if($id_usuario!=null){
            $this->id_usuario=$id_usuario;
        }
        if($id_arma!=null){
            $this->id_arma=$id_arma;
        }
        if($accion!=null){
            $this->accion=$accion;
        }
        if($fecha!=null){
            $this->fecha=$fecha;
        }
    }
    public function GuardarEnDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs (id_usuario, id_arma, accion, fecha_accion) VALUES (:id_usuario, :id_arma, :accion, :fecha_accion)");
        $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $consulta->bindValue(':id_arma', $this->id_arma, PDO::PARAM_INT);
        $consulta->bindValue(':accion', $this->accion, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_accion', $this->fecha, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, id_arma, accion, fecha_accion  FROM logs");
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,"App\Models\Registro");
        return $consulta->fetchAll();
    }
    public static function DescargarDatosEnCSV($namefile){
        $lista=Registro::TraerTodos();
        $contenidoCSV="";
        for($i=0;$i<count($lista);$i++){
            $contenidoCSV= $contenidoCSV . $lista[$i]->id_usuario .",". $lista[$i]->id_arma .",". $lista[$i]->accion .",". $lista[$i]->fecha_accion ."\n";
        }
        ArchivosCSV::EscribirArchivo($namefile,$contenidoCSV);
    }
}