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
            $query = "SELECT COUNT(*) as `num`,SUM(ValorDocumento) AS Total FROM {$statement}";
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
                           
                            print('<span id="BtnEditar_'.$idItem.'"  onclick="EditarFactura('.$NumeroFactura.');" style=cursor:pointer> '.$DatosFactura["NumeroFactura"].' <i class="fa fa-fw fa-edit"></i></span>');
                        print("</td>");
                        
                        
                        $css->ColTabla($DatosFactura["FechaFactura"], 1);
                        $css->ColTabla($DatosFactura["NumeroRadicado"], 1);
                        $css->ColTabla($DatosFactura["FechaRadicado"], 1);
                        $css->ColTabla(number_format($DatosFactura["ValorDocumento"]), 1,'R');
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
        
        case 3: //Dibuja las facturas que tienen las dos
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
            
            $statement=" $db.`vista_cruce_cartera_asmet` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 50;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorDocumento) AS Total FROM {$statement}";
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
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    
                    print("<td colspan='2' style='text-align:center'>");
                        $st1= urlencode($st_reporte);
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','vista_cruce_cartera_asmet','')", "verde", "");
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
                    $css->ColTabla("<strong>Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Valor Menos Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Total Pagos</strong>", 1);
                    $css->ColTabla("<strong>Total Anticipos</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa Inicial</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa A Favor</strong>", 1);
                    $css->ColTabla("<strong>Total Glosa en Contra</strong>", 1);
                    $css->ColTabla("<strong>Glosa por Conciliar</strong>", 1);
                    $css->ColTabla("<strong>Otros Descuentos</strong>", 1);
                    $css->ColTabla("<strong>Saldo Según EPS</strong>", 1);
                    $css->ColTabla("<strong>Saldo Según IPS</strong>", 1);
                    $css->ColTabla("<strong>Diferencia</strong>", 1);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $NumeroFactura=$DatosFactura["NumeroFactura"];
                        $css->ColTabla($DatosFactura["NumeroContrato"], 1);
                        $css->ColTabla($DatosFactura["NumeroFactura"], 1);
                        $css->ColTabla($DatosFactura["FechaFactura"], 1);
                        $css->ColTabla($DatosFactura["NumeroRadicado"], 1);
                        $css->ColTabla($DatosFactura["FechaRadicado"], 1);
                        $css->ColTabla(number_format($DatosFactura["ValorDocumento"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["Impuestos"]), 1,'R');
                        $css->ColTabla(number_format($DatosFactura["ValorMenosImpuestos"]), 1,'R');
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura('$NumeroFactura',4)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalPagos"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura('$NumeroFactura',5)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalAnticipos"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura('$NumeroFactura',6)", "style=cursor:pointer;");
                            
                                print(number_format($DatosFactura["TotalGlosaInicial"]));
                            $css->CerrarDiv();
                            
                        print("</td>");
                        
                        print("<td>");
                            $css->div("", "", "", "", "", "onclick=VerHistorialFactura('$NumeroFactura',6)", "style=cursor:pointer;");
                            
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
                        print("<td style='text-align:center'>");
                            print('<a id="BtnVer_'.$idItem.'" href="#" onclick="DibujeFactura('.$idItem.');"><i class="fa fa-fw fa-eye"></i></a>');
                        print("</td>");
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 3
        
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
        
        case 5://Muestra los pagos de una factura
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Anticipos Realizados a la Factura No. $NumeroFactura</strong>", 12,'C');
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
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>