<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //Crea un formulario para el registro de un nuevo documento
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 1, "", "", "", "");
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "Field", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Fecha</a>");
                    $css->Clegend();           
                    $css->input("date", "TxtFecha", "form-control", "TxtFecha", "", date("Y-m-d"), "Fecha", "off", "", "style='line-height: 15px;'");
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Tipo de Documento</a>");
                    $css->Clegend();           
                    $css->select("CmbTipoDocumento", "form-control", "CmbTipoDocumento", "", "", "", "");
                    
                    $Consulta=$obCon->ConsultarTabla("documentos_contables", "");
                    while($DatosDocumentos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosDocumentos["ID"], "", "");
                            print($DatosDocumentos["Prefijo"]." ".$DatosDocumentos["Nombre"]);
                        
                        $css->Coption();
                    }
                $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Empresa:</a>");
                    $css->Clegend();           
                    $css->select("CmbEmpresa", "form-control", "CmbEmpresa", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM empresapro ";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $css->option("", "", "", $DatosCuenta["idEmpresaPro"], "", "");
                                print($DatosCuenta["idEmpresaPro"]." ".$DatosCuenta["RazonSocial"]." ".$DatosCuenta["NIT"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Sucursal:</a>");
                    $css->Clegend();           
                    $css->select("CmbSucursal", "form-control", "CmbSucursal", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM empresa_pro_sucursales ";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $css->option("", "", "", $DatosCuenta["ID"], "", "");
                                print($DatosCuenta["Nombre"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Costos:</a>");
                    $css->Clegend();           
                    $css->select("CmbCentroCosto", "form-control", "CmbCentroCosto", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM centrocosto ";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $css->option("", "", "", $DatosCuenta["ID"], "", "");
                                print($DatosCuenta["Nombre"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
                 print("<br><br><br><br><br>");       
            $css->CrearDiv("", "col-md-12", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Observaciones</a>");
                    $css->Clegend();           
                        $css->textarea("TxtObservaciones", "form-control", "TxtObservaciones", "Observaciones", "Observaciones", "", "");
                        $css->Ctextarea();
                    $css->Cfieldset();
            $css->CerrarDiv();
            
            
            print("<br><br><br><br><br><br><br><br><br>");
            
        break; //Fin caso 1
    
        case 2://Formulario para editar un documento
            $idDocumento=$obCon->normalizar($_REQUEST["idDocumento"]);
            $DatosDocumentoGeneral=$obCon->DevuelveValores("documentos_contables_control", "ID", $idDocumento);
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 2, "", "", "", "");
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "Field", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Fecha</a>");
                    $css->Clegend();           
                    $css->input("date", "TxtFecha", "form-control", "TxtFecha", "", $DatosDocumentoGeneral["Fecha"], "Fecha", "off", "", "style='line-height: 15px;'");
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Tipo de Documento</a>");
                    $css->Clegend();           
                    $css->select("CmbTipoDocumento", "form-control", "CmbTipoDocumento", "", "", "", "disabled");
                    
                    $Consulta=$obCon->ConsultarTabla("documentos_contables", "");
                    while($DatosDocumentos=$obCon->FetchAssoc($Consulta)){
                        $Sel=0;
                        if($DatosDocumentoGeneral["idDocumento"]==$DatosDocumentos["ID"]){
                            $Sel=1;
                        }
                        
                        $css->option("", "", "", $DatosDocumentos["ID"], "", "",$Sel);
                            print($DatosDocumentos["Prefijo"]." ".$DatosDocumentos["Nombre"]);                        
                        $css->Coption();
                    }
                $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Empresa:</a>");
                    $css->Clegend();           
                    $css->select("CmbEmpresa", "form-control", "CmbEmpresa", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM empresapro ";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $Sel=0;
                            if($DatosDocumentoGeneral["idEmpresa"]==$DatosCuenta["idEmpresaPro"]){
                                $Sel=1;
                            }
                            $css->option("", "", "", $DatosCuenta["idEmpresaPro"], "", "",$Sel);
                                print($DatosCuenta["idEmpresaPro"]." ".$DatosCuenta["RazonSocial"]." ".$DatosCuenta["NIT"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Sucursal:</a>");
                    $css->Clegend();           
                    $css->select("CmbSucursal", "form-control", "CmbSucursal", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM empresa_pro_sucursales ";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $Sel=0;
                            if($DatosDocumentoGeneral["idSucursal"]==$DatosCuenta["ID"]){
                                $Sel=1;
                            }
                            $css->option("", "", "", $DatosCuenta["ID"], "", "",$Sel);
                                print($DatosCuenta["Nombre"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Documento", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Costos:</a>");
                    $css->Clegend();           
                    $css->select("CmbCentroCosto", "form-control", "CmbCentroCosto", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM centrocosto ";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $Sel=0;
                            if($DatosDocumentoGeneral["idCentroCostos"]==$DatosCuenta["ID"]){
                                $Sel=1;
                            }
                            $css->option("", "", "", $DatosCuenta["ID"], "", "");
                                print($DatosCuenta["Nombre"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
                 print("<br><br><br><br><br>");       
            $css->CrearDiv("", "col-md-12", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Observaciones</a>");
                    $css->Clegend();           
                        $css->textarea("TxtObservaciones", "form-control", "TxtObservaciones", "Observaciones", "Observaciones", "", "");
                        print($DatosDocumentoGeneral["Descripcion"]);
                        $css->Ctextarea();
                    $css->Cfieldset();
            $css->CerrarDiv();
            
            
            print("<br><br><br><br><br><br><br><br><br>");
        break;//Fin caso 2
        
        case 3://Dibuja los movimientos de un documento contable
            
            $idDocumento=$obCon->normalizar($_REQUEST["idDocumento"]);
            if($idDocumento<=0 or $idDocumento==''){
                print(" ");
                exit();
            }
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>TERCERO</strong>", 1);
                    $css->ColTabla("<strong>CUENTA CONTABLE</strong>", 1);
                    
                    $css->ColTabla("<strong>DÉBITO</strong>", 1);
                    $css->ColTabla("<strong>CRÉDITO</strong>", 1);
                    $css->ColTabla("<strong>CONCEPTO</strong>", 1);
                    $css->ColTabla("<strong>ACCIÓNES</strong>", 1);
                $css->CierraFilaTabla();
                $sql="SELECT *,"
                        . "(SELECT Base FROM documentos_contables_registro_bases WHERE documentos_contables_items.ID=documentos_contables_registro_bases.idItemDocumentoContable) AS Base,"
                        . "(SELECT Porcentaje FROM documentos_contables_registro_bases WHERE documentos_contables_items.ID=documentos_contables_registro_bases.idItemDocumentoContable) AS Porcentaje,"
                        . "(SELECT Valor FROM documentos_contables_registro_bases WHERE documentos_contables_items.ID=documentos_contables_registro_bases.idItemDocumentoContable) AS Valor,"
                        . "(SELECT ID FROM documentos_contables_registro_bases WHERE documentos_contables_items.ID=documentos_contables_registro_bases.idItemDocumentoContable) AS idBase"
                        
                        . " FROM documentos_contables_items WHERE idDocumento='$idDocumento' ORDER BY ID DESC";
                $Consulta=$obCon->Query($sql);
                while($DatosItems=$obCon->FetchAssoc($Consulta)){
                   $idItem=$DatosItems["ID"];
                   
                    $css->FilaTabla(14);
                        print("<td>");
                            $css->select("CmbTerceroItems_$idItem","form-control", "CmbTerceroItems_$idItem", "", "", "onclick=ConviertaSelectTerceroItems($idItem)", "style=width:100%");
                                $css->option("", "", "", $DatosItems["Tercero"], "", "");
                                    print($DatosItems["Tercero"]);
                                $css->Coption();
                            $css->Cselect();
                        print("</td>");
                        print("<td>");
                            $css->select("CmbCuentaPUCItems_$idItem","form-control", "CmbCuentaPUCItems_$idItem", "", "", "onclick=ConviertaSelectItems($idItem)", "style=width:100%");
                                $css->option("", "", "", $DatosItems["CuentaPUC"], "", "");
                                    print($DatosItems["CuentaPUC"]." ".$DatosItems["NombreCuenta"]);
                                $css->Coption();
                            $css->Cselect();
                        print("</td>");
                        
                        if($DatosItems["Debito"]>0){
                            print("<td>");
                                $css->input("number", "TxtValorItems_$idItem", "form-control", "TxtValorItems_$idItem", "", $DatosItems["Debito"], "Débito", "off", "", "onchange=EditeDebitoCredito(`DB`,`$idItem`)");
                            print("</td>");
                        }else{
                            $css->ColTabla($DatosItems["Debito"], 1);
                        }
                        
                        if($DatosItems["Credito"]>0){
                            print("<td>");
                                $css->input("number", "TxtValorItems_$idItem", "form-control", "TxtValorItems_$idItem", "", $DatosItems["Credito"], "Credito", "off", "", "onchange=EditeDebitoCredito(`CR`,`$idItem`)");
                            print("</td>");
                        }else{
                            $css->ColTabla($DatosItems["Credito"], 1);
                        }
                        
                        
                        $css->ColTabla($DatosItems["Concepto"], 1);
                        
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`1`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                    $css->CierraFilaTabla();
                    
                    if($DatosItems["Base"]>0){
                        $idBase=$DatosItems["idBase"];
                        $css->FilaTabla(13);
                            
                            print("<td><label for='TxtBaseItems_$idBase'>Base:</label>");
                            $css->input("text", "TxtBaseItems_$idBase", "form-control", "TxtBaseItems_$idBase", "", $DatosItems["Base"], "Base", "off", "", "onchange=EditeBase(`Base`,`$idBase`)");
                            print("</td>");
                            print("<td><label for='TxtPorcentajeItems_$idBase'>Porcentaje:</label>");
                            
                            $css->input("text", "TxtPorcentajeItems_$idBase", "form-control", "TxtPorcentajeItems_$idBase", "", $DatosItems["Porcentaje"], "Base", "off", "", "onchange=EditeBase(`Porcentaje`,`$idBase`)");
                            print("</td>");
                            
                            
                        $css->CierraFilaTabla();
                        
                        $css->FilaTabla(13);
                            $css->ColTabla(" ---------------- ", 6,'C');
                        $css->CierraFilaTabla();
                    }
                    
                }
            $css->CerrarTabla();
            
        break;//Fin caso 3
        
        case 4://Dibuja totales del documento
            
            $idDocumento=$obCon->normalizar($_REQUEST["idDocumento"]);
            if($idDocumento<=0 or $idDocumento==''){
                print(" ");
                exit();
            }
            $Debitos=$obCon->Sume("documentos_contables_items", "Debito", "WHERE idDocumento='$idDocumento'");
            $Creditos=$obCon->Sume("documentos_contables_items", "Credito", "WHERE idDocumento='$idDocumento'");
            $Diferencia=$Debitos-$Creditos;
            $sql="SELECT COUNT(*) as TotalItems FROM documentos_contables_items WHERE idDocumento='$idDocumento'";
            $Consulta=$obCon->Query($sql);
            $DatosConteo=$obCon->FetchAssoc($Consulta);
            $TotalItems=$DatosConteo["TotalItems"];
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>MOVIMIENTOS</strong>", 1);
                    $css->ColTabla(number_format($TotalItems), 1);
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>DÉBITOS</strong>", 1);
                    $css->ColTabla(number_format($Debitos), 1);
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>CRÉDITOS</strong>", 1);
                    $css->ColTabla(number_format($Creditos), 1);
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>DIFERENCIA</strong>", 1);
                    $css->ColTabla(number_format($Diferencia), 1);
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    print("<td colspan=2>");
                        $enabled=0;
                        if($Diferencia==0 and $TotalItems>0){
                            $enabled=1;
                        }
                        $css->CrearBotonEvento("BtnGuardar", "Guardar", $enabled, "onclick", "GuardarDocumento()", "rojo", "");
                    print("</td>");
                $css->CierraFilaTabla();
            $css->CerrarTabla();
            
        break;//Fin caso 4
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>