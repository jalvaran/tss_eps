<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos para construir recetas
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class Usuarios extends conexion{
    
    public function AgregueIPSAUsuario($idUsuario,$idIps) {
        $sql="SELECT ID FROM relacion_usuarios_ips WHERE idUsuario='$idUsuario' AND idIPS='$idIps'";
        $Consulta=$this->Query($sql);
        $DatosExistentes=$this->FetchAssoc($Consulta);
        if($DatosExistentes["ID"]==''){
            $Datos["idUsuario"]=$idUsuario;
            $Datos["idIPS"]=$idIps;
            $sql=$this->getSQLInsert("relacion_usuarios_ips", $Datos);
            $this->Query($sql);
        }
    }
    //Fin Clases
}
