<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/ReciboOrdenCompra.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ReciboOrdenCompra($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Agrega una cantidad por codigo de barras
            $idOrden=$obCon->normalizar($_REQUEST["idOrden"]); 
            $Codigo=$obCon->normalizar($_REQUEST["TxtCodigoBarras"]);
            $DatosCodigoBarras=$obCon->DevuelveValores("prod_codbarras", "CodigoBarras", $Codigo);
            
            if($DatosCodigoBarras["ProductosVenta_idProductosVenta"]<>''){
                $Codigo=$DatosCodigoBarras["ProductosVenta_idProductosVenta"];
            }
            $sql="SELECT * FROM ordenesdecompra_items WHERE NumOrden='$idOrden' AND idProducto='$Codigo'";
            $Consulta=$obCon->Query($sql);
            $DatosItem=$obCon->FetchAssoc($Consulta);
            
            if($DatosItem["ID"]<>''){
                $idItem=$DatosItem["ID"];
                $Cantidad=$DatosItem["Recibido"]+1;
                if($Cantidad<=$DatosItem["Cantidad"]){
                    $obCon->update("ordenesdecompra_items", "Recibido", $Cantidad, "WHERE ID='$idItem'");
                    print("OK");
                }else{
                    print("Error2");
                    exit();
                }
                
            }else{
                print("Error1"); //No existe el producto en la orden de compra
            }
            
            
        break; 
        
        case 2: //editar datos en una tabla
            $Cantidad=$obCon->normalizar($_REQUEST["Cantidad"]);  
            $idItem=$obCon->normalizar($_REQUEST["idItem"]); 
            
            $obCon->update("ordenesdecompra_items", "Recibido", $Cantidad, "WHERE ID='$idItem'");
            print("OK");
            
        break; 
    
        case 3: //editar datos en una tabla
            $idOrden=$obCon->normalizar($_REQUEST["idOrden"]);  
            
            $obCon->update("ordenesdecompra", "Estado", "VERIFICADA", "WHERE ID='$idOrden'");
            print("OK");
            
        break; 
         
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>