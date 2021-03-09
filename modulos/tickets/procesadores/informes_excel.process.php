<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/informes_excel.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ExcelReportes($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear el informe de gestion y uso del tags en excel 
            $FechaInicial=$obCon->normalizar($_REQUEST["FechaInicial"]); 
            $FechaFinal=$obCon->normalizar($_REQUEST["FechaFinal"]);
            $CmbEstado=$obCon->normalizar($_REQUEST["CmbEstado"]);
            $CmbProyectosTicketsListado=$obCon->normalizar($_REQUEST["CmbProyectosTicketsListado"]);
            $CmbModulosTicketsListado=$obCon->normalizar($_REQUEST["CmbModulosTicketsListado"]);
            $CmbTiposTicketsListado=$obCon->normalizar($_REQUEST["CmbTiposTicketsListado"]);
            $usuario_id=$obCon->normalizar($_REQUEST["usuario_id"]);
            
            $obCon->informe_gestion($FechaInicial,$FechaFinal,$CmbEstado,$CmbProyectosTicketsListado,$CmbModulosTicketsListado,$CmbTiposTicketsListado,$usuario_id);
        break; //fin caso 1
        
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>