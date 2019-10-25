<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/actas_conciliaciones.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ActasConciliacion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Abrir un acta de conciliacion
            
            
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            
            
           
            if($idActaConciliacion==''){
                exit("E1;No se Recibió un Acta de Conciliación;");
            }
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion);
            $DatosIPS= $obCon->DevuelveValores("ips", "NIT", $DatosActa["NIT_IPS"]);
            $db=$DatosIPS["DataBase"];
            
            $obCon->HabiliteFacturasEnHojaDeTrabajo($db,$idActaConciliacion);
            $obCon->ActualizaRegistro("actas_conciliaciones", "Estado", 0, "ID", $idActaConciliacion);
            $obCon->EliminarItemsActaConciliacion($db,$idActaConciliacion);
            
            print("OK;Acta de Conciliación Abierta Correctamente");
            
        break; //fin caso 1
         
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>