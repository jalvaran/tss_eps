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
                    print("<td colspan=3 style='text-align:center'>");
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    
                    print("<td colspan='2' style='text-align:center'>");
                        $st1= urlencode($st_reporte);
                        //$css->CrearImageLink("ProcesadoresJS/GeneradorCSVReportesCartera.php?Opcion=1&sp=$Separador&st=$st1", "../images/csv.png", "_blank", 50, 50);

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
                        $css->ColTabla($DatosFactura["NumeroContrato"], 1);
                        $css->ColTabla($DatosFactura["NumeroFactura"], 1);
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
        
        case 2: //Dibuja las facturas que tiene la eps pero no la EPS
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
                    print("<td colspan=3 style='text-align:center'>");
                        print("<strong>Total:</strong> <h4 style=color:red>". number_format($Total)."</h4>");
                    print("</td>");
                    
                    print("<td colspan='2' style='text-align:center'>");
                        $st1= urlencode($st_reporte);
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
                    $css->ColTabla("<strong>Valor Menos Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Total Pagos</strong>", 1);
                    $css->ColTabla("<strong>Total Anticipos</strong>", 1);
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
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>