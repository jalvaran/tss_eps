<?php 
session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}

if(isset($_REQUEST["Opcion"])){
    $myPage="GeneradorCSV.php";
    include_once("../clases/administrador.class.php");
    
       
    $idUser=$_SESSION['idUser'];
    $obCon = new Administrador($idUser);
    
    $DatosRuta=$obCon->DevuelveValores("configuracion_general", "ID", 1);
    $OuputFile=$DatosRuta["Valor"];
    //$Link1=substr($OuputFile, -17);
    //print($Link1);
    $Link="../../exports/";
    //print($Link);
    $a='"';
    $Enclosed=" ENCLOSED BY '$a' ";
    $Opcion=$_REQUEST["Opcion"];
    
    switch ($Opcion){
        case 1: //Exportar CSV 
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $FileName=$Tabla."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            
            if(file_exists($OuputFile)){
                unlink($OuputFile);
            }
            $Condicion=$obCon->normalizar($_REQUEST["Condicion"]);
            $OrdenColumna=$obCon->normalizar($_REQUEST["OrdenColumna"]);
            $AscDesc=$obCon->normalizar($_REQUEST["Orden"]);
            $Separador=$obCon->normalizar($_REQUEST["Separador"]);
            $NumPage="";
            $limit="";
            $startpoint="";
            $ColumnasSeleccionadas=$obCon->getColumnasVisibles($Tabla, "");  
            
            $DatosConsulta=$obCon->getConsultaTabla($Tabla,$ColumnasSeleccionadas, $Condicion, $OrdenColumna, $AscDesc, $NumPage, $limit,$startpoint);
           
            $TotalRegistros=$DatosConsulta["TotalRegistros"];
            $QueryCompleto=$DatosConsulta["QueryCompleto"];
            $QueryParcial=$DatosConsulta["QueryParcial"];
            
            
            $idTabla=$ColumnasSeleccionadas["Field"][0];        
            if($Condicion<>""){
                $Condicion=" WHERE ".$Condicion;
            }
            if($OrdenColumna==''){
                $OrdenColumna=$idTabla;
            }
            
            $Orden=" ORDER BY $OrdenColumna $AscDesc ";
            
            
            
            $sqlColumnas="SELECT ";
            $CamposShow="";
            foreach($ColumnasSeleccionadas["Field"] as $key => $value){
                $Titulo= utf8_encode($ColumnasSeleccionadas["Visualiza"][$key]);                
                $sqlColumnas.="'$Titulo' ,";
                $CamposShow.=" CONVERT(`$value` USING utf8mb4),"; 
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $CamposShow=substr($CamposShow, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            $Indice=$ColumnasSeleccionadas["Field"][0];
            
            //$sql=$sqlColumnas."SELECT $CamposShow FROM $Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            $sql=$sqlColumnas."$QueryParcial $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            
            $obCon->Query($sql);
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$Tabla.csv' target='_top' style='text-align:center;position: absolute;top:5%;left:50%;padding:5px;' onclick=document.getElementById('DivImagenDescargarTablaDB').style.display='none';><h1>Descargar: </h1><img style='heigth:100px;width:100px' src='../../images/descargar3.png'></img></a></div>");
            break;
            
        case 2: //Exportar CSV directamente
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $st=($obCon->normalizar($_REQUEST["st"]));
            //print($st);
            $FileName=$Tabla."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            //print($OuputFile);
            if(file_exists($OuputFile)){
                unlink($OuputFile);
            }
            
            $db=$obCon->normalizar($_REQUEST["db"]);
            $NIT= str_replace("ts_eps_ips_", "", $db);
            $Condicion="";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT  ";
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT * FROM $db.$Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
            $Inical= str_replace("ips", "proveedor", $Tabla);
            $NombreArchivo=$Inical."_$NIT"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
            break;//Fin caso 2
            
        case 3: //Exportar CSV Anexo Acta Cruce
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $db=$obCon->normalizar($_REQUEST["db"]);
            $NIT= str_replace("ts_eps_ips_", "", $db);
            $Tabla="hoja_de_trabajo";
            $FileName="AnexoActa$idActaConciliacion"."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            
            
            $Condicion=" WHERE EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=$Tabla.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT  ";
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT * FROM $db.$Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            //print($sql);
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
           
            $NombreArchivo="ActaConciliacion$idActaConciliacion"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
        break;//Fin caso 3
        /*
        case 4: //Exportar CSV Anexo Acta Cruce
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $db=$obCon->normalizar($_REQUEST["db"]);
            $NIT= str_replace("ts_eps_ips_", "", $db);
            $Tabla="vista_reporte_ips";
            $FileName="AnexoActa$idActaConciliacion"."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            
            
            $Condicion=" WHERE EXISTS (SELECT 1 FROM ts_eps.actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=$Tabla.NumeroContrato) AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT  ";
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT * FROM $db.$Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            $sql=$sqlColumnas." SELECT * FROM $db.$Tabla $Condicion ;";
            //print($sql);
            
            $Consulta=$obCon->Query($sql);
            if($archivo = fopen($Link, "a")){
                $mensaje="";
                $r=0;
                while($DatosExportacion= $obCon->FetchArray($Consulta)){
                    $r++;
                    foreach ($Columnas["Field"] as $NombreColumna){
                        $Dato="";
                        if(isset($DatosExportacion[$NombreColumna])){
                            $Dato=$DatosExportacion[$NombreColumna];
                        }
                        $mensaje.='"'.str_replace(";", "", $Dato).'";'; 
                    }
                    $mensaje=substr($mensaje, 0, -1);
                    $mensaje.="\r\n";
                    if($r==1000){
                        $r=0;
                        fwrite($archivo, $mensaje);
                        $mensaje="";
                    }
                }
                
            }
            
            $Tabla2="vista_cruce_cartera_eps_sin_relacion_segun_ags";
            $Condicion=" WHERE EXISTS (SELECT 1 FROM ts_eps.actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=$Tabla2.NumeroContrato) AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            
            $sql=" SELECT * FROM $db.$Tabla2 $Condicion ;";
            //print($sql);
            
            $Consulta=$obCon->Query($sql);
              
                $r=0;
                while($DatosExportacion= $obCon->FetchArray($Consulta)){
                    $r++;
                    foreach ($Columnas["Field"] as $NombreColumna){
                        $Dato="";
                        if(isset($DatosExportacion[$NombreColumna])){
                            $Dato=$DatosExportacion[$NombreColumna];
                        }
                        $mensaje.='"'.str_replace(";", "", $Dato).'";'; 
                    }
                    $mensaje=substr($mensaje, 0, -1);
                    $mensaje.="\r\n";
                    if($r==1000){
                        $r=0;
                        fwrite($archivo, $mensaje);
                        $mensaje="";
                    }
                }
                
            
            fwrite($archivo, $mensaje);
            fclose($archivo);
            
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
           
            $NombreArchivo="ActaConciliacion$idActaConciliacion"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
        break;//Fin caso 4
        
         * 
         */
        case 4: //Exportar CSV Anexo Acta Cruce
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $db=$obCon->normalizar($_REQUEST["db"]);
            $NIT= str_replace("ts_eps_ips_", "", $db);
            $Tabla="hoja_de_trabajo";
            $FileName="AnexoActa$idActaConciliacion"."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            
            
            $Condicion=" WHERE EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=$Tabla.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            $Fecha=date("Ymd_His");
            
            $sqlColumnas="SELECT  ";
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT * FROM $db.$Tabla $Condicion ;";
            
            $Consulta=$obCon->Query($sql); 
            
            if($archivo = fopen($Link, "a")){
                $mensaje="";
                $r=0;
                while($DatosExportacion= $obCon->FetchAssoc($Consulta)){
                    $r++;
                    foreach ($Columnas["Field"] as $NombreColumna){
                        $Dato="";
                        if(isset($DatosExportacion[$NombreColumna])){
                            $Dato=$DatosExportacion[$NombreColumna];
                        }
                        $mensaje.='"'.str_replace(";", "", $Dato).'";'; 
                    }
                    $mensaje=substr($mensaje, 0, -1);
                    $mensaje.="\r\n";
                    if($r==1000){
                        $r=0;
                        fwrite($archivo, $mensaje);
                        $mensaje="";
                    }
                }
                

                fwrite($archivo, $mensaje);
                
                $Tabla="vista_cruce_cartera_eps_sin_relacion_segun_ags";
                $Condicion=" WHERE EXISTS (SELECT 1 FROM ts_eps.actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=$Tabla.NumeroContrato) AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            
                $sql=" SELECT * FROM $db.$Tabla $Condicion ;";
            
                $Consulta=$obCon->Query($sql); 
                
                $mensaje="";
                $r=0;
                while($DatosExportacion= $obCon->FetchAssoc($Consulta)){
                    $r++;
                    foreach ($Columnas["Field"] as $NombreColumna){
                        $Dato="";
                        if(isset($DatosExportacion[$NombreColumna])){
                            $Dato=$DatosExportacion[$NombreColumna];
                        }
                        $mensaje.='"'.str_replace(";", "", $Dato).'";'; 
                    }
                    $mensaje=substr($mensaje, 0, -1);
                    $mensaje.="\r\n";
                    if($r==1000){
                        $r=0;
                        fwrite($archivo, $mensaje);
                        $mensaje="";
                    }
                }
                fwrite($archivo, $mensaje);
                fclose($archivo);
                unset($mensaje);
                unset($DatosExportacion);
            }
            //print($sql);
            $Fecha=date("Ymd_His");
            
           
            $NombreArchivo="ActaConciliacion$idActaConciliacion"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
        break;//Fin caso 4
        
        case 5: //Exportar CSV fuera de rango
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $db=$obCon->normalizar($_REQUEST["db"]);
            $NIT= str_replace("ts_eps_ips_", "", $db);
            $Tabla="vista_reporte_ips";
            $FileName="AnexoActa$idActaConciliacion"."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            
            
            $Condicion=" WHERE EXISTS (SELECT 1 FROM ts_eps.actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=$Tabla.NumeroContrato) AND (MesServicio<='$MesServicioInicial' OR MesServicio>='$MesServicioFinal')";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT  ";
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT * FROM $db.$Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            //print($sql);
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
           
            $NombreArchivo="ActaConciliacion$idActaConciliacion"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
        break;//Fin caso 5
        
        case 6: //Exportar CSV Anexo Acta Cerrada
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $NIT= $obCon->normalizar($_REQUEST["NIT_IPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $NIT);
            $db=$DatosIPS["DataBase"];
            
            $Tabla="actas_conciliaciones_items";
            $FileName="AnexoActa_$idActaConciliacion"."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            
            
            $Condicion=" WHERE idActaConciliacion='$idActaConciliacion'";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT  ";
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT * FROM $db.$Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            //print($sql);
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
           
            $NombreArchivo="Anexo_Acta_Conciliacion_$idActaConciliacion"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
        break;//Fin caso 6
        
        case 7: //Exportar hoja de trabajo
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $Condicion=($obCon->normalizar($_REQUEST["TxtCondicional"]));
            $CmbTipoNegociacion=$obCon->normalizar($_REQUEST["CmbTipoNegociacion"]);
            //print($st);
            $FileName=$Tabla."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            //print($OuputFile);
            if(file_exists($OuputFile)){
                unlink($OuputFile);
            }
            
            $db=$obCon->normalizar($_REQUEST["db"]);
            $NIT= str_replace("ts_eps_ips_", "", $db);
            //$Condicion="";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT  ";
            
            
            
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            $ColumnasSeleccion="";
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                if($CmbTipoNegociacion=="EVENTO"){
                    if($value<>'Estado' and $value<>'NoRelacionada' and $value<>'DivisorMesServicio' and $value<>'ConciliacionEPSXPagos1' and $value<>'ConciliacionEPSXPagos2' and $value<>'ConciliacionEPSXGlosas1' and $value<>'ConciliacionEPSXGlosas2' and $value<>'ConciliacionEPSXCopagos' and $value<>'ConciliacionEPSXImpuestos' and $value<>'ConciliacionEPSXPagos' and $value<>'ConciliacionEPSXGlosas'  and $value<>'NumeroAfiliadosLMA' and $value<>'NumeroDiasLMA' and $value<>'ValorPercapita' and $value<>'PorcentajePoblacional' and $value<>'ValorAPagarLMA' and $value<>'DescuentoReconocimientoBDUA' and $value<>'FacturasDevueltasAnticipos' and $value<>'NumeroFacturasDevueltasAnticipos' and $value<>'DevolucionesPresentadas' and $value<>'FacturasPresentadas' and $value<>'FacturasDevueltas' and $value<>'TotalDevolucionesNotas' and $value<>'TotalDevolucionesParciales'){
                        $sqlColumnas.="'$value',";
                        $ColumnasSeleccion.="`$value`,";
                    }
                    
                }
                
                if($CmbTipoNegociacion=="CAPITA"){
                    if($value<>'Estado' and $value<>'NoRelacionada' and $value<>'ConciliacionEPSXPagos1' and $value<>'ConciliacionEPSXPagos2' and $value<>'ConciliacionEPSXGlosas1' and $value<>'ConciliacionEPSXGlosas2' and $value<>'ConciliacionEPSXCopagos' and $value<>'ConciliacionEPSXImpuestos' and $value<>'ConciliacionEPSXPagos' and $value<>'ConciliacionEPSXGlosas' and $value<>'FacturasDevueltasAnticipos' and $value<>'NumeroFacturasDevueltasAnticipos' and $value<>'DevolucionesPresentadas' and $value<>'FacturasPresentadas' and $value<>'FacturasDevueltas' and $value<>'TotalDevolucionesNotas' and $value<>'TotalDevolucionesParciales'){
                        $sqlColumnas.="'$value',";
                        $ColumnasSeleccion.="`$value`,";
                    }
                    
                }
                
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $ColumnasSeleccion=substr($ColumnasSeleccion, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT $ColumnasSeleccion FROM $db.$Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
            $Inical= str_replace("ips", "proveedor", $Tabla);
            $NombreArchivo=$CmbTipoNegociacion."_".$Inical."_$NIT"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
            break;//Fin caso 7
        
       
        
        case 8: //Exportar CSV Anexo Acta Liquidacion por facturas
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $db=$obCon->normalizar($_REQUEST["db"]);
            $NIT= str_replace("ts_eps_ips_", "", $db);
            $Tabla="actas_conciliaciones_items";
            $FileName="AnexoActa_Liquidacion_$idActaLiquidacion"."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            
            
            //$Condicion=" WHERE EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=$Tabla.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            $Condicion=" WHERE ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$Tabla.NumeroContrato) ";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT  ";
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT * FROM $db.$Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            //print($sql);
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
           
            $NombreArchivo="ActaLiquidacion_por_facturas_$idActaLiquidacion"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
        break;//Fin caso 8
        
        case 9: //Exportar CSV Anexo Acta Liquidacion por radicados
            $idActaLiquidacion=$obCon->normalizar($_REQUEST["idActaLiquidacion"]);
            $DatosActa=$obCon->DevuelveValores("actas_liquidaciones", "ID", $idActaLiquidacion); 
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $db=$obCon->normalizar($_REQUEST["db"]);
            $NIT= str_replace("ts_eps_ips_", "", $db);
            $Tabla="actas_conciliaciones_items";
            $FileName="AnexoActa_Liquidacion_$idActaLiquidacion"."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
            
            
            //$Condicion=" WHERE EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=$Tabla.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
            $Condicion=" WHERE ($Tabla.MesServicio BETWEEN $MesServicioInicial AND $MesServicioFinal) AND EXISTS (SELECT 1 FROM actas_liquidaciones_contratos t2 WHERE t2.idContrato=$Tabla.NumeroContrato) GROUP BY NumeroRadicado,MesServicio,NumeroContrato";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT 'Mes de Servicio','Departamento de Radicacion','Numero de Radicado','Fecha de Radicado',
                    'Numer de oContrato','Valor del Documento','Impuestos','Total Pagos','Total Anticipos','Total Copagos',
                    'DescuentoPGP','Descuento BDUA','Otros Descuentos','Ajustes de Cartera','Total Glosa Inicial','Total Glosa Favor',
                    'Total Glosa Contra','Glosa por Conciliar','Total de Devoluciones','Valor Segun EPS','Valor Segun IPS','Diferencia'
                    ";
            /*
            $Columnas=$obCon->ShowColums($db.".".$Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
             * 
             */
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT MesServicio,DepartamentoRadicacion,NumeroRadicado,FechaRadicado,NumeroContrato,SUM(ValorDocumento) AS ValorDocumento,
                                SUM(Impuestos) AS Impuestos,SUM(TotalPagos) AS TotalPagos,SUM(TotalAnticipos) AS TotalAnticipos,SUM(TotalCopagos) AS TotalCopagos,
                                SUM(DescuentoPGP) AS DescuentoPGP,SUM(DescuentoBDUA) AS DescuentoBDUA,SUM(OtrosDescuentos) AS OtrosDescuentos,SUM(AjustesCartera) AS AjustesCartera
                                ,SUM(TotalGlosaInicial) AS TotalGlosaInicial,SUM(TotalGlosaFavor) AS TotalGlosaFavor,SUM(TotalGlosaContra) AS TotalGlosaContra,
                                SUM(GlosaXConciliar) AS GlosaXConciliar,SUM(TotalDevoluciones) AS TotalDevoluciones,SUM(ValorSegunEPS) AS ValorSegunEPS,
                                SUM(ValorSegunIPS) AS ValorSegunIPS,SUM(Diferencia) AS Diferencia 
                                FROM $db.$Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            //print($sql);
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
           
            $NombreArchivo="ActaLiquidacion_Radicados_$idActaLiquidacion"."_$Fecha";
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
        break;//Fin caso 9
        
        }
}else{
    print("No se recibió parametro de opcion");
}

?>