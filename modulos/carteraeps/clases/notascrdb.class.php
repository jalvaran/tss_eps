<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos de la cartera IPS
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class NotasCRBD extends conexion{
    
    public function getKeyArchivo($FechaCorteCartera,$CmbIPS,$CmbEPS) {
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return("notas_cr_db_".$CmbEPS."_".$CmbIPS."_".$Fecha);
    }
    
       
    public function RegistreArchivo($key,$idEPS,$idIPS,$Soporte,$Ruta,$Extension,$idUser) {
        $Fecha=date("Y-m-d H:i:s");
        $DatosCargas=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosCargas["DataBase"];
        $Datos["NombreCargue"]=$key;        
        $Datos["Nit_EPS"]=$idEPS;
        $Datos["Soporte"]=$Soporte;
        $Datos["RutaArchivo"]=$Ruta;
        $Datos["ExtensionArchivo"]=$Extension;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$Fecha;
        $Datos["FechaActualizacion"]=$Fecha;
        $sql=$this->getSQLInsert("controlcargueseps", $Datos);
        //$this->Query($sql);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }
    
    public function GuardeNotasEnTemporal($keyArchivo,$idIPS,$idEPS,$idUser) {
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
        
        //$objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                 
        //$objFecha = new PHPExcel_Shared_Date();      
        
        
        $hojas=$objPHPExcel->getSheetCount();
        
        date_default_timezone_set('UTC'); //establecemos la hora local
        $Proceso="";
        $DescripcionProceso="";
        $Estado="";
        $Cuenta="";
        $Banco="";
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        $ColumnasTabla= $this->ShowColums($db.".temporal_notas_dv_cr");
        
        for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
          
            if($columnas<>'CS' AND $columnas<>'CT'){
                exit('E1;<h3>No se recibió el archivo de <strong>Notas Débito y Crédito de la EPS ASMET Mutual</strong></h3>');
            }
            $sql= "INSERT INTO $db.`temporal_notas_dv_cr` ( ";
            foreach ($ColumnasTabla["Field"] as $key => $value) {
                $sql.="`$value`,";
            }
            $sql=substr($sql, 0, -1);
            $sql.=") VALUES ";
            $r=0;
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){

                    continue; 

                }
                $c=0;  
                $r++;//Contador de filas a insertar
                $sql.="(";
                foreach ($ColumnasTabla["Field"] as $key => $value) {
                    if($value=='ID'){
                        $sql.="'',";
                        continue;
                    }
                    
                    $c=$c+1;
                    $Dato=$objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    if($value=="FechaTransaccion" or $value=="FechaValidacionImpuesto" or $value=="FechaTasa" or $value=="C55" or $value=="C56" or $value=="C73"){
                        $cell = $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i);
                        if(PHPExcel_Shared_Date::isDateTime($cell)){
                            $Dato=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getValue());
                            if($value=="FechaValidacionImpuesto"){
                                $Dato = date('Y-m-d H:i:s', $Dato);
                            }else{
                                $Dato = date('Y-m-d', $Dato);
                            }
                            
                        }
                        
                    }
                    
                    if($value=="Soporte"){
                        $Dato=$Soporte;
                    }
                    
                    if($value=="idUser"){
                        $Dato=$idUser;
                    }
                    
                    if($value=="keyFile"){
                        $Dato=$keyArchivo;
                    }
                    
                    if($value=="FechaRegistro"){
                        $Dato=$FechaActual;
                    }
                    
                    if($value=="FlagUpdate"){
                        $Dato="";
                    }
                    $Dato= str_replace("'", "", $Dato);
                    $sql.="'$Dato',";
                    
                }
                $sql=substr($sql, 0, -1);
                $sql.="),";
                if($r==1000){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temporal_notas_dv_cr` ( ";
                    foreach ($ColumnasTabla["Field"] as $key => $value) {
                        $sql.="`$value`,";
                    }
                    $sql=substr($sql, 0, -1);
                    
                    $sql.=") VALUES ";
                    $r=0;
                }  
                
            } 
        
        }
        
        
        $sql=substr($sql, 0, -1);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($Dato);
        unset($objFecha);
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
        
    }
    
    public function GuardeNotasEnTemporal2($keyArchivo,$idIPS,$idEPS,$idUser) {
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
        
        //$objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                 
        //$objFecha = new PHPExcel_Shared_Date();      
        
        
        $hojas=$objPHPExcel->getSheetCount();
        
        date_default_timezone_set('UTC'); //establecemos la hora local
        $Proceso="";
        $DescripcionProceso="";
        $Estado="";
        $Cuenta="";
        $Banco="";
        $Tabla="temporal_notas_db_cr_2";
        $ColumnasTabla= $this->ShowColums($db.".$Tabla");
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        
        
        for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
          
            if($columnas<>'AT' and $columnas<>'AU'){
                exit("E1;<h3>No se recibió el archivo de <strong>Notas Débito y Crédito de la EPS ASMET Mutual</strong>, Columnas hasta: $columnas</h3>");
            }
            $sql= "INSERT INTO $db.`$Tabla` ( ";
            foreach ($ColumnasTabla["Field"] as $key => $value) {
                $sql.="`$value`,";
            }
            $sql=substr($sql, 0, -1);
            $sql.=") VALUES ";
            $r=0;
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){

                    continue; 

                }
                $c=0;  
                $r++;//Contador de filas a insertar
                $sql.="(";
                foreach ($ColumnasTabla["Field"] as $key => $value) {
                    if($value=='ID'){
                        $sql.="'',";
                        continue;
                    }
                    
                    $c=$c+1;
                    $Dato=$objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    //if($value=="FechaTransaccion" or $value=="FechaValidacionImpuesto" or $value=="FechaTasa" or $value=="C55" or $value=="C56" or $value=="C73"){
                        $cell = $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i);
                        if(PHPExcel_Shared_Date::isDateTime($cell)){
                            $Dato=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getValue());
                            if($value=="C4" or $value=="FechaOrdenPago" or $value=="FechaDesconocida"){
                                $Dato = date('Y-m-d H:i:s', $Dato);
                            }else{
                                $Dato = date('Y-m-d', $Dato);
                            }
                            
                        }
                        
                    //}
                    
                    if($value=="Soporte"){
                        $Dato=$Soporte;
                    }
                    
                    if($value=="idUser"){
                        $Dato=$idUser;
                    }
                    
                    if($value=="keyFile"){
                        $Dato=$keyArchivo;
                    }
                    
                    if($value=="FechaRegistro"){
                        $Dato=$FechaActual;
                    }
                    
                    if($value=="FlagUpdate"){
                        $Dato="0";
                    }
                    $Dato= str_replace("'", "", $Dato);
                    $sql.="'$Dato',";
                    
                }
                $sql=substr($sql, 0, -1);
                $sql.="),";
                if($r==1000){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`$Tabla` ( ";
                    foreach ($ColumnasTabla["Field"] as $key => $value) {
                        $sql.="`$value`,";
                    }
                    $sql=substr($sql, 0, -1);
                    
                    $sql.=") VALUES ";
                    $r=0;
                }  
                
            } 
        
        }
        
        
        $sql=substr($sql, 0, -1);
        $this->Query($sql);
        
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($Dato);
        unset($objFecha);
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
        
        
    }
    
    public function GuardeNotasCRDBEnTemporal($keyArchivo,$idIPS,$idEPS,$idUser,$NumeroColumnasEncabezado) {
        clearstatcache();
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchAssoc($Consulta);
        
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        $i=0;
        $Separador="\t";
        $Fecha=date("Y-m-d H:i:s");
        $Tabla="temporal_notas_db_cr_2";
        $ColumnasTabla= $this->ShowColums($db.".$Tabla");
        $sql= "INSERT INTO $db.`$Tabla` ( ";
        foreach ($ColumnasTabla["Field"] as $key => $value) {
            $sql.="`$value`,";
        }
        $sql=substr($sql, 0, -1);
        $sql.=") VALUES ";

            $handle = fopen($RutaArchivo, "r");
            $r=0;
            $LimiteInferior=$NumeroColumnasEncabezado-2;
            $LimiteSuperior=$NumeroColumnasEncabezado+2;
            while (($data = fgetcsv($handle, 1000, $Separador)) !== FALSE) {
                
                $i++;
               if($i==1){ //Verifico que el archivo recibido corresponda al solicitado
                   if(!$NumeroColumnasEncabezado>=$LimiteInferior AND !$NumeroColumnasEncabezado<=$LimiteSuperior){
                       $NumeroColumnas=count($data);
                       exit("E1;<h3>El archivo enviado no corresponde al archivo esperado o ha sufrido cambios, contiene $NumeroColumnas Columnas</h3>");
                   }
               }
               
               $r++;//Contador de filas a insertar
               $sql.="(";
               $z=1*(-1);
               foreach ($ColumnasTabla["Field"] as $key => $value) {
                   if($value=="ID"){
                       $sql.="'',";
                       continue;
                   }
                   if($value=="Soporte"){
                       $sql.="'$RutaArchivo',";
                       continue;
                   }
                   if($value=="idUser"){
                       $sql.="'$idUser',";
                       continue;
                   }
                   if($value=="keyFile"){
                       $sql.="'$keyArchivo',";
                       continue;
                   }
                   
                   if($value=="FlagUpdate"){
                       $sql.="'0',";
                       continue;
                   }
                   
                   
                   if($value=="FechaRegistro"){
                       $sql.="'$Fecha',";
                       continue;
                   }
                   $z++;
                   if($value=="FechaTransaccion" or $value=="FechaAprobacion" ){
                       if(isset($data[$z])){
                           $FechaTransaccion=$this->ConviertaStringToDate($data[$z]);
                       }else{
                           $FechaTransaccion="";
                       }
                       
                       $sql.="'$FechaTransaccion',";
                       continue;
                   }
                   
                   if(isset($data[$z])){
                       $Dato= str_replace("'", "", $data[$z]);
                       $sql.="'$Dato',";
                   }else{
                       $Dato= "";
                       $sql.="'$Dato',";
                   }
                   
                   
                   
               }
               $sql=substr($sql, 0, -1);
               $sql.="),";
               
               $r++;
               if($r>=5000){
                    $r=0;
                    $sql=substr($sql, 0, -1);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`$Tabla` ( ";
                     foreach ($ColumnasTabla["Field"] as $key => $value) {
                         $sql.="`$value`,";
                     }
                     $sql=substr($sql, 0, -1);
                     $sql.=") VALUES "; 
               }
               
                
                
            }
            if($r<>5000){
                $sql=substr($sql, 0, -1);
                $this->Query($sql);
            }
            fclose($handle); 
        clearstatcache();
    }
    
    
    public function ConviertaStringToDate($Dato) {
        $Fecha="0000-00-00";
        if($Dato<>''){
            $Vector= explode("/", $Dato);
            if(count($Vector)>1){
                $Fecha= $Vector[2]."-".$Vector[1]."-".$Vector[0];
            }else{
                $Fecha=$Dato;
            }

        }
        return($Fecha);
    }
    
    //Fin Clases
}
