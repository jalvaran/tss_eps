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
        case 1: //dibuje el formulario para crear un acta de liquidacion
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $DatosRepresentanteEPS=$obCon->DevuelveValores("eps_representantes_legales", "idEPS", $CmbEPS);
            print("<h3><strong>Crear Acta de Liquidación para la IPS: $DatosIPS[Nombre], NIT: $CmbIPS</strong></h3>");
            //$css->Notificacion("Crear Acta de Liquidación para la IPS: $DatosIPS[Nombre], NIT: $CmbIPS", "", "azulclaro", "", "");
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 1, "", "", "", "");
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>ACTA DE LIQUIDACIÓN</strong>", 4,"C");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Fecha Inicial</strong>", 2);
                    $css->ColTabla("<strong>Fecha Final</strong>", 2);
                    
                $css->CierraFilaTabla();    
                
                $css->FilaTabla(14);
                    print("<td colspan=2>");
                        $css->input("date", "FechaInicial", "form-control", "FechaInicial", "", date("Y-m-d"), "Fecha Inicial", "off", "", "","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    
                    print("</td>");
                    print("<td colspan=2>");
                       $css->input("date", "FechaFinal", "form-control", "FechaFinal", "", date("Y-m-d"), "Fecha Final", "off", "", "","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Tipo de Acta</strong>", 1);
                    $css->ColTabla("<strong>Prefijo</strong>", 1);
                    $css->ColTabla("<strong>Consecutivo</strong>", 1);
                    $css->ColTabla("<strong>Año</strong>", 1);
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    print("<td>");
                        
                         $css->select("TipoActa", "form-control", "TipoActa", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione un Tipo de Acta de Liquidación");
                            $css->Coption();
                            $sql="SELECT * FROM actas_liquidaciones_tipo";
                            $Consulta=$obCon->Query($sql);
                            while($DatosActas=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "", "", $DatosActas["ID"], "", "");
                                    print($DatosActas["Nombre"]);
                                $css->Coption();
                            }
                        $css->Cselect();
                        
                    print("</td>");
                    print("<td>");
                        $css->input("text", "TxtPrefijo", "form-control", "TxtPrefijo", "Prefijo", "", "Prefijo", "off", "", "");
                    print("</td>");
                    print("<td colspan=1>");
                        $css->input("text", "TxtConsecutivo", "form-control", "TxtConsecutivo", "Consecutivo", "", "Consecutivo", "off", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "TxtAnio", "form-control", "TxtAnio", "Año", "", "Año", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Representante Legal EPS</strong>", 2,"C");
                    $css->ColTabla("<strong>Representante Legal IPS</strong>", 2,"C");                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Nombres:</strong>", 1);
                    print("<td>");
                        $css->input("text", "NombreRepresentanteEPS", "form-control", "NombreRepresentanteEPS", "Representante EPS", $DatosRepresentanteEPS["Nombres"], "Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Nombres:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "NombreRepresentanteIPS", "form-control", "NombreRepresentanteIPS", "Representante IPS", "", "Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Apellidos:</strong>", 1);
                    print("<td>");
                        $css->input("text", "ApellidosRepresentanteEPS", "form-control", "ApellidosRepresentanteEPS", "Representante EPS", $DatosRepresentanteEPS["Apellidos"], "Apellidos Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Apellidos:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "ApellidosRepresentanteIPS", "form-control", "ApellidosRepresentanteIPS", "Representante IPS", "", "Apellidos Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Identificación:</strong>", 1);
                    print("<td>");
                        $css->input("text", "IdentificacionRepresentanteEPS", "form-control", "IdentificacionRepresentanteEPS", "Identificación Representante EPS", $DatosRepresentanteEPS["Identificacion"], "Identificación Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Identificación:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "IdentificacionRepresentanteIPS", "form-control", "IdentificacionRepresentanteIPS", "IdentificaciónRepresentante IPS", "", "Identificación Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Domicilio:</strong>", 1);
                    print("<td>");
                        $css->input("text", "DomicilioRepresentanteEPS", "form-control", "DomicilioRepresentanteEPS", "Domicilio Representante EPS", $DatosRepresentanteEPS["Domicilio"], "Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Domicilio:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "DomicilioRepresentanteIPS", "form-control", "DomicilioRepresentanteIPS", "Domicilio Representante IPS", "", "Domicilio Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Dirección:</strong>", 1);
                    print("<td>");
                        $css->input("text", "DireccionRepresentanteEPS", "form-control", "DireccionRepresentanteEPS", "Dirección Representante EPS", $DatosRepresentanteEPS["Direccion"], "Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Dirección:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "DireccionRepresentanteIPS", "form-control", "DireccionRepresentanteIPS", "Dirección Representante IPS", "", "Dirección Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Teléfono:</strong>", 1);
                    print("<td>");
                        $css->input("text", "TelefonoRepresentanteEPS", "form-control", "TelefonoRepresentanteEPS", "Teléfono Representante EPS", $DatosRepresentanteEPS["Telefono"], "Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Teléfono:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "TelefonoRepresentanteIPS", "form-control", "TelefonoRepresentanteIPS", "Teléfono Representante IPS", "", "Teléfono Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
        break;//Fin caso 1
    
        case 2:// dibuje el selector de actas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $css->select("idActaLiquidacion", "form-control", "idActaLiquidacion", "", "", "onchange=MostrarActa()", "");
                $css->option("", "", "", "", "", "");
                    print("Seleccione un Acta de Liquidación");
                $css->Coption();
                $sql="SELECT * FROM actas_liquidaciones WHERE Estado='0' AND NIT_IPS='$CmbIPS'";
                $Consulta=$obCon->Query($sql);
                while($DatosActas=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $DatosActas["ID"], "", "");
                        print($DatosActas["ID"]." ".$DatosActas["FechaFinal"]." ".$DatosActas["RazonSocialIPS"]." ".$DatosActas["NIT_IPS"]);
                    $css->Coption();
                }
            $css->Cselect();
        break;  //Fin caso 2 
        
        case 3:// dibuje el acta de liquidacion
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            
            if($idActaLiquidacion==""){
                $css->CrearTitulo("Seleccione un acta de liquidación");
                exit();
            }
            $sql="SELECT t1.*, t2.Nombre AS NombreTipoActa,t2.Titulo FROM actas_liquidaciones t1 "
                    . "INNER JOIN actas_liquidaciones_tipo t2 ON t2.ID=t1.TipoActaLiquidacion WHERE t1.ID='$idActaLiquidacion';";
            
            $DatosActa=$obCon->FetchAssoc($obCon->Query($sql));
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
                    
            $css->CrearTitulo("<strong>Acta de Liquidación No. $idActaLiquidacion, Tipo: " .utf8_encode($DatosActa["Titulo"])."</strong>");
            
            
                
                $css->div("DivContratosDisponiblesActaLiquidacion", "col-md-6", "", "", "", "", "");
                    $css->CrearTitulo("<strong>Contratos Disponibles:</strong>","verde");                
                    $css->CrearTabla();    
                        
                        $sql="SELECT DISTINCT t1.NumeroContrato FROM actas_conciliaciones_contratos t1 "
                                . "INNER JOIN actas_conciliaciones t2 ON t1.idActaConciliacion=t2.ID "
                                . "WHERE t2.Estado=1 AND t2.MesServicioInicial>='$MesServicioInicial' AND t2.MesServicioFinal<='$MesServicioFinal' ;";
                       //print($sql);
                        $Consulta=$obCon->Query($sql);

                        while($Contratos=$obCon->FetchAssoc($Consulta)){
                            print("<tr>");
                                $idContrato=$Contratos["NumeroContrato"];
                                $sql="SELECT ID FROM contratos WHERE ContratoEquivalente='$idContrato'";
                                $DatosContratos=$obCon->FetchAssoc($obCon->Query($sql));
                                print("<td>");
                                print("<strong>".$Contratos["NumeroContrato"]."</strong> ");
                                print("</td>");
                                print("<td>");
                                if($DatosContratos["ID"]==""){                                    
                                    $css->CrearBotonEvento("btnCrearContrato", "Crear Contrato", 1, "onclick", "AbreFormularioCrearContrato(`$idActaLiquidacion`,`$idContrato`)", "azul", "style='width:150px;'");
                                }else{
                                    $css->CrearBotonEvento("btnAgregarContrato", "Agregar Contrato", 1, "onclick", "AgregarContratoLiquidacion(`$idActaLiquidacion`,`$idContrato`)", "verde", "");
                                }
                                print("</td>");
                            print("</tr>");
                        }
                        $css->CerrarTabla();
                    print("</div>");


                $css->div("DivContratosAgregadosActaLiquidacion", "col-md-6", "", "", "", "", "");
                    $css->CrearTitulo("<strong>Contratos Agregados al Acta</strong>","verde");                
                        $css->CrearTabla();  
                        
                        $css->CerrarTabla();    
                $css->Cdiv();
                $css->section("", "", "", "");
            $css->div("DivDatosGeneralesActaLiquidacion", "col-md-12", "", "", "", "", "");
                $css->CrearTabla();

                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>REPRESENTANTES LEGALES</strong>", 4);
                    $css->CierraFilaTabla();

                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Representante Legal EPS</strong>", 2,"C");
                        $css->ColTabla("<strong>Representante Legal IPS</strong>", 2,"C");                    
                    $css->CierraFilaTabla();

                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Nombres:</strong>", 1);
                        print("<td>");
                            $css->input("text", "NombreRepresentanteEPS", "form-control", "NombreRepresentanteEPS", "Representante EPS", $DatosActa["EPS_Nombres_Representante_Legal"], "Representante EPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`NombreRepresentanteEPS`,`EPS_Nombres_Representante_Legal`)");
                        print("</td>");
                        $css->ColTabla("<strong>Nombres:</strong>", 1);  
                        print("<td>");
                            $css->input("text", "NombreRepresentanteIPS", "form-control", "NombreRepresentanteIPS", "Representante IPS", $DatosActa["IPS_Nombres_Representante_Legal"], "Representante IPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`NombreRepresentanteIPS`,`IPS_Nombres_Representante_Legal`)");
                        print("</td>");
                    $css->CierraFilaTabla();

                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Apellidos:</strong>", 1);
                        print("<td>");
                            $css->input("text", "ApellidosRepresentanteEPS", "form-control", "ApellidosRepresentanteEPS", "Representante EPS", $DatosActa["EPS_Apellidos_Representante_Legal"], "Apellidos Representante EPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`ApellidosRepresentanteEPS`,`EPS_Apellidos_Representante_Legal`)");
                        print("</td>");
                        $css->ColTabla("<strong>Apellidos:</strong>", 1);  
                        print("<td>");
                            $css->input("text", "ApellidosRepresentanteIPS", "form-control", "ApellidosRepresentanteIPS", "Representante IPS", $DatosActa["IPS_Apellidos_Representante_Legal"], "Apellidos Representante IPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`ApellidosRepresentanteIPS`,`IPS_Apellidos_Representante_Legal`)");
                        print("</td>");
                    $css->CierraFilaTabla();


                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Identificación:</strong>", 1);
                        print("<td>");
                            $css->input("text", "IdentificacionRepresentanteEPS", "form-control", "IdentificacionRepresentanteEPS", "Identificación Representante EPS", $DatosActa["EPS_Identificacion_Representante_Legal"], "Identificación Representante EPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`IdentificacionRepresentanteEPS`,`EPS_Identificacion_Representante_Legal`)");
                        print("</td>");
                        $css->ColTabla("<strong>Identificación:</strong>", 1);  
                        print("<td>");
                            $css->input("text", "IdentificacionRepresentanteIPS", "form-control", "IdentificacionRepresentanteIPS", "IdentificaciónRepresentante IPS", $DatosActa["IPS_Identificacion_Representante_Legal"], "Identificación Representante IPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`IdentificacionRepresentanteIPS`,`IPS_Identificacion_Representante_Legal`)");
                        print("</td>");
                    $css->CierraFilaTabla();


                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Domicilio:</strong>", 1);
                        print("<td>");
                            $css->input("text", "DomicilioRepresentanteEPS", "form-control", "DomicilioRepresentanteEPS", "Domicilio Representante EPS", $DatosActa["EPS_Domicilio"], "Domicilio Representante EPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`DomicilioRepresentanteEPS`,`EPS_Domicilio`)");
                        print("</td>");
                        $css->ColTabla("<strong>Domicilio:</strong>", 1);  
                        print("<td>");
                            $css->input("text", "DomicilioRepresentanteIPS", "form-control", "DomicilioRepresentanteIPS", "Domicilio Representante IPS", $DatosActa["IPS_Domicilio"], "Domicilio Representante IPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`DomicilioRepresentanteIPS`,`IPS_Domicilio`)");
                        print("</td>");
                    $css->CierraFilaTabla();

                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Dirección:</strong>", 1);
                        print("<td>");
                            $css->input("text", "DireccionRepresentanteEPS", "form-control", "DireccionRepresentanteEPS", "Dirección Representante EPS", $DatosActa["EPS_Direccion"], "Dirección Representante EPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`DireccionRepresentanteEPS`,`EPS_Direccion`)");
                        print("</td>");
                        $css->ColTabla("<strong>Dirección:</strong>", 1);  
                        print("<td>");
                            $css->input("text", "DireccionRepresentanteIPS", "form-control", "DireccionRepresentanteIPS", "Dirección Representante IPS", $DatosActa["IPS_Direccion"], "Dirección Representante IPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`DireccionRepresentanteIPS`,`IPS_Direccion`)");
                        print("</td>");
                    $css->CierraFilaTabla();

                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Teléfono:</strong>", 1);
                        print("<td>");
                            $css->input("text", "TelefonoRepresentanteEPS", "form-control", "TelefonoRepresentanteEPS", "Teléfono Representante EPS", $DatosActa["EPS_Telefono"], "Télefono Representante EPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TelefonoRepresentanteEPS`,`EPS_Telefono`)");
                        print("</td>");
                        $css->ColTabla("<strong>Teléfono:</strong>", 1);  
                        print("<td>");
                            $css->input("text", "TelefonoRepresentanteIPS", "form-control", "TelefonoRepresentanteIPS", "Teléfono Representante IPS", $DatosActa["IPS_Telefono"], "Teléfono Representante IPS", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TelefonoRepresentanteIPS`,`IPS_Telefono`)");
                        print("</td>");
                    $css->CierraFilaTabla();

                $css->CerrarTabla();
            $css->Cdiv();
            print("<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>");
            $css->Csection();
        break;   //Fin caso 3
       
        case 4: //Dibujar el formulario para crear un contrato
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosEPS=$obCon->DevuelveValores("eps", "ID", $CmbEPS);
                   
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $Contrato=$obCon->normalizar($_REQUEST["Contrato"]);
            $css->CrearTitulo("Crear Contrato No. <span id='spContratoEquivalente'>".$Contrato."</span>");
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>CONTRATANTE:</strong>", 1);
                    print("<td>");
                        $css->span("spNitEPS", "", "", "");
                            print("<strong>".$DatosEPS["NIT"]."</strong>");
                        $css->Cspan();
                    print("</td>");
                    $css->ColTabla("<strong>CONTRATISTA:</strong>", 1);
                    print("<td>");
                        $css->span("spNitIPS", "", "", "");
                            print("<strong>".$CmbIPS."</strong>");
                        $css->Cspan();
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Clasificación</strong>", 1);
                    $css->ColTabla("<strong>Número de Contrato</strong>", 1);
                    $css->ColTabla("<strong>Fecha Inicial</strong>", 1);
                    $css->ColTabla("<strong>Fecha Final</strong>", 1);
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    print("<td>");
                        $css->select("CmbClasificacionContrato", "form-control", "CmbClasificacionContrato", "", "", "onchange=ValidarClasificacionContrato()", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione Clasificación");
                            $css->Coption();
                            $css->option("", "", "", "ACUERDO", "", "");
                                print("ACUERDO");
                            $css->Coption();
                            
                            $css->option("", "", "", "CONTRATO", "", "");
                                print("CONTRATO");
                            $css->Coption();
                            
                            $css->option("", "", "", "OTRO SI", "", "");
                                print("OTRO SI");
                            $css->Coption();
                            
                            $css->option("", "", "", "SIN CONTRATO", "", "");
                                print("SIN CONTRATO");
                            $css->Coption();
                            
                            $css->option("", "", "", "COTIZACION", "", "");
                                print("COTIZACIÓN");
                            $css->Coption();
                            
                            $css->option("", "", "", "URGENCIAS", "", "");
                                print("URGENCIAS");
                            $css->Coption();
                        $css->Cselect();
                        $css->CrearDiv("DivSelectoresOtroSI", "", "", 0, 1);
                            $css->select("CmbContratoPadre", "form-control", "CmbContratoPadre", "<strong>Contrato Padre:</strong><br>", "", "", "");
                                $css->option("", "", "", "", "", "");
                                    print("Seleccione el contrato padre");
                                $css->Coption();
                                $sql="SELECT NumeroContrato FROM contratos WHERE NitIPSContratada='$CmbIPS' AND ClasificacionContrato='CONTRATO' AND (EstadoContrato='ACTIVO' OR EstadoContrato='CONCILIADO') ";
                                $Consulta=$obCon->Query($sql);
                                while($DatosContratos=$obCon->FetchAssoc($Consulta)){
                                    $css->option("", "", "", $DatosContratos["NumeroContrato"], "", "");
                                        print($DatosContratos["NumeroContrato"]);
                                    $css->Coption();
                                }
                            $css->Cselect();
                            print("<br>");
                            
                            $css->select("CmbNumeroOtroSI", "form-control", "CmbNumeroOtroSI", "<strong>Número de Otro SI:</strong><br>", "", "", "");
                                $css->option("", "", "", "1", "", "");
                                    print("OTRO SI 001");
                                $css->Coption();
                                $css->option("", "", "", "2", "", "");
                                    print("OTRO SI 002");
                                $css->Coption();
                                $css->option("", "", "", "3", "", "");
                                    print("OTRO SI 003");
                                $css->Coption();
                                $css->option("", "", "", "4", "", "");
                                    print("OTRO SI 004");
                                $css->Coption();
                                $css->option("", "", "", "5", "", "");
                                    print("OTRO SI 005");
                                $css->Coption();
                                $css->option("", "", "", "6", "", "");
                                    print("OTRO SI 006");
                                $css->Coption();
                                $css->option("", "", "", "7", "", "");
                                    print("OTRO SI 007");
                                $css->Coption();
                                $css->option("", "", "", "8", "", "");
                                    print("OTRO SI 008");
                                $css->Coption();
                                
                            $css->Cselect();
                            
                        $css->CerrarDiv();
                        
                        $css->CrearDiv("DivSelectorTipoContrato", "", "", 0, 1);
                            $css->select("CmbTipoContrato", "form-control", "CmbTipoContrato", "Tipo de Contrato", "", "onchange=ValidaOpcionesTipoContrato();", "");
                                $css->option("", "", "", "", "", "");
                                    print("Seleccione el Tipo de Contrato");
                                $css->Coption();
                                $sql="SELECT Nombre FROM contratos_tipo;";
                                $Consulta=$obCon->Query($sql);
                                while($DatosContratos=$obCon->FetchAssoc($Consulta)){
                                    $css->option("", "", "", $DatosContratos["Nombre"], "", "");
                                        print($DatosContratos["Nombre"]);
                                    $css->Coption();
                                }
                            $css->Cselect();
                            
                            $css->CrearDiv("DivUPCCapita", "", "", 0, 1);
                                $css->input("text", "TxtUPC", "form-control", "TxtUPC", "UPC", "", "UPC", "off", "", "");
                                $css->input("text", "TxtNumeroAfiliados", "form-control", "TxtNumeroAfiliados", "Número de Afiliados", "", "Número de Afiliados", "off", "", "");
                            $css->CerrarDiv();
                        
                        $css->CerrarDiv();
                        
                        
                        
                    print("</td>");
                    print("<td>");
                        $css->input("text", "NumeroContrato", "form-control", "NumeroContrato", "", "", "Número de Contrato", "off", "", "");
                    print("</td>");
                    print("<td>");                    
                        $css->input("date", "FechaInicial", "form-control", "FechaInicial", "", date("Y-m-d"), "Fecha Inicial", "off", "", "","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    print("</td>");                    
                    print("<td>");                    
                        $css->input("date", "FechaFinal", "form-control", "FechaInicial", "", date("Y-m-d"), "Fecha Inicial", "off", "", "","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    print("</td>");  
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    
                $css->CierraFilaTabla();
                            
            $css->CerrarTabla();
            
        break;  //Fin caso 4  
    }
    
          
}else{
    print("No se enviaron parametros");
}
?>