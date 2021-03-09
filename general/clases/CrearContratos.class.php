<?php
if(file_exists("../../modelo/php_conexion.php")){
    include_once("../../modelo/php_conexion.php");
}

class ContratosEPS extends conexion{
   
   public function RegistreAdjuntoContrato($contrato_id, $destino, $Tamano, $NombreArchivo, $Extension, $idUser) {
        
        $tab="contratos_adjuntos";
        
        $Datos["contrato_id"]=$contrato_id;
        
        $Datos["Ruta"]=$destino;    
        $Datos["NombreArchivo"]=$NombreArchivo;    
        $Datos["Extension"]=$Extension;    
        $Datos["Tamano"]=$Tamano; 
        $Datos["idUser"]=$idUser;		
        $Datos["created"]=date("Y-m-d H:i:s");	
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
    }
    
}
