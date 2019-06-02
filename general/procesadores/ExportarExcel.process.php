<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/ExportarExcel.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ExcelExport($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Obtener numero de filas de la tabla
            $db=$obCon->normalizar($_REQUEST["db"]);
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $sql="SELECT COUNT(*) as TotalFilas FROM $db.$Tabla";
            $Consulta=$obCon->Query($sql);
            $Datos=$obCon->FetchAssoc($Consulta);
            $TotalFilas=$Datos["TotalFilas"];
            print("OK;Total de Filas a Exportar $TotalFilas;$TotalFilas");
            
        break; //fin caso 1
        
        case 2: //se recibe el archivo
            $db=$obCon->normalizar($_REQUEST["db"]);
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            print("OK;Archivo Recibido;$destino;$Extension");   
        break;//Fin caso 2
        
        
            
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>