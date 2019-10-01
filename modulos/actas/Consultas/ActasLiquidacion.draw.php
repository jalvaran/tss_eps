<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");
include_once '../../../general/clases/numeros_letras.class.php';

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    $obNumLetra=new numeros_letras();
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
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            if($idActaLiquidacion==""){
                $css->CrearTitulo("Seleccione un acta de liquidación");
                exit();
            }
            $sql="SELECT t1.*, t2.Nombre AS NombreTipoActa,t2.Titulo FROM actas_liquidaciones t1 "
                    . "INNER JOIN actas_liquidaciones_tipo t2 ON t2.ID=t1.TipoActaLiquidacion WHERE t1.ID='$idActaLiquidacion';";
            
            $DatosActa=$obCon->FetchAssoc($obCon->Query($sql));
            $TipoActa=$DatosActa["TipoActaLiquidacion"];
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
                    
            $css->CrearTitulo("<strong>Acta de Liquidación No. $idActaLiquidacion, Tipo: " .utf8_encode($DatosActa["Titulo"])."</strong>");
            
            
                
                $css->div("DivContratosDisponiblesActaLiquidacion", "", "", "", "", "", "");
                    
                $css->CrearTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>CONTRATOS DISPONIBLES PARA LIQUIDAR:</strong>", 3,"C");
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>CONTRATOS</strong>", 1,"C");
                        $css->ColTabla("<strong>DATOS GENERALES</strong>", 1,"C");
                        $css->ColTabla("<strong>VALORES PERCAPITA POR MUNICIPIO</strong>", 1,"C");
                    $css->CierraFilaTabla();
               
               
                $sql="SELECT DISTINCT t1.NumeroContrato FROM actas_conciliaciones_contratos t1 "
                                . "INNER JOIN actas_conciliaciones t2 ON t1.idActaConciliacion=t2.ID "
                                . "WHERE t2.Estado=1 AND t2.MesServicioInicial>='$MesServicioInicial' AND t2.MesServicioFinal<='$MesServicioFinal' AND t2.NIT_IPS='$CmbIPS';";
                       
                
                $Consulta=$obCon->Query($sql);
                $i=0;
                while ($DatosContratos=$obCon->FetchAssoc($Consulta)){
                    $i++;
                    $idContrato=$DatosContratos["NumeroContrato"];
                    $sql="SELECT * FROM contratos WHERE NitIPSContratada='$CmbIPS' AND (ContratoEquivalente='$idContrato')";
                    
                    $DatosContratoExistente=$obCon->FetchArray($obCon->Query($sql));
                    
                    $css->FilaTabla(16);
                        print("<td>");
                            print("<span id='idContratoCapita'>");
                                print($DatosContratos["NumeroContrato"]);
                            print("</span>");
                        print("</td>");
                        print("<td>");
                            if($DatosContratoExistente["NumeroContrato"]==''){
                                $css->select("CmbContratoExistente_$i", "selector", "CmbContratoExistente", "", "", "", "style=width:100%;");
                                    $css->option("", "", "", "", "", "");
                                        print("Buscar contrato para asociar");
                                    $css->Coption();
                                $css->Cselect();
                                $css->CrearBotonEvento("btnAsociarContrato", "Asociar Contrato", 1, "onclick", "AsociarContratoEquivalente(`".$DatosContratos["NumeroContrato"]."`,`CmbContratoExistente_$i`)", "verde", "style='width:150px;'");
                                $css->CrearBotonEvento("btnCrearContrato", "Crear Contrato", 1, "onclick", "AbreFormularioCrearContrato(`".$DatosContratos["NumeroContrato"]."`)", "azul", "style='width:150px;'");
                            }else{
                                $css->input("date", "FechaInicioContratoCapita", "form-control", "FechaInicioContratoCapita", "", $DatosContratoExistente["FechaInicioContrato"], "Fecha Inicio Contrato", "", "", "style='line-height: 15px;'"."max=".date("Y-m-d"));
                                $css->input("date", "FechaFinalContratoCapita", "form-control", "FechaFinalContratoCapita", "", $DatosContratoExistente["FechaFinalContrato"], "Fecha Final Contrato", "", "", "style='line-height: 15px;'"."max=".date("Y-m-d"));
                                $css->input("text", "TxtValorCapita", "form-control", "TxtValorCapita", "", $DatosContratoExistente["ValorContrato"], "Valor Contrato", "off", "", "script");
                                $css->CrearBotonEvento("btnAgregarContrato", "Agregar Contrato", 1, "onclick", "AgregarContratoActaLiquidacion(`".$DatosContratoExistente["ContratoEquivalente"]."`,`$idActaLiquidacion`)", "verde", "style='width:150px;'");
                            }    
                            
                        print("</td>");
                        
                        print("<td>");
                            $Contrato=$DatosContratoExistente["Contrato"];
                            $sql="SELECT *,(SELECT Ciudad FROM municipios_dane WHERE contrato_percapita.CodigoDane=municipios_dane.CodigoDane LIMIT 1) AS NombreMunicipio FROM contrato_percapita WHERE NIT_IPS='$CmbIPS' AND Contrato='$Contrato'";
                            $ConsultaPercapita=$obCon->Query($sql);
                            while($DatosPercapita=$obCon->FetchAssoc($ConsultaPercapita)){
                                $css->div("", "col-md-3", "", "", "", "", "");
                                    print($DatosPercapita["NombreMunicipio"]);
                                $css->Cdiv();
                                $css->div("", "col-md-2", "", "", "", "", "");
                                    print($DatosPercapita["PorcentajePoblacional"]);
                                $css->Cdiv();
                                $css->div("", "col-md-2", "", "", "", "", "");
                                    print($DatosPercapita["ValorPercapitaXDia"]);
                                $css->Cdiv();
                                $css->div("", "col-md-2", "", "", "", "", "");
                                    print($DatosPercapita["FechaInicioPercapita"]);
                                $css->Cdiv();
                                $css->div("", "col-md-2", "", "", "", "", "");
                                    print($DatosPercapita["FechaFinPercapita"]);
                                $css->Cdiv();
                            }
                        print("</td>");
                        
                    $css->CierraFilaTabla();
                }
                
                $css->CerrarTabla();
                
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
                    
                    $css->FilaTabla(14);
                        
                        $css->ColTabla("<strong>CONTRATOS AGREGADOS AL ACTA DE LIQUIDACIÓN No. $idActaLiquidacion:</strong>", 4);
                    
                    $css->CierraFilaTabla();
                    
                    $sql="SELECT t1.ID,t2.Contrato,t2.FechaInicioContrato,t2.FechaFinalContrato,t2.ValorContrato
                             FROM actas_liquidaciones_contratos t1 INNER JOIN contratos t2 ON t1.idContrato=t2.ContratoEquivalente 
                             WHERE t1.idActaLiquidacion='$idActaLiquidacion' AND NitIPSContratada='$CmbIPS'";
                    $Consulta=$obCon->Query($sql);
                    
                    while($DatosContratos=$obCon->FetchAssoc($Consulta)){
                        $idItem=$DatosContratos["ID"];
                        $css->FilaTabla(14);                        
                            $css->ColTabla("<strong>CONTRATO DE PRESTACIÓN DE SERVICIOS No:</strong>", 2);                            
                            $css->ColTabla("<strong>".$DatosContratos["Contrato"]."</strong>", 1);
                            print("<td style=text-align:right>");
                                $css->CrearBotonEvento("btnEliminarContratoActaLiquidacion", "X", 1, "onclick", "EliminarContratoActa($idItem)", "rojo","style=width:50px;");
                            print("</td>");
                        $css->CierraFilaTabla();
                        $css->FilaTabla(14);       
                            $css->ColTabla("<strong>Fecha Inicial:</strong>", 3,"R");
                            $css->ColTabla($DatosContratos["FechaInicioContrato"], 1);
                        $css->CierraFilaTabla();
                        $css->FilaTabla(14);       
                            $css->ColTabla("<strong>Fecha Final:</strong>", 3,"R");
                            $css->ColTabla($DatosContratos["FechaFinalContrato"], 1);
                        $css->CierraFilaTabla();
                        $css->FilaTabla(14);       
                            $css->ColTabla("<strong>Valor del Contrato:</strong>", 3,"R");
                            $css->ColTabla(number_format($DatosContratos["ValorContrato"]), 1);
                        $css->CierraFilaTabla();
                    }
                    
                    
                $css->CerrarTabla();
                $css->CrearTabla();
                    $sql="SELECT SUM(t1.ValorDocumento) as TotalFacturado, 
                        SUM(t1.Impuestos) as Impuestos, SUM(t1.TotalDevoluciones) AS Devoluciones,
                        SUM(t1.TotalGlosaInicial) as Glosa, SUM(t1.TotalGlosaFavor) AS GlosaFavor,
                        SUM(t1.TotalCopagos) as Copagos, SUM(t1.OtrosDescuentos) AS OtrosDescuentos,
                        SUM(t1.TotalPagos) as TotalPagos,SUM(t1.TotalAnticipos) as TotalAnticipos,
                        SUM(t1.AjustesCartera) as AjustesCartera,SUM(t1.ValorSegunEPS) AS Saldo,
                        SUM(t1.DescuentoBDUA) as DescuentoBDUA
                        FROM $db.actas_conciliaciones_items t1 
                        WHERE EXISTS 
                        (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t1.NumeroContrato=t2.idContrato);  ";
                    $TotalesActa=$obCon->FetchAssoc($obCon->Query($sql));
                    $sql="UPDATE actas_liquidaciones 
                            SET ValorFacturado=$TotalesActa[TotalFacturado], 
                                
                                RetencionImpuestos=$TotalesActa[Impuestos], 
                                Devolucion=$TotalesActa[Devoluciones], 
                                Glosa=$TotalesActa[Glosa],
                                GlosaFavor=$TotalesActa[GlosaFavor],
                                NotasCopagos=$TotalesActa[Copagos] + $TotalesActa[TotalAnticipos],
                                RecuperacionImpuestos=0, 
                                OtrosDescuentos=$TotalesActa[OtrosDescuentos] + $TotalesActa[AjustesCartera] , 
                                ValorPagado=$TotalesActa[TotalPagos],
                                Saldo=$TotalesActa[Saldo],
                                DescuentoBDUA=$TotalesActa[DescuentoBDUA]    
                              
                            WHERE ID='$idActaLiquidacion'
                             ";
                    $obCon->Query($sql);
                    $DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion);
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>TOTALES ACTA DE LIQUIDACIÓN:</strong>", 5,'C');
                    $css->CierraFilaTabla();
                    if($TipoActa==1){
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>VALOR FACTURADO</strong>", 1);
                            $css->ColTabla("<strong>RETENCION IMPUESTOS</strong>", 1);
                            $css->ColTabla("<strong>DEVOLUCION</strong>", 1);
                            $css->ColTabla("<strong>GLOSA</strong>", 1);
                            $css->ColTabla("<strong>GLOSA A FAVOR DE ASMET</strong>", 1);
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            $css->ColTabla(number_format($DatosActa["ValorFacturado"]), 1);
                            $css->ColTabla(number_format($DatosActa["RetencionImpuestos"]), 1);
                            $css->ColTabla(number_format($DatosActa["Devolucion"]), 1);
                            $css->ColTabla(number_format($DatosActa["Glosa"]), 1);
                            $css->ColTabla(number_format($DatosActa["GlosaFavor"]), 1);
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>NOTA CREDITO / COPAGOS</strong>", 1);
                            $css->ColTabla("<strong>RECUPERACION EN IMPUESTOS</strong>", 1);
                            $css->ColTabla("<strong>OTROS DESCUENTOS</strong>", 1);
                            $css->ColTabla("<strong>VALOR PAGADO</strong>", 1);
                            $css->ColTabla("<strong>SALDO</strong>", 1);
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            $css->ColTabla(number_format($DatosActa["NotasCopagos"]), 1);
                            $css->ColTabla(number_format($DatosActa["RecuperacionImpuestos"]), 1);
                            $css->ColTabla(number_format($DatosActa["OtrosDescuentos"]), 1);
                            $css->ColTabla(number_format($DatosActa["ValorPagado"]), 1);
                            $css->ColTabla(number_format($DatosActa["Saldo"]), 1);
                        $css->CierraFilaTabla();
                    }
                    
                    if($TipoActa==4){
                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>VALOR FACTURADO</strong>", 1);
                            $css->ColTabla("<strong>RETENCION IMPUESTOS</strong>", 1);
                            $css->ColTabla("<strong>Descuento o Reconocimiento por BDUA</strong>", 1);
                            $css->ColTabla("<strong>DESCUENTOS CONCILIADO A FAVOR ASMET</strong>", 1);
                            $css->ColTabla("<strong>VALOR PAGADO</strong>", 1);
                            $css->ColTabla("<strong>SALDO</strong>", 1);
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            $css->ColTabla(number_format($DatosActa["ValorFacturado"]), 1);
                            $css->ColTabla(number_format($DatosActa["RetencionImpuestos"]), 1);
                            $css->ColTabla(number_format($DatosActa["DescuentoBDUA"]), 1);
                            $css->ColTabla(number_format($DatosActa["GlosaFavor"]), 1);
                            $css->ColTabla(number_format($DatosActa["ValorPagado"]), 1);
                            $css->ColTabla(number_format($DatosActa["Saldo"]), 1);
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            $css->ColTabla("<strong>NOTA CREDITO / COPAGOS</strong>", 1);
                            $css->ColTabla("<strong>RECUPERACION EN IMPUESTOS</strong>", 1);
                            $css->ColTabla("<strong>OTROS DESCUENTOS</strong>", 1);
                            $css->ColTabla("<strong>VALOR PAGADO</strong>", 1);
                            $css->ColTabla("<strong>SALDO</strong>", 1);
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            $css->ColTabla(number_format($DatosActa["NotasCopagos"]), 1);
                            $css->ColTabla(number_format($DatosActa["RecuperacionImpuestos"]), 1);
                            $css->ColTabla(number_format($DatosActa["OtrosDescuentos"]), 1);
                            $css->ColTabla(number_format($DatosActa["ValorPagado"]), 1);
                            $css->ColTabla(number_format($DatosActa["Saldo"]), 1);
                        $css->CierraFilaTabla();
                    }
                    print("<tr>");
                    print("<td colspan=5 style=font-size:16px;text-align:center>");
                        print("<strong>AGREGAR FIRMAS:</strong>");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    print("<td>");
                    print("</td>");
                    print("<td colspan=3>");
                        print("<strong>DEL BANCO DE FIRMAS AGS:</strong>");
                    print("</td>");
                     print("<td>");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                     print("<td>");
                    print("</td>");
                    print("<td colspan=3>");
                        $css->select("CmbFirmaUsual", "form-control", "CmbFirmaUsual", "", "", "", "style=width:100%");
                        $ConsultaFirmas=$obCon->ConsultarTabla("actas_conciliaciones_firmas_usuales", " ORDER BY ID");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione una firma");
                            $css->Coption();
                            $css->option("", "", "", "RI", "", "");
                                print("REPRESENTANTE IPS");
                            $css->Coption();
                            while($DatosFirmas=$obCon->FetchAssoc($ConsultaFirmas)){
                                $css->option("", "", "", $DatosFirmas["ID"], "", "");
                                    print($DatosFirmas["Nombre"]." ".$DatosFirmas["Cargo"]);
                                $css->Coption();
                            }
                        $css->Cselect();
                        
                                    
                        $css->CrearBotonEvento("btnAgregarFirma1", "Agregar", 1, "onclick", "AgregueFirma(1)", "azul", "","","");
                        
                        
                        
                    print("</td>");
                     print("<td>");
                    print("</td>");
                
                print("<tr><td></td></tr>");
                print("<tr>");
                    print("<td colspan=1 style=font-size:16px;>");
                        print("<strong>Fecha de Firma: </strong>");
                        $css->input("date", "TxtFechaDeFirma", "", "TxtFechaDeFirma", "", ($DatosActa["FechaFirma"]), "Fecha de la firma", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TxtFechaDeFirma`,`FechaFirma`);DibujeConstanciaFirmaActa();","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    
                    print("</td>");
                    print("<td colspan=2 style=font-size:16px;>");
                        print("<strong>Ciudad de Firma: </strong>");
                        $css->input("text", "TxtCiudadDeFirma", "", "TxtCiudadDeFirma", "", $DatosActa["CiudadFirma"], "Ciudad", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TxtCiudadDeFirma`,`CiudadFirma`);DibujeConstanciaFirmaActa();");
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    print("<td colspan=3 style=font-size:16px;>");
                        $css->CrearDiv("DivConstanciaFirma", "","", 1, 1);
                            $DatosFechaFirma= explode("-", $DatosActa["FechaFirma"]);  
                            $dia=$obNumLetra->convertir($DatosFechaFirma[2]);
                            $mes=$obNumLetra->meses($DatosFechaFirma[1]);
                            $anio=$obNumLetra->convertir($DatosFechaFirma[0]);
                            print("Para constancia se firma en <strong>".($DatosActa["CiudadFirma"])."</strong>");
                            
                            print(", a los $dia ($DatosFechaFirma[2]) días del mes de $mes del $anio ($DatosFechaFirma[0]):");
                        $css->CerrarDiv();
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    print("<td colspan=5>");
                   // $css->CerrarTabla();
                    $css->CrearDiv("DivFirmasActaConciliacion", "", "", 1, 1);

                    $css->CerrarDiv();
                   // $css->CrearTabla();
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    print("<td colspan=6>");
                        print("<strong>Opciones del Documento:<strong>");
                    print("</td>");
                print("</tr>");
                print("<tr>");
                
                print("<tr>");
                    print("<td colspan=2>");
                        print("<strong>Imprimir<strong>");
                    print("</td>");
                    print("<td>");
                        print("<strong>Soporte del Acta<strong>");
                    print("</td>");
                    
                    print("<td colspan=2>");
                        print("<strong>Cerrar Acta<strong>");
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    
                    print("<td colspan=2>");
                        $Ruta="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=37&idActaLiquidacion=$idActaLiquidacion";
                        print("<a href='$Ruta' target='_BLANK'><button class='btn btn-success'>Imprimir PDF</button></a>");
                        $Ruta="../../general/procesadores/GeneradorCSV.process.php?Opcion=3&idActaConciliacion=$idActaLiquidacion&db=$db";
                        print(" <a href='$Ruta' target='_BLANK'><button class='btn btn-primary'>Anexo por facturas</button></a>");
                        $Ruta="../../general/procesadores/GeneradorCSV.process.php?Opcion=4&idActaConciliacion=$idActaLiquidacion&db=$db";
                        print(" <a href='$Ruta' target='_BLANK'><button class='btn btn-warning'>Anexo por radicados</button></a>");
                        
                    print("</td>");
                    print("<td>");        
                       $css->input("file", "UpSoporteActaLiquidacionCierre", "", "UpSoporteActaLiquidacionCierre", "Soporte Acta", "Subir Acta Firmada", "Subir Acta Firmada", "off", "", "");
                    print("</td>");
                    print("<td colspan=2>");
        
                        $css->CrearBotonEvento("btnGuardarActaLiquidacion", "Cerrar Acta", 1, "onclick", "CerrarActaLiquidacion()", "rojo", "");
                        $css->ProgressBar("PgProgresoCruce", "LyProgresoCruce", "", 0, 0, 100, 0, "100%", "", "");
                        
                        $css->CrearDiv("DivMensajesCerrarActa", "", "center", 1, 1);
                        
                        $css->CerrarDiv();
                    print("</td>");
                    
                print("</tr>");
                $css->CerrarTabla();
            $css->Cdiv();
            print("<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>");
            $css->Csection();
        break;   //Fin caso 3
       
        case 4://Se dibujan las firmas
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            
            if($idActaLiquidacion==''){
                $css->CrearTitulo("Por favor Seleccione un Acta", "rojo");
                exit();
            }
            
            $Consulta=$obCon->ConsultarTabla("actas_liquidaciones_firmas", "WHERE idActaLiquidacion='$idActaLiquidacion'");
            $i=0;
            while($DatosFirmas=$obCon->FetchAssoc($Consulta)){
                
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    print("<br><br><hr></hr>");
                    $idFirma=$DatosFirmas["ID"];
                    $css->li("", "fa  fa-remove", "", "onclick=EliminarFirma(`$idFirma`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                    $css->Cli();
                    $NombreCaja="TxtFirmaNombreActa_".$idFirma; 
                    $css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosFirmas["Nombre"], "Nombre", "off", "", "onchange=EditeFirmaActaConciliacion(`$idFirma`,`$NombreCaja`,`Nombre`);");
                    $NombreCaja="TxtFirmaCargoActa_".$idFirma; 
                    $css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosFirmas["Cargo"], "Cargo", "off", "", "onchange=EditeFirmaActaConciliacion(`$idFirma`,`$NombreCaja`,`Cargo`);");
                    $NombreCaja="TxtFirmaEmpresaActa_".$idFirma; 
                    $css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosFirmas["Empresa"], "Empresa", "off", "", "onchange=EditeFirmaActaConciliacion(`$idFirma`,`$NombreCaja`,`Empresa`);");
                    
                $css->CerrarDiv();
                
            }  
        break;//Fin caso 4    
        
        case 5://Dibuja la constancia de la firma
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion);
            $DatosFechaFirma= explode("-", $DatosActa["FechaFirma"]);  
            $dia=$obNumLetra->convertir($DatosFechaFirma[2]);
            //$dia=$obNumLetra->convertir(31);
            $mes=$obNumLetra->meses($DatosFechaFirma[1]);
            $anio=$obNumLetra->convertir($DatosFechaFirma[0]);
            print("Para constancia, se firma en <strong>".($DatosActa["CiudadFirma"])."</strong>");

            print(", a los $dia ($DatosFechaFirma[2]) días del mes de $mes del $anio ($DatosFechaFirma[0]):");
        break;// Fin caso 5
        
    }
    
          
}else{
    print("No se enviaron parametros");
}
?>