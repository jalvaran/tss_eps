<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../clases/revision_facturas.class.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new RevisionFacturas($idUser);
    
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
            $Condicional=" WHERE TotalRepetidas>1 ";
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional=" AND NumeroFactura like '%$Busqueda%' ";
                }
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $sql="SELECT COLUMN_NAME 
                    FROM information_schema.COLUMNS 
                    WHERE 
                        TABLE_SCHEMA = '$db' 
                    AND TABLE_NAME = 'facturas_para_revision_por_ceros_izquierda' 
                    AND COLUMN_NAME = 'ID' ";
            
            $DatosValidacion=$obCon->FetchAssoc($obCon->Query($sql));
            if($DatosValidacion["COLUMN_NAME"]==''){
                $css->CrearTitulo("<strong>Por favor crea la tabla con facturas con posibles problemas</strong>");
                exit();
            }
            
            $statement=" $db.`facturas_para_revision_por_ceros_izquierda` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 50;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num` FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $Limit");
            
            $css->CrearTabla();
            
            
                $css->FilaTabla(16);
                    print("<td style='text-align:center'>");
                        print("<strong>Registros:</strong> <h4 style=color:green>". number_format($ResultadosTotales)."</h4>");
                    print("</td>");
                    
                    print("<td>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$db','facturas_para_revision_por_ceros_izquierda','')", "verde", "");
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
                            print('<span class="input-group-addon" onclick=CambiePaginaFacturasCerosIzquierda('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-left"></i></span>');
                            }
                            $FuncionJS="onchange=CambiePaginaFacturasCerosIzquierda();";
                            $css->select("CmbPageFacturasCeroIzquierda", "form-control", "CmbPageFacturasCeroIzquierda", "", "", $FuncionJS, "");
                            
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
                            print('<span class="input-group-addon" onclick=CambiePaginaFacturasCerosIzquierda('.$NumPage1.') style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                            print("<div>");
                            print("</td>");
                            
                            
                           $css->CierraFilaTabla(); 
                        }
                      
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>TipoOperacion</strong>", 1);
                    $css->ColTabla("<strong>NumeroOperacion</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Factura</strong>", 1);
                    $css->ColTabla("<strong>Número de Factura</strong>", 1);
                    $css->ColTabla("<strong>Número del Radicado</strong>", 1);
                    $css->ColTabla("<strong>MesServicio</strong>", 1);
                    $css->ColTabla("<strong>Valor</strong>", 1);
                    $css->ColTabla("<strong>Total Repetidas</strong>", 1);
                    $css->ColTabla("<strong>Descripcion</strong>", 1);
                $css->CierraFilaTabla();
                
                
                while($DatosFactura=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idItem=$DatosFactura["ID"];
                        $NumeroFactura=$DatosFactura["NumeroFactura"];
                        $css->ColTabla($DatosFactura["TipoOperacion"], 1);                                                
                        $css->ColTabla($DatosFactura["NumeroOperacion"], 1);
                        $css->ColTabla($DatosFactura["FechaFactura"], 1);                        
                        $css->ColTabla($DatosFactura["NumeroFactura"], 1);
                        $css->ColTabla($DatosFactura["NumeroRadicado"], 1);
                        $css->ColTabla($DatosFactura["MesServicio"], 1);
                        $css->ColTabla(number_format($DatosFactura["ValorOriginal"]), 1,'R');
                        $css->ColTabla($DatosFactura["TotalRepetidas"], 1);
                        $css->ColTabla($DatosFactura["Descripcion"], 1);
                        /*
                        print("<td style='text-align:center'>");
                            print('<a id="BtnVer_'.$idItem.'" href="#" onclick="DibujeFactura('.$idItem.');"><i class="fa fa-fw fa-eye"></i></a>');
                        print("</td>");
                         * 
                         */
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break; //Fin caso 1
        
         
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>