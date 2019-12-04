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
            $DatosEPS=$obCon->DevuelveValores("eps", "NIT", $CmbEPS);
            $DatosRepresentanteEPS=$obCon->DevuelveValores("eps_representantes_legales", "idEPS", $DatosEPS["ID"]);
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
                        $css->input("text", "NombreRepresentanteIPS", "form-control", "NombreRepresentanteIPS", "Representante IPS", $DatosIPS["NombresRepresentante"], "Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Apellidos:</strong>", 1);
                    print("<td>");
                        $css->input("text", "ApellidosRepresentanteEPS", "form-control", "ApellidosRepresentanteEPS", "Representante EPS", $DatosRepresentanteEPS["Apellidos"], "Apellidos Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Apellidos:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "ApellidosRepresentanteIPS", "form-control", "ApellidosRepresentanteIPS", "Representante IPS", $DatosIPS["ApellidosRepresentante"], "Apellidos Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Identificación:</strong>", 1);
                    print("<td>");
                        $css->input("text", "IdentificacionRepresentanteEPS", "form-control", "IdentificacionRepresentanteEPS", "Identificación Representante EPS", $DatosRepresentanteEPS["Identificacion"], "Identificación Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Identificación:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "IdentificacionRepresentanteIPS", "form-control", "IdentificacionRepresentanteIPS", "IdentificaciónRepresentante IPS", $DatosIPS["CedulaRepresentante"], "Identificación Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Domicilio:</strong>", 1);
                    print("<td>");
                        $css->input("text", "DomicilioRepresentanteEPS", "form-control", "DomicilioRepresentanteEPS", "Domicilio Representante EPS", $DatosRepresentanteEPS["Domicilio"], "Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Domicilio:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "DomicilioRepresentanteIPS", "form-control", "DomicilioRepresentanteIPS", "Domicilio Representante IPS", $DatosIPS["Municipio"], "Domicilio Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Dirección:</strong>", 1);
                    print("<td>");
                        $css->input("text", "DireccionRepresentanteEPS", "form-control", "DireccionRepresentanteEPS", "Dirección Representante EPS", $DatosRepresentanteEPS["Direccion"], "Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Dirección:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "DireccionRepresentanteIPS", "form-control", "DireccionRepresentanteIPS", "Dirección Representante IPS", $DatosIPS["Direccion"], "Dirección Representante IPS", "off", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Teléfono:</strong>", 1);
                    print("<td>");
                        $css->input("text", "TelefonoRepresentanteEPS", "form-control", "TelefonoRepresentanteEPS", "Teléfono Representante EPS", $DatosRepresentanteEPS["Telefono"], "Representante EPS", "off", "", "");
                    print("</td>");
                    $css->ColTabla("<strong>Teléfono:</strong>", 1);  
                    print("<td>");
                        $css->input("text", "TelefonoRepresentanteIPS", "form-control", "TelefonoRepresentanteIPS", "Teléfono Representante IPS", $DatosIPS["Telefono"], "Teléfono Representante IPS", "off", "", "");
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
                    $idActa=$DatosActas["ID"];
                    $TextCombo=$DatosActas["ID"]." ".$DatosActas["FechaFinal"]." ".$DatosActas["RazonSocialIPS"]." ".$DatosActas["NIT_IPS"];
                    $TipoActa=$DatosActas["TipoActaLiquidacion"];
                    $sql="SELECT Nombre FROM actas_liquidaciones_tipo WHERE ID='$TipoActa'";
                    $DatosTipoActa=$obCon->FetchAssoc($obCon->Query($sql));
                    $TextCombo.=" ".$DatosTipoActa["Nombre"];
                    
                    $sql="SELECT GROUP_CONCAT(idContrato) as ContratosAgregados FROM actas_liquidaciones_contratos WHERE idActaLiquidacion='$idActa'";
                    $ConsultaContratos=$obCon->FetchAssoc($obCon->Query($sql));
                    $TextCombo.=" ".$ConsultaContratos["ContratosAgregados"];
                    $css->option("", "", "", $DatosActas["ID"], "", "");
                        print($TextCombo);
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
            
            $Titulo="Ajustes";
            $Nombre="ImgShowMenu";
            $RutaImage="../../images/actualizar.gif";
            $javascript="onclick=MostrarActa()";
            $VectorBim["f"]=0;
            $target="#DialTabla";
            $css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,$javascript,50,50,"fixed","right:10px;top:200;z-index:100;",$VectorBim);
            
            $DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion);
            
            if($DatosActa["IPS_Nombres_Representante_Legal"]==''){
                $obCon->ActualizaRegistro("actas_liquidaciones", "IPS_Nombres_Representante_Legal", $DatosIPS["NombresRepresentante"], "ID", $idActaLiquidacion);
            }
            if($DatosActa["IPS_Apellidos_Representante_Legal"]==''){
                $obCon->ActualizaRegistro("actas_liquidaciones", "IPS_Apellidos_Representante_Legal", $DatosIPS["ApellidosRepresentante"], "ID", $idActaLiquidacion);
            }
            if($DatosActa["IPS_Identificacion_Representante_Legal"]==''){
                $obCon->ActualizaRegistro("actas_liquidaciones", "IPS_Identificacion_Representante_Legal", $DatosIPS["CedulaRepresentante"], "ID", $idActaLiquidacion);
            }
            if($DatosActa["IPS_Domicilio"]==''){
                $obCon->ActualizaRegistro("actas_liquidaciones", "IPS_Domicilio", $DatosIPS["Municipio"], "ID", $idActaLiquidacion);
            }
            if($DatosActa["IPS_Direccion"]==''){
                $obCon->ActualizaRegistro("actas_liquidaciones", "IPS_Direccion", $DatosIPS["Direccion"], "ID", $idActaLiquidacion);
            }
            if($DatosActa["IPS_Telefono"]==''){
                $obCon->ActualizaRegistro("actas_liquidaciones", "IPS_Telefono", $DatosIPS["Telefono"], "ID", $idActaLiquidacion);
            }
            
            $sql="SELECT t1.*, t2.Nombre AS NombreTipoActa,t2.Titulo FROM actas_liquidaciones t1 "
                    . "INNER JOIN actas_liquidaciones_tipo t2 ON t2.ID=t1.TipoActaLiquidacion WHERE t1.ID='$idActaLiquidacion';";
            
            $DatosActa=$obCon->FetchAssoc($obCon->Query($sql));
            $NitIPS=$DatosActa["NIT_IPS"];
            $TipoActa=$DatosActa["TipoActaLiquidacion"];
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
                    
            $css->CrearTitulo("<strong>Acta de Liquidación No. $idActaLiquidacion, Tipo: " .utf8_encode($DatosActa["Titulo"])."</strong>");
            
                $css->CrearTabla();
                    print("<tr style=font-size:18px;border-left-style:double;border-right-style:double;border-width:5px;>");
                        print("<td>");
                            print("<strong>Fecha Inicial:</strong>");
                        print("</td>");
                        print("<td>");
                            $css->input("date", "TxtFechaInicialActaLiquidacion", "form-control", "TxtFechaInicialActaLiquidacion", "", ($DatosActa["FechaInicial"]), "Fecha Inicial", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TxtFechaInicialActaLiquidacion`,`FechaInicial`)","style='line-height: 15px;'"."max=".date("Y-m-d"));

                        print("</td>");
                    print("</tr>");
                    print("<tr style=font-size:18px;border-left-style:double;border-bottom-style:double;border-right-style:double;border-width:5px;>");
                        print("<td>");
                            print("<strong>Fecha Final:</strong>");
                        print("</td>");
                        print("<td>");
                            $css->input("date", "TxtFechaFinalLiquidacion", "form-control", "TxtFechaFinalLiquidacion", "", ($DatosActa["FechaFinal"]), "Fecha Final", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TxtFechaFinalLiquidacion`,`FechaFinal`)","style='line-height: 15px;'"."max=".date("Y-m-d"));

                            //print(utf8_decode($DatosActa["FechaCorte"]));
                        print("</td>");
                    print("</tr>");
                
                $css->div("DivContratosDisponiblesActaLiquidacion", "", "", "", "", "", "");
                    
                
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>CONTRATOS DISPONIBLES PARA LIQUIDAR:</strong>", 3,"C");
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>CONTRATOS</strong>", 1,"C");
                        $css->ColTabla("<strong>DATOS GENERALES</strong>", 1,"C");
                        $css->ColTabla("<strong>VALORES PERCAPITA POR MUNICIPIO</strong>", 1,"C");
                    $css->CierraFilaTabla();
               
               /*
                $sql="SELECT DISTINCT t1.NumeroContrato FROM actas_conciliaciones_contratos t1 "
                                . "INNER JOIN actas_conciliaciones t2 ON t1.idActaConciliacion=t2.ID "
                                . "WHERE t2.Estado=1 AND t2.MesServicioInicial>='$MesServicioInicial' AND t2.MesServicioFinal<='$MesServicioFinal' AND t2.NIT_IPS='$CmbIPS' ORDER BY t1.NumeroContrato;";
                
                * 
                */
                if($TipoActa==3 or $TipoActa==6) {  
                    $css->FilaTabla(16);
                        print("<td>");
                            
                            $css->select("CmbContratoNoEjecutado", "form-control", "", "", "", "", "style=width:500px");
                                
                                $css->option("", "", "", "", "", "");
                                    print("Seleccione un contrato");
                                $css->Coption();    
                               
                            $css->Cselect();
                            
                            $css->CrearBotonEvento("btnAgregarContratoNoEjecutado", "Agregar", 1, "onclick", "AgregarContratoNoEjecutado(`$idActaLiquidacion`)", "naranja","style=width:500px");
                        print("</td>");
                    $css->CierraFilaTabla();
                }else{
                   $sql="SELECT DISTINCT t1.NumeroContrato FROM $db.actas_conciliaciones_items t1 "
                                . "INNER JOIN actas_conciliaciones t2 ON t1.idActaConciliacion=t2.ID "
                                . "WHERE t2.Estado=1 AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal' AND t2.NIT_IPS='$CmbIPS' ORDER BY t1.NumeroContrato;";
                
                
                
                $Consulta=$obCon->Query($sql);
                $i=0;
                while ($DatosContratos=$obCon->FetchAssoc($Consulta)){
                    $i++;
                    
                    $idContrato=$DatosContratos["NumeroContrato"];
                    $sql="SELECT * FROM contratos WHERE NitIPSContratada='$CmbIPS' AND (ContratoEquivalente='$idContrato') ORDER BY Contrato";
                    
                    $DatosContratoExistente=$obCon->FetchArray($obCon->Query($sql));
                    $idItem=$DatosContratoExistente["ID"];
                    $sql="SELECT * FROM actas_liquidaciones_contratos WHERE idContrato='$idContrato'";
                    $DatosValidacionContratoActa=$obCon->FetchArray($obCon->Query($sql));
                    $css->FilaTabla(16);
                        print("<td>");
                            print("<span id='idContratoCapita'><pre>");
                                print($DatosContratos["NumeroContrato"]);
                            print("</pre></span>");
                        print("</td>");
                        if($DatosValidacionContratoActa==''){
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
                                    //$css->input($type, $id, $class, $name, $title, $value, $placeholder, $autocomplete, $vectorhtml, $Script, $styles, $Pattern, $np_app)
                                    $css->input("text", "TxtNombreContrato_$i", "form-control", "TxtNombreContrato_$i", "", $DatosContratoExistente["Contrato"], "Contrato", "off", "", "onchange=EditeContrato(`$idItem`,`TxtNombreContrato_$i`,`Contrato`)");
                                    $css->input("date", "FechaInicioContratoCapita_$i", "form-control", "FechaInicioContratoCapita_$i", "", $DatosContratoExistente["FechaInicioContrato"], "Fecha Inicio Contrato", "","", "onchange=EditeContrato(`$idItem`,`FechaInicioContratoCapita_$i`,`FechaInicioContrato`)", "style='line-height: 15px;'"."max=".date("Y-m-d"));
                                    $css->input("date", "FechaFinalContratoCapita_$i", "form-control", "FechaFinalContratoCapita_$i", "", $DatosContratoExistente["FechaFinalContrato"], "Fecha Final Contrato", "", "","onchange=EditeContrato(`$idItem`,`FechaFinalContratoCapita_$i`,`FechaFinalContrato`)", "style='line-height: 15px;'"."max=".date("Y-m-d"));
                                    $css->input("text", "TxtValorCapita_$i", "form-control", "TxtValorCapita", "", $DatosContratoExistente["ValorContrato"], "Valor Contrato", "off", "", "onchange=EditeContrato(`$idItem`,`TxtValorCapita_$i`,`ValorContrato`)");
                                    $css->CrearBotonEvento("btnAgregarContrato", "Agregar Contrato", 1, "onclick", "AgregarContratoActaLiquidacion(`".$DatosContratoExistente["ContratoEquivalente"]."`,`$idActaLiquidacion`,`$i`)", "verde", "style='width:150px;'");
                                }    
                                $css->CrearBotonEvento("btnRenombrarContrato", "Renombrar Contrato", 1, "onclick", "ModalRenombrarContrato(`".$DatosContratos["NumeroContrato"]."`)", "rojo", "style='width:150px;'");
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
                        }else{
                            print("<td>");
                                print("Contrato agregado al acta de liquidación No. ".$DatosValidacionContratoActa["idActaLiquidacion"]."<br>");
                                
                                //$css->CrearBotonEvento("btnAsociarContrato", "Asociar Contrato", 1, "onclick", "AsociarContratoEquivalente(`".$DatosContratos["NumeroContrato"]."`,`CmbContratoExistente_$i`)", "verde", "style='width:150px;'");
                               
                                    $css->CrearBotonEvento("btnCrearContrato", "Crear Contrato", 1, "onclick", "AbreFormularioCrearContrato(`".$DatosContratos["NumeroContrato"]."`)", "azul", "style='width:150px;'");
                                     print("<br>");
                                    $css->CrearBotonEvento("btnRenombrarContrato", "Renombrar Contrato", 1, "onclick", "ModalRenombrarContrato(`".$DatosContratos["NumeroContrato"]."`)", "rojo", "style='width:150px;'");
                            print("</td>");
                            print("<td>");
                                
                            print("</td>");
                        }
                        
                    $css->CierraFilaTabla();
                    }
                
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
                    $Verificacion=$obCon->DevuelveValores("actas_liquidaciones_contratos", "idActaLiquidacion", $idActaLiquidacion);
                    if($Verificacion["ID"]==""){
                        $css->CrearTitulo("Debe agregar al menos un contrato a esta acta",'rojo');
                        exit();
                    }
                    $sql="SELECT SUM(t1.ValorDocumento) as TotalFacturado, 
                        SUM(t1.Impuestos) as Impuestos, SUM(t1.TotalDevoluciones) AS Devoluciones,
                        SUM(t1.TotalGlosaInicial) as Glosa, SUM(t1.TotalGlosaFavor) AS GlosaFavor,
                        SUM(t1.TotalCopagos) as Copagos, SUM(t1.OtrosDescuentos) AS OtrosDescuentos,
                        SUM(t1.TotalPagos) as TotalPagos,SUM(t1.TotalAnticipos) as TotalAnticipos,
                        SUM(t1.AjustesCartera) as AjustesCartera,SUM(t1.ValorSegunEPS) AS Saldo,
                        SUM(t1.DescuentoBDUA) as DescuentoBDUA
                        FROM $db.actas_conciliaciones_items t1 
                        WHERE EXISTS 
                        (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t1.NumeroContrato=t2.idContrato AND idActaLiquidacion='$idActaLiquidacion') AND t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal;  ";
                    
                    $TotalesActa=$obCon->FetchAssoc($obCon->Query($sql));
                    
                    $TotalesActaHistorial["TotalFacturado"]=0;
                    $TotalesActaHistorial2["TotalFacturado"]=0;
                    if($TipoActa<>4 and $TipoActa<>5 and $TipoActa<>6 and $TipoActa<>7){
                        $sql="SELECT  SUM(t1.ValorOriginal) as TotalFacturado
                            
                            FROM $db.historial_carteracargada_eps t1 WHERE NOT EXISTS (SELECT 1 FROM $db.actas_conciliaciones_items t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado)
                        AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                       AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                       ";
                        $TotalesActaHistorial=$obCon->FetchAssoc($obCon->Query($sql));
                        
                        $sql="SELECT  SUM(t1.ValorOriginal) as TotalFacturado
                            
                            FROM $db.historial_carteracargada_eps t1 WHERE NOT 
                    EXISTS (SELECT 1 FROM $db.actas_conciliaciones_items t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND t1.NumeroRadicado=t2.NumeroRadicado)
                     AND (t1.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal)                         
                    AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t3 WHERE t3.idContrato=t1.NumeroContrato AND t3.idActaLiquidacion='$idActaLiquidacion')     
                       ";
                       // $TotalesActaHistorial2=$obCon->FetchAssoc($obCon->Query($sql));
                    }
                    $TotalFacturado=$TotalesActa["TotalFacturado"]+$TotalesActaHistorial["TotalFacturado"];
                    $TotalDevolucion=($TotalesActa["Devoluciones"])+($TotalesActaHistorial["TotalFacturado"]);
                    if($TotalesActa["Impuestos"]==""){
                        $TotalesActa["Impuestos"]=0;
                    }
                    if($TotalesActa["Glosa"]==""){
                        $TotalesActa["Glosa"]=0;
                    }
                    if($TotalesActa["GlosaFavor"]==""){
                        $TotalesActa["GlosaFavor"]=0;
                    }
                    if($TotalesActa["Copagos"]==""){
                        $TotalesActa["Copagos"]=0;
                    }
                    if($TotalesActa["OtrosDescuentos"]==""){
                        $TotalesActa["OtrosDescuentos"]=0;
                    }
                    if($TotalesActa["AjustesCartera"]==""){
                        $TotalesActa["AjustesCartera"]=0;
                    }
                    if($TotalesActa["TotalPagos"]==""){
                        $TotalesActa["TotalPagos"]=0;
                    }
                    if($TotalesActa["TotalAnticipos"]==""){
                        $TotalesActa["TotalAnticipos"]=0;
                    }
                    if($TotalesActa["Saldo"]==""){
                        $TotalesActa["Saldo"]=0;
                    }
                    if($TotalesActa["DescuentoBDUA"]==""){
                        $TotalesActa["DescuentoBDUA"]=0;
                    }
                    $SaldoTotal=$TotalesActa["Saldo"]-$DatosActa["OtrosDescuentosConciliadosAfavor"];
                    $sql="UPDATE actas_liquidaciones 
                            SET ValorFacturado=".$TotalFacturado.", 
                                
                                RetencionImpuestos=".$TotalesActa["Impuestos"].", 
                                Devolucion=".$TotalDevolucion.", 
                                Glosa=".$TotalesActa["Glosa"].",
                                GlosaFavor=".$TotalesActa["GlosaFavor"].",
                                NotasCopagos=".$TotalesActa["Copagos"].",
                                RecuperacionImpuestos=0, 
                                OtrosDescuentos=".$TotalesActa["OtrosDescuentos"]." + ".$TotalesActa["AjustesCartera"]." , 
                                ValorPagado=".$TotalesActa["TotalPagos"]." + ".$TotalesActa["TotalAnticipos"]." ,
                                Saldo=".$SaldoTotal.",
                                DescuentoBDUA=".$TotalesActa["DescuentoBDUA"]."    
                              
                            WHERE ID='$idActaLiquidacion'
                             ";
                    $obCon->Query($sql);
                    
                    $sql="SELECT ID,FechaInicial,FechaFinal,Valor,NombreContrato 
                             FROM actas_liquidaciones_contratos
                             WHERE idActaLiquidacion='$idActaLiquidacion'";
                    $Consulta=$obCon->Query($sql);
                    
                    while($DatosContratos=$obCon->FetchAssoc($Consulta)){
                        $idItem=$DatosContratos["ID"];
                        $css->FilaTabla(14);                        
                            $css->ColTabla("<strong>CONTRATO DE PRESTACIÓN DE SERVICIOS No:</strong>", 2);                            
                            $css->ColTabla("<strong>".$DatosContratos["NombreContrato"]."</strong>", 1);
                            print("<td style=text-align:right>");
                                $css->CrearBotonEvento("btnEliminarContratoActaLiquidacion", "X", 1, "onclick", "EliminarContratoActa($idItem)", "rojo","style=width:50px;");
                            print("</td>");
                        $css->CierraFilaTabla();
                        $css->FilaTabla(14);       
                            $css->ColTabla("<strong>Fecha Inicial:</strong>", 3,"R");
                            $css->ColTabla($DatosContratos["FechaInicial"], 1);
                        $css->CierraFilaTabla();
                        $css->FilaTabla(14);       
                            $css->ColTabla("<strong>Fecha Final:</strong>", 3,"R");
                            $css->ColTabla($DatosContratos["FechaFinal"], 1);
                        $css->CierraFilaTabla();
                        $css->FilaTabla(14);       
                            $css->ColTabla("<strong>Valor del Contrato:</strong>", 3,"R");
                            $css->ColTabla(number_format($DatosContratos["Valor"]), 1);
                        $css->CierraFilaTabla();
                    }
                    
                    
                $css->CerrarTabla();
                $css->CrearTabla();
                    
                    $DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion);
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>TOTALES ACTA DE LIQUIDACIÓN:</strong>", 5,'C');
                    $css->CierraFilaTabla();
                    if($TipoActa==1 OR $TipoActa==7 OR $TipoActa==9){
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
                            $css->ColTabla("<strong>DESCUENTOS A FAVOR DE ASMET</strong>", 1);
                            $css->ColTabla("<strong>OTROS DESCUENTOS CONCILIADOS A FAVOR DE ASMET</strong>", 1);
                            
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            $css->ColTabla(number_format($DatosActa["ValorFacturado"]), 1);
                            $css->ColTabla(number_format($DatosActa["RetencionImpuestos"]), 1);
                            $css->ColTabla(number_format($DatosActa["DescuentoBDUA"]), 1);
                            $css->ColTabla(number_format($DatosActa["GlosaFavor"]), 1);
                            print("<td>");
                                $css->input("text", "TxtOtrosDescuentosAFavorAsmet", "form-control", "TxtOtrosDescuentosAFavorAsmet", "Otros Descuentos A Favor", $DatosActa["OtrosDescuentosConciliadosAfavor"], "Otros Descuentos A Favor", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TxtOtrosDescuentosAFavorAsmet`,`OtrosDescuentosConciliadosAfavor`)");
                            print("</td>");
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            //$css->ColTabla("<strong>NOTA CREDITO / COPAGOS</strong>", 1);
                            $css->ColTabla("<strong>RECUPERACION EN IMPUESTOS</strong>", 1);
                            $css->ColTabla("<strong>OTROS DESCUENTOS</strong>", 1);
                            $css->ColTabla("<strong>VALOR PAGADO</strong>", 2);
                            $css->ColTabla("<strong>SALDO</strong>", 1);
                        $css->CierraFilaTabla();

                        $css->FilaTabla(16);
                            //$css->ColTabla(number_format($DatosActa["NotasCopagos"]), 1);
                            $css->ColTabla(number_format($DatosActa["RecuperacionImpuestos"]), 1);
                            $css->ColTabla(number_format($DatosActa["OtrosDescuentos"]), 1);
                            $css->ColTabla(number_format($DatosActa["ValorPagado"]+$DatosActa["NotasCopagos"]), 2);
                            $css->ColTabla(number_format($DatosActa["Saldo"]), 1);
                        $css->CierraFilaTabla();
                    }
                    print("<tr>");
                        print("<td>");
                        print("</td>");
                    print("</tr>");
                    print("<tr>");
                        print("<td colspan=5 style=font-size:16px;text-align:center>");
                            print("<strong>OBSERVACIONES:</strong>");
                        print("</td>");
                    print("</tr>");
                
                    print("<tr>");
                        print("<td colspan=5>");
                            $css->textarea("ObservacionesActaLiquidacion", "form-control", "ObservacionesActaLiquidacion", "", "Observaciones", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`ObservacionesActaLiquidacion`,`Observaciones`)");
                                print(($DatosActa["Observaciones"]));
                            $css->Ctextarea();
                           // $css->input("text", "ObservacionesActaLiquidacion", "form-control", "ObservacionesActaLiquidacion", "Observaciones", $DatosActa["Observaciones"], "Observaciones", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`ObservacionesActaLiquidacion`,`Observaciones`)");
                        print("</td>");
                    print("</tr>");
                    
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
                        
                        $css->input("date", "TxtFechaDeFirma", "", "TxtFechaDeFirma", "", ($DatosActa["FechaFirma"]), "Fecha de la firma", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TxtFechaDeFirma`,`FechaFirma`);DibujeConstanciaFirmaActa();","style='line-height: 15px;'"."max=".date("Y-m-d",strtotime(date("Y-m-d")."+ 15 days")));
                    
                    print("</td>");
                    print("<td colspan=2 style=font-size:16px;>");
                        print("<strong>Ciudad de Firma: </strong>");
                        $css->input("text", "TxtCiudadDeFirma", "", "TxtCiudadDeFirma", "", $DatosActa["CiudadFirma"], "Ciudad", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TxtCiudadDeFirma`,`CiudadFirma`);DibujeConstanciaFirmaActa();");
                    print("</td>");
                    print("<td colspan=2 style=font-size:16px;>");
                        print("<strong>Revisará: </strong>");
                        $css->input("text", "TxtRevisaActaliquidacion", "", "TxtRevisaActaliquidacion", "", $DatosActa["Revisa"], "Revisará", "off", "", "onchange=EditeActaLiquidacion(`$idActaLiquidacion`,`TxtRevisaActaliquidacion`,`Revisa`);");
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
                        $Ruta="../../general/procesadores/GeneradorExcel.php?idDocumento=2&CmbIPS=$CmbIPS&idActaLiquidacion=$idActaLiquidacion&TipoConsulta=1&FacturaRadicado=0";
                        print(" <a href='$Ruta' target='_BLANK'><button class='btn btn-primary'>Anexo por facturas</button></a>");
                        $Ruta="../../general/procesadores/GeneradorExcel.php?idDocumento=2&CmbIPS=$CmbIPS&idActaLiquidacion=$idActaLiquidacion&TipoConsulta=1&FacturaRadicado=1";
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
            //$DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion);
            $Consulta=$obCon->ConsultarTabla("actas_liquidaciones_firmas", "WHERE idActaLiquidacion='$idActaLiquidacion'");
            $i=0;
            while($DatosFirmas=$obCon->FetchAssoc($Consulta)){
                
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    print("<br><br><hr></hr>");
                    $idFirma=$DatosFirmas["ID"];
                    $css->li("", "fa  fa-remove", "", "onclick=EliminarFirma(`$idFirma`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                    $css->Cli();
                    if($DatosFirmas["Aprueba"]==1){
                        $Texto="Aprueba";
                        $Color="verde";
                    }
                    if($DatosFirmas["Aprueba"]==0){
                        $Texto="Marcar como usuario que aprueba";
                        $Color="azul";
                    }
                    
                    $css->CrearBotonEvento("BtnAprueba_".$idFirma, $Texto, 1, "onclick", "MarcarComoAprobador(`$idFirma`)", $Color);
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
        case 6: //dibuje el historial de actas de liquidacion
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Condicional="";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional.=" WHERE NIT_IPS='$CmbIPS' or ID = '$Busqueda' or IdentificadorActaEPS like '$Busqueda%' ";
                }
                
            }
            
            $dbPrincipal=DB;
            $statement=" `actas_liquidaciones` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 20;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(Saldo) AS TotalEPS,SUM(TotalPagosDespuesDeFirma) as TotalPagosPosteriores,SUM(NuevoSaldo) AS TotalNuevoSaldo FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $TotalEPS=$row['TotalEPS'];
            $TotalPagosPosteriores=$row['TotalPagosPosteriores'];
            $TotalNuevoSaldo=$row['TotalNuevoSaldo'];
            
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                    print("<td style='text-align:center'>");
                        print("<strong>Registros:</strong> <h4 style=color:green>". number_format($ResultadosTotales)."</h4>");
                    print("</td>");
                    print("<td colspan=3 style='text-align:center'>");
                        print("<strong>Saldo:</strong> <h4 style=color:red>". number_format($TotalEPS)."</h4>");
                        
                    print("</td>");
                    print("<td colspan=3 style='text-align:center'>");
                        
                        print("<strong>Pagos Después de Firmar:</strong> <h4 style=color:red>". number_format($TotalPagosPosteriores)."</h4>");
                        
                    print("</td>");
                    print("<td colspan=3 style='text-align:center'>");
                       
                        print("<strong>Saldo Final:</strong> <h4 style=color:red>". number_format($TotalNuevoSaldo)."</h4>");
                    print("</td>");
                    
                    print("<td>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$dbPrincipal','actas_liquidaciones','$st_reporte')", "verde", "");
                    print("</td>");
                    
                //$css->CierraFilaTabla();
                
                $st= urlencode($st_reporte);
                    if($ResultadosTotales>$limit){

                        //$css->FilaTabla(14);
                            
                            $TotalPaginas= ceil($ResultadosTotales/$limit);
                            print("<td  style=text-align:center>");
                            //print("<strong>Página: </strong>");
                            
                            print('<div class="input-group" style=width:150px>');
                            if($NumPage>1){
                                $NumPage1=$NumPage-1;
                            print('<span class="input-group-addon" onclick=CambiePagina('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePagina();";
                            $css->select("CmbPage", "form-control", "CmbPage", "", "", $FuncionJS, "");
                            
                                for($p=1;$p<=$TotalPaginas;$p++){
                                    if($p==$NumPage){
                                        $sel=1;
                                    }else{
                                        $sel=0;
                                    }
                                    
                                    $css->option("", "", "", $p, "", "",$sel);
                                        print($p);
                                    $css->Coption();
                                    
                                }

                            $css->Cselect();
                            if($ResultadosTotales>($startpoint+$limit)){
                                $NumPage1=$NumPage+1;
                            print('<span class="input-group-addon" onclick=CambiePagina('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("</div>");
                            print("</td>");
                            
                            
                          
                        }
                        print("<td>");
                            $css->CrearBotonEvento("BtnActualizarSaldos", "Actualizar Saldos", 1, "onClick", "ConfirmaActualizarSaldosLiquidaciones()", "naranja");
                        print("</td>");
                        print("<td colspan=5>");
                            $css->CrearDiv("DivProcessActualizacionActas", "", "left", 1, 1);
                            
                            $css->CerrarDiv();
                            
                            $css->CrearDiv("DivMensajesActualizacionActas", "", "left", 1, 1);
                            
                            $css->CerrarDiv();
                        print("</td>");
                            
                        $css->CierraFilaTabla(); 
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                    $css->ColTabla("<strong>ID</strong>", 1);
                    $css->ColTabla("<strong>No. Acta</strong>", 1);
                    $css->ColTabla("<strong>Fecha Inicial</strong>", 1);
                    $css->ColTabla("<strong>Fecha Final</strong>", 1);
                    $css->ColTabla("<strong>Tipo de Acta</strong>", 1);
                    $css->ColTabla("<strong>Razon Social de la IPS</strong>", 1);
                    $css->ColTabla("<strong>NIT IPS</strong>", 1);
                    $css->ColTabla("<strong>Fecha Firma</strong>", 1);
                    $css->ColTabla("<strong>Valor Facturado</strong>", 1);
                    $css->ColTabla("<strong>Retencion Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Devolucion</strong>", 1);
                    $css->ColTabla("<strong>Glosa</strong>", 1);
                    $css->ColTabla("<strong>Glosa Favor</strong>", 1);
                    $css->ColTabla("<strong>Notas Copagos</strong>", 1);
                    $css->ColTabla("<strong>Recuperación Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Otros Descuentos</strong>", 1);
                    $css->ColTabla("<strong>Descuento BDUA</strong>", 1);
                    $css->ColTabla("<strong>Valor Pagado</strong>", 1);
                    $css->ColTabla("<strong>Saldo</strong>", 1);
                    $css->ColTabla("<strong>Pagos Después de Firma</strong>", 1);
                    $css->ColTabla("<strong>Nuevo Saldo</strong>", 1);
                    $css->ColTabla("<strong>Usuario Creador</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Registro</strong>", 1);
                    
                $css->CierraFilaTabla();
                
                
                while($DatosConciliacion=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idActaLiquidacion=$DatosConciliacion["ID"];
                        $NIT_IPS=$DatosConciliacion["NIT_IPS"];
                        print("<td>");
                            $Ruta="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=37&idActaLiquidacion=$idActaLiquidacion";
                            print("<a href='$Ruta' target='_BLANK'><button class='form-control btn btn-success'>Imprimir PDF</button></a>");
                            if($DatosConciliacion["Estado"]=="1"){ //Si el acta está cerrada
                                print("<br>");
                                $Ruta="../../general/procesadores/GeneradorExcel.php?idDocumento=2&CmbIPS=$CmbIPS&idActaLiquidacion=$idActaLiquidacion&TipoConsulta=2&FacturaRadicado=0";
                                print(" <a href='$Ruta' target='_BLANK'><button class='form-control btn btn-primary'>Anexo del Acta X Facturas</button></a>");
                                print("<br>");
                                if($DatosConciliacion["TipoActaLiquidacion"]<>4){
                                    $Ruta="../../general/procesadores/GeneradorExcel.php?idDocumento=2&CmbIPS=$CmbIPS&idActaLiquidacion=$idActaLiquidacion&TipoConsulta=2&FacturaRadicado=1";
                                    print(" <a href='$Ruta' target='_BLANK'><button class='form-control btn btn-warning'>Anexo del Acta X Radicados</button></a>");
                                }
                                
                            }
                        print("</td>");
                        
                        $css->ColTabla($DatosConciliacion["ID"], 1);
                        $css->ColTabla($DatosConciliacion["IdentificadorActaEPS"], 1);
                        $css->ColTabla($DatosConciliacion["FechaInicial"], 1);
                        $css->ColTabla($DatosConciliacion["FechaFinal"], 1);
                        $css->ColTabla($DatosConciliacion["TipoActaLiquidacion"], 1);
                        $css->ColTabla($DatosConciliacion["RazonSocialIPS"], 1);
                        $css->ColTabla($DatosConciliacion["NIT_IPS"], 1);
                        
                        $css->ColTabla($DatosConciliacion["FechaFirma"], 1);
                        $css->ColTabla($DatosConciliacion["ValorFacturado"], 1);
                        $css->ColTabla($DatosConciliacion["RetencionImpuestos"], 1);
                        $css->ColTabla($DatosConciliacion["Devolucion"], 1);
                        $css->ColTabla($DatosConciliacion["Glosa"], 1);
                        $css->ColTabla($DatosConciliacion["GlosaFavor"], 1);
                        $css->ColTabla($DatosConciliacion["NotasCopagos"], 1);
                        $css->ColTabla($DatosConciliacion["RecuperacionImpuestos"], 1);
                        $css->ColTabla($DatosConciliacion["OtrosDescuentos"], 1);
                        $css->ColTabla($DatosConciliacion["DescuentoBDUA"], 1);
                        $css->ColTabla($DatosConciliacion["ValorPagado"], 1);
                        $css->ColTabla($DatosConciliacion["Saldo"], 1);
                        $css->ColTabla($DatosConciliacion["TotalPagosDespuesDeFirma"], 1);
                        $css->ColTabla($DatosConciliacion["NuevoSaldo"], 1);
                        $css->ColTabla($DatosConciliacion["idUser"], 1);
                        $css->ColTabla($DatosConciliacion["FechaRegistro"], 1);
                        
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break;//Fin caso 6
        case 7://Dibuja el formaulario para renombrar un contrato
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            
            $NumeroContrato=$obCon->normalizar($_REQUEST["NumeroContrato"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 1, "", "", "", "");
            
            $css->CrearTitulo("Renombrar Contrato: ".$NumeroContrato, "naranja");
            
            $css->CrearTabla();
                
                print("<td>");
                    $css->input("text", "TxtNumeroContratoRenombrar", "form-control", "TxtNumeroContratoRenombrar", "", "", "Contrato Nuevo", "off", "", "", "");
                print("</td>");
                print("<td>");
                    $css->CrearBotonEvento("BtnRenombrarContrato", "Renombrar", 1, "onclick", "RenombrarContrato(`$NumeroContrato`)", "rojo");
                print("</td>");
            $css->CerrarTabla();
            
        break;//Fin caso 7
    
        case 8: //Dibuja los items de las actas de liquidacion
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Condicional="";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional.=" WHERE NIT_IPS='$CmbIPS' AND (NumeroContrato like '%$Busqueda%' OR NumeroFactura like '%$Busqueda%' OR idActaLiquidacion = '$Busqueda') ";
                }
                
            }
            
            $dbPrincipal=DB;
            $statement=" $db.`vista_pagos_actas_liquidaciones` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 20;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorSegunEPS) AS TotalEPS,SUM(TotalPagosPosteriores) as TotalPagosPosteriores,SUM(SaldoFinal) AS TotalNuevoSaldo FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $TotalEPS=$row['TotalEPS'];
            $TotalPagosPosteriores=$row['TotalPagosPosteriores'];
            $TotalNuevoSaldo=$row['TotalNuevoSaldo'];
            
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                    print("<td style='text-align:center'>");
                        print("<strong>Registros:</strong> <h4 style=color:green>". number_format($ResultadosTotales)."</h4>");
                    print("</td>");
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Saldo:</strong> <h4 style=color:red>". number_format($TotalEPS)."</h4>");
                        
                    print("</td>");
                    print("<td colspan=1 style='text-align:center'>");
                        
                        print("<strong>Pagos Después de Firmar:</strong> <h4 style=color:red>". number_format($TotalPagosPosteriores)."</h4>");
                        
                    print("</td>");
                    print("<td colspan=1 style='text-align:center'>");
                       
                        print("<strong>Saldo Final:</strong> <h4 style=color:red>". number_format($TotalNuevoSaldo)."</h4>");
                    print("</td>");
                    
                    print("<td>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_pagos_actas_liquidaciones','$st_reporte')", "verde", "");
                    print("</td>");
                    
                //$css->CierraFilaTabla();
                
                $st= urlencode($st_reporte);
                    if($ResultadosTotales>$limit){

                        //$css->FilaTabla(14);
                            
                            $TotalPaginas= ceil($ResultadosTotales/$limit);
                            print("<td  style=text-align:center>");
                            //print("<strong>Página: </strong>");
                            
                            print('<div class="input-group" style=width:150px>');
                            if($NumPage>1){
                                $NumPage1=$NumPage-1;
                            print('<span class="input-group-addon" onclick=CambiePaginaItemsActas('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaItemsActas();";
                            $css->select("CmbPageItemsActas", "form-control", "CmbPageItemsActas", "", "", $FuncionJS, "");
                            
                                for($p=1;$p<=$TotalPaginas;$p++){
                                    if($p==$NumPage){
                                        $sel=1;
                                    }else{
                                        $sel=0;
                                    }
                                    
                                    $css->option("", "", "", $p, "", "",$sel);
                                        print($p);
                                    $css->Coption();
                                    
                                }

                            $css->Cselect();
                            if($ResultadosTotales>($startpoint+$limit)){
                                $NumPage1=$NumPage+1;
                            print('<span class="input-group-addon" onclick=CambiePaginaItemsActas('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("</div>");
                            print("</td>");
                            
                            
                          
                        }
                                                    
                        $css->CierraFilaTabla(); 
                $Columnas=$obCon->ShowColums($db.".vista_pagos_actas_liquidaciones");
                $css->FilaTabla(16);
                foreach ($Columnas["Field"] as $key => $value) {
                    $css->ColTabla("<strong>$value</strong>", 1);
                }
                                                        
                $css->CierraFilaTabla();
                
                
                while($DatosConciliacion=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        foreach ($Columnas["Field"] as $key => $value) {
                            $css->ColTabla($DatosConciliacion[$value], 1);
                        }
                                                
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break;//Fin caso 8
        
    }
    
          
}else{
    print("No se enviaron parametros");
}
?>