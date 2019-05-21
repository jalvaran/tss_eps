<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/Compras.class.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Compras($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //dibujar el formulario para crear una compra nueva
            $css->input("hidden", "idAccion", "", "TxtOpcionGuardarEditar", "", "1", "", "", "", "");        
           
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<h4><strong>Fecha:</strong></h4>");
                $css->input("date", "TxtFecha", "form-control", "TxtFecha", "Fecha", date("Y-m-d"), "Fecha", "off", "", "","style='line-height: 15px;'");
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                print("<h4><strong>Tercero:</strong></h4>");
                $css->select("CmbTerceroCrearCompra", "form-control", "CmbTerceroCrearCompra", "", "", "", "style=width:300px");
                    $css->option("", "", "", "", "", "");
                        print("Seleccione un tercero");
                    $css->Coption();
                $css->Cselect();
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "center", 1, 1); 
                print("<h4><strong>Centro de costos:</strong></h4>");
                $css->select("CmbCentroCosto", "form-control", "CmbCentroCosto", "", "", "", "");
                $Consulta = $obCon->ConsultarTabla("centrocosto","");
                while($CentroCosto=$obCon->FetchArray($Consulta)){
                    $css->option("", "", "", $CentroCosto['ID'], "", "");
                        print($CentroCosto['ID']." ".$CentroCosto['Nombre']);
                    $css->Coption();
                    							
                }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1); 
                print("<h4><strong>Sucursal:</strong></h4>");
                $css->select("idSucursal", "form-control", "idSucursal", "", "", "", "");
                $Consulta = $obCon->ConsultarTabla("empresa_pro_sucursales","");
                while($CentroCosto=$obCon->FetchArray($Consulta)){
                    $css->option("", "", "", $CentroCosto['ID'], "", "");
                        print($CentroCosto['ID']." ".$CentroCosto['Nombre']);
                    $css->Coption();
                    							
                }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<h4><strong>Tipo:</strong></h4>");
                $css->select("TipoCompra", "form-control", "TipoCompra", "", "", "", "");
                    $css->option("", "", "", "FC", "", "");
                        print("FC");
                    $css->Coption();
                    $css->option("", "", "", "RM", "", "");
                        print("RM");
                    $css->Coption();
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                print("<h4><strong>Concepto:</strong></h4>");
                $css->textarea("TxtConcepto", "form-control", "TxtConcepto", "Concepto", "Concepto", "", "");
                $css->Ctextarea();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                print("<h4><strong>No. Comprobante:</strong></h4>");
                $css->input("text", "TxtNumFactura", "form-control", "TxtNumFactura", "Comprobante", "", "Comprobante", "off", "", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                print("<h4><strong>Soporte:</strong></h4>");
                $css->input("file", "UpSoporte", "", "UpSoporte", "Soporte", "", "Soporte", "off", "", "style=width:100%");
            $css->CerrarDiv();
            
            print("<br><br><br><br><br><br><br><br><br><br>");
            
        break; 
        case 2:// se dibuja el formulario para editar los datos generales de la compra
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            $DatosCompra=$obCon->DevuelveValores("factura_compra", "ID", $idCompra);
            $DatosTercero=$obCon->DevuelveValores("proveedores", "Num_Identificacion", $DatosCompra["Tercero"]);
            $css->input("hidden", "idAccion", "", "TxtOpcionGuardarEditar", "", "2", "", "", "", "");        
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<h4><strong>Fecha:</strong></h4>");
                $css->input("date", "TxtFecha", "form-control", "TxtFecha", "Fecha", $DatosCompra["Fecha"], "Fecha", "off", "", "","style='line-height: 15px;'");
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                print("<h4><strong>Tercero:</strong></h4>");
                $css->select("CmbTerceroCrearCompra", "form-control", "CmbTerceroCrearCompra", "", "", "", "style=width:300px");
                    $css->option("", "", "", $DatosCompra["Tercero"], "", "");
                        print($DatosTercero["RazonSocial"]." ".$DatosCompra["Tercero"]);
                    $css->Coption();
                $css->Cselect();
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "center", 1, 1); 
                print("<h4><strong>Centro de costos:</strong></h4>");
                $css->select("CmbCentroCosto", "form-control", "CmbCentroCosto", "", "", "", "");
                $Consulta = $obCon->ConsultarTabla("centrocosto","");
                while($CentroCosto=$obCon->FetchArray($Consulta)){
                    $Sel=0;
                    if($CentroCosto["ID"]==$DatosCompra["idCentroCostos"]){
                        $Sel=1;
                    }
                    $css->option("", "", "", $CentroCosto['ID'], "", "",$Sel);
                        print($CentroCosto['ID']." ".$CentroCosto['Nombre']);
                    $css->Coption();
                    							
                }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1); 
                print("<h4><strong>Sucursal:</strong></h4>");
                $css->select("idSucursal", "form-control", "idSucursal", "", "", "", "");
                $Consulta = $obCon->ConsultarTabla("empresa_pro_sucursales","");
                while($CentroCosto=$obCon->FetchArray($Consulta)){
                    $Sel=0;
                    if($CentroCosto["ID"]==$DatosCompra["idSucursal"]){
                        $Sel=1;
                    }
                    $css->option("", "", "", $CentroCosto['ID'], "", "",$Sel);
                        print($CentroCosto['ID']." ".$CentroCosto['Nombre']);
                    $css->Coption();
                    							
                }
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<h4><strong>Tipo:</strong></h4>");
                $css->select("TipoCompra", "form-control", "TipoCompra", "", "", "", "");
                    $css->option("", "", "", "FC", "", "");
                        print("FC");
                    $css->Coption();
                    $css->option("", "", "", "RM", "", "");
                        print("RM");
                    $css->Coption();
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                print("<h4><strong>Concepto:</strong></h4>");
                $css->textarea("TxtConcepto", "form-control", "TxtConcepto", "Concepto", "Concepto", "", "");
                    print($DatosCompra["Concepto"]);
                $css->Ctextarea();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                print("<h4><strong>No. Comprobante:</strong></h4>");
                $css->input("text", "TxtNumFactura", "form-control", "TxtNumFactura", "Comprobante", $DatosCompra["NumeroFactura"], "Comprobante", "off", "", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                print("<h4><strong>Soporte:</strong></h4>");
                $css->input("file", "UpSoporte", "", "UpSoporte", "Soporte", "", "Soporte", "off", "", "style=width:100%");
            $css->CerrarDiv();
            
            print("<br><br><br><br><br><br><br><br><br><br>");
            
        break;  
        
        case 3://Dibuja los items de una compra
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>ID</strong>", 1, "C");
                    
                    print("<td style=text-align:center;width:100px>");
                        print("<strong>Tiquetes</strong>");
                        $css->input("number", "CantidadTiquetes", "form-control", "CantidadTiquetes", "Tiquetes", 1, "Tiquetes", "off", "", "min=1 max=100");
                    print("</td>");
                    
                    $css->ColTabla("<strong>Nombre</strong>", 1, "C");
                    $css->ColTabla("<strong>Cantidad</strong>", 1, "C");
                    $css->ColTabla("<strong>Costo Unitario</strong>", 1, "C");
                    $css->ColTabla("<strong>Subtotal</strong>", 1, "C");
                    $css->ColTabla("<strong>Impuestos</strong>", 1, "C");
                    $css->ColTabla("<strong>Total</strong>", 1, "C");
                    $css->ColTabla("<strong>Precio Venta</strong>", 1, "C");
                    $css->ColTabla("<strong>Descontar %</strong>", 1, "C");
                    $css->ColTabla("<strong>% Impuestos</strong>", 1, "C");
                    print("<td style=text-align:center;width:100px>");
                        print("<strong>Devolver</strong>");
                        $css->input("number", "CantidadDevolucion", "form-control", "CantidadDevolucion", "Devolver", 0, "Devolver", "off", "", "min=1 max=100");
                    print("</td>");
                    $css->ColTabla("<strong>Eliminar</strong>", 1, "C");
                    
                $css->CierraFilaTabla();
                //Dibujo los productos
                $sql="SELECT *,(SELECT Nombre FROM productosventa WHERE idProductosVenta=factura_compra_items.idProducto) as NombreProducto
                            ,(SELECT Referencia FROM productosventa WHERE idProductosVenta=factura_compra_items.idProducto) as Referencia
                         FROM factura_compra_items WHERE idFacturaCompra='$idCompra' ORDER BY ID DESC";
                $Consulta=$obCon->Query($sql);
                while ($DatosItems = $obCon->FetchAssoc($Consulta)) {
                    $idItem=$DatosItems["ID"];
                    $idProducto=$DatosItems["idProducto"];
                    $css->FilaTabla(14);
                        $css->ColTabla($DatosItems["Referencia"], 1, "C");
                        
                        print("<td onclick=PrintEtiqueta($idProducto) style='font-size:16px;cursor:pointer;text-align:center;color:green' title='Imprimir Tiquete'>");
                        
                            $css->li("", "fa fa-print", "", "");
                            $css->Cli();
                        print("</td>");
                        if(is_numeric($DatosItems["Tipo_Impuesto"])){
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"]*100;
                            $PorcentajeImpuestos=$PorcentajeImpuestos."%";
                        }else{
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"];
                        }
                        $css->ColTabla($DatosItems["NombreProducto"], 1, "C");
                        print("<td>");
                            $NombreCaja="CantidadItem_$idItem";
                            $css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosItems["Cantidad"], "Costo", "off", "", "onchange=EditarCantidadItem(`$NombreCaja`,`1`,`$idItem`)");
                        print("</td>");
                        print("<td>");
                            $NombreCaja="CostoProducto_$idItem";
                            $css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosItems["CostoUnitarioCompra"], "Costo", "off", "", "onchange=EditarCostoUnitario(`$NombreCaja`,`1`,`$idItem`)");
                        print("</td>");
                        //$css->ColTabla(number_format($DatosItems["CostoUnitarioCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["SubtotalCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["ImpuestoCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["TotalCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["PrecioVenta"],2,",","."), 1, "C");
                        
                        
                        print("<td>");
                            print('<div class="input-group input-group-md" style=width:100px>');
                            
                                $css->input("number", "TxtDescuentoItem_$idItem", "form-control", "TxtDescuentoItem_$idItem", "Descuento", 0, "", "off", "", "");
                                print('<span class="input-group-btn">');
                                    print('<button type="button" id="BtnEditarDescuento_'.$idItem.'" class="btn btn-info btn-flat" onclick=AplicarDescuentoItem('.$idItem.')>E</button>');
                                    
                                print('</span>');
                            print('</div>');
                            
                            
                        print("</td>");
                        
                        
                        $css->ColTabla($PorcentajeImpuestos, 1, "C");
                        
                       print("<td style='font-size:16px;text-align:center;color:red' title='Devolver'>");   
                            
                            $css->li("", "fa fa-reply-all", "", "onclick=DevolverItem(`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                        
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`1`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                    $css->CierraFilaTabla();
                }
                //Dibujo los insumos
                $sql="SELECT *,(SELECT Nombre FROM insumos WHERE ID=factura_compra_insumos.idProducto) as NombreProducto
                         FROM factura_compra_insumos WHERE idFacturaCompra='$idCompra' ORDER BY ID DESC";
                $Consulta=$obCon->Query($sql);
                while ($DatosItems = $obCon->FetchAssoc($Consulta)) {
                    $idItem=$DatosItems["ID"];
                    $idProducto=$DatosItems["idProducto"];
                    $css->FilaTabla(12);
                        $css->ColTabla($DatosItems["idProducto"], 1, "C");
                        
                        print("<td style='font-size:16px;cursor:pointer;text-align:center;color:green' title='Insumos'>");
                        
                           print("Insumo");
                        print("</td>");
                        if(is_numeric($DatosItems["Tipo_Impuesto"])){
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"]*100;
                            $PorcentajeImpuestos=$PorcentajeImpuestos."%";
                        }else{
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"];
                        }
                        $css->ColTabla($DatosItems["NombreProducto"], 1, "C");
                        $css->ColTabla(number_format($DatosItems["Cantidad"]), 1, "C");
                        $css->ColTabla(number_format($DatosItems["CostoUnitarioCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["SubtotalCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["ImpuestoCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["TotalCompra"],2,",","."), 1, "C");
                        $css->ColTabla("NA", 1, "C");
                        $css->ColTabla("NA", 1, "C");
                        $css->ColTabla($PorcentajeImpuestos, 1, "C");
                        
                        $css->ColTabla("NA", 1, "C");
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`3`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                    $css->CierraFilaTabla();
                }
                
                //Dibujo los servicios
                
                $sql="SELECT * FROM factura_compra_servicios WHERE idFacturaCompra='$idCompra' ORDER BY ID DESC";
                $Consulta=$obCon->Query($sql);
                while ($DatosItems = $obCon->FetchAssoc($Consulta)) {
                    $idItem=$DatosItems["ID"];
                    
                    $css->FilaTabla(12);
                        $css->ColTabla($DatosItems["CuentaPUC_Servicio"], 1, "C");
                        
                        print("<td style='font-size:16px;cursor:pointer;text-align:center;color:blue' title='Servicios'>");
                        
                            print("Servicio");
                        print("</td>");
                        if(is_numeric($DatosItems["Tipo_Impuesto"])){
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"]*100;
                            $PorcentajeImpuestos=$PorcentajeImpuestos."%";
                        }else{
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"];
                        }
                        $css->ColTabla($DatosItems["Concepto_Servicio"], 1, "C");
                        $css->ColTabla(1, 1, "C");
                        $css->ColTabla(number_format($DatosItems["Subtotal_Servicio"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["Subtotal_Servicio"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["Impuesto_Servicio"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["Total_Servicio"],2,",","."), 1, "C");
                        $css->ColTabla("NA", 1, "C");
                        $css->ColTabla("NA", 1, "C");
                        $css->ColTabla($PorcentajeImpuestos, 1, "C");
                        
                        $css->ColTabla("NA", 1, "C");
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`2`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                    $css->CierraFilaTabla();
                }
                //dibujo los productos devueltos
                $sql="SELECT *,(SELECT Nombre FROM productosventa WHERE idProductosVenta=factura_compra_items_devoluciones.idProducto) as NombreProducto
                         FROM factura_compra_items_devoluciones WHERE idFacturaCompra='$idCompra' ORDER BY ID DESC";
                $Consulta=$obCon->Query($sql);
                $FlagTitulo=1;
                while ($DatosItems = $obCon->FetchAssoc($Consulta)) {
                    if($FlagTitulo==1){
                        $css->FilaTabla(12);
                            $css->ColTabla("<strong>Devoluciones</strong>", 11, "C");
                        $css->CierraFilaTabla(); 
                        $FlagTitulo=0;
                    }
                    $idItem=$DatosItems["ID"];
                    $idProducto=$DatosItems["idProducto"];
                    $css->FilaTabla(12);
                        $css->ColTabla($DatosItems["idProducto"], 1, "C");
                        
                        print("<td style='font-size:16px;cursor:pointer;text-align:center;color:red' title='Insumos'>");
                        
                           print("Devolución");
                        print("</td>");
                        if(is_numeric($DatosItems["Tipo_Impuesto"])){
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"]*100;
                            $PorcentajeImpuestos=$PorcentajeImpuestos."%";
                        }else{
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"];
                        }
                        $css->ColTabla($DatosItems["NombreProducto"], 1, "C");
                        $css->ColTabla(number_format($DatosItems["Cantidad"]), 1, "C");
                        $css->ColTabla(number_format($DatosItems["CostoUnitarioCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["SubtotalCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["ImpuestoCompra"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["TotalCompra"],2,",","."), 1, "C");
                        $css->ColTabla("NA", 1, "C");
                        $css->ColTabla("NA", 1, "C");
                        $css->ColTabla($PorcentajeImpuestos, 1, "C");
                        
                       print("<td style='text-align:center' title=''>");   
                            
                            print("NA");
                        print("</td>");
                        
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`4`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;// fin caso 3
        
        case 4://Dibujo los Totales
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            $TotalesCompras=$obCon->CalculeTotalesCompra($idCompra);
            if(($TotalesCompras["Subtotal_Productos_Add"]+$TotalesCompras["Subtotal_Insumos"]+$TotalesCompras["Subtotal_Servicios"])>0){ //Verifico que hayan productos, servicios o insumos agregados
            $css->div("", "col-md-8", "", "", "","", "style=text-align:left");
                $css->CrearTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>PRODUCTOS</strong>", 7,'C');
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Subtotal:</strong>", 1,'R');
                        
                        $css->ColTabla(number_format($TotalesCompras["Subtotal_Productos_Add"]+$TotalesCompras["Subtotal_Descuentos_Productos_Add"]), 1,'R');
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Descuentos:</strong>", 1,'R');
                        
                        $css->ColTabla(number_format($TotalesCompras["Subtotal_Descuentos_Productos_Add"]), 1,'R');
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Devoluciones:</strong>", 1,'R');
                        
                        $css->ColTabla(number_format($TotalesCompras["Subtotal_Productos_Dev"]), 1,'R');
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Subtotal - Devoluciones - Descuentos:</strong>", 1,'R');
                        $css->input("hidden", "TxtSubtotalProductos", "", "", "", $TotalesCompras["Subtotal_Productos"], "", "", "", "");
                        $css->ColTabla(number_format($TotalesCompras["Subtotal_Productos"]), 1,'R');
                        print("<td>");
                            
                            $css->select("CmbImpRetDesProductos", "form-control", "CmbImpRetDesProductos", "", "", "", "onclick=MuestreOpcionesEnTotales(1)");
                                $css->option("", "", "", "", "", "");
                                    print("Elija una opción para aplicar:");
                                $css->Coption();
                                $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 24);
                                $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                    print("Retefuente ".$Parametros["CuentaPUC"]);
                                $css->Coption();
                                $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 25);
                                $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                    print("ReteICA ".$Parametros["CuentaPUC"]);
                                $css->Coption();   
                                $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 28);
                                $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                    print("Descuentos Generales ".$Parametros["CuentaPUC"]);
                                $css->Coption();
                                $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 29);
                                $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                    print("Impoconsumo ".$Parametros["CuentaPUC"]);
                                $css->Coption();
                            $css->Cselect();
                                
                        print("</td>");
                            
                                                
                        print("<td>");
                            $css->CrearDiv("DivImpRetDesPro2", "", "", 0, 0);
                            
                                $css->CrearInputText("TxtCargosPorcentajeProductos", "text", "", "", "%", "", "onkeyup", "CalculeRetencionDescuento(1)", 60, 30, 0, 1);
                            $css->CerrarDiv();    
                        print("</td>");                        
                        print("<td>"); 
                            $css->CrearDiv("DivImpRetDesPro3", "", "", 0, 0);
                                $css->CrearInputText("TxtCargosValorProductos", "text", "", "", "Valor", "", "onkeyup", "CalculeRetencionDescuento(2)", 100, 30, 0, 1);
                            $css->CerrarDiv();
                        print("</td>");
                        print("<td>"); 
                            $css->CrearDiv("DivImpRetDesPro4", "", "", 0, 0);
                                $css->CrearBotonEvento("BtnAgregarCargosProductos", "Agregar", 1, "onclick", "AgregarCargosProductos(event)", "naranja", "");
                            $css->CerrarDiv();
                        print("</td>");
                    $css->CierraFilaTabla();
                    
                    if($TotalesCompras["Impuestos_Productos_Add"]>0){
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>Impuestos:</strong>", 1,'R');
                            $css->input("hidden", "TxtImpuestosProductos", "", "", "", $TotalesCompras["Impuestos_Productos_Add"], "", "", "", "");
                            $css->ColTabla(number_format($TotalesCompras["Impuestos_Productos_Add"]), 1,'R');
                            print("<td>");

                                $css->select("CmbImpuestosProductos", "form-control", "CmbImpuestosProductos", "", "", "", "onclick=MuestreOpcionesEnTotales(2)");
                                    $css->option("", "", "", "", "", "");
                                        print("Elija una opción para aplicar:");
                                    $css->Coption();
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 26);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("ReteIVA ".$Parametros["CuentaPUC"]);
                                    $css->Coption();
                                    
                                $css->Cselect();

                            print("</td>");


                            print("<td>");
                                $css->CrearDiv("DivImpRetDesPro5", "", "", 0, 0);

                                    $css->CrearInputText("TxtCargosPorcentajeProductosImpuestos", "text", "", "", "%", "", "onkeyup", "CalculeRetencionDescuento(3)", 60, 30, 0, 1);
                                $css->CerrarDiv();    
                            print("</td>");                        
                            print("<td>"); 
                                $css->CrearDiv("DivImpRetDesPro6", "", "", 0, 0);
                                    $css->CrearInputText("TxtCargosValorProductosImpuestos", "text", "", "", "Valor", "", "onkeyup", "CalculeRetencionDescuento(4)", 100, 30, 0, 1);
                                $css->CerrarDiv();
                            print("</td>");
                            print("<td>"); 
                                $css->CrearDiv("DivImpRetDesPro7", "", "", 0, 0);
                                    $css->CrearBotonEvento("BtnAgregarCargosProductos", "Agregar", 1, "onclick", "AgregarCargosProductosImpuestos(event)", "naranja", "");
                                $css->CerrarDiv();
                            print("</td>");
                        $css->CierraFilaTabla();
                    }
                    
                    // dibujo el total de lss insumos
                    
                    if($TotalesCompras["Subtotal_Insumos"]>0){
                        
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>INSUMOS</strong>", 7,'C');
                        $css->CierraFilaTabla();
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>Subtotal:</strong>", 1,'R');
                            $css->input("hidden", "TxtSubtotalInsumos", "", "", "", $TotalesCompras["Subtotal_Insumos"], "", "", "", "");
                            $css->ColTabla(number_format($TotalesCompras["Subtotal_Insumos"]), 1,'R');
                            print("<td>");

                                $css->select("CmbImpRetDesInsumos", "form-control", "CmbImpRetDesInsumos", "", "", "", "onclick=MuestreOpcionesEnTotales(3)");
                                    $css->option("", "", "", "", "", "");
                                        print("Elija una opción para aplicar:");
                                    $css->Coption();
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 24);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("Retefuente ".$Parametros["CuentaPUC"]);
                                    $css->Coption();
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 25);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("ReteICA ".$Parametros["CuentaPUC"]);
                                    $css->Coption();   
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 28);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("Descuentos Generales ".$Parametros["CuentaPUC"]);
                                    $css->Coption();
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 29);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("Impoconsumo ".$Parametros["CuentaPUC"]);
                                    $css->Coption();
                                $css->Cselect();

                            print("</td>");


                            print("<td>");
                                $css->CrearDiv("DivImpRetDesPro8", "", "", 0, 0);

                                    $css->CrearInputText("TxtCargosPorcentajeInsumos", "text", "", "", "%", "", "onkeyup", "CalculeRetencionDescuento(5)", 60, 30, 0, 1);
                                $css->CerrarDiv();    
                            print("</td>");                        
                            print("<td>"); 
                                $css->CrearDiv("DivImpRetDesPro9", "", "", 0, 0);
                                    $css->CrearInputText("TxtCargosValorInsumos", "text", "", "", "Valor", "", "onkeyup", "CalculeRetencionDescuento(6)", 100, 30, 0, 1);
                                $css->CerrarDiv();
                            print("</td>");
                            print("<td>"); 
                                $css->CrearDiv("DivImpRetDesPro10", "", "", 0, 0);
                                    $css->CrearBotonEvento("BtnAgregarCargosInsumos", "Agregar", 1, "onclick", "AgregarCargosSubtotalInsumos(event)", "naranja", "");
                                $css->CerrarDiv();
                            print("</td>");
                        $css->CierraFilaTabla();

                        if($TotalesCompras["Impuestos_Insumos"]>0){
                            $css->FilaTabla(16);
                                $css->ColTabla("<strong>Impuestos:</strong>", 1,'R');
                                $css->input("hidden", "TxtImpuestosInsumos", "", "", "", $TotalesCompras["Impuestos_Insumos"], "", "", "", "");
                                $css->ColTabla(number_format($TotalesCompras["Impuestos_Insumos"]), 1,'R');
                                print("<td>");

                                    $css->select("CmbImpuestosInsumos", "form-control", "CmbImpuestosInsumos", "", "", "", "onclick=MuestreOpcionesEnTotales(4)");
                                        $css->option("", "", "", "", "", "");
                                            print("Elija una opción para aplicar:");
                                        $css->Coption();
                                        $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 26);
                                        $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                            print("ReteIVA ".$Parametros["CuentaPUC"]);
                                        $css->Coption();

                                    $css->Cselect();

                                print("</td>");


                                print("<td>");
                                    $css->CrearDiv("DivImpRetDesPro11", "", "", 0, 0);

                                        $css->CrearInputText("TxtCargosPorcentajeInsumosImpuestos", "text", "", "", "%", "", "onkeyup", "CalculeRetencionDescuento(7)", 60, 30, 0, 1);
                                    $css->CerrarDiv();    
                                print("</td>");                        
                                print("<td>"); 
                                    $css->CrearDiv("DivImpRetDesPro12", "", "", 0, 0);
                                        $css->CrearInputText("TxtCargosValorInsumosImpuestos", "text", "", "", "Valor", "", "onkeyup", "CalculeRetencionDescuento(8)", 100, 30, 0, 1);
                                    $css->CerrarDiv();
                                print("</td>");
                                print("<td>"); 
                                    $css->CrearDiv("DivImpRetDesPro13", "", "", 0, 0);
                                        $css->CrearBotonEvento("BtnAgregarCargosInsumos", "Agregar", 1, "onclick", "AgregarCargosInsumosImpuestos(event)", "naranja", "");
                                    $css->CerrarDiv();
                                print("</td>");
                            $css->CierraFilaTabla();
                        }
                    }
                    
                    
                    // dibujo el total de lss servicios
                    
                    if($TotalesCompras["Subtotal_Servicios"]>0){
                        
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>SERVICIOS</strong>", 7,'C');
                        $css->CierraFilaTabla();
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>Subtotal:</strong>", 1,'R');
                            $css->input("hidden", "TxtSubtotalServicios", "", "", "", $TotalesCompras["Subtotal_Servicios"], "", "", "", "");
                            $css->ColTabla(number_format($TotalesCompras["Subtotal_Servicios"]), 1,'R');
                            print("<td>");

                                $css->select("CmbImpRetDesServicios", "form-control", "CmbImpRetDesServicios", "", "", "", "onclick=MuestreOpcionesEnTotales(5)");
                                    $css->option("", "", "", "", "", "");
                                        print("Elija una opción para aplicar:");
                                    $css->Coption();
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 27);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("Retefuente por Servicios ".$Parametros["CuentaPUC"]);
                                    $css->Coption();
                                    
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 32);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("Retefuente por Honorarios ".$Parametros["CuentaPUC"]);
                                    $css->Coption();
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 25);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("ReteICA ".$Parametros["CuentaPUC"]);
                                    $css->Coption();   
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 28);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("Descuentos Generales ".$Parametros["CuentaPUC"]);
                                    $css->Coption();
                                    $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 29);
                                    $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                        print("Impoconsumo ".$Parametros["CuentaPUC"]);
                                    $css->Coption();
                                $css->Cselect();

                            print("</td>");


                            print("<td>");
                                $css->CrearDiv("DivImpRetDesPro14", "", "", 0, 0);

                                    $css->CrearInputText("TxtCargosPorcentajeServicios", "text", "", "", "%", "", "onkeyup", "CalculeRetencionDescuento(9)", 60, 30, 0, 1);
                                $css->CerrarDiv();    
                            print("</td>");                        
                            print("<td>"); 
                                $css->CrearDiv("DivImpRetDesPro15", "", "", 0, 0);
                                    $css->CrearInputText("TxtCargosValorServicios", "text", "", "", "Valor", "", "onkeyup", "CalculeRetencionDescuento(10)", 100, 30, 0, 1);
                                $css->CerrarDiv();
                            print("</td>");
                            print("<td>"); 
                                $css->CrearDiv("DivImpRetDesPro16", "", "", 0, 0);
                                    $css->CrearBotonEvento("BtnAgregarCargosServicios", "Agregar", 1, "onclick", "AgregarCargosSubtotalServicios(event)", "naranja", "");
                                $css->CerrarDiv();
                            print("</td>");
                        $css->CierraFilaTabla();

                        if($TotalesCompras["Impuestos_Servicios"]>0){
                            $css->FilaTabla(16);
                                $css->ColTabla("<strong>Impuestos:</strong>", 1,'R');
                                $css->input("hidden", "TxtImpuestosServicios", "", "", "", $TotalesCompras["Impuestos_Servicios"], "", "", "", "");
                                $css->ColTabla(number_format($TotalesCompras["Impuestos_Servicios"]), 1,'R');
                                print("<td>");

                                    $css->select("CmbImpuestosServicios", "form-control", "CmbImpuestosServicios", "", "", "", "onclick=MuestreOpcionesEnTotales(6)");
                                        $css->option("", "", "", "", "", "");
                                            print("Elija una opción para aplicar:");
                                        $css->Coption();
                                        $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 26);
                                        $css->option("", "", "", $Parametros["CuentaPUC"], "", "");
                                            print("ReteIVA ".$Parametros["CuentaPUC"]);
                                        $css->Coption();

                                    $css->Cselect();

                                print("</td>");


                                print("<td>");
                                    $css->CrearDiv("DivImpRetDesPro17", "", "", 0, 0);

                                        $css->CrearInputText("TxtCargosPorcentajeServiciosImpuestos", "text", "", "", "%", "", "onkeyup", "CalculeRetencionDescuento(11)", 60, 30, 0, 1);
                                    $css->CerrarDiv();    
                                print("</td>");                        
                                print("<td>"); 
                                    $css->CrearDiv("DivImpRetDesPro18", "", "", 0, 0);
                                        $css->CrearInputText("TxtCargosValorServiciosImpuestos", "text", "", "", "Valor", "", "onkeyup", "CalculeRetencionDescuento(12)", 100, 30, 0, 1);
                                    $css->CerrarDiv();
                                print("</td>");
                                print("<td>"); 
                                    $css->CrearDiv("DivImpRetDesPro19", "", "", 0, 0);
                                        $css->CrearBotonEvento("BtnAgregarCargosServiciosImpuestos", "Agregar", 1, "onclick", "AgregarCargosServiciosImpuestos(event)", "naranja", "");
                                    $css->CerrarDiv();
                                print("</td>");
                            $css->CierraFilaTabla();
                        }
                    }
                    
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>CONSOLIDADO</strong>", 6,"C");
                        
                    $css->CierraFilaTabla();
                    if($TotalesCompras["Total_Productos_Dev"]>0){
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>Total Devoluciones:</strong>", 1,"R");
                            $css->ColTabla(number_format($TotalesCompras["Total_Productos_Dev"]), 1,"R");
                            $css->ColTabla("", 4);
                        $css->CierraFilaTabla();
                    }
                    
                    
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Subtotal Factura:</strong>", 1,"R");
                        $css->ColTabla(number_format($TotalesCompras["Gran_Subtotal"]), 1,"R");
                        $css->ColTabla("", 4);
                    $css->CierraFilaTabla();
                                        
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Impuestos Factura:</strong>", 1,"R");
                        $css->ColTabla(number_format($TotalesCompras["Gran_Impuestos"]), 1,"R");
                        $css->ColTabla("", 4);
                    $css->CierraFilaTabla();
                    if($TotalesCompras["ImpuestosAdicionales"]>0){
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>Impuestos Adicionales:</strong>", 1,"R");
                            $css->ColTabla(number_format($TotalesCompras["ImpuestosAdicionales"]), 1,"R");
                            $css->ColTabla("", 4);
                        $css->CierraFilaTabla();
                    }
                    if($TotalesCompras["DescuentosGlobales"]>0){
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>Descuentos Globales:</strong>", 1,"R");
                            $css->ColTabla(number_format($TotalesCompras["DescuentosGlobales"]), 1,"R");
                            $css->ColTabla("", 4);
                        $css->CierraFilaTabla();
                    }
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Total Factura:</strong>", 1,"R");
                        $css->ColTabla(number_format($TotalesCompras["Gran_Total"]), 1,"R");
                        $css->ColTabla("", 4);
                    $css->CierraFilaTabla();
                    
                    if($TotalesCompras["Total_Retenciones"]>0){
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>Retenciones:</strong>", 1,"R");
                            $css->ColTabla(number_format($TotalesCompras["Total_Retenciones"]), 1,"R");
                            $css->ColTabla("", 4);
                        $css->CierraFilaTabla();
                    }
                    
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Total a Pagar:</strong>", 1,"R");
                        $css->ColTabla(number_format($TotalesCompras["Total_Pago"]), 1,"R");
                        $css->ColTabla("", 4);
                    $css->CierraFilaTabla();
                    
                    //Formas de pago y opciones
                    
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>OPCIONES</strong>", 6,"C");
                        
                    $css->CierraFilaTabla();
                    
                    $css->FilaTabla(14);
                        
                        print("<td colspan=6>");
                        print("<strong>Tipo de Pago:</strong><br>");
                        $css->select("CmbTipoPago", "form-control", "CmbTipoPago", "", "", "onchange=MuestraOcultaXIDCompras('DivCuentaOrigen');MuestraOcultaXIDCompras('DivCuentaXPagar')", "");
                            $css->option("", "", "", "Contado", "", "",1);
                                print("Contado");
                            $css->Coption();                            
                            $css->option("", "", "", "Credito", "", "");
                                print("Credito");
                            $css->Coption();
                            
                        $css->Cselect();
                        
                        $css->CrearDiv("DivCuentaOrigen", "", "left", 1, 1);
                        print("<strong>Cuenta Origen: </strong><br>");
                            $css->select("CmbCuentaOrigen", "form-control","CmbTipoPago", "", "","","");
                            $consulta=$obCon->ConsultarTabla("subcuentas", " WHERE PUC LIKE '11%'");
                            while($DatosCuenta=$obCon->FetchArray($consulta)){
                                $sel=0;
                                if($DatosCuenta["PUC"]==1105){
                                    $sel=1;
                                }
                                $css->option("", "", "", $DatosCuenta["PUC"], "", "",$sel);
                                    print($DatosCuenta["Nombre"]." ".$DatosCuenta["PUC"]);
                                $css->Coption(); 
                                
                            }
                            
                        $css->Cselect();
                        $css->CerrarDiv();
                        $css->CrearDiv("DivCuentaXPagar", "", "left", 0, 1);
                            
                            print("<strong>Llevar Cuenta X Pagar a: </strong><br>");
                            $css->select("CmbCuentaPUCCXP", "form-control","CmbCuentaPUCCXP", "", "","","");
                            
                            $consulta=$obCon->ConsultarTabla("subcuentas", " WHERE PUC LIKE '22%' or PUC LIKE '23%' or PUC LIKE '24%'");
                            while($DatosCuenta=$obCon->FetchArray($consulta)){
                                $sel=0;
                                if($DatosCuenta["PUC"]==220505){
                                    $sel=1;
                                }
                                $css->option("", "", "", $DatosCuenta["PUC"], "", "",$sel);
                                    print($DatosCuenta["Nombre"]." ".$DatosCuenta["PUC"]);
                                $css->Coption(); 
                                
                            }
                            
                        $css->Cselect();
                        print("<strong>Fecha Programada:</strong><br>");
                        $css->input("date", "TxtFechaProgramada", "form-control", "TxtFechaProgramada", "Fecha", date("Y-m-d"), "Fecha programada", "off", "", "","style='line-height: 15px;'");
                        //$css->CrearInputText("TxtFechaProgramada", "date", "Fecha Programada <br>", date("Y-m-d"), "Fecha Programada", "", "", "", 150, 30, 0, 0);
                        $css->CerrarDiv();
                   
                    print("<strong>Trasladar?</strong><br>");
                    $css->select("CmbTraslado", "form-control","CmbTraslado", "", "","","");
                        $css->option("", "", "", "", "", "",$sel);
                            print("NO");
                        $css->Coption();     
                            $consulta=$obCon->ConsultarTabla("empresa_pro_sucursales", "");
                            while($DatosCuenta=$obCon->FetchArray($consulta)){
                                
                                $css->option("", "", "", $DatosCuenta["ID"], "", "",$sel);
                                    print($DatosCuenta["Nombre"]." ".$DatosCuenta["Ciudad"]);
                                $css->Coption(); 
                                
                            }
                         
                            
                     print("</td>");
                     print("<tr><td>");
                     $Habilidato=1;
                     if($TotalesCompras["Total_Pago"]<0){
                         $Habilidato=0;
                         print("<strong style=color:red>No es posible guardar una factura negativa</strong><br>");
                     }
                     $css->CrearBotonEvento("BtnGuardarCompra", "Guardar", $Habilidato, "onclick", "GuardarCompra($idCompra)", "verde", "style=widht:40px");
                     print("</td></tr>"); 
                    $css->CierraFilaTabla();
                    
                    
                $css->CerrarTabla();
                
            $css->Cdiv();//Cierra div general de totales
            
            $css->div("", "col-md-4", "", "", "","", "style=text-align:left");
               $css->CrearTabla();
                    //Dibujo las retenciones
                    $sql="SELECT * FROM factura_compra_retenciones WHERE idCompra='$idCompra' ORDER BY ID DESC";
                    $Consulta=$obCon->Query($sql);
                    $FlagEncabezado=1;
                    while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                        $idItem=$DatosConsulta["ID"];
                        if($FlagEncabezado==1){
                            $css->FilaTabla(16);
                                $css->ColTabla("<strong>RETENCIONES</strong>", 5,'C');
                            $css->CierraFilaTabla();
                            
                            $css->FilaTabla(12);
                                $css->ColTabla("<strong>Cuenta</strong>", 1,'C');
                                $css->ColTabla("<strong>Nombre Cuenta</strong>", 1,'C');
                                $css->ColTabla("<strong>%</strong>", 1,'C');
                                $css->ColTabla("<strong>Valor</strong>", 1,'C');
                                $css->ColTabla("<strong>Eliminar</strong>", 1,'C');
                            $css->CierraFilaTabla();
                            $FlagEncabezado=0;
                        }
                        $css->FilaTabla(12);
                            $css->ColTabla($DatosConsulta["CuentaPUC"], 1,'C');
                            $css->ColTabla($DatosConsulta["NombreCuenta"], 1,'C');
                            $css->ColTabla($DatosConsulta["PorcentajeRetenido"], 1,'C');
                            $css->ColTabla($DatosConsulta["ValorRetencion"], 1,'C');
                            print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                                $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`5`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                                $css->Cli();
                            print("</td>");
                        $css->CierraFilaTabla();
                        
                    }
                    
                    //Dibujo los descuentos
                    $sql="SELECT * FROM factura_compra_descuentos WHERE idCompra='$idCompra' ORDER BY ID DESC";
                    $Consulta=$obCon->Query($sql);
                    $FlagEncabezado=1;
                    while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                        $idItem=$DatosConsulta["ID"];
                        if($FlagEncabezado==1){
                            $css->FilaTabla(16);
                                $css->ColTabla("<strong>DESCUENTOS GLOBALES</strong>", 5,'C');
                            $css->CierraFilaTabla();
                            
                            $css->FilaTabla(12);
                                $css->ColTabla("<strong>Cuenta</strong>", 1,'C');
                                $css->ColTabla("<strong>Nombre Cuenta</strong>", 1,'C');
                                $css->ColTabla("<strong>%</strong>", 1,'C');
                                $css->ColTabla("<strong>Valor</strong>", 1,'C');
                                $css->ColTabla("<strong>Eliminar</strong>", 1,'C');
                            $css->CierraFilaTabla();
                            $FlagEncabezado=0;
                        }
                        $css->FilaTabla(12);
                            $css->ColTabla($DatosConsulta["CuentaPUCDescuento"], 1,'C');
                            $css->ColTabla($DatosConsulta["NombreCuentaDescuento"], 1,'C');
                            $css->ColTabla($DatosConsulta["PorcentajeDescuento"], 1,'C');
                            $css->ColTabla($DatosConsulta["ValorDescuento"], 1,'C');
                            print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                                $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`6`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                                $css->Cli();
                            print("</td>");
                        $css->CierraFilaTabla();
                        
                    }
                    
                    //Dibujo los impuestos adicionales
                    $sql="SELECT * FROM factura_compra_impuestos_adicionales WHERE idCompra='$idCompra' ORDER BY ID DESC";
                    $Consulta=$obCon->Query($sql);
                    $FlagEncabezado=1;
                    while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                        $idItem=$DatosConsulta["ID"];
                        if($FlagEncabezado==1){
                            $css->FilaTabla(16);
                                $css->ColTabla("<strong>IMPUESTOS ADICIONALES</strong>", 5,'C');
                            $css->CierraFilaTabla();
                            
                            $css->FilaTabla(12);
                                $css->ColTabla("<strong>Cuenta</strong>", 1,'C');
                                $css->ColTabla("<strong>Nombre Cuenta</strong>", 1,'C');
                                $css->ColTabla("<strong>%</strong>", 1,'C');
                                $css->ColTabla("<strong>Valor</strong>", 1,'C');
                                $css->ColTabla("<strong>Eliminar</strong>", 1,'C');
                            $css->CierraFilaTabla();
                            $FlagEncabezado=0;
                        }
                        $css->FilaTabla(12);
                            $css->ColTabla($DatosConsulta["CuentaPUC"], 1,'C');
                            $css->ColTabla($DatosConsulta["NombreCuenta"], 1,'C');
                            $css->ColTabla($DatosConsulta["Porcentaje"], 1,'C');
                            $css->ColTabla($DatosConsulta["Valor"], 1,'C');
                            print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                                $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`7`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                                $css->Cli();
                            print("</td>");
                        $css->CierraFilaTabla();
                        
                    }
               $css->CerrarTabla();
            $css->Cdiv();//Cierra div de retenciones e impuestos adicionales
            }
        break;// fin caso 4
        
        case 5: //consulta el precio de venta y costo de un producto o servicio
            $Listado=$obCon->normalizar($_REQUEST["listado"]);
            $idBusqueda=$obCon->normalizar($_REQUEST["Codigo"]);
            $PrecioVenta=0;
            $CostoUnitario=0;
            if($Listado==1){
                $tab="productosventa";
                $Datos=$obCon->ValorActual($tab, "PrecioVenta,CostoUnitario", " idProductosVenta='$idBusqueda'");
                $PrecioVenta=$Datos["PrecioVenta"];
                $CostoUnitario=$Datos["CostoUnitario"];
            }
            
            if($Listado==3){
                $tab="insumos";
                $Datos=$obCon->ValorActual($tab, "CostoUnitario", " ID='$idBusqueda'");
                
                $CostoUnitario=$Datos["CostoUnitario"];
            }
            
            print("OK;".$CostoUnitario.";".$PrecioVenta);
            break;//Fin caso 5
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>