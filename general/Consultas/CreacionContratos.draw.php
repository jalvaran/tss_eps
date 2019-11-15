<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../../modelo/php_conexion.php");
include_once("../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    
    switch ($_REQUEST["Accion"]) {        
       
        case 1: //Dibujar el formulario para crear un contrato
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            //$DatosEPS=$obCon->DevuelveValores("eps", "NIT", $CmbEPS);
            $Contrato="";     
            if(isset($_REQUEST["Contrato"])){
                $Contrato=$obCon->normalizar($_REQUEST["Contrato"]);
            }
            
            $css->CrearTitulo("Crear Contrato No. <span id='spContratoEquivalente'>".$Contrato."</span>");
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>CONTRATANTE:</strong>", 1);
                    print("<td>");
                        $css->span("spNitEPS", "", "", "");
                            print("<strong>".$CmbEPS."</strong>");
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
                                $sql="SELECT NumeroContrato FROM contratos WHERE NitIPSContratada='$CmbIPS' AND (ClasificacionContrato='CONTRATO' OR ClasificacionContrato='ACUERDO') AND (EstadoContrato='ACTIVO' OR EstadoContrato='CONCILIADO') ";
                                $Consulta=$obCon->Query($sql);
                                while($DatosContratos=$obCon->FetchAssoc($Consulta)){
                                    $css->option("", "", "", $DatosContratos["NumeroContrato"], "", "");
                                        print($DatosContratos["NumeroContrato"]);
                                    $css->Coption();
                                }
                            $css->Cselect();
                            print("<br>");
                            
                            $css->select("CmbNumeroOtroSI", "form-control", "CmbNumeroOtroSI", "<strong>Número de Otro SI:</strong><br>", "", "", "");
                                $css->option("", "", "", "OTRO SI 001", "", "");
                                    print("OTRO SI 001");
                                $css->Coption();
                                $css->option("", "", "", "OTRO SI 002", "", "");
                                    print("OTRO SI 002");
                                $css->Coption();
                                $css->option("", "", "", "OTRO SI 003", "", "");
                                    print("OTRO SI 003");
                                $css->Coption();
                                $css->option("", "", "", "OTRO SI 004", "", "");
                                    print("OTRO SI 004");
                                $css->Coption();
                                $css->option("", "", "", "OTRO SI 005", "", "");
                                    print("OTRO SI 005");
                                $css->Coption();
                                $css->option("", "", "", "OTRO SI 006", "", "");
                                    print("OTRO SI 006");
                                $css->Coption();
                                $css->option("", "", "", "OTRO SI 007", "", "");
                                    print("OTRO SI 007");
                                $css->Coption();
                                $css->option("", "", "", "OTRO SI 008", "", "");
                                    print("OTRO SI 008");
                                $css->Coption();
                                
                            $css->Cselect();
                            
                        $css->CerrarDiv();
                        
                        $css->CrearDiv("DivSelectorTipoContrato", "", "", 1, 1);
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
                                $css->input("text", "TxtUPC", "form-control", "TxtUPC", "UPC", "", "UPC (Valor Percapita Por Usuario) ", "off", "", "");
                                $css->input("text", "TxtNumeroAfiliados", "form-control", "TxtNumeroAfiliados", "Número de Afiliados", "", "Número de Afiliados", "off", "", "");
                            $css->CerrarDiv();
                        
                        $css->CerrarDiv();
                        
                        
                        
                    print("</td>");
                    print("<td>");
                        $css->input("text", "NumeroContrato", "form-control", "NumeroContrato", "", "", "Número de Contrato", "off", "", "");
                        print("<br>");
                        $css->input("text", "ContratoEquivalente", "form-control", "ContratoEquivalente", "", $Contrato, "Contrato Equivalente", "off", "", "");
                    print("</td>");
                    print("<td>");                    
                        $css->input("date", "FechaInicial", "form-control", "FechaInicial", "", date("Y-m-d"), "Fecha Inicial", "off", "", "","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    print("</td>");                    
                    print("<td>");                    
                        $css->input("date", "FechaFinal", "form-control", "FechaInicial", "", date("Y-m-d"), "Fecha Inicial", "off", "", "","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    print("</td>");  
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td colspan=1>");  
                        print("<strong>Objeto del contrato:</strong><br>");
                        $css->textarea("TxtObjetoContrato", "form-control", "TxtObjetoContrato", "", "Objeto del contrato", "", "");
                        $css->Ctextarea();
                    print("</td>");  
                    
                    print("<td colspan=1>");  
                        print("<strong>Nivel de complejidad:</strong><br>");
                        $css->textarea("TxtNivelComplejidad", "form-control", "TxtNivelComplejidad", "", "Nivel de complejidad", "", "");
                        $css->Ctextarea();
                    print("</td>");
                    
                    print("<td colspan=1>");  
                        print("<strong>Finalidad del contrato:</strong><br>");
                        $css->textarea("FinalidadContrato", "form-control", "FinalidadContrato", "", "Finalidad del contrato", "", "");
                        $css->Ctextarea();
                    print("</td>");
                    
                    print("<td colspan=1>");  
                        print("<strong>Departamento Cobertura:</strong><br>");
                        $css->textarea("DepartamentoCobertura", "form-control", "DepartamentoCobertura", "", "Departamentos de Cobertura", "", "");
                        $css->Ctextarea();
                    print("</td>");
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        print("<strong>Valor del Contrato:</strong><br>");
                        $css->input("number", "ValorContrato", "form-control", "ValorContrato", "", "", "Valor del Contrato", "off", "", "");
                    print("</td>");
                    
                    print("<td>");
                        print("<strong>Tipo de Plan:</strong><br>");
                        $css->select("CmbTipoPlan", "form-control", "CmbTipoPlan", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione un Regimén");
                            $css->Coption();
                            
                        $Consulta=$obCon->ConsultarTabla("salud_regimen", "");
                        
                        while($DatosRegimen=$obCon->FetchAssoc($Consulta)){
                            $css->option("", "", "", $DatosRegimen["Regimen"], "", "");
                                print($DatosRegimen["Regimen"]);
                            $css->Coption();
                        }
                        
                            
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        print("<strong>Nivel de Prioridad:</strong><br>");
                        $css->select("CmbNivelPrioridad", "form-control", "CmbNivelPrioridad", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione una Prioridad");
                            $css->Coption();
                            $css->option("", "", "", "1", "", "");
                                print("1");
                            $css->Coption();
                            $css->option("", "", "", "2", "", "");
                                print("2");
                            $css->Coption();
                            /*
                            $css->option("", "", "", "3", "", "");
                                print("3");
                            $css->Coption();
                             * 
                             */
                                                    
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        print("<strong>Cobertura:</strong><br>");
                        $css->select("CmbCobertura", "form-control", "CmbCobertura", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione una opción");
                            $css->Coption();
                            $css->option("", "", "", "NACIONAL", "", "");
                                print("NACIONAL");
                            $css->Coption();
                            $css->option("", "", "", "DEPARTAMENTAL", "", "");
                                print("DEPARTAMENTAL");
                            $css->Coption();
                            $css->option("", "", "", "MUNICIPAL", "", "");
                                print("MUNICIPAL");
                            $css->Coption();
                                                    
                        $css->Cselect();
                    print("</td>");
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                    print("</td>");
                    print("<td colspan=2>");
                        $css->CrearBotonEvento("btnCrearContrato", "Crear Contrato", 1, "onclick", "CrearContratoEPS()", "naranja");
                    print("</td>");
                    print("<td>");
                    print("</td>");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
            
        break;  //Fin caso 4  
    }
    
          
}else{
    print("No se enviaron parametros");
}
?>