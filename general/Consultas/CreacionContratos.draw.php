<?php

@session_start();
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
        
        case 5:// Dibuja la interfaz para crear un contrato percapita
            $idContrato=$obCon->normalizar($_REQUEST["idContrato"]);                
            $DatosContratos=$obCon->DevuelveValores("contratos", "ID", $idContrato);
            
            $css->input("hidden", "idContratoPadre", "", "idContratoPadre", "", $idContrato, "", "", "", "");
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 110, "", "", "", "");
            $Mensaje="Crear un Valor Percapita para el Contrato ".$DatosContratos["Contrato"];
            $css->CrearTitulo("<strong>".$Mensaje."</strong>");
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>FECHA DE INICIO</strong>", 1,"C");
                    $css->ColTabla("<strong>FECHA FINAL</strong>", 1,"C");
                    $css->ColTabla("<strong>MUNICIPIO</strong>", 1,"C");
                    $css->ColTabla("<strong>PORCENTAJE POBLACIONAL</strong>", 1,"C");
                    $css->ColTabla("<strong>VALOR PERCAPITA POR DÍA</strong>", 1,"C");                    
                $css->CierraFilaTabla();
                
                    print("<td>");
                        $css->input("date", "FechaInicialPercapita", "form-control", "FechaInicialPercapita", "", $DatosContratos["FechaInicioContrato"], "Fecha Inicial", "off", "", "","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    print("</td>");
                    
                    print("<td>");
                        $css->input("date", "FechaFinalPercapita", "form-control", "FechaFinalPercapita", "", $DatosContratos["FechaFinalContrato"], "Fecha Final", "off", "", "","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    print("</td>");
                    
                    print("<td>");
                        $css->select("CmbMunicipioPercapita", "form-control", "CmbMunicipioPercapita", "", "", "", "");
                            $css->option("", "form-control", "", "", "", "");
                                print("Seleccione un municipio");
                            $css->Coption();
                        $css->Cselect();
                        
                    print("</td>");
                    
                    print("<td>");
                        $css->input("text", "TxtPorcentajePercapita", "form-control", "TxtPorcentajePercapita", "", "", "Porcentaje Poblacional", "off", "", "");
                    print("</td>");
                    
                    print("<td>");
                        $css->input("text", "TxtValorPercapita", "form-control", "TxtValorPercapita", "", "", "Valor Percapita", "off", "", "");
                    print("</td>");
                
            $css->CerrarTabla();
            
        break;//fin caso 5
    
        case 6:// Dibuja la interfaz para adjuntar un archivo a un contrato
            $contrato_id=$obCon->normalizar($_REQUEST["contrato_id"]);                
            $DatosContratos=$obCon->DevuelveValores("contratos", "ID", $contrato_id);
            
            $css->input("hidden", "idFormulario", "", "idFormulario", "", '111', "", "", "", "");
           
            $Mensaje="Adjuntos para el contrato: ".$DatosContratos["Contrato"];
            $css->CrearTitulo("<strong>".$Mensaje."</strong>");
            
            $css->CrearDiv("", "row", "center", 1, 1);
                $css->CrearDiv("", "col-md-6", "center", 1, 1);
                $css->CrearTitulo("<strong>Subir adjuntos al contrato</strong>", "verde");
                print('<div class="panel">
                            
                            <div class="panel-body">
                                <form data-contrato_id="'.$contrato_id.'" action="/" class="dropzone dz-clickable" id="contrato_adjuntos"><div class="dz-default dz-message"><span><i class="icon-plus"></i>Arrastre archivos aquí o de click para subir.<br> Suba cualquier tipo de archivos.</span></div></form>
                            </div>
                        </div>
                    ');
                $css->Cdiv();
                $css->CrearDiv("div_adjuntos_contrato", "col-md-6", "center", 1, 1);
                    
                $css->CerrarDiv();
                
            $css->Cdiv();
            
        break;  //Fin caso 6
    
        case 7: //Dibuja los adjuntos en un proyecto
            
            $contrato_id=$obCon->normalizar($_REQUEST["contrato_id"]);                
            $DatosContratos=$obCon->DevuelveValores("contratos", "ID", $contrato_id);
            
            $css->CrearTitulo("Adjuntos de este contrato");
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Nombre de Archivo", 1);
                    
                    $css->ColTabla("Eliminar", 1);
                    
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*
                        FROM contratos_adjuntos t1 
                        WHERE contrato_id='$contrato_id' 
                            ";
                $Consulta=$obCon->Query($sql);
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["ID"];
                    $Nombre=$DatosConsulta["NombreArchivo"];
                    $css->FilaTabla(14);
                
                        $css->ColTabla($idItem, 1);
                       
                        print('<td style="text-align:center;color:blue;font-size:18px;">');
                            $Ruta= "../../".str_replace("../", "", $DatosConsulta["Ruta"]);
                            print('<a href="'.$Ruta.'" target="blank">'.$Nombre.' <li class="fa fa-paperclip"></li></a>');
                        print('</td>');
                        
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItemContrato(`1`,`$idItem`,`$contrato_id`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                          
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
            
        break; //Fin caso 7
    }
    
          
}else{
    print("No se enviaron parametros");
}
?>