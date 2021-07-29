<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/auditoria_excel.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ExcelReportes($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear la Hoja de Trabajo de auditoria en excel
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $hoja_trabajo_id=$obCon->normalizar($_REQUEST["hoja_trabajo_id"]);
            
            $obCon->auditoria_hoja_trabajo($CmbIPS,$hoja_trabajo_id);
        break; //fin caso 1
        
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>