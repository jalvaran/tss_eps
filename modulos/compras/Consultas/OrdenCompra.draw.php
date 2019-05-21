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
                print("<h4><strong>Plazo de Entrega:</strong></h4>");
                $css->input("number", "PlazoEntrega", "form-control", "PlazoEntrega", "Plazo de entrega", 1, "Plazo de entrega", "off", "", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<h4><strong>Condiciones:</strong></h4>");
                $css->textarea("TxtCondiciones", "form-control", "TxtCondiciones", "Condiciones", "Condiciones", "", "");
                $css->Ctextarea();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<h4><strong>Concepto:</strong></h4>");
                $css->textarea("TxtConcepto", "form-control", "TxtConcepto", "Concepto", "Concepto", "", "");
                $css->Ctextarea();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                print("<h4><strong>Solicitante:</strong></h4>");
                $css->input("text", "TxtSolicitante", "form-control", "TxtSolicitante", "Solicitante", "", "Solicitante", "off", "", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                print("<h4><strong>No. Cotización:</strong></h4>");
                $css->input("text", "TxtNumCotizacion", "form-control", "TxtNumCotizacion", "Cotización", "", "Cotización", "off", "", "");
            $css->CerrarDiv();
            
            
            
            print("<br><br><br><br><br><br><br><br><br><br>");
            
        break; 
        case 2:// se dibuja el formulario para editar los datos generales de la compra
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            $DatosCompra=$obCon->DevuelveValores("ordenesdecompra", "ID", $idCompra);
            $DatosTercero=$obCon->DevuelveValores("proveedores", "idProveedores", $DatosCompra["Tercero"]);
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
                print("<h4><strong>Plazo de Entrega:</strong></h4>");
                $css->input("number", "PlazoEntrega", "form-control", "PlazoEntrega", "Plazo de entrega", $DatosCompra["PlazoEntrega"], "Plazo de entrega", "off", "", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<h4><strong>Condiciones:</strong></h4>");
                $css->textarea("TxtCondiciones", "form-control", "TxtCondiciones", "Condiciones", "Condiciones", "", "");
                    print($DatosCompra["Condiciones"]);
                    
                $css->Ctextarea();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<h4><strong>Concepto:</strong></h4>");
                $css->textarea("TxtConcepto", "form-control", "TxtConcepto", "Concepto", "Concepto", "", "");
                    print($DatosCompra["Descripcion"]);
                $css->Ctextarea();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                print("<h4><strong>Solicitante:</strong></h4>");
                $css->input("text", "TxtSolicitante", "form-control", "TxtSolicitante", "Solicitante", $DatosCompra["Solicitante"], "Solicitante", "off", "", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                print("<h4><strong>No. Cotización:</strong></h4>");
                $css->input("text", "TxtNumCotizacion", "form-control", "TxtNumCotizacion", "Cotización", $DatosCompra["NoCotizacion"], "Cotización", "off", "", "");
            $css->CerrarDiv();
            
            print("<br><br><br><br><br><br><br><br><br><br>");
            
        break;  
        
        case 3://Dibuja los items de una compra
            $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>ID</strong>", 1, "C");                    
                    $css->ColTabla("<strong>Nombre</strong>", 1, "C");
                    $css->ColTabla("<strong>Cantidad</strong>", 1, "C");
                    $css->ColTabla("<strong>Costo Unitario</strong>", 1, "C");
                    $css->ColTabla("<strong>Subtotal</strong>", 1, "C");
                    $css->ColTabla("<strong>Impuestos</strong>", 1, "C");
                    $css->ColTabla("<strong>Total</strong>", 1, "C");                    
                    $css->ColTabla("<strong>% Impuestos</strong>", 1, "C");                    
                    $css->ColTabla("<strong>Eliminar</strong>", 1, "C");
                    
                $css->CierraFilaTabla();
                //Dibujo los productos
                $sql="SELECT * FROM ordenesdecompra_items WHERE NumOrden='$idCompra' ORDER BY ID DESC";
                $Consulta=$obCon->Query($sql);
                while ($DatosItems = $obCon->FetchAssoc($Consulta)) {
                    $idItem=$DatosItems["ID"];
                    $idProducto=$DatosItems["idProducto"];
                    $css->FilaTabla(14);
                        $css->ColTabla($DatosItems["Referencia"], 1, "C");
                        if(is_numeric($DatosItems["Tipo_Impuesto"])){
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"]*100;
                            $PorcentajeImpuestos=$PorcentajeImpuestos."%";
                        }else{
                            $PorcentajeImpuestos=$DatosItems["Tipo_Impuesto"];
                        }
                        $css->ColTabla($DatosItems["Descripcion"], 1, "C");
                        print("<td>");
                            $idCaja="TxtCantidadItem_".$idItem;
                            $css->input("number", $idCaja, "form-control", $idCaja, "", $DatosItems["Cantidad"], "Cantidad", "off", "", "onchange=EditarValorItem(`11`,`$idCaja`,`$idItem`)");
                        print("</td>");
                        
                        print("<td>");
                            $idCaja="TxtValorUnitario_".$idItem;
                            $css->input("number", $idCaja, "form-control", $idCaja, "", $DatosItems["ValorUnitario"], "Cantidad", "off", "", "onchange=EditarValorItem(`12`,`$idCaja`,`$idItem`)");
                        print("</td>");
                        //$css->ColTabla(number_format($DatosItems["ValorUnitario"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["Subtotal"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["IVA"],2,",","."), 1, "C");
                        $css->ColTabla(number_format($DatosItems["Total"],2,",","."), 1, "C");
                        
                        $css->ColTabla($PorcentajeImpuestos, 1, "C");
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`1`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                    $css->CierraFilaTabla();
                }
                
                
            $css->CerrarTabla();
        break;// fin caso 3
        
        case 4://Dibujo los Totales
            $css->CrearDiv("", "col-md-6", "left", 1, 1);
                $idCompra=$obCon->normalizar($_REQUEST["idCompra"]);
                $sql="SELECT SUM(Subtotal) as Subtotal, SUM(IVA) as IVA,SUM(Total) as Total "
                    . " FROM ordenesdecompra_items WHERE NumOrden='$idCompra'";
                $Datos=$obCon->Query($sql);
                $DatosTotalesNota=$obCon->FetchArray($Datos);
                $css->CrearTabla();

                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>SUBTOTAL:</strong>", 1);
                        $css->ColTabla(number_format($DatosTotalesNota["Subtotal"]), 1);

                    $css->CierraFilaTabla();
                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>IVA:</strong>", 1);
                        $css->ColTabla(number_format($DatosTotalesNota["IVA"]), 1);

                    $css->CierraFilaTabla();
                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>TOTAL:</strong>", 1);
                        $css->ColTabla(number_format($DatosTotalesNota["Total"]), 1);

                    $css->CierraFilaTabla();
                    
                    $css->FilaTabla(14);
                        print("<td colspan=2>");
                            $habilita=0;
                            if($DatosTotalesNota["Total"]>0){
                                $habilita=1;
                            }
                            $css->CrearBotonEvento("BtnGuardarCompra", "Guardar", $habilita, "onclick", "GuardarCompra($idCompra)", "rojo", "");
                        print("</td>");

                    $css->CierraFilaTabla();
                    
                $css->CerrarTabla();
            $css->CerrarDiv();
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