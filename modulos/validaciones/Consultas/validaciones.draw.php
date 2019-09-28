<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../clases/validaciones.class.php");
include_once("../../../constructores/paginas_constructor.php");
include_once '../../../general/clases/numeros_letras.class.php';
//include_once '../clases/validacionesWorker.class.php';
//include_once '../clases/validacionesWorker.class.php';


if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new ValidacionesEPS($idUser);
    $obNumLetra=new numeros_letras();
    switch ($_REQUEST["Accion"]) {
        case 1: //Dibuja las facturas que tiene la ips pero no la EPS
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Condicional=" ";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional=" WHERE NumeroContrato like '$Busqueda%' or NumeroFactura like '%$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $statement=" $db.`vista_facturas_sr_ips` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 50;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorTotalpagar) AS Total FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $Total=$row['Total'];
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                    print("<td style='text-align:center'>");
                        print("<strong>Registros:</strong> <h4 style=color:green>". number_format($ResultadosTotales)."</h4>");
                    print("</td>");
                    print("<td colspan=4 style='text-align:center'>");
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    print("<td>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_facturas_sr_ips','')", "verde", "");
                    print("</td>");
                    print("<td colspan='1' style='text-align:center'>");
                        print("<strong>Subir Actualizaciones de Facturas Masivas</strong>");
                        print('<div class="input-group">');
                            $css->input("file", "UpActualizaciones", "form-control", "UpActualizaciones", "Actualización de Facturas Por Excel", "Subir Actualizaciónes masivas", "Subir Actualizaciónes masivas", "off", "", "");
                        print('<span id="BtnSubirActualizacionesMasivas" class="input-group-addon" style="cursor:pointer;background-color:orange" onclick="ConfirmarCarga()"><i class="fa fa-fw fa-upload" style=color:white></i></span>
                                </div>');
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
                            print('<span class="input-group-addon" onclick=CambiePaginaFacturasIPS('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaFacturasIPS();";
                            $css->select("CmbPageFacturasIPS", "form-control", "CmbPageFacturasIPS", "", "", $FuncionJS, "");
                            
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
                            print('<span class="input-group-addon" onclick=CambiePaginaFacturasIPS('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("<div>");
                            print("</td>");
                            
                            
                           $css->CierraFilaTabla(); 
                        }
                      
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Contrato</strong>", 1);
                    $css->ColTabla("<strong>Factura</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Factura</strong>", 1);
                    $css->ColTabla("<strong>Radicado</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Radicado</strong>", 1);
                    $css->ColTabla("<strong>Valor</strong>", 1);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $NumeroFactura=$DatosFactura["NumeroFactura"];
                        $css->ColTabla($DatosFactura["NumeroContrato"], 1);
                        print("<td>");
                           
                            print('<span id="BtnEditar_'.$idItem.'"  onclick="EditarFactura(`'.$NumeroFactura.'`);" style=cursor:pointer> '.$DatosFactura["NumeroFactura"].' <i class="fa fa-fw fa-edit"></i></span>');
                        print("</td>");
                        
                        
                        $css->ColTabla($DatosFactura["FechaFactura"], 1);
                        $css->ColTabla($DatosFactura["NumeroRadicado"], 1);
                        $css->ColTabla($DatosFactura["FechaRadicado"], 1);
                        $css->ColTabla(number_format($DatosFactura["ValorTotalpagar"]), 1,'R');
                        print("<td style='text-align:center'>");
                            print('<a id="BtnVer_'.$idItem.'" href="#" onclick="DibujeFactura('.$idItem.');"><i class="fa fa-fw fa-eye"></i></a>');
                        print("</td>");
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 1
        
        case 2: //Dibuja las facturas que tiene la eps pero no la IPS
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Condicional=" ";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional=" WHERE NumeroContrato like '$Busqueda%' or NumeroFactura like '%$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $statement=" $db.`vista_facturas_sr_eps` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 50;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorOriginal) AS Total FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $Total=$row['Total'];
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
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    
                    print("<td colspan='2' style='text-align:center'>");
                        $st1= urlencode($st_reporte);
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_facturas_sr_eps','')", "verde", "");
                    print("</td>");
                //$css->CierraFilaTabla();
                
                $st= urlencode($st_reporte);
                    if($ResultadosTotales>$limit){

                        //$css->FilaTabla(14);
                            
                            $TotalPaginas= ceil($ResultadosTotales/$limit);
                            print("<td  style=text-align:center>");
                            //print("<strong>Página: </strong>");
                            
                            print('<div class="input-group" style=width:180px>');
                            if($NumPage>1){
                                $NumPage1=$NumPage-1;
                            print('<span class="input-group-addon" onclick=CambiePaginaFacturasEPS('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaFacturasEPS();";
                            $css->select("CmbPageFacturasEPS", "form-control", "CmbPageFacturasEPS", "", "", $FuncionJS, "");
                            
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
                            print('<span class="input-group-addon" onclick=CambiePaginaFacturasEPS('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("<div>");
                            print("</td>");
                            
                            
                           $css->CierraFilaTabla(); 
                        }
                      
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Contrato</strong>", 1);
                    $css->ColTabla("<strong>Factura</strong>", 1);
                    
                    $css->ColTabla("<strong>Departamento Radicación</strong>", 1);
                    
                    $css->ColTabla("<strong>Valor</strong>", 1);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $css->ColTabla($DatosFactura["NumeroContrato"], 1);
                        $css->ColTabla($DatosFactura["NumeroFactura"], 1);
                        
                        
                        $css->ColTabla($DatosFactura["DepartamentoRadicacion"], 1);
                        $css->ColTabla(number_format($DatosFactura["ValorOriginal"]), 1,'R');
                        print("<td style='text-align:center'>");
                            print('<a id="BtnVer_'.$idItem.'" href="#" onclick="DibujeFactura('.$idItem.');"><i class="fa fa-fw fa-eye"></i></a>');
                        print("</td>");
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 2 
        
        case 3: //Dibuja el cruce de la cartera
            $TipoNegociacion=$obCon->normalizar($_REQUEST["CmbTipoNegociacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            
            
            $css->CrearDiv("DivTotalesCruce", "", "left", 1, 1);

            $css->CerrarDiv();
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Condicional=" WHERE TipoNegociacion='$TipoNegociacion' ";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional=" AND NumeroContrato like '$Busqueda%' or NumeroFactura like '%$Busqueda%' or NumeroRadicado like '%$Busqueda%' ";
                }
                
            }
            $css->input("hidden", "TxtCondicional", "", "TxtCondicional", "", $Condicional, "", "", "", "");
            //$css->CrearInputText("TxtCondicional", "", "", $Condicional, "", "", "", "", "", "", "", "");
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $sql="SELECT COLUMN_NAME 
                    FROM information_schema.COLUMNS 
                    WHERE 
                        TABLE_SCHEMA = '$db' 
                    AND TABLE_NAME = 'hoja_de_trabajo' 
                    AND COLUMN_NAME = 'TipoNegociacion' ";
            $Verificacion=$obCon->FetchAssoc($obCon->Query($sql));
            if($Verificacion["COLUMN_NAME"]==""){
                $css->CrearBotonEvento("BtnConstruirHojaTrabajo", "Construir Hoja de Trabajo", 1, "onclick", "ObtenerNumeroRegistrosACopiarEnHoja()", "azulclaro", "");
                exit();
                
            }
            if($TipoNegociacion=='CAPITA'){
                $css->CrearTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>CONTRATOS DISPONIBLES EN ESTE CRUCE:</strong>", 3,"C");
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>CONTRATOS</strong>", 1,"C");
                        $css->ColTabla("<strong>DATOS GENERALES</strong>", 1,"C");
                        $css->ColTabla("<strong>VALORES PERCAPITA POR MUNICIPIO</strong>", 1,"C");
                    $css->CierraFilaTabla();
               
                $sql="SELECT DISTINCT NumeroContrato FROM $db.hoja_de_trabajo WHERE TipoNegociacion='CAPITA' ";
                
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
            
            
                 
               
            }
            $statement=" $db.`hoja_de_trabajo` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 10;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`
                     FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            
            
            $css->CrearTabla();            
            $st1= urlencode($st_reporte);
                $css->FilaTabla(16);
                 print("<td colspan='1' style='text-align:center'>");
                
                
                    $st= urlencode($st_reporte);
                    if($ResultadosTotales>$limit){
                        print("<strong>Paginador:</strong>");
                        
                            
                            $TotalPaginas= ceil($ResultadosTotales/$limit);
                          
                            print('<br><div class="input-group" style=width:180px>');
                            if($NumPage>1){
                                $NumPage1=$NumPage-1;
                            print('<span class="input-group-addon" onclick=CambiePaginaCruce('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaCruce();";
                            $css->select("CmbPageCruce", "form-control", "CmbPageCruce", "", "", $FuncionJS, "");
                            
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
                            print('<span class="input-group-addon" onclick=CambiePaginaCruce('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("</div>");
                        }
                    print("</td>");
                    print("<td style='text-align:center;'>");
                    
                        print("<strong>Registros:</strong> <h4 style=color:green>". number_format($ResultadosTotales)."</h4>");
                    print("</td>");
                    print("<td colspan='1' style='text-align:center'>");                        
                        $css->CrearBotonEvento("BtnConstruirHojaTrabajo", "Construir Hoja de Trabajo", 1, "onclick", "ObtenerNumeroRegistrosACopiarEnHoja()", "azulclaro", "");
                    print("</td>"); 
                   
                    print("<td colspan='1' style='text-align:center'>");                        
                        $css->CrearBotonEvento("BtnCalcularDiferencias", "Calcular Diferencias", 1, "onclick", "CalcularDiferenciasVaridas()", "naranja", "");
                    print("</td>"); 
                    /*
                    print("<td colspan='1' style='text-align:center'>");                        
                        $css->CrearBotonEvento("BtnActualizarCruce", "Actualizar Hoja de Trabajo", 1, "onclick", "ActualizarHojaDeTrabajo()", "azulclaro", "");
                    print("</td>"); 
                     */
                    print("<td colspan='1' style='text-align:center'>");
                        $css->CrearBotonEvento("BtnCargarTotales", "Ver Totales", 1, "onclick", "DibujaTotalesCruce()", "azul", "");
                    print("</td>"); 
                    print("<td colspan='1' style='text-align:center'>");    
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarHojaDeTrabajo('$db','hoja_de_trabajo','')", "verde", "");
                    print("</td>"); 
                    print("<td colspan='1' style='text-align:center'>");
                        $css->CrearBotonEvento("BtnExportarReporteIps", "Exportar-Reporte_Ips", 1, "onclick", "ExportarHojaDeTrabajo('$db','vista_reporte_ips','')", "verde", "");
                    print("</td>"); 
                                       
                    print("<td style='text-align:left;' colspan=12>");
                        
                        
                    print("</td>");
                    /*
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Total Según EPS:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Total Según IPS:</strong> <h4 style=color:red>". number_format($TotalIPS)."</h4>");
                    print("</td>");
                    
                    print("<td colspan=1 style='text-align:center'>");
                        
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`1`,`21`)", "style=cursor:pointer;");

                              print("<strong>Pendientes Radicados:</strong> <h4 style=color:red>". number_format($TotalPendientesRadicados)."</h4>");
                           $css->CerrarDiv();
                         
                        
                    print("</td>");
                    
                    print("<td colspan=1 style='text-align:center'>");
                        $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`1`,`22`)", "style=cursor:pointer;");

                            print("<strong>Pendientes Devoluciones:</strong> <h4 style=color:red>". number_format($TotalPendientesDevoluciones)."</h4>");
                        $css->CerrarDiv();
                        
                    print("</td>");
                    
                    
                    print("<td colspan=1 style='text-align:center'>");
                        $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`1`,`23`)", "style=cursor:pointer;");

                            print("<strong>Pendientes Copagos:</strong> <h4 style=color:red>". number_format($TotalPendientesCopagos)."</h4>");
                        $css->CerrarDiv();
                        
                    print("</td>");
                    
                    print("<td colspan=1 style='text-align:center'>");
                        $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`1`,`24`)", "style=cursor:pointer;");

                            print("<strong>Pendientes Notas Crédito:</strong> <h4 style=color:red>". number_format($TotalPendientesNotas)."</h4>");
                        $css->CerrarDiv();
                        
                    print("</td>");
                    //$sql="SELECT SUM(ValorImpuestosCalculados) AS TotalRetencionesDevueltas FROM $db.vista_facturas_sr_eps_2 WHERE Saldo<0";
                    //$Consulta2=$obCon->Query($sql);
                    //$DatosSaldosDevoluciones=$obCon->FetchAssoc($Consulta2);
                    //$TotalRetencionesDevolucionesNoRelacionadas=$DatosSaldosDevoluciones["TotalRetencionesDevueltas"];
                    $TotalRetencionesDevolucionesNoRelacionadas=0;
                    //print("<td colspan=1 style='text-align:center'>");
                     //   print("<strong>Retenciones Pagadas en Devoluciones:</strong> <h4 style=color:red>". number_format($TotalRetencionesDevolucionesNoRelacionadas)."</h4>");
                    //print("</td>");
                    
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Posible Valor a Liquidar:</strong> <h4 style=color:red>". number_format($Total-$TotalPendientesNotas-$TotalPendientesCopagos-$TotalPendientesDevoluciones-$TotalPendientesRadicados-$TotalRetencionesDevolucionesNoRelacionadas)."</h4>");
                    print("</td>");
                    
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Facturas Conciliadas:</strong> <h4 style=color:red>". number_format($NumeroConciliaciones)."</h4>");
                    print("</td>");
                    
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Total Conciliado:</strong> <h4 style=color:red>". number_format($TotalConciliaciones)."</h4>");
                    print("</td>");
                    
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Total Conciliado - Pendientes:</strong> <h4 style=color:red>". number_format($TotalConciliaciones-$TotalPendientesNotas-$TotalPendientesCopagos-$TotalPendientesDevoluciones-$TotalPendientesRadicados-$TotalRetencionesDevolucionesNoRelacionadas)."</h4>");
                    print("</td>");
                    
                    
                       */     
                            
                           $css->CierraFilaTabla(); 
                      
                      
                
                $css->FilaTabla(16);
                    print("<td>");
                        print("<strong>Acciones</strong><br>");
                        $css->CrearBotonEvento("btnOpcionesMasivas", "Cargas Masivas", 1, "onclick", "AbreOpcionesMasivas()", "naranja", "");
                    print("</td>");
                    
                    $css->ColTabla("<strong>Contrato</strong>", 1);
                    $css->ColTabla("<strong>Factura</strong>", 1);
                    $css->ColTabla("<strong>Saldo IPS Menor?</strong>", 1);
                    $css->ColTabla("<strong>Conciliaciones Pendientes?</strong>", 1);
                    $css->ColTabla("<strong>Conciliada?</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Conciliación</strong>", 1);
                    $css->ColTabla("<strong>Mes de Servicio</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Factura</strong>", 1);
                    $css->ColTabla("<strong>Radicado</strong>", 1);
                    $css->ColTabla("<strong>Pendientes</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Radicado</strong>", 1);
                    $css->ColTabla("<strong>Departamento de Radicacion</strong>", 1);
                    $css->ColTabla("<strong>Valor</strong>", 1);
                    $css->ColTabla("<strong>Impuestos Conciliaciones</strong>", 1);
                    $css->ColTabla("<strong>Impuestos Segun Retencion</strong>", 1);
                    $css->ColTabla("<strong>Valor Menos Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Total Pagos</strong>", 1);
                    $css->ColTabla("<strong>Capitalización</strong>", 1);
                    $css->ColTabla("<strong>Total Anticipos</strong>", 1);
                    $css->ColTabla("<strong>Total Copagos</strong>", 1);
                    $css->ColTabla("<strong>Total Devoluciones</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa Inicial</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa A Favor</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa en Contra</strong>", 1);
                    $css->ColTabla("<strong>Glosa por Conciliar</strong>", 1);
                    $css->ColTabla("<strong>Otros Descuentos</strong>", 1);
                    $css->ColTabla("<strong>Descuento PGP</strong>", 1);
                    $css->ColTabla("<strong>Cartera por Edades</strong>", 1);
                    $css->ColTabla("<strong>Saldo Según EPS</strong>", 1);
                    $css->ColTabla("<strong>Saldo Según IPS</strong>", 1);
                    $css->ColTabla("<strong>Diferencia</strong>", 1);
                    $css->ColTabla("<strong>Conciliado a Favor EPS</strong>", 1);
                    $css->ColTabla("<strong>Conciliado a Favor IPS</strong>", 1);
                    $css->ColTabla("<strong>Total A Pagar</strong>", 1);
                    
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $NumeroFactura=$DatosFactura["NumeroFactura"];
                        $NumeroRadicado=$DatosFactura["NumeroRadicado"];
                        if($DatosFactura["Estado"]==1){
                            $EstadoConciliado="SI";
                        }else{
                            $EstadoConciliado="NO";
                        }
                        print("<td style='text-align:center'>");
                            print('<a id="BtnVer_'.$idItem.'" href="#" onclick="VerHistorialFactura(`'.$NumeroFactura.'`,`14`);"><i class="fa fa-fw fa-eye"></i></a>');
                            print('&nbsp;&nbsp;&nbsp;<a id="BtnConciliar_'.$idItem.'" href="#" onclick="VerHistorialFactura(`'.$NumeroFactura.'`,`15`);"><i class="fa fa-money"></i></a>');
                            print('&nbsp;&nbsp;&nbsp;<a id="BtnConciliar_'.$idItem.'" href="#" onclick="VerHistorialFactura(`'.$NumeroFactura.'`,`16`);"><i class="fa fa-map-o"></i></a>');
                        print("</td>");
                        
                        $css->ColTabla($DatosFactura["NumeroContrato"], 1);
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerConsolidadoFactura(`$NumeroFactura`,`11`)", "style=cursor:pointer;");
                                print(($DatosFactura["NumeroFactura"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        $css->ColTabla($DatosFactura["ValorIPSMenor"], 1);
                        $css->ColTabla($DatosFactura["ConciliacionesPendientes"], 1);
                        $css->ColTabla($EstadoConciliado, 1);
                        $css->ColTabla($DatosFactura["FechaConciliacion"], 1);
                        $css->ColTabla($DatosFactura["MesServicio"], 1);
                        $css->ColTabla($DatosFactura["FechaFactura"], 1);
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`$NumeroRadicado`,`13`)", "style=cursor:pointer;");

                               print(($DatosFactura["NumeroRadicado"]));
                           $css->CerrarDiv();
                         print("</td>"); 
                        
                        $css->ColTabla($DatosFactura["Pendientes"], 1);
                        $css->ColTabla($DatosFactura["FechaRadicado"], 1);
                        $css->ColTabla($DatosFactura["DepartamentoRadicacion"], 1);
                        $css->ColTabla(number_format($DatosFactura["ValorDocumento"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["Impuestos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ImpuestosSegunASMET"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorMenosImpuestos"]), 1,'R');
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`$NumeroFactura`,`4`)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalPagos"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        $css->ColTabla(number_format($DatosFactura["Capitalizacion"]), 1,'R');
                        
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`$NumeroFactura`,`5`)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalAnticipos"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`$NumeroFactura`,`9`)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalCopagos"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`".$NumeroFactura."`,`10`)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalDevoluciones"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`$NumeroFactura`,6)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalGlosaInicial"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`$NumeroFactura`,6)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalGlosaFavor"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura('$NumeroFactura',6)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalGlosaContra"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        $css->ColTabla(number_format($DatosFactura["GlosaXConciliar"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["OtrosDescuentos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["DescuentoPGP"]), 1,'R');
                        print("<td style=text-align:center;font-size:18px>");
                            print(number_format($DatosFactura["CarteraXEdades"]));
                        print("</td>");
                        
                        print("<td style=text-align:center;font-size:18px>");
                           print(number_format($DatosFactura["ValorSegunEPS"]));
                           $idBoton="btnConciliarXEPS_$idItem";
                           //$css->CrearBotonEvento("btnConciliarXEPS_$idItem", "Conciliar Según Valor EPS", 1, "onclick", "ConciliarFactura(`$idBoton`,`$NumeroFactura`,`1`)", "verde", "");
                        print("</td>");
                        print("<td style=text-align:center;font-size:18px> ");
                           print(number_format($DatosFactura["ValorSegunIPS"]));
                           $idBoton="btnConciliarXIPS_$idItem";
                           //$css->CrearBotonEvento("btnConciliarXIPS_$idItem", "Conciliar Según Valor IPS", 1, "onclick", "ConciliarFactura(`$idBoton`,`$NumeroFactura`,`2`)", "naranja", "");
                        print("</td>");
                        
                        $css->ColTabla(number_format($DatosFactura["Diferencia"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ConciliacionesAFavorEPS"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ConciliacionesAFavorIPS"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalAPagar"]), 1,'R');
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 3
        /*
        case 4://Muestra los pagos de una factura
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Pagos Realizados a la Factura No. $NumeroFactura</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Fecha de Pago</strong>", 1);
                    $css->ColTabla("<strong>Numero de Pago</strong>", 1);
                    $css->ColTabla("<strong>Tipo de Operacion</strong>", 1);
                    $css->ColTabla("<strong>Valor Bruto A Pagar</strong>", 1);
                    $css->ColTabla("<strong>Descuento</strong>", 1);
                    $css->ColTabla("<strong>IVA</strong>", 1);
                    $css->ColTabla("<strong>ReteFuente</strong>", 1);
                    $css->ColTabla("<strong>ReteIVA</strong>", 1);
                    $css->ColTabla("<strong>ReteICA</strong>", 1);
                    $css->ColTabla("<strong>Otras Retenciones</strong>", 1);
                    $css->ColTabla("<strong>Anticipos</strong>", 1);
                    $css->ColTabla("<strong>Valor Transferido</strong>", 1);
                    $css->ColTabla("<strong>Proceso</strong>", 1);
                    $css->ColTabla("<strong>Banco</strong>", 1);
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.pagos_asmet WHERE NumeroFactura='$NumeroFactura'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla($DatosPagos["FechaPagoFactura"], 1);
                        $css->ColTabla($DatosPagos["NumeroPago"], 1);
                        $css->ColTabla($DatosPagos["TipoOperacion"], 1);
                        $css->ColTabla(number_format($DatosPagos["ValorBrutoPagar"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorDescuento"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorIva"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorRetefuente"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorReteiva"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorReteica"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorOtrasRetenciones"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorAnticipos"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorTranferido"]), 1,'R');
                        $css->ColTabla($DatosPagos["Proceso"]." ".$DatosPagos["DescripcionProceso"], 1);
                        $css->ColTabla($DatosPagos["Banco"]." ".$DatosPagos["Cuenta"], 1);
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;//Fin caso 4
        
         * 
         */
        case 4://Muestra los pagos temporalmente desde cxp de una factura
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Pagos Realizados a la Factura No. $NumeroFactura</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Estado CXP</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Pago</strong>", 1);
                    $css->ColTabla("<strong>Numero de Pago</strong>", 1);
                    $css->ColTabla("<strong>Numero de Autorización</strong>", 1);
                    $css->ColTabla("<strong>Tipo de Operacion</strong>", 1);                    
                    $css->ColTabla("<strong>Valor Transferido</strong>", 1);                    
                    $css->ColTabla("<strong>Cuenta Bancaria</strong>", 1);
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.notas_db_cr_2 WHERE NumeroFactura='$NumeroFactura' AND ValorPago<>'0'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla($DatosPagos["C13"], 1);
                        $css->ColTabla($DatosPagos["FechaNumero2"], 1);
                        $css->ColTabla($DatosPagos["NumeroOrdenPago"], 1);                        
                        $css->ColTabla($DatosPagos["NumeroAutorizacion"], 1);
                        $css->ColTabla($DatosPagos["TipoOperacion2"], 1);
                        $css->ColTabla(number_format($DatosPagos["ValorPago"]), 1,'R');
                        $css->ColTabla($DatosPagos["CuentaBancaria"], 1);
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;//Fin caso 4
        
        case 5://Muestra los anticipos de una factura
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Anticipos Realizados a la Factura No. $NumeroFactura</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Fecha</strong>", 1);
                    $css->ColTabla("<strong>Mes de Servicio</strong>", 1);
                    $css->ColTabla("<strong>Referencia</strong>", 1);
                    $css->ColTabla("<strong>Numero de Anticipo</strong>", 1);
                   
                    
                    $css->ColTabla("<strong>Valor Anticipado</strong>", 1);
                    
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.anticipos2 WHERE NumeroFactura='$NumeroFactura'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla($DatosPagos["FechaAnticipo"], 1);
                        $css->ColTabla($DatosPagos["MesServicio"], 1);
                        $css->ColTabla($DatosPagos["DescripcionEgreso"], 1);
                        $css->ColTabla($DatosPagos["NumeroAnticipo"], 1);
                        
                        $css->ColTabla(number_format($DatosPagos["ValorAnticipado"]), 1,'R');
                        
                        
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;//Fin caso 5
        
        case 6://Muestra las glosas
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Glosas Realizadas a la Factura No. $NumeroFactura</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Sede</strong>", 1);
                    $css->ColTabla("<strong>Fecha del Radicado</strong>", 1);
                    $css->ColTabla("<strong>Número del Radicado</strong>", 1);
                    
                    $css->ColTabla("<strong>Total del Documento</strong>", 1);
                    $css->ColTabla("<strong>Valor Total Glosa</strong>", 1);
                    $css->ColTabla("<strong>Valor Glosa Favor</strong>", 1);
                    $css->ColTabla("<strong>Valor Glosa Contra</strong>", 1);
                    $css->ColTabla("<strong>Valor Pendiente Resolver</strong>", 1);
                    
                    
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.glosaseps_asmet WHERE NumeroFactura='$NumeroFactura'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla(($DatosPagos["Sede"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaRadicado"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroRadicado"]), 1,'L');
                        $css->ColTabla(number_format($DatosPagos["ValorFactura"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorTotalGlosa"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorGlosaFavor"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorGlosaContra"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorPendienteResolver"]), 1,'R');
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;//Fin caso 6
        
        
        case 7: //Dibuja las facturas que tiene la eps pero no la IPS
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Condicional=" ";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional=" WHERE NumeroContrato like '$Busqueda%' or NumeroFactura like '%$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $statement=" $db.`vista_facturas_pagadas_no_relacionadas` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 50;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorTranferido) AS Total FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $Total=$row['Total'];
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                    print("<td style='text-align:center'>");
                        print("<strong>Registros:</strong> <h4 style=color:green>". number_format($ResultadosTotales)."</h4>");
                    print("</td>");
                    print("<td colspan=6 style='text-align:center'>");
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    
                   print("<td>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_facturas_pagadas_no_relacionadas','')", "verde", "");
				   print("</td>");
					
					

                //$css->CierraFilaTabla();
                
                $st= urlencode($st_reporte);
                    if($ResultadosTotales>$limit){

                        //$css->FilaTabla(14);
                            
                            $TotalPaginas= ceil($ResultadosTotales/$limit);
                            print("<td  style=text-align:center>");
                            //print("<strong>Página: </strong>");
                            
                            print('<div class="input-group" style=width:180px>');
                            if($NumPage>1){
                                $NumPage1=$NumPage-1;
                            print('<span class="input-group-addon" onclick=CambiePaginaPagadasSR('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaPagadasSR();";
                            $css->select("CmbPagePagasSR", "form-control", "CmbPagePagasSR", "", "", $FuncionJS, "");
                            
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
                            print('<span class="input-group-addon" onclick=CambiePaginaPagadasSR('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("<div>");
                            print("</td>");
                            
                            
                           $css->CierraFilaTabla(); 
                        }
                      
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Proceso</strong>", 1);
                    $css->ColTabla("<strong>Fecha Pago Factura</strong>", 1);                    
                    $css->ColTabla("<strong>Cuenta</strong>", 1);                    
                    $css->ColTabla("<strong>Número Pago</strong>", 1);
                    $css->ColTabla("<strong>Tipo de Operacion</strong>", 1);
                    $css->ColTabla("<strong>Número Factura</strong>", 1);
                    $css->ColTabla("<strong>Valor Bruto a Pagar</strong>", 1);                    
                    $css->ColTabla("<strong>Valor Tranferido</strong>", 1);
                    $css->ColTabla("<strong>Regional</strong>", 1);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $css->ColTabla($DatosFactura["Proceso"]." ".$DatosFactura["DescripcionProceso"], 1);
                        $css->ColTabla($DatosFactura["FechaPagoFactura"], 1);
                        
                        
                        $css->ColTabla($DatosFactura["Banco"]." ".$DatosFactura["Cuenta"], 1);
                        $css->ColTabla($DatosFactura["NumeroPago"], 1);
                        $css->ColTabla($DatosFactura["TipoOperacion"], 1);
                        $css->ColTabla($DatosFactura["NumeroFactura"], 1);
                        
                        $css->ColTabla(number_format($DatosFactura["ValorBrutoPagar"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorTranferido"]), 1,'R');
                        $css->ColTabla($DatosFactura["Regional"], 1);
                        print("<td style='text-align:center'>");
                            print('<a id="BtnVer_'.$idItem.'" href="#" onclick="DibujeFactura('.$idItem.');"><i class="fa fa-fw fa-eye"></i></a>');
                        print("</td>");
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 7
        
        case 8://Dibuje el formulario para editar una factura de la ips
            
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            
            $css->CrearTabla();
            
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Factura a Editar</strong>", 1);
                    $css->ColTabla("<strong>Factura Nueva</strong>", 1);
                    $css->ColTabla("<strong>Observaciones</strong>", 1);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "TxtNumeroFacturaEdit", "form-control", "TxtNumeroFacturaEdit", "$NumeroFactura", "$NumeroFactura", "Factura a Editar", "off", "", "");
                    print("</td>");
                    
                    print("<td>");
                        $css->input("text", "TxtFacturaNueva", "form-control", "TxtFacturaNueva", "", "", "Factura Nueva", "off", "", "");
                    print("</td>");
                    
                    print("<td>");
                        $css->textarea("TxtObservacionesEdicioFactura", "form-control", "TxtObservacionesEdicioFactura", "", "Observaciones", "", "");
                        $css->Ctextarea();
                    print("</td>");
                    
                    print("<td>");
                        $css->CrearBotonEvento("BtnEjecutar", "Actualizar", 1, "onclick", "EnviarFacturaEditar()", "rojo", "");
             
                    print("</td>");
                $css->CierraFilaTabla();
            $css->CerrarTabla();
            
            
            
            
            
        break; //Fin caso 8
    
        case 9://Muestra los copagos realizados a una factura
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Copagos y Otros Realizados a la Factura No. $NumeroFactura</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Estado CXP</strong>", 1);
                    $css->ColTabla("<strong>Tipo de Operación</strong>", 1);
                    $css->ColTabla("<strong>Número de Transacción</strong>", 1);
                    $css->ColTabla("<strong>Fecha</strong>", 1);                    
                    $css->ColTabla("<strong>Sucursal</strong>", 1);
                    $css->ColTabla("<strong>Número de Referencia</strong>", 1);
                    $css->ColTabla("<strong>Valor del Copago</strong>", 1);
                    
                    
                    
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.notas_db_cr_2 WHERE NumeroFactura='$NumeroFactura' AND "
                        . "(TipoOperacion='2258' or TipoOperacion='2225' or TipoOperacion='2214' or TipoOperacion='2254' or TipoOperacion='2039' or TipoOperacion='2020' or TipoOperacion='2601' or TipoOperacion='2218' or TipoOperacion='2402' or TipoOperacion='2500')";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla(($DatosPagos["C13"]), 1,'C');
                        $css->ColTabla(($DatosPagos["TipoOperacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroTransaccion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaTransaccion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NombreSucursal"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NombreOperacion"]), 1,'L');
                        $css->ColTabla(number_format($DatosPagos["ValorTotal"]), 1,'R');
                        
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;//Fin caso 9
        
        case 10://Muestra los devoluciones realizados a una factura
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Devoluciones Realizadas a la Factura No. $NumeroFactura</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Estado CXP</strong>", 1);
                    $css->ColTabla("<strong>Tipo de Operación</strong>", 1);
                    $css->ColTabla("<strong>Número de Transacción</strong>", 1);
                    $css->ColTabla("<strong>Fecha</strong>", 1);                    
                    $css->ColTabla("<strong>Sucursal</strong>", 1);
                    $css->ColTabla("<strong>Número de Referencia</strong>", 1);
                    $css->ColTabla("<strong>Valor de la Devolución</strong>", 1);
                    
                    
                    
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.notas_db_cr_2 WHERE NumeroFactura='$NumeroFactura' AND TipoOperacion='2259'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla(($DatosPagos["C13"]), 1,'C');
                        $css->ColTabla(($DatosPagos["TipoOperacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroTransaccion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaTransaccion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NombreSucursal"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NombreOperacion"]), 1,'L');
                        $css->ColTabla(number_format($DatosPagos["ValorTotal"]), 1,'R');
                        
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;//Fin caso 10
        
        case 11://Muestra el consolidado de movimientos de una factura
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearBotonEvento("BtnExportar", "Exportar", 1, "onclick", "ExportarTablaToExcel('TblResumenFactura')", "verde", "");
            $css->CrearTabla('TblResumenFactura');
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Consolidado de la Factura No. $NumeroFactura</strong>", 14,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>INFORMACIÓN GENERAL</strong>", 14,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Fecha</strong>", 1);
                    $css->ColTabla("<strong>Contrato</strong>", 1);
                                   
                    $css->ColTabla("<strong>Fecha Radicado</strong>", 1);
                    $css->ColTabla("<strong>Número del radicado</strong>", 1);
                    $css->ColTabla("<strong>Valor Factura</strong>", 1);
                    $css->ColTabla("<strong>Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Valor Menos Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Total a Pagar</strong>", 1);
                $css->CierraFilaTabla();
                
                $sql="SELECT * FROM $db.vista_cruce_cartera_asmet WHERE NumeroFactura='$NumeroFactura' ";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla(($DatosPagos["FechaFactura"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroContrato"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaRadicado"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroRadicado"]), 1,'L');                        
                        $css->ColTabla(number_format($DatosPagos["ValorDocumento"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["Impuestos"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorMenosImpuestos"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorSegunEPS"]), 1,'R');
                    $css->CierraFilaTabla();
                }
                
                /*
                 * Anticipos
                 */
                $css->FilaTabla(16);
                    $css->ColTabla("_____FIN DE INFORMACION DE INFORMACION GENERAL", 14,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Anticipos Realizados a la Factura No. $NumeroFactura</strong>", 14,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Referencia</strong>", 1);
                    $css->ColTabla("<strong>Numero de Anticipo</strong>", 1);
                    $css->ColTabla("<strong>Radicado</strong>", 1);
                    $css->ColTabla("<strong>Valor Factura</strong>", 1);
                    $css->ColTabla("<strong>Valor Menos Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Valor Saldo</strong>", 1);
                    $css->ColTabla("<strong>Valor Anticipado</strong>", 1);
                    
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.anticipos_asmet WHERE NumeroFactura='$NumeroFactura'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla($DatosPagos["DescripcionNC"], 1);
                        $css->ColTabla($DatosPagos["NumeroAnticipo"], 1);
                        $css->ColTabla($DatosPagos["NumeroRadicado"], 1);
                        $css->ColTabla(number_format($DatosPagos["ValorFactura"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorMenosImpuestos"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorSaldo"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorAnticipado"]), 1,'R');
                        
                    $css->CierraFilaTabla();
                }
                
                /*
                 * Copagos
                 */
                 $css->FilaTabla(16);
                    $css->ColTabla("_____FIN DE INFORMACION DE ANTICIPOS", 14,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Copagos Realizados a la Factura No. $NumeroFactura</strong>", 14,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Tipo de Operación</strong>", 1);
                    $css->ColTabla("<strong>Número de Transacción</strong>", 1);
                    $css->ColTabla("<strong>Fecha</strong>", 1);                    
                    $css->ColTabla("<strong>Sucursal</strong>", 1);
                    $css->ColTabla("<strong>Número de Referencia</strong>", 1);
                    $css->ColTabla("<strong>Valor del Copago</strong>", 1);
                    
                    
                    
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.notas_dv_cr WHERE NumeroFactura='$NumeroFactura' AND TipoOperacion='2258'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla(($DatosPagos["TipoOperacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroTransaccion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaTransaccion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NombreSucursal"]), 1,'L');
                        $css->ColTabla(($DatosPagos["C51"]), 1,'L');
                        $css->ColTabla(number_format($DatosPagos["Valor"]), 1,'R');
                        
                    $css->CierraFilaTabla();
                }
                
                /*
                 * Devoluciones
                 */
                 $css->FilaTabla(16);
                    $css->ColTabla("_____FIN DE INFORMACION DE COPAGOS", 14,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Devoluciones Realizadas a la Factura No. $NumeroFactura</strong>", 14,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Tipo de Operación</strong>", 1);
                    $css->ColTabla("<strong>Número de Transacción</strong>", 1);
                    $css->ColTabla("<strong>Fecha</strong>", 1);                    
                    $css->ColTabla("<strong>Sucursal</strong>", 1);
                    $css->ColTabla("<strong>Número de Referencia</strong>", 1);
                    $css->ColTabla("<strong>Valor de la Devolución</strong>", 1);
                    
                    
                    
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.notas_dv_cr WHERE NumeroFactura='$NumeroFactura' AND TipoOperacion='2259'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla(($DatosPagos["TipoOperacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroTransaccion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaTransaccion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NombreSucursal"]), 1,'L');
                        $css->ColTabla(($DatosPagos["C51"]), 1,'L');
                        $css->ColTabla(number_format($DatosPagos["Valor"]), 1,'R');
                        
                    $css->CierraFilaTabla();
                }
                
                /*
                 * Glosas
                 */
                 $css->FilaTabla(16);
                    $css->ColTabla("_____FIN DE INFORMACION DE DEVOLUCIONES", 14,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Glosas Realizadas a la Factura No. $NumeroFactura</strong>", 14,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Sede</strong>", 1);
                    $css->ColTabla("<strong>Fecha del Radicado</strong>", 1);
                    $css->ColTabla("<strong>Número del Radicado</strong>", 1);
                    
                    $css->ColTabla("<strong>Total del Documento</strong>", 1);
                    $css->ColTabla("<strong>Valor Total Glosa</strong>", 1);
                    $css->ColTabla("<strong>Valor Glosa Favor</strong>", 1);
                    $css->ColTabla("<strong>Valor Glosa Contra</strong>", 1);
                    $css->ColTabla("<strong>Valor Pendiente Resolver</strong>", 1);
                    
                    
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.glosaseps_asmet WHERE NumeroFactura='$NumeroFactura'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla(($DatosPagos["Sede"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaRadicado"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroRadicado"]), 1,'L');
                        $css->ColTabla(number_format($DatosPagos["ValorFactura"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorTotalGlosa"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorGlosaFavor"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorGlosaContra"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorPendienteResolver"]), 1,'R');
                    $css->CierraFilaTabla();
                }
                
                
                /*
                 * Pagos
                 */
                 $css->FilaTabla(16);
                    $css->ColTabla("_____FIN DE INFORMACION DE GLOSAS", 14,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Pagos </strong>", 14,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Fecha de Pago</strong>", 1);
                    $css->ColTabla("<strong>Numero de Pago</strong>", 1);
                    $css->ColTabla("<strong>Tipo de Operacion</strong>", 1);
                    $css->ColTabla("<strong>Valor Bruto A Pagar</strong>", 1);
                    $css->ColTabla("<strong>Descuento</strong>", 1);
                    $css->ColTabla("<strong>IVA</strong>", 1);
                    $css->ColTabla("<strong>ReteFuente</strong>", 1);
                    $css->ColTabla("<strong>ReteIVA</strong>", 1);
                    $css->ColTabla("<strong>ReteICA</strong>", 1);
                    $css->ColTabla("<strong>Otras Retenciones</strong>", 1);
                    $css->ColTabla("<strong>Anticipos</strong>", 1);
                    $css->ColTabla("<strong>Valor Transferido</strong>", 1);
                    $css->ColTabla("<strong>Proceso</strong>", 1);
                    $css->ColTabla("<strong>Banco</strong>", 1);
                $css->CierraFilaTabla();
                $sql="SELECT * FROM $db.pagos_asmet WHERE NumeroFactura='$NumeroFactura'";
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla($DatosPagos["FechaPagoFactura"], 1);
                        $css->ColTabla($DatosPagos["NumeroPago"], 1);
                        $css->ColTabla($DatosPagos["TipoOperacion"], 1);
                        $css->ColTabla(number_format($DatosPagos["ValorBrutoPagar"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorDescuento"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorIva"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorRetefuente"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorReteiva"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorReteica"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorOtrasRetenciones"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorAnticipos"]), 1,'R');
                        $css->ColTabla(number_format($DatosPagos["ValorTranferido"]), 1,'R');
                        $css->ColTabla($DatosPagos["Proceso"]." ".$DatosPagos["DescripcionProceso"], 1);
                        $css->ColTabla($DatosPagos["Banco"]." ".$DatosPagos["Cuenta"], 1);
                    $css->CierraFilaTabla();
                }
                
                $css->FilaTabla(16);
                    $css->ColTabla("_____FIN DE INFORMACION DE PAGOS", 14,'C');
                $css->CierraFilaTabla();
                
                
            $css->CerrarTabla();
        break;//Fin caso 11
        
        
        case 12: //Dibuja el consolidado de la informacion
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Condicional=" ";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional=" WHERE NumeroContrato like '$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $statement=" $db.`vista_resumen_cruce_cartera_asmet` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 50;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorSegunEPS) AS Total FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $Total=$row['Total'];
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                    print("<td style='text-align:center'>");
                        print("<strong>Registros:</strong> <h4 style=color:green>". number_format($ResultadosTotales)."</h4>");
                    print("</td>");
                    print("<td colspan=8 style='text-align:center'>");
                        print("<strong>Total Según EPS:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    
                    print("<td colspan='2' style='text-align:center'>");
                        $st1= urlencode($st_reporte);
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_resumen_cruce_cartera_asmet','')", "verde", "");
						
                        //$css->CrearImageLink("ProcesadoresJS/GeneradorCSVReportesCartera.php?Opcion=1&sp=$Separador&st=$st1", "../images/csv.png", "_blank", 50, 50);

                    print("</td>");
                //$css->CierraFilaTabla();
                
                $st= urlencode($st_reporte);
                    if($ResultadosTotales>$limit){

                        //$css->FilaTabla(14);
                            
                            $TotalPaginas= ceil($ResultadosTotales/$limit);
                            print("<td  style=text-align:center>");
                            //print("<strong>Página: </strong>");
                            
                            print('<div class="input-group" style=width:180px>');
                            if($NumPage>1){
                                $NumPage1=$NumPage-1;
                            print('<span class="input-group-addon" onclick=CambiePaginaConsolidado('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaConsolidado();";
                            $css->select("CmbPageConsolidado", "form-control", "CmbPageConsolidado", "", "", $FuncionJS, "");
                            
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
                            print('<span class="input-group-addon" onclick=CambiePaginaConsolidado('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("<div>");
                            print("</td>");
                            
                            
                           $css->CierraFilaTabla(); 
                        }
                      
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Contrato</strong>", 1);                    
                    $css->ColTabla("<strong>Total Facturas</strong>", 1);
                    $css->ColTabla("<strong>Total Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Total Valor Menos Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Total Pagos</strong>", 1);
                    $css->ColTabla("<strong>Total Anticipos</strong>", 1);
                    $css->ColTabla("<strong>Total Copagos</strong>", 1);
                    $css->ColTabla("<strong>Total Devoluciones</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa Inicial</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa A Favor</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa en Contra</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa por Conciliar</strong>", 1);
                    $css->ColTabla("<strong>Total Otros Descuentos</strong>", 1);
                    $css->ColTabla("<strong>Saldo Según EPS</strong>", 1);
                    
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        
                        
                        $css->ColTabla($DatosFactura["NumeroContrato"], 1);                       
                        
                        $css->ColTabla(number_format($DatosFactura["TotalFacturas"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["Impuestos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalMenosImpuestos"]), 1,'R');
                        
                        $css->ColTabla(number_format($DatosFactura["TotalPagos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalAnticipos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalCopagos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalDevoluciones"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalGlosaInicial"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalGlosaFavor"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalGlosaContra"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalGlosaXConciliar"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalOtrosDescuentos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorSegunEPS"]), 1,'R');
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 12
        
        case 13: //Dibuja el consolidado de los pendientes
            $NumeroRadicado=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Pendientes del Radicado No. $NumeroRadicado</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Tabla Origen</strong>", 1);
                    $css->ColTabla("<strong>Origen</strong>", 1);
                    $css->ColTabla("<strong>Departamento</strong>", 1);
                    $css->ColTabla("<strong>Fecha</strong>", 1);
                    $css->ColTabla("<strong>Numero de Registros</strong>", 1);
                    $css->ColTabla("<strong>Aplicados</strong>", 1);
                    $css->ColTabla("<strong>No enviados</strong>", 1);
                    $css->ColTabla("<strong>Estado de Auditoria</strong>", 1);
                    
                    $css->ColTabla("<strong>Valor</strong>", 1);                    
                      
                $css->CierraFilaTabla();
                $sql="SELECT 'Radicados' as TablaOrigen,Origen,NumeroRadicado, DepartamentoRadicacion, FechaRadicacion, 'NA' as NumeroRegistros, 'NA' as Aplicados,'NA' as NoEnviados, EstadoAuditoria, Valor 
                         FROM $db.radicadospendientes WHERE NumeroRadicado='$NumeroRadicado' 
                         UNION
                         SELECT 'Devoluciones' as TablaOrigen,Origen,NumeroRadicado, DepartamentoRadicacion, FechaRadicacion, NumeroRegistros, Aplicados, NoEnviados,'NA' as EstadoAuditoria, Valor 
                         FROM $db.devoluciones_pendientes WHERE NumeroRadicado='$NumeroRadicado'
                         UNION
                         SELECT 'Copagos' as TablaOrigen,Origen,NumeroRadicado, DepartamentoRadicacion, FechaRadicacion, NumeroRegistros, Aplicados, NoEnviados,'NA' as EstadoAuditoria, Valor 
                         FROM $db.copagos_pendientes WHERE NumeroRadicado='$NumeroRadicado'
                         UNION
                         SELECT 'Notas' as TablaOrigen,Origen,NumeroRadicado, DepartamentoRadicacion, FechaRadicacion, NumeroRegistros, Aplicados, NoEnviados,'NA' as EstadoAuditoria, Valor 
                         FROM $db.notas_pendientes WHERE NumeroRadicado='$NumeroRadicado'    
                         
                        ";
               
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        $css->ColTabla(($DatosPagos["TablaOrigen"]), 1,'L');
                        $css->ColTabla(($DatosPagos["Origen"]), 1,'L');
                        $css->ColTabla(($DatosPagos["DepartamentoRadicacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaRadicacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NumeroRegistros"]), 1,'L');
                        $css->ColTabla(($DatosPagos["Aplicados"]), 1,'L');
                        $css->ColTabla(($DatosPagos["NoEnviados"]), 1,'L');
                        $css->ColTabla(($DatosPagos["EstadoAuditoria"]), 1,'L');
                        
                        $css->ColTabla(number_format($DatosPagos["Valor"]), 1,'R');
                        
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
            
        break; //Fin caso 13
        
        
        case 14: //Dibuja toda el historial de la factura 
            
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Archivo de consolidaciones de la Factura No. $NumeroFactura</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                
                $Columnas=$obCon->getColumnasDisponibles("$db.historial_carteracargada_eps","");

                foreach ($Columnas["Field"] as $key => $value) {
                    $css->ColTabla("<strong>$value</strong>",1);
                }
                                  
                      
                $css->CierraFilaTabla();
                $sql=" SELECT * FROM $db.historial_carteracargada_eps WHERE NumeroFactura='$NumeroFactura'
                        ";
               
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        foreach ($Columnas["Field"] as $key => $value) {
                            
                            $css->ColTabla(($DatosPagos[$value]), 1,'L');
                        }
                                                
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
            
                
        break; //Fin caso 14
        case 15://Formulario para conciliar
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $DatosFactura=$obCon->DevuelveValores("$db.vista_cruce_cartera_asmet", "NumeroFactura", $NumeroFactura);
            /*
            if($DatosFactura["Diferencia"]==0){
                exit("<h2>Esta Factura no tiene una diferencia para conciliar</h2>");
            }
             * 
             */
            $css->CrearInputText("TxtNumeroFactura", "hidden", "", $NumeroFactura, "", "", "", "", "", "", 0, 0);        
            
            $sql="SELECT SUM(ValorConciliacion) as TotalConciliaciones FROM $db.conciliaciones_cruces WHERE NumeroFactura='$NumeroFactura' AND Estado<>'ANULADO'";
            $Consulta=$obCon->Query($sql);
            $DatosTotales=$obCon->FetchAssoc($Consulta);
            $TotalConciliaciones=$DatosTotales["TotalConciliaciones"];

            if($TotalConciliaciones==''){
                $TotalConciliaciones=0;
            }
            //print($TotalConciliaciones);
            $css->TabInit();
                $css->TabLabel("TabModal1", "<strong >Conciliar</strong>", "TabModal_1", 1,"");
                $css->TabLabel("TabModal2", "<strong >Detalles</strong>", "TabModal_2",0,"");
            $css->TabInitEnd();

            $css->TabContentInit();
            $css->TabPaneInit("TabModal_1", 1);
            /*
            if($TotalConciliaciones>=abs($DatosFactura["Diferencia"])){
                exit("<strong>La Factura $NumeroFactura está Conciliada</strong>");
            }
              
             * 
             */  
            
            $css->CrearTabla();
            
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>CONCILIAR FACTURA No. $NumeroFactura</strong>", 1);
                    $css->ColTabla("Fecha: <strong><span >$DatosFactura[FechaFactura]</span></strong>", 1);
                    $css->ColTabla("Mes de Servicio: <strong><span >$DatosFactura[MesServicio]</span></strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                                    
                    
                
                    $css->ColTabla("Contrato: <strong><span >$DatosFactura[NumeroContrato]</span></strong>", 1);
                
                    $css->ColTabla("Pendientes: <strong><span >$DatosFactura[Pendientes]</span></strong>", 1);
                    $css->ColTabla("Fecha de Radicado: <strong><span >$DatosFactura[FechaRadicado]</span></strong>", 1);
                    $css->ColTabla("Radicado: <strong><span >$DatosFactura[NumeroRadicado]</span></strong>", 1);
                
                    
                
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Valor de la Factura: </strong>", 1,'L');                
                    $css->ColTabla(number_format($DatosFactura["ValorDocumento"]), 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Impuestos Calculados: </strong>", 1,'L');  
                          
                    $css->ColTabla(number_format($DatosFactura["Impuestos"]), 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Impuestos Segun ASMET: </strong>", 1,'L');                
                    $css->ColTabla(number_format($DatosFactura["ImpuestosSegunASMET"]), 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Valor Menos Impuestos: </strong>", 1,'L');                
                    $css->ColTabla(number_format($DatosFactura["ValorMenosImpuestos"]), 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Pagos: </strong>", 1,'L');  
                    $css->ColTabla("", 2,'L');   
                    print("<td>");
                        $css->div("", "", "", "", "", "onclick=VerHistoriales('$NumeroFactura',4)", "style=cursor:pointer;");

                        
                            print(number_format($DatosFactura["TotalPagos"]));
                        $css->Cdiv();
                       
                    print("<td>");
                                     
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Anticipos: </strong>", 1,'L'); 
                    $css->ColTabla("", 2,'L');  
                    print("<td>");
                        $css->div("", "", "", "", "", "onclick=VerHistoriales('$NumeroFactura',5)", "style=cursor:pointer;");

                        
                            print(number_format($DatosFactura["TotalAnticipos"]));
                        $css->Cdiv();
                       
                    print("<td>");
                                
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Copagos: </strong>", 1,'L');  
                    $css->ColTabla("", 2,'L');  
                    print("<td>");
                        $css->div("", "", "", "", "", "onclick=VerHistoriales('$NumeroFactura',9)", "style=cursor:pointer;");

                        
                            print(number_format($DatosFactura["TotalCopagos"]));
                        $css->Cdiv();
                       
                    print("<td>");
                                   
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Devoluciones: </strong>", 1,'L');
                    $css->ColTabla("", 2,'L');   
                    print("<td>");
                        $css->div("", "", "", "", "", "onclick=VerHistoriales('$NumeroFactura',10)", "style=cursor:pointer;");

                        
                            print(number_format($DatosFactura["TotalDevoluciones"]));
                        $css->Cdiv();
                       
                    print("<td>");
                                      
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Glosa Inicial: </strong>", 1,'L');
                    print("<td>");
                        $css->div("", "", "", "", "", "onclick=VerHistoriales('$NumeroFactura',6)", "style=cursor:pointer;");

                        
                            print(number_format($DatosFactura["TotalGlosaInicial"]));
                        $css->Cdiv();
                       
                    print("<td>");
                                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Glosa a Favor: </strong>", 1,'L'); 
                    $css->ColTabla("", 2,'L');   
                    $css->ColTabla(number_format($DatosFactura["TotalGlosaFavor"]), 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Glosa en Contra: </strong>", 1,'L');                
                    $css->ColTabla(number_format($DatosFactura["TotalGlosaContra"]), 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Glosa X Conciliar: </strong>", 1,'L');                
                    $css->ColTabla(number_format($DatosFactura["GlosaXConciliar"]), 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Cartera Por Edades: </strong>", 1,'L');                
                    $css->ColTabla(number_format($DatosFactura["CarteraXEdades"]), 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Saldo Según EPS: </strong>", 1,'L'); 
                    $css->ColTabla("", 2,'L');   
                    $css->ColTabla("<strong>".number_format($DatosFactura["ValorSegunEPS"])."</strong>", 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Saldo Según IPS: </strong>", 1,'L');   
                    $css->ColTabla("", 2,'L');   
                    $css->ColTabla("<strong>".number_format($DatosFactura["ValorSegunIPS"])."</strong>", 2,'L');                   
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Diferencia: </strong>", 1,'L');  
                    $css->ColTabla("", 2,'L');   
                    $css->ColTabla("<strong>".number_format($DatosFactura["Diferencia"],2)."</strong>", 2,'L');                   
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    print("<td colspan='1'>");
                        $css->select("CmbTipoConciliacion", "form-control", "CmbTipoConciliacion", "", "", "onclick=HabiliteValores()", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione a Favor de Quien se Concilia");
                            $css->Coption();
                            $css->option("", "", "", "1", "", "");
                                print("A Favor de la EPS");
                            $css->Coption();
                            $css->option("", "", "", "2", "", "");
                                print("A Favor de la IPS");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    
                    
                    print("<td colspan='1'>");
                        $css->select("CmbConcepto", "form-control", "CmbConcepto", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el concepto IPS");
                            $css->Coption();
                            $sql="SELECT * FROM conciliaciones_conceptos WHERE Interno=0";
                            $Consulta=$obCon->Query($sql);
                            while($DatosConceptos=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "", "", $DatosConceptos["ID"], "", "");
                                    print($DatosConceptos["Concepto"]);
                                $css->Coption();
                            }
                            
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td colspan='1'>");
                        $css->select("CmbConceptoAGS", "form-control", "CmbConceptoAGS", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el concepto AGS");
                            $css->Coption();
                            $sql="SELECT * FROM conciliaciones_conceptos WHERE Interno=1";
                            $Consulta=$obCon->Query($sql);
                            while($DatosConceptos=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "", "", $DatosConceptos["ID"], "", "");
                                    print($DatosConceptos["Concepto"]);
                                $css->Coption();
                            }
                            
                        $css->Cselect();
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td colspan='4'>");
                        $css->textarea("TxtObservaciones", "form-control", "TxtObservaciones", "", "Observaciones", "", "");
                            
                        $css->Ctextarea();
                    print("</td>");
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    
                    print("<td colspan='4'>");
                        print("<strong>Seleccione el archivo soporte:</strong><br>");
                        $css->input("file", "UpSoporte", "form-control", "UpSoporte", "", "", "", "", "", "style='line-height: 15px;'");
                    print("</td>");
                    
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Valores Conciliados Anteriormente:</strong>", 1);
                
                    $css->ColTabla("<strong>Valor A Favor de la EPS:</strong>", 1);
                
                    $css->ColTabla("<strong>Valor A Favor de la IPS:</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->CrearInputText("TotalConciliaciones", "text", "", $TotalConciliaciones, "", "", "", "", 300, 30, 1, 1);
                    print("</td>");
                
                    print("<td>");
                        $css->CrearInputText("ValorEPS", "text", "", "", "Valor EPS", "", "", "", 300, 30, 0, 1);
                    print("</td>");
                
                    print("<td>");
                        $css->CrearInputText("ValorIPS", "text", "", "", "Valor IPS", "", "", "", 300, 30, 0, 1);
                    print("</td>");
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("date", "FechaConciliacion", "form-control", "FechaConciliacion", "", date("Y-m-d"), "Fecha Corte Cartera", "", "", "style='line-height: 15px;'"."max=".date("Y-m-d"));
        
                    print("</td>");
                    print("<td >");
                        $css->input("text", "ConciliadorIPS", "form-control", "ConciliadorIPS", "", "", "Conciliador IPS", "off", "", "");
                        
                    print("</td>");
                    print("<td colspan='1'>");
                        $css->select("CmbMetodoConciliacion", "form-control", "CmbMetodoConciliacion", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione la forma en la que se realiza la conciliacion");
                            $css->Coption();
                            $css->option("", "", "", "1", "", "");
                                print("Presencial");
                            $css->Coption();
                            $css->option("", "", "", "2", "", "");
                                print("Telefonicamente");
                            $css->Coption();
                            $css->option("", "", "", "3", "", "");
                                print("Virtualmente");
                            $css->Coption();
                        $css->Cselect();
                    print("<td>");
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                     print("<td colspan=3>");
                        $css->CrearBotonEvento("BtnConciliar", "Guardar", 1, "onclick", "ConfirmarConciliacion()", "rojo", "");
                     print("</td>");
                $css->CierraFilaTabla();
                
                
            $css->CerrarTabla();
            
            $css->TabPaneEnd();

            $css->TabPaneInit("TabModal_2", 0);

                $css->div("DivModalHistoricos", "", "", "", "", "", "");
                $css->Cdiv();    

            $css->TabPaneEnd();
            
            
        break;//Fin caso 15
        
        case 16: //Dibuja toda el historial de las conciliaciones 
            
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Conciliaciones de la Factura No. $NumeroFactura</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                
                    $css->ColTabla("<strong>Acciones</strong>",1);
                    $css->ColTabla("<strong>Conciliacion A Favor De</strong>",1);
                    $css->ColTabla("<strong>Observaciones</strong>",1);
                    $css->ColTabla("<strong>Valor de Conciliación</strong>",1);
                    $css->ColTabla("<strong>ConciliadorIps</strong>",1);
                    $css->ColTabla("<strong>Via de Conciliacion</strong>",1);
                    $css->ColTabla("<strong>idUser</strong>",1);
                    $css->ColTabla("<strong>FechaRegistro</strong>",1);
                    
                               
                      
                $css->CierraFilaTabla();
                $sql=" SELECT * FROM $db.conciliaciones_cruces WHERE NumeroFactura='$NumeroFactura'
                        ";
               
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosPagos["ID"];
                    $css->FilaTabla(14);
                        print("<td style='text-align:center;color:red'>");
                            print('<a id="BtnAnular_'.$idItem.'" href="#" onclick="VerHistorialFactura(`'.$idItem.'`,`17`);"><i class="fa fa-remove "></i></a>');
                            
                        print("</td>");
                        $ConciliadoAFavorDe="IPS";
                        if($DatosPagos["ConciliacionAFavorDe"]==1){
                            $ConciliadoAFavorDe="EPS";
                        }
                        $css->ColTabla(($ConciliadoAFavorDe), 1,'L');
                        $css->ColTabla(($DatosPagos["Observacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["ValorConciliacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["ConciliadorIps"]), 1,'L');
                        $css->ColTabla(($DatosPagos["ViaConciliacion"]), 1,'L');
                        $css->ColTabla(($DatosPagos["idUser"]), 1,'L');
                        $css->ColTabla(($DatosPagos["FechaRegistro"]), 1,'L');
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
            
                
        break; //Fin caso 16
        
        case 17:// formulario para anular una conciliacion
            $idConciliacion=$obCon->normalizar($_REQUEST["NumeroFactura"]);
              
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $DatosConciliacion=$obCon->DevuelveValores("$db.conciliaciones_cruces", "ID", $idConciliacion);
            $NumeroFactura=$DatosConciliacion["NumeroFactura"]; 
            $css->input("hidden", "TxtIdAnulacionConciliacion", "", "", "", $idConciliacion, "", "", "", "");
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong style=color:red>ANULAR CONCILIACIÓN DE LA FACTURA $NumeroFactura</strong>", 4,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>DATOS DE LA CONCILIACIÓN</strong>", 4,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>USUARIO</strong>", 1);
                    $css->ColTabla("<strong>A FAVOR DE</strong>", 1);
                    $css->ColTabla("<strong>FECHA</strong>", 1);
                    $css->ColTabla("<strong>VALOR CONCILIADO</strong>", 1);
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    $css->ColTabla($DatosConciliacion["idUser"], 1);
                    if($DatosConciliacion["ConciliacionAFavorDe"]==1){
                        $ConciliacionAFavorDe="EPS";
                    }else{
                        $ConciliacionAFavorDe="IPS";
                    }
                    $css->ColTabla($ConciliacionAFavorDe, 1);
                    $css->ColTabla($DatosConciliacion["FechaRegistro"], 1);
                    $css->ColTabla(number_format($DatosConciliacion["ValorConciliacion"]), 1);
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Motivo de Anulación</strong>", 1);
                    $css->ColTabla("<strong>Observaciones</strong>", 2);
                    $css->ColTabla("<strong>Guardar</strong>", 1);
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    print("<td>");
                        $css->select("CmbTipoAnulacion", "form-control", "CmbTipoAnulacion", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione por qué se que anula");
                            $css->Coption();
                            $sql="SELECT * FROM conciliaciones_tipo_anulaciones";
                            $Consulta=$obCon->Query($sql);
                            while($DatosTipo=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "", "", $DatosTipo["ID"], "", "");
                                    print($DatosTipo["Tipo"]);
                                $css->Coption();
                            }
                        $css->Cselect();
                    print("</td>");
                    print("<td colspan=2>");
                        $css->textarea("TxtObservacionesAnulacion", "form-control", "TxtObservacionesAnulacion", "", "Observaciones", "", "");
                        
                        $css->Ctextarea();
                    print("</td>");
                    print("<td>");
                        $css->CrearBotonEvento("btnGuardarAnulacion", "ANULAR", 1, "onclick", "ConfirmarAnulacion()", "rojo", "");
                    print("</td>");
                $css->CierraFilaTabla();
            $css->CerrarTabla();
        break;   //fin caso 17
        
        case 18://Se abre formulario para consolidaciones masivas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            //$DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            //$db=$DatosIPS["DataBase"];
            $css->div("DivProcessConciliacionMasiva", "", "", "", "", "", "");
            $css->Cdiv();
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Subir conciliaciones desde Archivo</strong>", 6,'C');
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    print("<td colspan=6>");
                        $Ruta="../../general/procesadores/GeneradorExcel.php?idDocumento=1&CmbIPS=$CmbIPS";
                        print("<a href='$Ruta' target='_blank'><button class='btn btn-success'>Descargar Base Conciliación</button></a>");
                        //$css->CrearBotonEvento("btnDescargarFormatoConciliacionMasiva", "Descargar Formato", 1, "onclick", "DescargarConciliacionMasiva()", "azul", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Fecha</strong>", 1,'C');
                    $css->ColTabla("<strong>Conciliador IPS</strong>", 1,'C');
                    $css->ColTabla("<strong>Concepto de Conciliación Con IPS</strong>", 1,'C');
                    $css->ColTabla("<strong>Concepto de Conciliación AGS</strong>", 1,'C');
                    $css->ColTabla("<strong>Vía de Conciliación</strong>", 1,'C');
                $css->CierraFilaTabla();    
               
                
                $css->FilaTabla(14);
                    print("<td colspan=1>");
                        $css->input("date", "FechaConciliacionMasiva", "form-control", "FechaConciliacionMasiva", "", date("Y-m-d"), "Fecha Corte Cartera", "", "", "style='line-height: 15px;'"."max=".date("Y-m-d"));
                    print("</td>");
                    print("<td >");
                        $css->input("text", "ConciliadorIPSMasivo", "form-control", "ConciliadorIPSMasivo", "", "", "Conciliador IPS", "off", "", "");
                        
                    print("</td>");
                    print("<td colspan='1'>");
                        $css->select("CmbConceptoConciliacion", "form-control", "CmbConceptoConciliacion", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el concepto por el cual se Concilia");
                            $css->Coption();
                            $sql="SELECT * FROM conciliaciones_conceptos WHERE Interno=0";
                            $Consulta=$obCon->Query($sql);
                            while($DatosConceptos=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "", "", $DatosConceptos["ID"], "", "");
                                    print($DatosConceptos["Concepto"]);
                                $css->Coption();
                            }
                            
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td colspan='1'>");
                        $css->select("CmbConceptoConciliacionAGS", "form-control", "CmbConceptoConciliacionAGS", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el concepto por el cual se Concilia");
                            $css->Coption();
                            $sql="SELECT * FROM conciliaciones_conceptos WHERE Interno=1";
                            $Consulta=$obCon->Query($sql);
                            while($DatosConceptos=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "", "", $DatosConceptos["ID"], "", "");
                                    print($DatosConceptos["Concepto"]);
                                $css->Coption();
                            }
                            
                        $css->Cselect();
                    print("</td>");
                    
                     
                    
                    print("<td colspan='1'>");
                        $css->select("CmbMetodoConciliacionMasivo", "form-control", "CmbMetodoConciliacionMasivo", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione la forma en la que se realiza la conciliacion");
                            $css->Coption();
                            $css->option("", "", "", "1", "", "");
                                print("Presencial");
                            $css->Coption();
                            $css->option("", "", "", "2", "", "");
                                print("Telefonicamente");
                            $css->Coption();
                            $css->option("", "", "", "3", "", "");
                                print("Virtualmente");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    
                    $css->CierraFilaTabla();  
                    
                    $css->FilaTabla(16);    
                        $css->ColTabla("<strong>Archivo de conciliaciones</strong>", 2,'C');
                        $css->ColTabla("<strong>Soporte</strong>", 1,'C');
                        $css->ColTabla("<strong>Ejecutar</strong>", 1,'C');
                    $css->CierraFilaTabla();
                    
                    $css->FilaTabla(16);   
                    
                    print("<td colspan=3>");
                        $css->input("file", "UpConciliacionMasiva", "form-control", "UpConciliacionMasiva", "", "", "", "", "", "style='line-height: 15px;'");
                    print("</td>");
                    print("<td colspan=1>");
                        $css->input("file", "UpSoporteConciliacionMasiva", "form-control", "UpSoporteConciliacionMasiva", "", "", "", "", "", "style='line-height: 15px;'");
                    print("</td>");
                    print("<td colspan=1>");
                        $css->CrearBotonEvento("btnGuardarConciliacionesMasivas", "Ejecutar", 1, "onclick", "ConfirmarConciliacionesMasivas()", "rojo", "");
                    print("</td>");
                $css->CierraFilaTabla();
            $css->CerrarTabla();
        break;//FIn caso 18    
        /*
        case 19: //Dibuja las Retenciones pagadas por la EPS y No relacionadas por IPS
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
                    $Condicional=" WHERE  NumeroContrato like '$Busqueda%' or NumeroFactura like '%$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $statement=" $db.`vista_facturas_sr_eps_3` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 50;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorImpuestosCalculados) AS Total FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $Total=$row['Total'];
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                    print("<td style='text-align:center'>");
                        print("<strong>Registros:</strong> <h4 style=color:green>". number_format($ResultadosTotales)."</h4>");
                    print("</td>");
                    print("<td colspan=4 style='text-align:center'>");
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    print("<td>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_facturas_sr_eps_3','')", "verde", "");
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
                            print('<span class="input-group-addon" onclick=CambiePaginaRetencionesSR('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaRetencionesSR();";
                            $css->select("CmbPageRetencionesSR", "form-control", "CmbPageRetencionesSR", "", "", $FuncionJS, "");
                            
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
                            print('<span class="input-group-addon" onclick=CambiePaginaRetencionesSR('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("<div>");
                            print("</td>");
                            
                            
                           $css->CierraFilaTabla(); 
                        }
                      
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>NIT EPS</strong>", 1);
                    $css->ColTabla("<strong>CodigoSucursal</strong>", 1);
                    $css->ColTabla("<strong>Sucursal</strong>", 1);
                    $css->ColTabla("<strong>NumeroFactura</strong>", 1);
                    $css->ColTabla("<strong>Descripcion</strong>", 1);
                    $css->ColTabla("<strong>RazonSocial</strong>", 1);
                    $css->ColTabla("<strong>NIT IPS</strong>", 1);
                    $css->ColTabla("<strong>NumeroContrato</strong>", 1);
                    $css->ColTabla("<strong>Prefijo</strong>", 1);
                    $css->ColTabla("<strong>DepartamentoRadicacion</strong>", 1);
                    $css->ColTabla("<strong>ValorOriginal</strong>", 1);
                    $css->ColTabla("<strong>ValorMenosImpuestos</strong>", 1);
                    
                    $css->ColTabla("<strong>MesServicio</strong>", 1);
                    $css->ColTabla("<strong>FechaRadicado</strong>", 1);
                    $css->ColTabla("<strong>NumeroRadicado</strong>", 1);
                    $css->ColTabla("<strong>FechaRegistro</strong>", 1);
                    $css->ColTabla("<strong>FechaActualizacion</strong>", 1);
                    $css->ColTabla("<strong>TotalDevoluciones</strong>", 1);
                    $css->ColTabla("<strong>TotalRetenciones</strong>", 1);
                    $css->ColTabla("<strong>TotalPagos</strong>", 1);
                    
                    $css->ColTabla("<strong>Valor A Favor</strong>", 1);
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $NumeroFactura=$DatosFactura["NumeroFactura"];
                        $css->ColTabla($DatosFactura["NitEPS"], 1);
                                                
                        $css->ColTabla($DatosFactura["CodigoSucursal"], 1);
                        $css->ColTabla($DatosFactura["Sucursal"], 1);
                        $css->ColTabla($DatosFactura["NumeroFactura"], 1);
                        $css->ColTabla($DatosFactura["Descripcion"], 1);
                        $css->ColTabla($DatosFactura["RazonSocial"], 1);
                        $css->ColTabla($DatosFactura["Nit_IPS"], 1);
                        $css->ColTabla($DatosFactura["NumeroContrato"], 1);
                        $css->ColTabla($DatosFactura["Prefijo"], 1);
                        $css->ColTabla($DatosFactura["DepartamentoRadicacion"], 1);
                        $css->ColTabla($DatosFactura["ValorOriginal"], 1);
                        $css->ColTabla($DatosFactura["ValorMenosImpuestos"], 1);
                        $css->ColTabla($DatosFactura["MesServicio"], 1);
                        $css->ColTabla($DatosFactura["FechaRadicado"], 1);
                        $css->ColTabla($DatosFactura["NumeroRadicado"], 1);
                        $css->ColTabla($DatosFactura["FechaRegistro"], 1);
                        $css->ColTabla($DatosFactura["FechaActualizacion"], 1);
                        
                        $css->ColTabla(number_format($DatosFactura["TotalDevoluciones"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalRetenciones"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["TotalPagos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["Saldo"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorImpuestosCalculados"]), 1,'R');
                        
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 19
        */
        
        case 19: //Dibuja las Retenciones pagadas por la EPS y No relacionadas por IPS
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
                    $Condicional=" WHERE  NumeroContrato like '$Busqueda%' or NumeroFactura like '%$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $statement=" $db.`vista_cruce_cartera_eps_no_relacionadas_ips` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 10;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            $css->div("DivProcesoCopia", "", "", "", "", "", "");
            
            $css->Cdiv();
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                                        
                    print("<td colspan=2>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_cruce_cartera_eps_no_relacionadas_ips','')", "verde", "");
                    print("</td>");
                    
                    
                    print("<td colspan=2>");
                        $css->input("text", "VigenciaInicialFSF", "form-control", "VigenciaInicialFSF", "Vigencia Inicial:", "", "Vigencia Inicial", "off", "", "");
                   
                        $css->input("text", "VigenciaFinalFSF", "form-control", "VigenciaFinalFSF", "Vigencia Final:", "", "Vigencia Final", "off", "", "");
                    
                        $css->CrearBotonEvento("BtnCopiarAlCruce", "Copiar al Cruce", 1, "onclick", "ConfirmarCopiaFacturasSFNR()", "rojo", "");
                    print("</td>");
                    
               
                            
                $css->CierraFilaTabla(); 
                        
                $css->FilaTabla(16);
                
                $Columnas=$obCon->getColumnasDisponibles("$db.vista_cruce_cartera_eps_no_relacionadas_ips","");

                    foreach ($Columnas["Field"] as $key => $value) {
                        $css->ColTabla("<strong>$value</strong>",1);
                    }
                
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $NumeroFactura=$DatosFactura["NumeroFactura"];

                        foreach ($Columnas["Field"] as $key => $value) {
                            $css->ColTabla(($DatosFactura[$value]), 1,'L');
                        }                                                
                    $css->CierraFilaTabla();
                    
                }
            $css->CerrarTabla();
            
        break; //Fin caso 19
        
        case 20: //Dibuja las conciliaciones realizadas 
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
                    $Condicional=" WHERE  NumeroContrato like '$Busqueda%' or NumeroFactura like '%$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $statement=" $db.`conciliaciones_cruces` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            $TotalConciliacionesIPS=$obCon->Sume("$db.conciliaciones_cruces", "ValorConciliacion", "WHERE ConciliacionAFavorDe=2");
            $TotalConciliacionesEPS=$obCon->Sume("$db.conciliaciones_cruces", "ValorConciliacion", "WHERE ConciliacionAFavorDe=1");
            $limit = 50;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorConciliacion) AS Total FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $Total=$row['Total'];
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
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Conciliado a Favor de La EPS:</strong> <h4 style=color:red>". number_format($TotalConciliacionesEPS)."</h4>");
                    print("</td>");
                    print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Conciliado a Favor de La IPS:</strong> <h4 style=color:red>". number_format($TotalConciliacionesIPS)."</h4>");
                    print("</td>");
                     print("<td colspan=1 style='text-align:center'>");
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    print("<td>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','conciliaciones_cruces','')", "verde", "");
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
                            print('<span class="input-group-addon" onclick=CambiePaginaConciliaciones('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaConciliaciones();";
                            $css->select("CmbPageConciliaciones", "form-control", "CmbPageConciliaciones", "", "", $FuncionJS, "");
                            
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
                            print('<span class="input-group-addon" onclick=CambiePaginaConciliaciones('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("<div>");
                            print("</td>");
                            
                            
                           $css->CierraFilaTabla(); 
                        }
                      
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>NumeroContrato</strong>", 1);
                    $css->ColTabla("<strong>NumeroFactura</strong>", 1);
                    $css->ColTabla("<strong>MesServicio</strong>", 1);
                    $css->ColTabla("<strong>FechaFactura</strong>", 1);
                    $css->ColTabla("<strong>NumeroRadicado</strong>", 1);
                    $css->ColTabla("<strong>Pendientes</strong>", 1);
                    $css->ColTabla("<strong>FechaRadicado</strong>", 1);
                    $css->ColTabla("<strong>ValorOriginal</strong>", 1);
                    $css->ColTabla("<strong>ValorImpuestoCalculado</strong>", 1);
                    $css->ColTabla("<strong>ValorImpuestoRetenciones</strong>", 1);
                    $css->ColTabla("<strong>ValorMenosImpuesto</strong>", 1);
                    $css->ColTabla("<strong>ValorPagos</strong>", 1);                    
                    $css->ColTabla("<strong>ValorAnticipos</strong>", 1);
                    $css->ColTabla("<strong>ValorCopagos</strong>", 1);
                    $css->ColTabla("<strong>ValorDevoluciones</strong>", 1);
                    $css->ColTabla("<strong>ValorGlosaInicial</strong>", 1);
                    $css->ColTabla("<strong>ValorGlosaFavor</strong>", 1);
                    $css->ColTabla("<strong>ValorGlosaContra</strong>", 1);
                    $css->ColTabla("<strong>ValorGlosaconciliar</strong>", 1);
                    $css->ColTabla("<strong>ValorSaldoEps</strong>", 1);
                    $css->ColTabla("<strong>ValorSaldoIps</strong>", 1);
                    $css->ColTabla("<strong>ValorDiferencia</strong>", 1);
                    $css->ColTabla("<strong>ConceptoConciliacion</strong>", 1);
                    $css->ColTabla("<strong>ConciliacionAFavorDe</strong>", 1);
                    $css->ColTabla("<strong>Observacion</strong>", 1);
                    $css->ColTabla("<strong>Soportes</strong>", 1);
                    $css->ColTabla("<strong>ValorConciliacion</strong>", 1);
                    $css->ColTabla("<strong>ConciliadorIps</strong>", 1);
                    $css->ColTabla("<strong>FechaConciliacion</strong>", 1);
                    $css->ColTabla("<strong>ViaConciliacion</strong>", 1);                    
                    $css->ColTabla("<strong>Estado</strong>", 1);
                    $css->ColTabla("<strong>idUser</strong>", 1);
                    $css->ColTabla("<strong>FechaRegistro</strong>", 1);
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $NumeroFactura=$DatosFactura["NumeroFactura"];
                        $Soporte=$DatosFactura["Soportes"];
                        $Soporte= str_replace("../","" , $Soporte);
                        $Soporte="../../".$Soporte;
                        $Link="<a href='$Soporte' target='_BLANK'>Ver Soporte</a>";
                        $css->ColTabla($DatosFactura["NumeroContrato"], 1);                                                
                        $css->ColTabla($DatosFactura["NumeroFactura"], 1);
                        $css->ColTabla($DatosFactura["MesServicio"], 1);
                        $css->ColTabla($DatosFactura["FechaFactura"], 1);
                        $css->ColTabla($DatosFactura["NumeroRadicado"], 1);
                        $css->ColTabla($DatosFactura["Pendientes"], 1);
                        $css->ColTabla($DatosFactura["FechaRadicado"], 1);
                        $css->ColTabla(number_format($DatosFactura["ValorOriginal"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorImpuestoCalculado"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorImpuestoRetenciones"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorMenosImpuesto"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorPagos"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorAnticipos"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorCopagos"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorDevoluciones"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorGlosaInicial"]), 1);
                        $css->ColTabla(number_format($DatosFactura["ValorGlosaFavor"]), 1);
                        
                        $css->ColTabla(number_format($DatosFactura["ValorGlosaContra"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorGlosaconciliar"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorSaldoEps"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorSaldoIps"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorDiferencia"]), 1,'R');
                        
                        $css->ColTabla($DatosFactura["ConceptoConciliacion"], 1);
                        $css->ColTabla($DatosFactura["ConciliacionAFavorDe"], 1);
                        $css->ColTabla(utf8_encode($DatosFactura["Observacion"]), 1);
                        $css->ColTabla($Link, 1);
                        $css->ColTabla($DatosFactura["ValorConciliacion"], 1);
                        $css->ColTabla($DatosFactura["ConciliadorIps"], 1);
                        $css->ColTabla($DatosFactura["FechaConciliacion"], 1);
                        $css->ColTabla($DatosFactura["ViaConciliacion"], 1);
                        $css->ColTabla($DatosFactura["Estado"], 1);
                        $css->ColTabla($DatosFactura["idUser"], 1);
                        $css->ColTabla($DatosFactura["FechaRegistro"], 1);
                        
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 20
        
        case 21: //Dibuja toda el historial de la los radicados pendientes
            
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Radicados Pendientes</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                
                $Columnas=$obCon->getColumnasDisponibles("$db.radicadospendientes","");

                foreach ($Columnas["Field"] as $key => $value) {
                    $css->ColTabla("<strong>$value</strong>",1);
                }
                                  
                      
                $css->CierraFilaTabla();
                $sql=" SELECT * FROM $db.radicadospendientes WHERE EstadoAuditoria LIKE '%AUDITORIA%' AND 
                        EXISTS (SELECT 1 FROM $db.vista_cruce_cartera_asmet WHERE $db.vista_cruce_cartera_asmet.NumeroRadicado=$db.radicadospendientes.NumeroRadicado LIMIT 1) 
                        ";
               
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        foreach ($Columnas["Field"] as $key => $value) {
                            
                            $css->ColTabla(($DatosPagos[$value]), 1,'L');
                        }
                                                
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
            
                
        break; //Fin caso 21
        
        case 22: //Dibuja toda el historial de la los devoluciones pendientes
            
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Devoluciones Pendientes</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                
                $Columnas=$obCon->getColumnasDisponibles("$db.devoluciones_pendientes","");

                foreach ($Columnas["Field"] as $key => $value) {
                    $css->ColTabla("<strong>$value</strong>",1);
                }
                                  
                      
                $css->CierraFilaTabla();
                $sql=" SELECT * FROM $db.devoluciones_pendientes
                            WHERE NoEnviados > '0' AND 
                           EXISTS (SELECT 1 FROM $db.vista_cruce_cartera_asmet WHERE $db.vista_cruce_cartera_asmet.NumeroRadicado=$db.devoluciones_pendientes.NumeroRadicado LIMIT 1) 
                        ";
               
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        foreach ($Columnas["Field"] as $key => $value) {
                            
                            $css->ColTabla(($DatosPagos[$value]), 1,'L');
                        }
                                                
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
            
                
        break; //Fin caso 22
        
        case 23: //Dibuja toda el historial de los copagos pendientes
            
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Copagos Pendientes</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                
                $Columnas=$obCon->getColumnasDisponibles("$db.copagos_pendientes","");

                foreach ($Columnas["Field"] as $key => $value) {
                    $css->ColTabla("<strong>$value</strong>",1);
                }
                                  
                      
                $css->CierraFilaTabla();
                $sql=" SELECT * FROM $db.copagos_pendientes
                            WHERE NoEnviados > '0' AND 
                           EXISTS (SELECT 1 FROM $db.vista_cruce_cartera_asmet WHERE $db.vista_cruce_cartera_asmet.NumeroRadicado=$db.copagos_pendientes.NumeroRadicado LIMIT 1) 
                        ";
               
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        foreach ($Columnas["Field"] as $key => $value) {
                            
                            $css->ColTabla(($DatosPagos[$value]), 1,'L');
                        }
                                                
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
            
                
        break; //Fin caso 23
        
        case 24: //Dibuja toda el historial de los notas pendientes
            
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Notas Pendientes</strong>", 12,'C');
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                
                $Columnas=$obCon->getColumnasDisponibles("$db.notas_pendientes","");

                foreach ($Columnas["Field"] as $key => $value) {
                    $css->ColTabla("<strong>$value</strong>",1);
                }
                                  
                      
                $css->CierraFilaTabla();
                $sql=" SELECT * FROM $db.notas_pendientes
                            WHERE NoEnviados > '0' AND 
                           EXISTS (SELECT 1 FROM $db.vista_cruce_cartera_asmet WHERE $db.vista_cruce_cartera_asmet.NumeroRadicado=$db.notas_pendientes.NumeroRadicado LIMIT 1) 
                        ";
               
                $Consulta=$obCon->Query($sql);
                while($DatosPagos=$obCon->FetchAssoc($Consulta)){
                     $css->FilaTabla(14);
                        foreach ($Columnas["Field"] as $key => $value) {
                            
                            $css->ColTabla(($DatosPagos[$value]), 1,'L');
                        }
                                                
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
            
                
        break; //Fin caso 24
        
        case 25://Dibuja el formulario para el Acta de Conciliacion
            $NitIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $NitIPS);
            $css->input("hidden", "TxtNitIPSActa", "form-control", "TxtNitIPS", "", $NitIPS, "", "", "", "");
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("Crear Nueva Acta de Conciliación para la IPS: <strong>". utf8_decode($DatosIPS["Nombre"])."</strong>, con NIT: ".$NitIPS, 3);
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Fecha de Inicial:</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Final:</strong>", 1);
                    $css->ColTabla("<strong>Representante Legal IPS:</strong>", 1);
                    $css->ColTabla("<strong>Encargado de la EPS:</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    print("<td>");
                        $css->input("date", "FechaActaInicial", "form-control", "FechaActaInicial", "", date("Y-m-d"), "Fecha Corte Cartera", "", "", "style='line-height: 15px;'"."max=".date("Y-m-d"));
        
                    print("</td>");
                    print("<td>");
                        $css->input("date", "FechaActaConciliacion", "form-control", "FechaActaConciliacion", "", date("Y-m-d"), "Fecha Corte Cartera", "", "", "style='line-height: 15px;'"."max=".date("Y-m-d"));
        
                    print("</td>");
                    print("<td>");
                        $css->input("text", "TxtRepresentanteLegalIPS", "form-control", "TxtRepresentanteLegalIPS", "", $DatosIPS["RepresentanteLegal"], "Representante Legal", "", "", "");
                    print("</td>");
                    
                     print("<td>");
                        $css->input("text", "TxtEncargadoEPS", "form-control", "TxtEncargadoEPS", "", "", "Encargado EPS", "", "", "");
                    print("</td>");
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td colspan=4>");
                        $css->CrearBotonEvento("BtnGuardarActa", "Crear Acta", 1, "onclick", "ConfirmarCrearActa()", "rojo", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
        break;//Fin caso 25
    
        case 26: //Dibuja las facturas que tiene la eps pero no la ips
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
                    $Condicional=" WHERE  NumeroContrato like '$Busqueda%' or NumeroFactura like '%$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $statement=" $db.`vista_cruce_cartera_eps_no_relacionadas_ips_completa` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 10;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            $css->div("DivProcesoCopia", "", "", "", "", "", "");
            
            $css->Cdiv();
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                                        
                    print("<td colspan=2>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_cruce_cartera_eps_no_relacionadas_ips_completa','')", "verde", "");
                    print("</td>");
                    
                    /*
                    print("<td colspan=2>");
                        $css->input("text", "VigenciaInicialFSF", "form-control", "VigenciaInicialFSF", "Vigencia Inicial:", "", "Vigencia Inicial", "off", "", "");
                   
                        $css->input("text", "VigenciaFinalFSF", "form-control", "VigenciaFinalFSF", "Vigencia Final:", "", "Vigencia Final", "off", "", "");
                    
                        $css->CrearBotonEvento("BtnCopiarAlCruce", "Copiar al Cruce", 1, "onclick", "ConfirmarCopiaFacturasSFNR()", "rojo", "");
                    print("</td>");
                    */
               
                            
                $css->CierraFilaTabla(); 
                        
                $css->FilaTabla(16);
                
                $Columnas=$obCon->getColumnasDisponibles("$db.vista_cruce_cartera_eps_no_relacionadas_ips_completa","");

                    foreach ($Columnas["Field"] as $key => $value) {
                        $css->ColTabla("<strong>$value</strong>",1);
                    }
                
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $NumeroFactura=$DatosFactura["NumeroFactura"];

                        foreach ($Columnas["Field"] as $key => $value) {
                            $css->ColTabla(($DatosFactura[$value]), 1,'L');
                        }                                                
                    $css->CierraFilaTabla();
                    
                }
            $css->CerrarTabla();
            
        break; //Fin caso 26
        
        case 27://Dibuja el selector para las actas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $css->select("idActaConciliacion", "form-control", "idActaConciliacion", "", "", "onchange=MostrarActa()", "");
                $css->option("", "", "", "", "", "");
                    print("Seleccione un Acta de Conciliación");
                $css->Coption();
                $sql="SELECT * FROM actas_conciliaciones WHERE Estado='0' AND NIT_IPS='$CmbIPS'";
                $Consulta=$obCon->Query($sql);
                while($DatosActas=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $DatosActas["ID"], "", "");
                        print($DatosActas["ID"]." ".$DatosActas["FechaCorte"]." ".$DatosActas["RazonSocialIPS"]." ".$DatosActas["NIT_IPS"]);
                    $css->Coption();
                }
            $css->Cselect();
        break;//fin caso 27
        
        case 28://Dibuja el acta de conciliacion
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $DibujeAreaContratos=$obCon->normalizar($_REQUEST["DibujeAreaContratos"]);
            if($idActaConciliacion==''){
                $css->CrearTitulo("Por favor Seleccione un Acta", "rojo");
                exit();
            }
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $TotalesActaConciliacion=$obCon->obtengaValoresGeneralesActaConciliacion($db, $idActaConciliacion);
            $ValorSegunEPS=$TotalesActaConciliacion["ValorSegunEPS"];
            $ValorSegunIPS=$TotalesActaConciliacion["ValorSegunIPS"];
            $Diferencia=$TotalesActaConciliacion["Diferencia"];
            $SaldoConciliadoParaPago=$TotalesActaConciliacion["SaldoConciliadoParaPago"];
            $TotalPendientesRadicados=$TotalesActaConciliacion["TotalPendientesRadicados"];
            $TotalFacturasSinRelacionsrXIPS=$TotalesActaConciliacion["TotalFacturasSinRelacionsrXIPS"];
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion);
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            if($DibujeAreaContratos==1){
                $css->CrearTabla();
            
                print("<tr>");
                    print("<td colspan=1 style=font-size:16px;>");
                        print("<span style='text-decoration: underline;cursor:pointer;'><strong >Contratos Disponibles en el cruce entre el Mes de Servicio $MesServicioInicial y $MesServicioFinal: </strong></span>");
                        $css->div("DivContratosDisponiblesActaConciliacion", "", "", "", "", "", "");
                            $sql="SELECT DISTINCT NumeroContrato FROM $db.carteraeps WHERE MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal' ";
                            $Consulta=$obCon->Query($sql);

                            while($Contratos=$obCon->FetchAssoc($Consulta)){
                                $idContrato=$Contratos["NumeroContrato"];
                                print("<br>".$Contratos["NumeroContrato"]." ");
                                
                                $css->li("", "fa fa-sign-out", "", "onclick='AgregarContratoActaConciliacion(`$idActaConciliacion`,`$idContrato`)' style=font-size:16px;cursor:pointer;text-align:center;color:green");
                                    
                                $css->Cli();

                            }
                        print("</div>");
                    print("</td>");
                    
                    print("<td colspan=2 style=font-size:16px;>");
                        print("<span style='text-decoration: underline;cursor:pointer;'><strong >Contratos Agregados a esta Acta: </strong></span>");
                        $css->div("DivContratosAgregadosActaConciliacion", "", "", "", "", "", "");
                            $sql="SELECT NumeroContrato FROM actas_conciliaciones_contratos WHERE idActaConciliacion='$idActaConciliacion'";
                            $Consulta=$obCon->Query($sql);
                            while($Contratos=$obCon->FetchAssoc($Consulta)){
                                print("<br>".$Contratos["NumeroContrato"]." ");
                                $css->li("", "fa  fa-remove", "", "onclick='EliminarItem(`2`,`$Contratos[NumeroContrato]`)' style=font-size:16px;cursor:pointer;text-align:center;color:red");
                                    //print(" ".$Contratos["NumeroContrato"]);
                                $css->Cli();
                            }
                        $css->Cdiv();
                    print("</td>");
                    
                print("</tr>");
                $css->CerrarTabla();    
            }
            
            $css->CrearDiv("DivDrawActaConciliacion", "", "", 1, 1);
            $css->CrearTitulo("<strong>Acta de Conciliación No. $idActaConciliacion, IPS: $DatosActa[RazonSocialIPS], con Fecha de Corte $DatosActa[FechaCorte]. </strong>", "azul");
            $css->CrearTabla("TablaActaConciliacion");
            
                print("<tr style=font-size:18px;border-top-style:double;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td>");
                        $css->img("", "", "../../LogosEmpresas/logoAsmet.png", "Sin Imagen", "", "","style=height:80px;width:400px;");
                    print("</td>");
                    print("<td >");
                        print("<strong>ACTA DE CONCILIACIÓN</strong>");
                    print("</td>");
                    print("<td>");
                        $css->img("", "", "../../LogosEmpresas/logoAGS.png", "Sin Imagen", "", "","style=height:100px;width:200px;");
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:18px;border-top-style:double;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td>");
                        print("<strong>Proveedor:</strong>");
                    print("</td>");
                    print("<td>");
                      $css->input("text", "TxtRazonSocialIPS", "form-control", "TxtRazonSocialIPS", "", $DatosActa["RazonSocialIPS"], "Representante IPS", "off", "", "onchange=EditeActaConciliacion(`$idActaConciliacion`,`TxtRazonSocialIPS`,`RazonSocialIPS`)");
                        
                    print("</td>");
                    /*
                    print("<td>");
                        print(utf8_decode($DatosActa["RazonSocialIPS"]));
                    print("</td>");
                     * 
                     */
                print("</tr>");
                print("<tr style=font-size:18px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td>");
                        print("<strong>Representante:</strong>");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "TxtRepresentanteActaConciliacion", "form-control", "TxtRepresentanteActaConciliacion", "", ($DatosActa["RepresentanteLegal"]), "Representante IPS", "off", "", "onchange=EditeActaConciliacion(`$idActaConciliacion`,`TxtRepresentanteActaConciliacion`,`RepresentanteLegal`)");
                        
                    print("</td>");
                print("</tr>");
                
                print("<tr style=font-size:18px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td>");
                        print("<strong>NIT:</strong>");
                    print("</td>");
                    print("<td>");
                        print(utf8_decode($DatosActa["NIT_IPS"]));
                    print("</td>");
                print("</tr>");
                
                print("<tr style=font-size:18px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td>");
                        print("<strong>Departamento:</strong>");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "TxtDepartamentoRadicacion", "form-control", "TxtDepartamentoRadicacion", "", $DatosActa["Departamento"], "Departamento", "off", "", "onchange=EditeActaConciliacion(`$idActaConciliacion`,`TxtDepartamentoRadicacion`,`Departamento`)");
                       
                        //print(utf8_decode($DatosActa["Departamento"]));
                    print("</td>");
                print("</tr>");
                
                print("<tr style=font-size:18px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td>");
                        print("<strong>Encargado ASMET SALUD:</strong>");
                    print("</td>");
                    print("<td>");
                        
                        $css->input("text", "TxtEncargadoEPS", "form-control", "TxtEncargadoEPS", "", ($DatosActa["EncargadoEPS"]), "Encargado EPS", "off", "", "onchange=EditeActaConciliacion(`$idActaConciliacion`,`TxtEncargadoEPS`,`EncargadoEPS`)");
                    
                        //print(utf8_decode($DatosActa["EncargadoEPS"]));
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:18px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td>");
                        print("<strong>Fecha Inicial:</strong>");
                    print("</td>");
                    print("<td>");
                        $css->input("date", "TxtFechaInicialActaConciliacion", "form-control", "TxtFechaInicialActaConciliacion", "", ($DatosActa["FechaInicial"]), "Fecha Inicial", "off", "", "onchange=EditeActaConciliacion(`$idActaConciliacion`,`TxtFechaInicialActaConciliacion`,`FechaInicial`)","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:18px;border-left-style:double;border-bottom-style:double;border-right-style:double;border-width:5px;>");
                    print("<td>");
                        print("<strong>Fecha Final:</strong>");
                    print("</td>");
                    print("<td>");
                        $css->input("date", "TxtFechaCorteConciliacion", "form-control", "TxtFechaCorteConciliacion", "", ($DatosActa["FechaCorte"]), "Encargado EPS", "off", "", "onchange=EditeActaConciliacion(`$idActaConciliacion`,`TxtFechaCorteConciliacion`,`FechaCorte`)","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    
                        //print(utf8_decode($DatosActa["FechaCorte"]));
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    print("<td colspan=3>");
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    print("<td colspan=2>");
                        print("<strong>Total Cuenta por pagar ASMET SALUD al corte:</strong>");
                    print("</td>");
                    print("<td style=font-size:18px;border-style:solid;border-width:3px;border-color:black;>");
                        
                        $css->input("hidden", "ACValorSegunEPS", "form-control", "ACValorSegunEPS", "", ($ValorSegunEPS), "Valor EPS", "off", "", "");
                        print("<span id='spValorSegunEPS'>".number_format($ValorSegunEPS)."</span>");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    print("<td colspan=2>");
                        print("<strong>Total Cuenta por Pagar del proveedor  al corte:</strong>");
                    print("</td>");
                    print("<td style=font-size:18px;border-style:solid;border-width:3px;border-color:black;>");
                        $css->input("hidden", "ACValorSegunIPS", "form-control", "ACValorSegunIPS", "", (round($ValorSegunIPS)), "Valor IPS", "off", "", "");
                        print("<span id='spValorSegunIPS'>".number_format($ValorSegunIPS)."</span>");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    print("<td colspan=2>");
                        print("<strong>Diferencia por conciliar:</strong>");
                    print("</td>");
                    print("<td style=font-size:18px;border-style:solid;border-width:3px;border-color:black;>");
                        $css->input("hidden", "ACDiferencia", "form-control", "ACDiferencia", "", ($Diferencia), "Diferencia", "off", "", "");
                        print("<span id='spACDiferencia'>".number_format(($Diferencia))."</span>");
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    print("<td colspan=2>");
                    
                    print("</td>");
                    print("<td >");
                        $css->CrearBotonEvento("BtnCalculosActaConciliaciones", "Calcular Diferencias", 1, "onclick", "CalcularDiferenciasActaConciliacion()", "azul", "");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    print("<td colspan=3>");
                        $css->CrearDiv("DivMensajesActaConciliacion", "", "center", 1, 1);
                        $css->CerrarDiv();
                    print("</td>");
                    
                print("</tr>");
                
                print("<tr style=font-size:16px;border-top-style:double;border-left-style:double;border-right-style:double;border-width:5px;>");
                    
                    print("<td colspan=3 style='text-align:center'>");
                        print("<strong>DETALLE DIFERENCIAS</strong>");
                    print("</td>");
                    
                print("</tr>");
                
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=text-align:rigth;font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        //$css->CrearBotonEvento("BtnEditarManualmenteDiferencias", "Ingresar Diferencias Manualmente", 1, "onclick", "MostrarCamposDiferencias()", "naranja", "");
                    print("</td>");
                    print("<td colspan=1 style=text-align:rigth;font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->CrearBotonEvento("BtnEditarManualmenteDiferencias", "Ingresar Diferencias Manualmente", 1, "onclick", "MostrarCamposDiferencias()", "naranja", "");
                    print("</td>");
                    
                print("</tr>");
                $TipoCaja="text";
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("1. Facturas canceladas por ASMET SALUD no descargadas por el proveedor");
                        
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACDiferenciaXPagos", "", "TxtACDiferenciaXPagos", "", $DatosActa["DiferenciaXPagos"], "", "off", "", "style='display:none;'");
                        $css->span("spACDiferenciaXPagos", "", "","style='display:block;'");  
                            print(number_format($DatosActa["DiferenciaXPagos"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("2. Relación de facturas no registradas por ASMET SALUD");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACFacturasIPSNoRelacionadasEPS", "", "TxtACFacturasIPSNoRelacionadasEPS", "", $DatosActa["FacturasNoRegistradasXEPS"], "", "off", "", "style='display:none;'");
                        $css->span("spACFacturasIPSNoRelacionadasEPS", "", "", "style='display:block;'");   
                            print(number_format($DatosActa["FacturasNoRegistradasXEPS"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("3. Glosas pendientes de conciliar");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACGlosasPendientesXConciliar", "", "TxtACGlosasPendientesXConciliar", "", $DatosActa["GlosasPendientesXConciliar"], "", "off", "", "style='display:none;'");
                        $css->span("spACGlosasPendientesXConciliar", "", "", "style='display:block;'");    
                            print(number_format($DatosActa["GlosasPendientesXConciliar"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("4. Facturas Devueltas");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACFacturasDevueltas", "", "TxtACFacturasDevueltas", "", $DatosActa["TotalDevoluciones"], "", "off", "", "style='display:none;'");
                        $css->span("spACFacturasDevueltas", "", "", "style='display:block;'");  
                            print(number_format($DatosActa["TotalDevoluciones"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("5. Impuestos no aplicados por la IPS");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACDiferenciaXImpuestos", "", "TxtACDiferenciaXImpuestos", "", $DatosActa["ImpuestosNoRelacionadosIPS"], "", "off", "", "style='display:none;'");
                        $css->span("spACDiferenciaXImpuestos", "", "", "style='display:block;'");
                            print(number_format($DatosActa["ImpuestosNoRelacionadosIPS"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("6. Descuentos financieros no merecidos en proceso de recobro RETEFUENTE ");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACDescuentoXRetefuente", "", "TxtACDescuentoXRetefuente", "", $DatosActa["RetefuenteNoMerecida"], "", "off", "", "style='display:none;'");
                        $css->span("spACDescuentoXRetefuente", "", "", "style='display:block;'");  
                            print(number_format($DatosActa["RetefuenteNoMerecida"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("7. Facturas registradas en ASMET SALUD que no estan en el listado del Proveedor");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACFacturasNoRelacionadasXIPS", "", "TxtACFacturasNoRelacionadasXIPS", "", $DatosActa["FacturasSinRelacionIPS"], "", "off", "", "style='display:none;'");
                        $css->span("spACFacturasNoRelacionadasXIPS", "", "", "style='display:block;'");
                            print(number_format($DatosActa["FacturasSinRelacionIPS"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("8. Retenciones de impuestos no procedentes (retefuente, ica, timbres)");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACRetencionesImpuestosNoProcedentes", "", "TxtACRetencionesImpuestosNoProcedentes", "", $DatosActa["RetencionesImpuestosNoProcedentes"], "", "off", "", "style='display:none;'");
                        $css->span("spACRetencionesImpuestosNoProcedentes", "", "", "style='display:block;'");   
                            print(number_format($DatosActa["RetencionesImpuestosNoProcedentes"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("9. Ajustes de Cartera en proceso (Notas credito IPS)");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACAjustesDeCartera", "", "TxtACAjustesDeCartera", "", $DatosActa["AjustesDeCartera"], "", "off", "", "style='display:none;'");
                        $css->span("spACAjustesDeCartera", "", "", "style='display:block;'");  
                            print(number_format($DatosActa["AjustesDeCartera"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("10. Facturas con diferencia en el Valor facturado");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACDiferenciaXValorFacturado", "", "TxtACDiferenciaXValorFacturado", "", $DatosActa["FacturasConValorDiferente"], "", "off", "", "style='display:none;'");
                        $css->span("spACDiferenciaXValorFacturado", "", "", "style='display:block;'");
                            print(number_format($DatosActa["FacturasConValorDiferente"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("11. Facturas presentadas por reajuste de UPC");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACDiferenciaXUPC", "", "TxtACDiferenciaXUPC", "", $DatosActa["FacturasConReajusteUPC"], "", "off", "", "style='display:none;'");
                        $css->span("spACDiferenciaXUPC", "", "", "style='display:block;'");   
                            print(number_format($DatosActa["FacturasConReajusteUPC"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("12. Glosas conciliadas pendientes de descargar por el proveedor");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACGlosasPendientesXDescargarIPS", "", "TxtACGlosasPendientesXDescargarIPS", "", $DatosActa["GlosasConciliadasPendientesDescargaIPS"], "", "off", "", "style='display:none;'");
                        $css->span("spACGlosasPendientesXDescargarIPS", "", "", "style='display:block;'"); 
                            print(number_format($DatosActa["GlosasConciliadasPendientesDescargaIPS"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("13. Anticipos pendientes de cruzar con facturas del proveedor");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACAnticiposPendientesXCruzar", "", "TxtACAnticiposPendientesXCruzar", "", $DatosActa["TotalAnticipos"], "", "off", "", "style='display:none;'");
                        $css->span("spACAnticiposPendientesXCruzar", "", "", "style='display:block;'"); 
                            print(number_format($DatosActa["TotalAnticipos"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("14. Descuentos y/o reconocimientos según LMA");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACDescuentosLMA", "", "TxtACDescuentosLMA", "", $DatosActa["DescuentosReconocimientosLMA"], "", "off", "", "style='display:none;'");
                        $css->span("spACDescuentosLMA", "", "", "style='display:block;'"); 
                            print(number_format($DatosActa["DescuentosReconocimientosLMA"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("15. Facturas pendientes de auditoría");
                    print("</td>");
                    print("<td style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->input("$TipoCaja", "TxtACPendientesAuditoria", "", "TxtACPendientesAuditoria", "", $DatosActa["FacturasPendienteAuditoria"], "", "off", "", "style='display:none;'");
                        $css->span("spACPendientesAuditoria", "", "", "style='display:block;'");    
                            print(number_format($DatosActa["FacturasPendienteAuditoria"]));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        
                    print("</td>");
                    print("<td colspan=1 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        $css->CrearDiv("DivBotonActualizarManual", "", "left", 0, 1);
                            $css->CrearBotonEvento("BtnActualizarDiferenciasManualmente", "Actualizar", 1, "onclick", "ActualizarDiferenciasManualmente()", "rojo", "");
                        $css->CerrarDiv();    
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("<strong>TOTAL DIFERENCIAS</strong>");
                    print("</td>");
                    print("<td style=font-size:18px;border-style:solid;border-width:3px;border-color:black;>");
                        $css->input("hidden", "TxtACTotalDiferencias", "", "TxtACTotalDiferencias", "", ($DatosActa["Diferencia"]), "", "off", "", "");
                        $css->span("spACTotalDiferencias", "", "", "");   
                            print(number_format(abs($DatosActa["Diferencia"])));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                print("<tr style=font-size:16px;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=3 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        
                    print("</td>");
                print("</tr>");
                
                print("<tr style=font-size:16px;border-bottom-style:double;border-left-style:double;border-right-style:double;border-width:5px;>");
                    print("<td colspan=2 style=font-size:16px;border-style:solid;border-width:1px;border-color:black;>");
                        print("<strong>SALDO CONCILIADO PARA PAGO</strong>");
                    print("</td>");
                    print("<td style=font-size:18px;border-style:solid;border-width:3px;border-color:black;>");
                        //$TipoCaja="text";
                        $css->input("hidden", "TxtACSaldoAcuerdoPago", "", "TxtACSaldoAcuerdoPago", "", "$SaldoConciliadoParaPago", "", "off", "", "");
                        $css->span("spACSaldoAcuerdoPago", "", "", "");    
                            print(number_format($SaldoConciliadoParaPago));
                        $css->Cspan();
                    print("</td>");
                print("</tr>");
                
                print("</tr>");
                print("<tr >");
                    print("<td>");
                        
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    print("<td colspan=3 style=font-size:16px;>");
                        print("<span style='text-decoration: underline;cursor:pointer;'><strong >RESULTADOS Y COMPROMISOS:</strong></span>");
                    print("</td>");
                    
                print("</tr>");
                
                print("<tr>");
                    print("<td colspan=3 style=font-size:16px;>");
                        $NombreCaja="TxtCompromisoNuevo";  
                        $css->textarea($NombreCaja, "form-control", $NombreCaja, "", "Nuevo", "Nuevo", "onchange=AgregueCompromiso()","style='overflow: hidden;'");
                            
                        $css->Ctextarea();
                        //$css->input("text", "TxtCompromisoNuevo", "form-control", "TxtCompromisoNuevo", "", "", "+", "off", "", "onchange=AgregueCompromiso()", "");
                        $css->div("DivCompromisosActaConciliacion", "", "", "", "", "", "");
                            $Consulta=$obCon->ConsultarTabla("actas_conciliaciones_resultados_compromisos", "WHERE idActaConciliacion='$idActaConciliacion'");
                            while($DatosCompromisos=$obCon->FetchAssoc($Consulta)){
                                $idCompromiso=$DatosCompromisos["ID"];
                                $NombreCaja="TxtCompromiso_".$idCompromiso;  
                                $css->textarea($NombreCaja, "form-control", $NombreCaja, "", "", "", "onchange=EditeCompromiso($idCompromiso)","style='overflow: hidden;'");
                                    print($DatosCompromisos["ResultadoCompromiso"]);
                                $css->Ctextarea();
                                //$css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosCompromisos["ResultadoCompromiso"], "", "off", "", "onchange=EditeCompromiso($idCompromiso)", "", "style='height: auto;'");
                                    
                            }
                        $css->Cdiv();
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    print("<td colspan=1 style=font-size:16px;>");
                        print("<strong>Fecha de Firma: </strong>");
                        $css->input("date", "TxtFechaDeFirma", "", "TxtFechaDeFirma", "", ($DatosActa["FechaFirma"]), "Encargado EPS", "off", "", "onchange=EditeActaConciliacion(`$idActaConciliacion`,`TxtFechaDeFirma`,`FechaFirma`);DibujeConstanciaFirmaActaConciliacion();","style='line-height: 15px;'"."max=".date("Y-m-d"));
                    
                    print("</td>");
                    print("<td colspan=2 style=font-size:16px;>");
                        print("<strong>Ciudad de Firma: </strong>");
                        $css->input("text", "TxtCiudadDeFirma", "", "TxtCiudadDeFirma", "", $DatosActa["CiudadFirma"], "Ciudad", "off", "", "onchange=EditeActaConciliacion(`$idActaConciliacion`,`TxtCiudadDeFirma`,`CiudadFirma`);DibujeConstanciaFirmaActaConciliacion();");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    print("<td colspan=3 style=font-size:16px;>");
                        $css->CrearDiv("DivConstanciaFirma", "","", 1, 1);
                            $DatosFechaFirma= explode("-", $DatosActa["FechaFirma"]);  
                            $dia=$obNumLetra->convertir($DatosFechaFirma[2]);
                            $mes=$obNumLetra->meses($DatosFechaFirma[1]);
                            $anio=$obNumLetra->convertir($DatosFechaFirma[0]);
                            print("Para constancia, se firma en <strong>".($DatosActa["CiudadFirma"])."</strong>");
                            
                            print(", a los $dia ($DatosFechaFirma[2]) días del mes de $mes del $anio ($DatosFechaFirma[0]) en dos originales:");
                        $css->CerrarDiv();
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    print("<td colspan=3 style=font-size:16px;>");
                        print("<strong>AGREGAR FIRMAS:</strong>");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    
                    print("<td>");
                        print("<strong>DEL BANCO DE FIRMAS AGS:</strong>");
                    print("</td>");
                    print("<td colspan=2>");
                        print("<strong>PERSONALIZADA:</strong>");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    
                    print("<td>");
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
                    print("<td colspan=2>");
                        $css->input("text", "TxtNombreFirmaActa", "form-control", "TxtNombreFirmaActa", "", "", "Nombre", "off", "", "");
                        $css->input("text", "TxtCargoFirmaActa", "form-control", "TxtCargoFirmaActa", "", "", "Cargo", "off", "", "");
                        $css->input("text", "TxtEmpresaFirmaActa", "form-control", "TxtEmpresaFirmaActa", "", "", "Empresa", "off", "", "");
                        $css->CrearBotonEvento("btnAgregarFirma2", "Agregar", 1, "onclick", "AgregueFirma(2)", "naranja", "");
                    print("</td>");
                print("</tr>");
                
                print("<tr>");
                    print("<td colspan=3>");
                        $css->CrearDiv("DivFirmasActaConciliacion", "", "left", 1, 1);
                        
                        $css->CerrarDiv();
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    print("<td colspan=3>");
                        print("<strong>Opciones del Documento:<strong>");
                    print("</td>");
                print("</tr>");
                print("<tr>");
                
                print("<tr>");
                    print("<td>");
                        print("<strong>Imprimir<strong>");
                    print("</td>");
                    print("<td>");
                        print("<strong>Soporte del Acta<strong>");
                    print("</td>");
                    
                    print("<td>");
                        print("<strong>Cerrar Acta<strong>");
                    print("</td>");
                print("</tr>");
                print("<tr>");
                    
                    print("<td>");
                        $Ruta="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=36&idActaConciliacion=$idActaConciliacion";
                        print("<a href='$Ruta' target='_BLANK'><button class='btn btn-success'>Imprimir PDF</button></a>");
                        $Ruta="../../general/procesadores/GeneradorCSV.process.php?Opcion=3&idActaConciliacion=$idActaConciliacion&db=$db";
                        print(" <a href='$Ruta' target='_BLANK'><button class='btn btn-primary'>Anexo Acta Cruce</button></a>");
                        $Ruta="../../general/procesadores/GeneradorCSV.process.php?Opcion=4&idActaConciliacion=$idActaConciliacion&db=$db";
                        print(" <a href='$Ruta' target='_BLANK'><button class='btn btn-success'>Anexo Acta Completa</button></a>");
                        $Ruta="../../general/procesadores/GeneradorCSV.process.php?Opcion=5&idActaConciliacion=$idActaConciliacion&db=$db";
                        print(" <a href='$Ruta' target='_BLANK'><button class='btn btn-warning'>Facturas Fuera del Rango</button></a>");
                    print("</td>");
                    print("<td>");        
                       $css->input("file", "UpSoporteActaConciliacionCierre", "", "UpSoporteActaConciliacionCierre", "Soporte Acta", "Subir Acta Firmada", "Subir Acta Firmada", "off", "", "");
                    print("</td>");
                    print("<td>");
        
                        $css->CrearBotonEvento("btnGuardarConciliacion", "Cerrar Acta", 1, "onclick", "CerrarActaConciliacion()", "rojo", "");
                        $css->ProgressBar("PgProgresoCruce", "LyProgresoCruce", "", 0, 0, 100, 0, "100%", "", "");
                        $css->ProgressBar("PgProgresoNoCruce", "LyProgresoNoCruce", "", 0, 0, 100, 0, "100%", "", "");
                        $css->CrearDiv("DivMensajesCerrarActa", "", "center", 1, 1);
                        
                        $css->CerrarDiv();
                    print("</td>");
                    
                print("</tr>");
                
            $css->CerrarTabla();
            $css->CerrarDiv();
        break;//fin caso 28
        
        case 29://Dibuja los compromisos de un acta de conciliacion
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            
            if($idActaConciliacion==''){
                $css->CrearTitulo("Por favor Seleccione un Acta", "rojo");
                exit();
            }
            
            $Consulta=$obCon->ConsultarTabla("actas_conciliaciones_resultados_compromisos", "WHERE idActaConciliacion='$idActaConciliacion'");
            
            while($DatosCompromisos=$obCon->FetchAssoc($Consulta)){
                $idCompromiso=$DatosCompromisos["ID"];
                $NombreCaja="TxtCompromiso_".$idCompromiso; 
                $css->textarea($NombreCaja, "form-control", $NombreCaja, "", "", "", "onchange=EditeCompromiso($idCompromiso)");
                    print($DatosCompromisos["ResultadoCompromiso"]);
                $css->Ctextarea();
                //$css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosCompromisos["ResultadoCompromiso"], "", "off", "", "onchange=EditeCompromiso($idCompromiso)","", "style='height: auto;'");
                
            }  
            
        break;// fin caso 29    
        
        case 30://Dibuja las firmas del acta
            
            
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            
            if($idActaConciliacion==''){
                $css->CrearTitulo("Por favor Seleccione un Acta", "rojo");
                exit();
            }
            
            $Consulta=$obCon->ConsultarTabla("actas_conciliaciones_firmas", "WHERE idActaConciliacion='$idActaConciliacion'");
            $i=0;
            while($DatosFirmas=$obCon->FetchAssoc($Consulta)){
                
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    print("<br><br><hr></hr>");
                    $idFirma=$DatosFirmas["ID"];
                    $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`1`,`$idFirma`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                    $css->Cli();
                    $NombreCaja="TxtFirmaNombreActa_".$idFirma; 
                    $css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosFirmas["Nombre"], "Nombre", "off", "", "onchange=EditeFirmaActaConciliacion(`$idFirma`,`$NombreCaja`,`Nombre`);");
                    $NombreCaja="TxtFirmaCargoActa_".$idFirma; 
                    $css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosFirmas["Cargo"], "Cargo", "off", "", "onchange=EditeFirmaActaConciliacion(`$idFirma`,`$NombreCaja`,`Cargo`);");
                    $NombreCaja="TxtFirmaEmpresaActa_".$idFirma; 
                    $css->input("text", $NombreCaja, "form-control", $NombreCaja, "", $DatosFirmas["Empresa"], "Empresa", "off", "", "onchange=EditeFirmaActaConciliacion(`$idFirma`,`$NombreCaja`,`Empresa`);");
                    
                $css->CerrarDiv();
                
            }  
            
        break;// fin caso 30
        
        case 31: // dibuja la constancia de la firma
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion);
            $DatosFechaFirma= explode("-", $DatosActa["FechaFirma"]);  
            $dia=$obNumLetra->convertir($DatosFechaFirma[2]);
            //$dia=$obNumLetra->convertir(31);
            $mes=$obNumLetra->meses($DatosFechaFirma[1]);
            $anio=$obNumLetra->convertir($DatosFechaFirma[0]);
            print("Para constancia, se firma en <strong>".($DatosActa["CiudadFirma"])."</strong>");

            print(", a los $dia ($DatosFechaFirma[2]) días del mes de $mes del $anio ($DatosFechaFirma[0]) en dos originales:");
        break;// fin caso 31
        
        case 32:// dibuje los contratos en un acta
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $sql="SELECT NumeroContrato FROM actas_conciliaciones_contratos WHERE idActaConciliacion='$idActaConciliacion'";
            $Consulta=$obCon->Query($sql);
            while($Contratos=$obCon->FetchAssoc($Consulta)){
                print("<br>".$Contratos["NumeroContrato"]." ");
                $css->li("", "fa  fa-remove", "", "onclick='EliminarItem(`2`,`$Contratos[NumeroContrato]`)' style=font-size:16px;cursor:pointer;text-align:center;color:red");
                    //print(" ".$Contratos["NumeroContrato"]);
                $css->Cli();
            }
            
        break;//Fin caso 32    
        
        case 33: //Dibuja los totales del cruce
            $TipoNegociacion=$obCon->normalizar($_REQUEST["CmbTipoNegociacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];

            $sql="SELECT SUM(ValorSegunEPS) AS TotalEPS, SUM(ValorSegunIPS) AS TotalIPS, 
                SUM(TotalAPagar) AS TotalConciliaciones, 
                SUM(ValorMenosImpuestos) AS Valormenosimpuesto,
                SUM(TotalPagos) AS TotalPagos,
                SUM(TotalAnticipos) AS TotalAnticipos,
                SUM(TotalGlosaFavor) AS TotalGlosaFavor,
                SUM(GlosaXConciliar) AS GlosaXConciliar,
                SUM(OtrosDescuentos) AS OtrosDescuentos,
                SUM(TotalCopagos) AS TotalCopagos,
                SUM(TotalDevoluciones) AS TotalDevoluciones,
                SUM(DescuentoPGP) AS DescuentoPGP,
                SUM(ConciliacionesAFavorEPS) AS ConciliacionesAFavorEPS,
                SUM(ConciliacionesAFavorIPS) AS ConciliacionesAFavorIPS,
                (SELECT COUNT(*) FROM $db.vista_cruce_cartera_asmet WHERE Estado=1) as NumeroConciliaciones
                FROM $db.hoja_de_trabajo WHERE TipoNegociacion='$TipoNegociacion';             
                    ";
            $row=$obCon->FetchAssoc($obCon->Query($sql));
            $TotalEPS=$row['TotalEPS'];
            $sql="SELECT SUM(ValorTotalpagar) as Total FROM $db.carteracargadaips WHERE TipoNegociacion='$TipoNegociacion'";
            $DatosIPS= $obCon->FetchAssoc($obCon->Query($sql));
            $TotalIPS=round($DatosIPS['Total']);
            $TotalConciliaciones=$row['TotalConciliaciones'];
            $NumeroConciliaciones=$row['NumeroConciliaciones'];
            $Valormenosimpuesto = $row['Valormenosimpuesto'];           
            $TotalPagos= $row['TotalPagos'];
            $TotalAnticipos= $row['TotalAnticipos'];
            $TotalGlosaFavor= $row['TotalGlosaFavor'];
            $GlosaXConciliar= $row['GlosaXConciliar'];
            $OtrosDescuentos= $row['OtrosDescuentos'];
            $TotalCopagos= $row['TotalCopagos'];
            $TotalDevoluciones= $row['TotalDevoluciones'];
            $DescuentoPGP= $row['DescuentoPGP'];
            $ConciliacionesAFavorEPS= $row['ConciliacionesAFavorEPS'];
            $ConciliacionesAFavorIPS= $row['ConciliacionesAFavorIPS'];

            $TotalPendientesDevoluciones=$obCon->SumeColumna("$db.vista_pendientes", "Total", "Radicados", "Devoluciones");
            $TotalPendientesCopagos=$obCon->SumeColumna("$db.vista_pendientes", "Total", "Radicados", "Copagos");
            $TotalPendientesRadicados=$obCon->SumeColumna("$db.vista_pendientes", "Total", "Radicados", "Radicados");
            $TotalPendientesNotas=$obCon->SumeColumna("$db.vista_pendientes", "Total", "Radicados", "Notas");

            $css->CrearTabla();

            $css->FilaTabla(16);
            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Total Según EPS:</strong> <h4 style=color:red>". number_format($TotalEPS)."</h4>");
            print("</td>");
            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Total Según IPS:</strong> <h4 style=color:red>". number_format($TotalIPS)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Diferencia:</strong> <h4 style=color:red>". number_format($TotalEPS-$TotalIPS)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");

                    $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`1`,`21`)", "style=cursor:pointer;");

                      print("<strong>Pendientes Radicados:</strong> <h4 style=color:red>". number_format($TotalPendientesRadicados)."</h4>");
                   $css->CerrarDiv();


            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`1`,`22`)", "style=cursor:pointer;");

                    print("<strong>Pendientes Devoluciones:</strong> <h4 style=color:red>". number_format($TotalPendientesDevoluciones)."</h4>");
                $css->CerrarDiv();

            print("</td>");


            print("<td colspan=1 style='text-align:center'>");
                $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`1`,`23`)", "style=cursor:pointer;");

                    print("<strong>Pendientes Copagos:</strong> <h4 style=color:red>". number_format($TotalPendientesCopagos)."</h4>");
                $css->CerrarDiv();

            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                $css->div("", "", "", "", "", "onclick=VerHistorialFactura(`1`,`24`)", "style=cursor:pointer;");

                    print("<strong>Pendientes Notas Crédito:</strong> <h4 style=color:red>". number_format($TotalPendientesNotas)."</h4>");
                $css->CerrarDiv();

            print("</td>");
            //$sql="SELECT SUM(ValorImpuestosCalculados) AS TotalRetencionesDevueltas FROM $db.vista_facturas_sr_eps_2 WHERE Saldo<0";
            //$Consulta2=$obCon->Query($sql);
            //$DatosSaldosDevoluciones=$obCon->FetchAssoc($Consulta2);
            //$TotalRetencionesDevolucionesNoRelacionadas=$DatosSaldosDevoluciones["TotalRetencionesDevueltas"];
            $TotalRetencionesDevolucionesNoRelacionadas=0;
            //print("<td colspan=1 style='text-align:center'>");
             //   print("<strong>Retenciones Pagadas en Devoluciones:</strong> <h4 style=color:red>". number_format($TotalRetencionesDevolucionesNoRelacionadas)."</h4>");
            //print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Posible Valor a Liquidar:</strong> <h4 style=color:red>". number_format($TotalEPS-$TotalPendientesNotas-$TotalPendientesCopagos-$TotalPendientesDevoluciones-$TotalPendientesRadicados-$TotalRetencionesDevolucionesNoRelacionadas)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Facturas Conciliadas:</strong> <h4 style=color:red>". number_format($NumeroConciliaciones)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Total Conciliado:</strong> <h4 style=color:red>". number_format($TotalConciliaciones)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Total Conciliado - Pendientes:</strong> <h4 style=color:red>". number_format($TotalConciliaciones-$TotalPendientesNotas-$TotalPendientesCopagos-$TotalPendientesDevoluciones-$TotalPendientesRadicados-$TotalRetencionesDevolucionesNoRelacionadas)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Total Pagos:</strong> <h4 style=color:red>". number_format($TotalPagos)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Anticipos:</strong> <h4 style=color:red>". number_format($TotalAnticipos)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Glosas A Favor:</strong> <h4 style=color:red>". number_format($TotalGlosaFavor)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Glosas X Conciliar:</strong> <h4 style=color:red>". number_format($GlosaXConciliar)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Otros Descuentos:</strong> <h4 style=color:red>". number_format($OtrosDescuentos)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Copagos:</strong> <h4 style=color:red>". number_format($TotalCopagos)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Devoluciones:</strong> <h4 style=color:red>". number_format($TotalDevoluciones)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Descuentos PGP:</strong> <h4 style=color:red>". number_format($DescuentoPGP)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Conciliaciones a Favor de la EPS:</strong> <h4 style=color:red>". number_format($ConciliacionesAFavorEPS)."</h4>");
            print("</td>");

            print("<td colspan=1 style='text-align:center'>");
                print("<strong>Conciliaciones a Favor de la IPS:</strong> <h4 style=color:red>". number_format($ConciliacionesAFavorIPS)."</h4>");
            print("</td>");

            $css->CierraFilaTabla();

            $css->CerrarTabla();
        break;// fin caso 33
    
        case 34://DIbuja los contratos disponibles en el cruce
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            //$css->div("DivContratosDisponiblesActaLiquidacion", "col-md-6", "", "", "", "", "");
            $css->CrearTitulo("<strong>Contratos Disponibles:</strong>","verde");                
            $css->CrearTabla();    

                $sql="SELECT DISTINCT NumeroContrato FROM $db.vista_cruce_cartera_eps ";
               //print($sql);
                $Consulta=$obCon->Query($sql);
                $i=0;
                while($Contratos=$obCon->FetchAssoc($Consulta)){
                    $i++;
                    print("<tr>");
                        $idContrato= str_replace(" ", "", $Contratos["NumeroContrato"]);
                        $sql="SELECT * FROM `contratos` WHERE REPLACE(`ContratoEquivalente`,' ','')= '$idContrato' LIMIT 1";
                        //$sql="SELECT * FROM `contratos` WHERE `ContratoEquivalente` like trim('".$Contratos["NumeroContrato"]."') LIMIT 1";
                        //print($sql);
                        $DatosContratos=$obCon->FetchAssoc($obCon->Query($sql));
                        //print_r($DatosContratos);
                        print("<td>");
                        print("<strong>".$Contratos["NumeroContrato"]."</strong> ");
                        print("</td>");
                        
                        if($DatosContratos["ID"]==""){   
                            print("<td>");
                            $css->CrearBotonEvento("btnCrearContrato", "Crear Contrato", 1, "onclick", "AbreFormularioCrearContrato(`".$Contratos["NumeroContrato"]."`)", "azul", "style='width:150px;'");
                            print("</td>");
                            print("<td>");
                            $css->select("CmbContratoExistente_$i", "selector", "CmbContratoExistente", "", "", "", "style=width:600px;");
                                $css->option("", "", "", "", "", "");
                                    print("Buscar contrato para asociar");
                                $css->Coption();
                            $css->Cselect();
                            print("</td>");
                            print("<td>");
                            $css->CrearBotonEvento("btnAsociarContrato", "Asociar Contrato", 1, "onclick", "AsociarContratoEquivalente(`".$Contratos["NumeroContrato"]."`,`CmbContratoExistente_$i`)", "verde", "style='width:150px;'");
                            print("</td>");
                        }else{
                            print("<td colspan =3>");
                                print("<strong>Clasificación: </strong>".$DatosContratos["ClasificacionContrato"]."; <strong>Tipo: </strong>".$DatosContratos["TipoContrato"]."; <strong>Valor: </strong> ".number_format($DatosContratos["ValorContrato"])."; <strong>Inicio: </strong> ".$DatosContratos["FechaInicioContrato"]."; <strong>Fin: </strong> ".$DatosContratos["FechaFinalContrato"]);
                            print("</td>");
                        }
                        
                    print("</tr>");
                }
                $css->CerrarTabla();
            //print("</div>");
        break;//Fin caso 34    
    
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>