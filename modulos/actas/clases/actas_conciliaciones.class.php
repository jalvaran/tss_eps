<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos de la cartera IPS
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class ActasConciliacion extends conexion{
    
    public function HabiliteFacturasEnHojaDeTrabajo($db,$idActa) {        
        
        $sql="UPDATE $db.carteraeps t1 INNER JOIN $db.actas_conciliaciones_items t2 ON t1.NumeroFactura=t2.NumeroFactura 
                SET t1.Estado=0 
                WHERE t1.Estado < 3;";
        $this->Query($sql);        
        
    }
    
    public function EliminarItemsActaConciliacion($db,$idActa) {        
        
        $this->BorraReg("$db.actas_conciliaciones_items", "idActaConciliacion", $idActa);    
        
    }
       
    
    
    
    //Fin Clases
}
