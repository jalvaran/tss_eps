<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/PrintBarras.php");
include_once("../clases/Compras.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Compras($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear una compra
            $Fecha=$obCon->normalizar($_REQUEST["Fecha"]); 
            $idTercero=$obCon->normalizar($_REQUEST["Tercero"]); 
            $CentroCostos=$obCon->normalizar($_REQUEST["ControCosto"]); 
            $idSucursal=$obCon->normalizar($_REQUEST["Sucursal"]); 
            $TipoCompra=$obCon->normalizar($_REQUEST["TipoCompra"]);
            $Concepto=$obCon->normalizar($_REQUEST["Concepto"]); 
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumFactura"]); 
            $Observaciones="";
            $idCompra=$obCon->CrearCompra($Fecha, $idTercero, $Observaciones, $CentroCostos, $idSucursal, $idUser, $TipoCompra, $NumeroFactura, $Concepto, "");
            print("OK;$idCompra");            
            
        break; 
        
        case 2: //editar datos generales de una factura de compra
            $idCompra=$obCon->normalizar($_REQUEST["idCompraActiva"]); 
            $Fecha=$obCon->normalizar($_REQUEST["Fecha"]); 
            $idTercero=$obCon->normalizar($_REQUEST["Tercero"]); 
            $CentroCostos=$obCon->normalizar($_REQUEST["ControCosto"]); 
            $idSucursal=$obCon->normalizar($_REQUEST["Sucursal"]); 
            $TipoCompra=$obCon->normalizar($_REQUEST["TipoCompra"]);
            $Concepto=$obCon->normalizar($_REQUEST["Concepto"]); 
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumFactura"]); 
            
            $obCon->ActualizaRegistro("factura_compra", "Fecha", $Fecha, "ID", $idCompra,0);
            $obCon->ActualizaRegistro("factura_compra", "Tercero", $idTercero, "ID", $idCompra,0);
            $obCon->ActualizaRegistro("factura_compra", "idCentroCostos", $CentroCostos, "ID", $idCompra,0);
            $obCon->ActualizaRegistro("factura_compra", "idSucursal", $idSucursal, "ID", $idCompra,0);
            $obCon->ActualizaRegistro("factura_compra", "TipoCompra", $TipoCompra, "ID", $idCompra,0);
            $obCon->ActualizaRegistro("factura_compra", "Concepto", $Concepto, "ID", $idCompra,0);
            $obCon->ActualizaRegistro("factura_compra", "NumeroFactura", $NumeroFactura, "ID", $idCompra,0);
            
            $destino="";
            $Atras="";
            $carpeta="";
            if(!empty($_FILES['Soporte']['name'])){
                //echo "<script>alert ('entra foto')</script>";
                $Atras="../";
                $carpeta="soportes/";
                opendir($Atras.$carpeta);
                $Name=$idCompra."_".str_replace(' ','_',$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$destino);
                $obCon->ActualizaRegistro("factura_compra", "Soporte", $destino, "ID", $idCompra);
            }
            
            $DatosTercero=$obCon->DevuelveValores("proveedores", "Num_Identificacion", $idTercero);
            
            print("OK;$DatosTercero[RazonSocial];$Concepto;$NumeroFactura");
            
        break; 
        case 3://Agregar un item
            
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]); 
            $CmbListado=$obCon->normalizar($_REQUEST["CmbListado"]); 
            $CmbBusquedas=$obCon->normalizar($_REQUEST["CmbBusquedas"]); 
            $CmbImpuestosIncluidos=$obCon->normalizar($_REQUEST["CmbImpuestosIncluidos"]); 
            $CmbTipoImpuesto=$obCon->normalizar($_REQUEST["CmbTipoImpuesto"]); 
            $CodigoBarras=$obCon->normalizar($_REQUEST["CodigoBarras"]);
            $TxtDescripcion=$obCon->normalizar($_REQUEST["TxtDescripcion"]); 
            $Cantidad=$obCon->normalizar($_REQUEST["Cantidad"]); 
            $ValorUnitario=$obCon->normalizar($_REQUEST["ValorUnitario"]); 
            $PrecioVenta=$obCon->normalizar($_REQUEST["PrecioVenta"]); 
            if($CmbListado==1){
                $idProducto=$CodigoBarras;
                $DatosCodigos=$obCon->DevuelveValores("prod_codbarras", "CodigoBarras", $CodigoBarras);
                if($DatosCodigos["ProductosVenta_idProductosVenta"]<>''){
                    $idProducto=$DatosCodigos["ProductosVenta_idProductosVenta"];
                }
                $DatosProducto=$obCon->DevuelveValores("productosventa", "idProductosVenta", $idProducto);
                if($DatosProducto["idProductosVenta"]==''){
                    exit("Este producto no existe en la base de datos");
                }
                $obCon->AgregueProductoCompra($idCompra, $idProducto, $Cantidad, $ValorUnitario,$PrecioVenta, $CmbTipoImpuesto, $CmbImpuestosIncluidos, "");
            
            }
            
            if($CmbListado==3){ //Insumos
                $idProducto=$CodigoBarras;
                $DatosProducto=$obCon->DevuelveValores("insumos", "ID", $idProducto);
                if($DatosProducto["ID"]==''){
                    exit("Este insumo no existe en la base de datos");
                }
                $obCon->AgregueInsumoCompra($idCompra, $idProducto, $Cantidad, $ValorUnitario, $CmbTipoImpuesto, $CmbImpuestosIncluidos, "");
            }
            
            if($CmbListado==2){ //Servicios
                $CuentaPUC=$CodigoBarras;                
                $obCon->AgregueServicioCompra($idCompra, $CuentaPUC, $TxtDescripcion, $ValorUnitario, $CmbTipoImpuesto,$CmbImpuestosIncluidos, "");
            }
            print("OK");
            
        break;//Fin caso 3
        
        case 4://Se envia a imprimir un tiquete para codigo de barras
            $obPrintBarras = new Barras($idUser);
            $idProducto=$obCon->normalizar($_REQUEST["idProducto"]);
            $Cantidad=$obCon->normalizar($_REQUEST["Cantidad"]);
            $Tabla="productosventa";
            $DatosCB["EmpresaPro"]=1;
            $DatosPuerto=$obCon->DevuelveValores("config_puertos", "ID", 2);
            if($DatosPuerto["Habilitado"]=='SI'){
                $obPrintBarras->ImprimirCodigoBarrasMonarch9416TM($Tabla,$idProducto,$Cantidad,$DatosPuerto["Puerto"],$DatosCB);
           
            }
            print("$Cantidad Tiquetes impresos para el producto $idProducto ");
        break;//fin caso 4
        case 5://Se elimina un item
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            if($Tabla==1){
                $Tabla="factura_compra_items";
            }
            if($Tabla==2){
                $Tabla="factura_compra_servicios";
            }
            if($Tabla==3){
                $Tabla="factura_compra_insumos";
            }
            if($Tabla==4){
                $Tabla="factura_compra_items_devoluciones";
            }
            if($Tabla==5){
                $Tabla="factura_compra_retenciones";
            }
            if($Tabla==6){
                $Tabla="factura_compra_descuentos";
            }
            if($Tabla==7){
                $Tabla="factura_compra_impuestos_adicionales";
            }
            $obCon->BorraReg($Tabla, "ID", $idItem);
            print("Item Eliminado");
        break;//Fin caso 5
        
        case 6://Se devuelve un producto
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $Cantidad=$obCon->normalizar($_REQUEST["Cantidad"]);
            $obCon->DevolverProductoCompra($Cantidad, $idItem, "");
            print("Item Devuelto");
        break;//Fin caso 6
        
        case 7://Se registra un cargo adicional para los totales en compra de productos
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            $Selector=$obCon->normalizar($_REQUEST["Selector"]);
            $CuentaPUC=$obCon->normalizar($_REQUEST["CuentaPUC"]);
            $Porcentaje=$obCon->normalizar($_REQUEST["Porcentaje"]);
            $Valor=$obCon->normalizar($_REQUEST["Valor"]);
            if($Selector==1 or $Selector==2){ //Retefuente o ReteICA
                $obCon->AgregueRetencionCompra($idCompra, $CuentaPUC, $Valor, $Porcentaje, "");
            }
            if($Selector==3){ //Descuentos Comerciales en compras
                $obCon->AgregueDescuentoCompra($idCompra, $CuentaPUC, $Valor, $Porcentaje, "");
            }
            if($Selector==4){ //Agrega un impuesto adicional
                $obCon->AgregueImpuestoAdicionalCompra($idCompra, $CuentaPUC, $Valor, $Porcentaje, "");
            }
            print("OK");
        break;//Fin caso 7
        
        case 8://Se registra cargos adicionales al iva de los productos
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            $Selector=$obCon->normalizar($_REQUEST["Selector"]);
            $CuentaPUC=$obCon->normalizar($_REQUEST["CuentaPUC"]);
            $Porcentaje=$obCon->normalizar($_REQUEST["Porcentaje"]);
            $Valor=$obCon->normalizar($_REQUEST["Valor"]);
            if($Selector==1){ //Retefuente o ReteICA
                $obCon->AgregueRetencionCompra($idCompra, $CuentaPUC, $Valor, $Porcentaje, "");
            }
            
            print("OK");
        break;//Fin caso 8
        
        case 9://Guardo la factura
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            
            $TipoPago=$obCon->normalizar($_REQUEST["CmbTipoPago"]);
            $CuentaOrigen=$obCon->normalizar($_REQUEST["CmbCuentaOrigen"]);
            $CuentaPUCCXP=$obCon->normalizar($_REQUEST["CmbCuentaPUCCXP"]);
            $FechaProgramada=$obCon->normalizar($_REQUEST["TxtFechaProgramada"]);
            $obCon->GuardarFacturaCompra($idCompra, $TipoPago, $CuentaOrigen,$CuentaPUCCXP, $FechaProgramada,"");
            $obCon->ActualicePreciosVentaFacturaCompra($idCompra);
            $LinkTraslado="";
            $LinkFactura="../../VAtencion/PDF_FCompra.php?ID=$idCompra";
            $MensajeTraslado="";
            if($_REQUEST["CmbTraslado"]>0){
                $idSede=$obCon->normalizar($_REQUEST["CmbTraslado"]);
                $idTraslado=$obCon->CrearTrasladoDesdeCompra($idCompra,$idSede, "");
                $LinkTraslado="../../tcpdf/examples/imprimirTraslado.php?idTraslado=$idTraslado";
                $MensajeTraslado="<br><strong>Traslado $idTraslado Creado Correctamente </strong><a href='$LinkTraslado'  target='blank'> Imprimir</a>";
            }
            $Mensaje="<strong>Factura $idCompra Creada Correctamente </strong><a href='$LinkFactura'  target='blank'> Imprimir</a>";
            $Mensaje.=$MensajeTraslado;
            print("OK;$Mensaje");
        break;//Fin caso 9
        
        case 10://copio los items de una orden a una compra
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);  
            $idOrdenCompra=$obCon->normalizar($_REQUEST["idOrdenCompra"]);    
            $obCon->AgregueItemDesdeOrdenCompra($idCompra, $idOrdenCompra, "");
            $Mensaje="Items Copiados";
            print("OK;$Mensaje");
        break;//Fin caso 10
    
        case 11://Aplico descuento a un item de una compra
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);  
            $idFacturaItems=$obCon->normalizar($_REQUEST["idItem"]);   
            $Descuento=$obCon->normalizar($_REQUEST["Descuento"]);  
            if(!is_numeric($Descuento)){
                print("E1;El Valor del descuento debe ser númerico");
                exit();
            }
            if($Descuento>100){
                print("E1;El Valor del descuento no puede ser mayor a 100");
                exit();
            }
            
            if($Descuento<=0){
                print("E1;El Valor del descuento no puede ser menor o igual a cero");
                exit();
            }
            $DatosItem=$obCon->DevuelveValores("factura_compra_items", "ID", $idFacturaItems);
    
            $ValorDescuento=round($DatosItem["CostoUnitarioCompra"]*($Descuento/100),2);
            $SubtotalDescuento=$ValorDescuento*$DatosItem["Cantidad"];
            $ValorUnitario=$DatosItem["CostoUnitarioCompra"]-$ValorDescuento;
            $Subtotal=$ValorUnitario*$DatosItem["Cantidad"];
            $IVA=round($Subtotal*$DatosItem["Tipo_Impuesto"],2);
            $Total=$Subtotal+$IVA;
            $obCon->ActualizaRegistro("factura_compra_items", "ProcentajeDescuento", $Descuento, "ID", $idFacturaItems); 
            $obCon->ActualizaRegistro("factura_compra_items", "ValorDescuento", $ValorDescuento, "ID", $idFacturaItems);
            $obCon->ActualizaRegistro("factura_compra_items", "SubtotalDescuento", $SubtotalDescuento, "ID", $idFacturaItems);
            $obCon->ActualizaRegistro("factura_compra_items", "CostoUnitarioCompra", $ValorUnitario, "ID", $idFacturaItems); 
            $obCon->ActualizaRegistro("factura_compra_items", "SubtotalCompra", $Subtotal, "ID", $idFacturaItems); 
            $obCon->ActualizaRegistro("factura_compra_items", "ImpuestoCompra", $IVA, "ID", $idFacturaItems); 
            $obCon->ActualizaRegistro("factura_compra_items", "TotalCompra", $Total, "ID", $idFacturaItems); 
            $Mensaje="Descuento aplicado";
            print("OK;$Mensaje");
        break;//Fin caso 11
        
        case 12://copio los items de una factura a otra
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            $idFacturaCopiar=$obCon->normalizar($_REQUEST["idFacturaCopiar"]); 
            $idCompraNew=$obCon->CopiarFacturaCompra($idFacturaCopiar,$idCompra,$idUser, "");
            
            $Mensaje="Factura $idCompra copiada";
            print("OK;$Mensaje");
        break;//Fin caso 12
    
        case 13://edito el Valor unitario de un item
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $idTabla=$obCon->normalizar($_REQUEST["idTabla"]); 
            $Valor=$obCon->normalizar($_REQUEST["Valor"]); 
            if(!isset($Valor) or $Valor<=0){
                print("el valor debe ser un numero mayor a cero back");
                exit();
            }
            $sql="UPDATE factura_compra_items SET CostoUnitarioCompra=round('$Valor',2),SubtotalCompra=CostoUnitarioCompra*Cantidad,ImpuestoCompra=round(SubtotalCompra*Tipo_Impuesto,2), TotalCompra=SubtotalCompra+ImpuestoCompra WHERE ID='$idItem'";
            $obCon->Query($sql);
            print("OK");
        break;//Fin caso 13
        
        case 14://edito la cantidad de un item
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $idTabla=$obCon->normalizar($_REQUEST["idTabla"]); 
            $Valor=$obCon->normalizar($_REQUEST["Valor"]); 
            if(!isset($Valor) or $Valor<=0){
                print("el valor digitado debe ser un numero mayor a cero");
                exit();
            }
            $sql="UPDATE factura_compra_items SET Cantidad=round('$Valor',2),SubtotalCompra=CostoUnitarioCompra*Cantidad,ImpuestoCompra=round(SubtotalCompra*Tipo_Impuesto,2), TotalCompra=SubtotalCompra+ImpuestoCompra WHERE ID='$idItem'";
            $obCon->Query($sql);
            print("OK");
        break;//Fin caso 14
        
        case 15://copio los items de una orden a una compra verificada
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);  
            $idOrdenCompra=$obCon->normalizar($_REQUEST["idOrdenCompra"]);    
            $obCon->AgregueItemDesdeOrdenCompraVerificada($idCompra, $idOrdenCompra, "");
            $Mensaje="Items Copiados";
            print("OK;$Mensaje");
        break;//Fin caso 15
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>