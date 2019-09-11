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
        case 1: //dibuje el listado de actas de conciliacion
            //$CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
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
                    $Condicional=" WHERE ID = '$Busqueda%' or NIT_IPS='$Busqueda' or RazonSocialIPS like '%$Busqueda%' ";
                }
                
            }
            
            $dbPrincipal=DB;
            $statement=" `actas_conciliaciones` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 20;
            $startpoint = ($NumPage * $limit) - $limit;
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num`,SUM(ValorSegunEPS) AS TotalEPS,SUM(ValorSegunIPS) AS TotalIPS,SUM(Diferencia) AS Diferencia FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            $TotalEPS=$row['TotalEPS'];
            $TotalIPS=$row['TotalIPS'];
            $Diferencia=$row['Diferencia'];
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
                        print("<strong>Total Según EPS:</strong> <h4 style=color:red>". number_format($TotalEPS)."</h4>");
                    print("</td>");
                    print("<td colspan=3 style='text-align:center'>");
                        print("<strong>Total Según IPS:</strong> <h4 style=color:red>". number_format($TotalIPS)."</h4>");
                    print("</td>");
                    print("<td colspan=3 style='text-align:center'>");
                        print("<strong>Total Diferencias:</strong> <h4 style=color:red>". number_format($Diferencia)."</h4>");
                    print("</td>");
                    print("<td>");
                        $css->CrearBotonEvento("BtnExportarExcelCruce", "Exportar", 1, "onclick", "ExportarExcel('$dbPrincipal','actas_conciliaciones','$st_reporte')", "verde", "");
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
                            print("<div>");
                            print("</td>");
                            
                            
                           $css->CierraFilaTabla(); 
                        }
                      
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                    $css->ColTabla("<strong>ID</strong>", 1);
                    $css->ColTabla("<strong>Fecha Inicial</strong>", 1);
                    $css->ColTabla("<strong>Fecha Final</strong>", 1);
                    $css->ColTabla("<strong>Razon Social de la IPS</strong>", 1);
                    $css->ColTabla("<strong>NIT IPS</strong>", 1);
                    $css->ColTabla("<strong>Representante Legal</strong>", 1);
                    $css->ColTabla("<strong>Departamento</strong>", 1);
                    $css->ColTabla("<strong>Encargado de la EPS</strong>", 1);
                    $css->ColTabla("<strong>Valor Según la EPS</strong>", 1);
                    $css->ColTabla("<strong>Valor Según la IPS</strong>", 1);
                    $css->ColTabla("<strong>Diferencia</strong>", 1);
                    $css->ColTabla("<strong>Saldo Conciliado para Pago</strong>", 1);                    
                    $css->ColTabla("<strong>Estado</strong>", 1);
                    $css->ColTabla("<strong>Mes de Servicio Inicial</strong>", 1);
                    $css->ColTabla("<strong>Mes de Servicio Final</strong>", 1);
                    $css->ColTabla("<strong>Usuario Creador</strong>", 1);
                    $css->ColTabla("<strong>Fecha de Registro</strong>", 1);
                    $css->ColTabla("<strong>Última Actualización</strong>", 1);
                    $css->ColTabla("<strong>Usuario que Actualiza</strong>", 1);
                $css->CierraFilaTabla();
                
                
                while($DatosConciliacion=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(14);
                        $idActaConciliacion=$DatosConciliacion["ID"];
                        $NIT_IPS=$DatosConciliacion["NIT_IPS"];
                        print("<td>");
                            $Ruta="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=36&idActaConciliacion=$idActaConciliacion";
                            print("<a href='$Ruta' target='_BLANK'><button class='form-control btn btn-success'>Imprimir PDF</button></a>");
                            if($DatosConciliacion["Estado"]=="1"){ //Si el acta está cerrada
                                print("<br>");
                                $Ruta="../../general/procesadores/GeneradorCSV.process.php?Opcion=6&idActaConciliacion=$idActaConciliacion&NIT_IPS=$NIT_IPS";
                                print(" <a href='$Ruta' target='_BLANK'><button class='form-control btn btn-primary'>Anexo del Acta</button></a>");
                            }
                        print("</td>");
                        
                        $css->ColTabla($DatosConciliacion["ID"], 1);
                        $css->ColTabla($DatosConciliacion["FechaInicial"], 1);
                        $css->ColTabla($DatosConciliacion["FechaCorte"], 1);
                        $css->ColTabla($DatosConciliacion["RazonSocialIPS"], 1);
                        $css->ColTabla($DatosConciliacion["NIT_IPS"], 1);
                        $css->ColTabla($DatosConciliacion["RepresentanteLegal"], 1);
                        $css->ColTabla($DatosConciliacion["Departamento"], 1);
                        $css->ColTabla($DatosConciliacion["EncargadoEPS"], 1);
                        $css->ColTabla($DatosConciliacion["ValorSegunEPS"], 1);
                        $css->ColTabla($DatosConciliacion["ValorSegunIPS"], 1);
                        $css->ColTabla($DatosConciliacion["Diferencia"], 1);
                        $css->ColTabla($DatosConciliacion["SaldoConciliadoPago"], 1);
                        $css->ColTabla($DatosConciliacion["Estado"], 1);
                        $css->ColTabla($DatosConciliacion["MesServicioInicial"], 1);
                        $css->ColTabla($DatosConciliacion["MesServicioFinal"], 1);
                        $css->ColTabla($DatosConciliacion["idUser"], 1);
                        $css->ColTabla($DatosConciliacion["FechaRegistro"], 1);
                        $css->ColTabla($DatosConciliacion["Updated"], 1);
                        $css->ColTabla($DatosConciliacion["idUserUpdate"], 1);
                        
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break;//Fin caso 1
        
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>