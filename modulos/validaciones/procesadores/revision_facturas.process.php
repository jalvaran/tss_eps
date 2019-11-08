<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/revision_facturas.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new RevisionFacturas($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Construir la tabla con la informacion de las facturas repetidas con ceros a la izquierda
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $sql="update $db.historial_carteracargada_eps set CantidadFacturasRepetidasConCerosIzquierda=0";
            $obCon->Query($sql);
            $obCon->ConstruirVistaParaComprobacion1($db, $idUser);
            $obCon->ActualizarCantidadFacturasConCeros($db, $idUser);
            $obCon->ConstruirVistaParaComprobacion2($db, $idUser);
            $obCon->ConstruirTablaDeFacturasConCerosIzquierda($db, $idUser);
            
            print("OK;Se ha construido La tabla de facturas con ceros a la izquierda");
            
        break; //fin caso 1
        
          
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>