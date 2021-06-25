<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/f10_excel.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new F10_Excel($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Generar Excel del F10
            $st_f10= urldecode(base64_decode($_REQUEST["st"]));
            
            $obCon->f10_excel_listado($st_f10);
            
        break; //fin caso 1
        case 2: //Generar Excel del F10 control de cambios
            $contrato_id= $obCon->normalizar($_REQUEST["contrato_id"]);
            
            $obCon->f10_control_cambios_excel($contrato_id);
            
        break; //fin caso 1
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>