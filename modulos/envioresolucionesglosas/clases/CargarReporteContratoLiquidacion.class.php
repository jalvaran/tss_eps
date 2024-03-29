<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos de la cartera IPS
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class ReporteContratos extends conexion{
    
    public function getKeyArchivo($NumeroContrato,$CmbIPS) {
               
        return("$NumeroContrato"."_".$CmbIPS);
    }
    
    public function getNumContract($NumeroContrato) {
        $NumeroContrato= str_replace(" ", "", $NumeroContrato);
        $NumeroContrato= str_replace(".", "", $NumeroContrato);
        $NumeroContrato=strtoupper($NumeroContrato);
        return $NumeroContrato;
    }
       
    public function RegistreArchivo($db,$key,$idIPS,$Soporte,$Ruta,$Extension,$idUser) {
        $Fecha=date("Y-m-d H:i:s");
        
        $Datos["NombreArchivo"]=$key;        
        
        $Datos["Soporte"]=$Soporte;
        $Datos["RutaArchivo"]=$Ruta;
        $Datos["ExtensionArchivo"]=$Extension;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$Fecha;        
        $sql=$this->getSQLInsert("resoluciones_glosas_control_envio_glosa", $Datos);
        //$this->Query($sql);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }
    
    public function GuardeArchivoEnTemporal($db,$keyArchivo,$idIPS,$NumeroContrato,$idUser) {
        clearstatcache();
        
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        //require_once('../../../librerias/Excel/phpspreadsheet\src\PhpSpreadsheet/IOFactory.php');
        //require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        
        $sql="SELECT * FROM $db.resoluciones_glosas_control_envio_glosa WHERE NombreArchivo='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchArray($Consulta);
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        $Soporte=$DatosUpload["RutaArchivo"];
        
        //print_r($DatosUpload);
        //$helper->log('Loading file ' . pathinfo($RutaArchivo, PATHINFO_BASENAME) . ' using IOFactory with a defined reader type of ' . $RutaArchivo);
        if($DatosUpload["ExtensionArchivo"]=="xlsx"){
            $objReader = IOFactory::createReader('Xlsx');
        }else if($DatosUpload["ExtensionArchivo"]=="xls"){
            $objReader = IOFactory::createReader('Xls');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        $Cols=[ 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $ColumnasEntradas=0;
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                 
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS) ;  
        $RazonSocialIPS=$DatosIPS["Nombre"];
        $hojas=$objPHPExcel->getSheetCount();
        
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        
        //for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex(0);
            $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            //print($filas);     
            $sql= "INSERT INTO $db.`temp_resoluciones_glosas_revision_contrato_glosa` ( ";            
            $sql.="`ID`,`Nit_IPS`,`RazonSocial`,`NumeroContrato`,`NumeroRadicado`,`NumeroFactura`,`ValorGlosa`,`ValorGlosaAFavorAsmet`,`Soporte`,`idUser`,`keyArchivo`,`FechaRegistro`";            
            $sql.=") VALUES ";
            $r=0;
            
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){
                    continue; 
                }
                
                if($ColumnasEntradas==0){
                    foreach ($Cols as $key => $value) {
                        $Titulo=$objPHPExcel->getActiveSheet()->getCell($value.$i)->getCalculatedValue();
                        $Titulo= str_replace(" ", "", $Titulo);
                        $Titulo= str_replace(".", "", $Titulo);
                        $Titulo= strtoupper($Titulo);
                        if($Titulo=="NORADICADO"){
                            $ColumnasEntradas=1;
                            $ColTitulos["NORADICADO"]=$value;
                        }
                        if($Titulo=="NOFACTURA"){
                            $ColumnasEntradas=1;
                            $ColTitulos["NOFACTURA"]=$value;
                        }
                        if($Titulo=="GLOSA"){
                            $ColumnasEntradas=1;
                            $ColTitulos["GLOSA"]=$value;
                        }
                        if($Titulo=="GLOSAAFAVORASMET"){
                            $ColumnasEntradas=1;
                            $ColTitulos["GLOSAAFAVORASMET"]=$value;
                        }
                        
                    }
                    continue;
                }
                if(!isset($ColTitulos["NORADICADO"]) or !isset($ColTitulos["NOFACTURA"]) or !isset($ColTitulos["GLOSA"]) or !isset($ColTitulos["GLOSAAFAVORASMET"]) ){
                    exit("E1;El archivo no contiene las columnas necesarias para realizar el registro");
                }
                
                                                    
                $r++;//Contador de filas a insertar
                $NumeroRadicado=$objPHPExcel->getActiveSheet()->getCell($ColTitulos["NORADICADO"].$i)->getCalculatedValue();
                $NumeroRadicado= str_replace("'", "", $NumeroRadicado);
                
                $NumeroFactura=$objPHPExcel->getActiveSheet()->getCell($ColTitulos["NOFACTURA"].$i)->getCalculatedValue();
                $NumeroFactura= str_replace("'", "", $NumeroFactura);
                
                if($NumeroFactura=='' or $NumeroRadicado==''){
                    continue;
                }
                
                $sql.="(";
                $sql.="'',";
                $sql.="'$idIPS',";
                $sql.="'$RazonSocialIPS',";
                $sql.="'$NumeroContrato',";
                
                
                $sql.="'$NumeroRadicado',";

                
                $sql.="'$NumeroFactura',";
                
                $ValorGlosa=$objPHPExcel->getActiveSheet()->getCell($ColTitulos["GLOSA"].$i)->getCalculatedValue();
                $ValorGlosa= str_replace("'", "", $ValorGlosa);
                $sql.="'$ValorGlosa',";
                
                $ValorGlosaAFavorAsmet=$objPHPExcel->getActiveSheet()->getCell($ColTitulos["GLOSAAFAVORASMET"].$i)->getCalculatedValue();
                $ValorGlosaAFavorAsmet= str_replace("'", "", $ValorGlosaAFavorAsmet);
                $sql.="'$ValorGlosaAFavorAsmet',";

                $sql.="'$Soporte','$idUser','$keyArchivo','$FechaActual'),";

                
                if($r==5000){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temp_resoluciones_glosas_revision_contrato_glosa` ( ";            
                    $sql.="`ID`,`Nit_IPS`,`RazonSocial`,`NumeroContrato`,`NumeroRadicado`,`NumeroFactura`,`ValorGlosa`,`ValorGlosaAFavorAsmet`,`Soporte`,`idUser`,`keyArchivo`,`FechaRegistro`";            
                    $sql.=") VALUES ";
                    $r=0;
                   
                }  
                
            } 
        
        //}
        
        
        $sql=substr($sql, 0, -1);
        //print($sql);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
        
    }
    
    
    public function GuardeArchivoMutualEnTemporal($keyArchivo,$idIPS,$idEPS,$idUser) {
        clearstatcache();
        require_once('../../../librerias/Excel/PHPExcel.php');
        require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchArray($Consulta);
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        $Soporte=$DatosUpload["RutaArchivo"];
       
        if($DatosUpload["ExtensionArchivo"]=="xlsx"){
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }else if($DatosUpload["ExtensionArchivo"]=="xls"){
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
       
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                 
              
        $hojas=$objPHPExcel->getSheetCount();
        
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        $ColumnasTabla= $this->ShowColums($db.".temporal_Anticipos2");
        
        //for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex(0);
            $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            
            if($columnas<>'K' AND $columnas<>'L'){
                exit('E1;<h3>No se recibió el archivo de <strong>Anticipos de la EPS ASMET, Ultima Columna: '.$columnas.'</strong></h3>');
            }
           
            $sql= "INSERT INTO $db.`temporal_Anticipos2` ( ";
            foreach ($ColumnasTabla["Field"] as $key => $value) {
                $sql.="`$value`,";
            }
            $sql=substr($sql, 0, -1);
            $sql.=") VALUES ";
            $r=0;
            
           $TipoOperacion="";
           $NumeroInterno="";
           $NumeroAnticipo="";
           
           $FechaAnticipo="";
           $Observacion="";
           $DescripcionEgreso="";
           
            
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA=='' or $FilaA=='Proveedor:' or $FilaA=='Documentos Relacionados:' or $FilaA=="T.Op."){
                    continue; 
                }
                
                
                $c=0;  
                $r++;//Contador de filas a insertar
                
                if($FilaA=='N.Deb.'){
                    $Linea=$i+1;
                    $NumeroInterno=$objPHPExcel->getActiveSheet()->getCell('B'.$Linea)->getCalculatedValue();
                    $NumeroInterno= str_replace("'", "", $NumeroInterno);
                    
                    $NumeroAnticipo=$objPHPExcel->getActiveSheet()->getCell('C'.$Linea)->getCalculatedValue();
                    $NumeroAnticipo= str_replace("'", "", $NumeroAnticipo);
                    
                    
                    $cell = $objPHPExcel->getActiveSheet()->getCell('D'.$Linea);
                    if(PHPExcel_Shared_Date::isDateTime($cell)){
                        $FechaAnticipo=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('D'.$Linea)->getValue());
                        $FechaAnticipo = date('Y-m-d', $FechaAnticipo);
                        
                    }else{
                        $FechaAnticipo='';
                    }
                    
                    $DescripcionEgreso=$objPHPExcel->getActiveSheet()->getCell('F'.$Linea)->getCalculatedValue();
                    $DescripcionEgreso= str_replace("'", "", $DescripcionEgreso);
                    
                    
                    $Observacion=$objPHPExcel->getActiveSheet()->getCell('G'.$Linea)->getCalculatedValue();
                    $Observacion= str_replace("'", "", $Observacion);
                               
                    continue; 
                }
                                
                if(is_numeric($FilaA)){
                    $sql.="(";
                    $sql.="'',";
                    $sql.="'$NumeroInterno',";
                    $sql.="'$NumeroAnticipo',";
                    $sql.="'$FechaAnticipo',";
                    $sql.="'$DescripcionEgreso',";
                    $sql.="'$Observacion',";
                    
                    $TipoOperacion=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    $TipoOperacion= str_replace("'", "", $TipoOperacion);
                    $sql.="'$TipoOperacion',";
                    
                    $NumeroOperacion=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $NumeroOperacion= str_replace("'", "", $NumeroOperacion);
                    $sql.="'$NumeroOperacion',";
                    
                    $cell = $objPHPExcel->getActiveSheet()->getCell('C'.$i);
                    if(PHPExcel_Shared_Date::isDateTime($cell)){
                        $Fecha=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getValue());
                        $Fecha = date('Y-m-d', $Fecha);
                        
                    }else{
                        $Fecha='';
                    }
                    $sql.="'$Fecha',";
                    
                    $NumeroFactura=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $NumeroFactura= str_replace("'", "", $NumeroFactura);
                    $sql.="'$NumeroFactura',";
                    
                                        
                    $MesServicio=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $MesServicio= str_replace("'", "", $MesServicio);
                    $sql.="'$MesServicio',";
                    
                    $DescripcionComplement=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $DescripcionComplement= str_replace("'", "", $DescripcionComplement);
                    $sql.="'$DescripcionComplement',";
                    
                    $ValorAnticipado=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $ValorAnticipado= str_replace("'", "", $ValorAnticipado);
                    $sql.="'$ValorAnticipado',";
                    
                    $sql.="'$Soporte','$idUser','0','$keyArchivo','$FechaActual',''),";
                    continue;
                }
                
                if($r==50){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temporal_Anticipos2` ( ";
                    foreach ($ColumnasTabla["Field"] as $key => $value) {
                        $sql.="`$value`,";
                    }
                    $sql=substr($sql, 0, -1);
                    
                    $sql.=") VALUES ";
                    $r=0;
                }  
                
            } 
        
        //}
        
        
        $sql=substr($sql, 0, -1);
        //print($sql);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
        
    }
    
    
    
    //Fin Clases
}
