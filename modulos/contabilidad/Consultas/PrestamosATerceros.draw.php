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
        case 1: //Crea un formulario para el registro de un nuevo prestamo
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 1, "", "", "", "");
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Fecha</a>");
                    $css->Clegend();           
                    $css->input("date", "TxtFecha", "form-control", "TxtFecha", "", date("Y-m-d"), "Fecha", "off", "", "style='line-height: 15px;'");
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Tercero</a>");
                    $css->Clegend();           
                    $css->select("CmbTercero", "form-control", "CmbTercero", "", "", "", "style=width:100%");
                        $css->option("", "", "", "", "", "");
                            print("Seleccione un tercero");
                        $css->Coption();
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
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
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
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
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
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
            
            print("<br><br><br><br><br><br>");
            
            
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Contabilizar en:</a>");
                    $css->Clegend();           
                    $css->select("CmbCuentaDestino", "form-control", "CmbCuentaDestino", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM subcuentas WHERE PUC LIKE '13%' AND PUC NOT LIKE '1305%' AND LENGTH(PUC) >=6";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $css->option("", "", "", $DatosCuenta["PUC"], "", "");
                                print($DatosCuenta["PUC"]." ".$DatosCuenta["Nombre"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Sale de:</a>");
                    $css->Clegend();           
                    $css->select("CmbCuentaOrigen", "form-control", "CmbCuentaOrigen", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM subcuentas WHERE PUC LIKE '11%' AND LENGTH(PUC) >=6";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $css->option("", "", "", $DatosCuenta["PUC"], "", "");
                                print($DatosCuenta["PUC"]." ".$DatosCuenta["Nombre"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Observaciones</a>");
                    $css->Clegend();           
                        $css->textarea("TxtObservaciones", "form-control", "TxtObservaciones", "Observaciones", "Observaciones", "", "");
                        $css->Ctextarea();
                    $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Valor</a>");
                    $css->Clegend();           
                    $css->input("number", "TxtValor", "form-control", "TxtValor", "", "", "Valor", "off", "", "");
                $css->Cfieldset();
            $css->CerrarDiv();
            
            print("<br><br><br><br><br><br><br><br>");
            
        break; //Fin caso 1
    
        case 2://Formulario para hacer un abono
            $idPrestamo=$obCon->normalizar($_REQUEST["idPrestamo"]);
            $DatosPrestamo=$obCon->DevuelveValores("prestamos_terceros", "ID", $idPrestamo);
            $DatosTercero=$obCon->DevuelveValores("clientes", "Num_Identificacion", $DatosPrestamo["Tercero"]);
            if($DatosTercero["RazonSocial"]==''){
                $DatosTercero=$obCon->DevuelveValores("proveedores", "Num_Identificacion", $DatosPrestamo["Tercero"]);
            }
            if($DatosPrestamo["Saldo"]<=0){
                print("A este prestamo no se le pueden realizar mas abonos");
            }
            print("<h4>Realizar un abono al prestamo <strong>$idPrestamo</strong>, Del Tercero: <strong>$DatosTercero[RazonSocial]</strong>, Saldo: <strong>".number_format($DatosPrestamo["Saldo"])."</strong></h4>");
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 2, "", "", "", "");
            $css->input("hidden", "idPrestamo", "", "idPrestamo", "", $idPrestamo, "", "", "", "");
            $css->CrearDiv("", "col-md-3", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Fecha</a>");
                    $css->Clegend();           
                    $css->input("date", "TxtFecha", "form-control", "TxtFecha", "", date("Y-m-d"), "Fecha", "off", "", "style='line-height: 15px;'");
                $css->Cfieldset();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-5", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Entra a:</a>");
                    $css->Clegend();           
                    $css->select("CmbCuentaDestino", "form-control", "CmbCuentaDestino", "", "", "", "style=width:100%");
                        $sql="SELECT * FROM subcuentas WHERE PUC LIKE '11%' AND LENGTH(PUC) >=6";
                        $consulta=$obCon->Query($sql);
                        
                        while($DatosCuenta=$obCon->FetchAssoc($consulta)){
                            $css->option("", "", "", $DatosCuenta["PUC"], "", "");
                                print($DatosCuenta["PUC"]." ".$DatosCuenta["Nombre"]);
                            $css->Coption();
                        }
                        
                    $css->Cselect();
                $css->Cfieldset();
            $css->CerrarDiv();
            
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                $css->fieldset("", "", "FieldPrestamos", "Prestamo", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Valor</a>");
                    $css->Clegend();           
                    $css->input("number", "TxtValor", "form-control", "TxtValor", "", "", "Valor", "off", "", "");
                $css->Cfieldset();
            $css->CerrarDiv();
            print("<br><br><br><br><br>");
        break;//Fin caso 2
        
        case 3://Historial de abonos de un prestamo
            $idPrestamo=$obCon->normalizar($_REQUEST["idPrestamo"]);
            $DatosPrestamo=$obCon->DevuelveValores("prestamos_terceros", "ID", $idPrestamo);
            $DatosTercero=$obCon->DevuelveValores("clientes", "Num_Identificacion", $DatosPrestamo["Tercero"]);
            if($DatosTercero["RazonSocial"]==''){
                $DatosTercero=$obCon->DevuelveValores("proveedores", "Num_Identificacion", $DatosPrestamo["Tercero"]);
            }
            
            print("<h4>Historial de abonos al prestamo <strong>$idPrestamo</strong>, Del Tercero: <strong>$DatosTercero[RazonSocial]</strong>, Saldo: <strong>".number_format($DatosPrestamo["Saldo"])."</strong></h4>");
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Fecha</strong>", 1);
                    $css->ColTabla("<strong>Valor</strong>", 1);
                    $css->ColTabla("<strong>Comprobante</strong>", 1);                    
                $css->CierraFilaTabla();
                
                $sql="SELECT * FROM prestamos_terceros_abonos WHERE idPrestamo='$idPrestamo'";
                $Consulta=$obCon->Query($sql);
                
                while($DatosAbonos=$obCon->FetchAssoc($Consulta)){
                    
                    $css->FilaTabla(14);
                        $css->ColTabla($DatosAbonos["Fecha"], 1);
                        $css->ColTabla(number_format($DatosAbonos["Valor"]), 1);
                        print("<td>");
                            $Link="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=4&idIngreso=".$DatosAbonos["idComprobanteIngreso"];
                            print("<a href='$Link' target='_BLANK'>Comprobante $DatosAbonos[idComprobanteIngreso]</a>");
                        print("</td>");
                    $css->CierraFilaTabla();
                    
                }
                
            $css->CerrarTabla();
            
        break;//Fin caso 3
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>