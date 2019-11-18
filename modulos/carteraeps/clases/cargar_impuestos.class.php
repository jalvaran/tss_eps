<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
/* 
 * Clase donde se realizaran procesos de la cartera IPS
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class CargarImpuestos extends conexion{
    
    public function getKeyArchivo($FechaCorteCartera,$CmbIPS,$CmbEPS) {
        $idUser=$_SESSION["idUser"];
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return("retenciones_"."$idUser"."_".$CmbEPS."_".$CmbIPS."_".$Fecha);
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
    
    public function GuardeArchivoEnTemporal($keyArchivo,$idIPS,$idEPS,$idUser) {
        clearstatcache();
        //require_once('../../../librerias/Excel/PHPExcel.php');
        //require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        require_once('../../../librerias/Excel/PHPExcel2.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchArray($Consulta);
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        $Soporte=$DatosUpload["RutaArchivo"];
       
        if($DatosUpload["ExtensionArchivo"]=="xlsx"){
            $objReader = IOFactory::createReader('Xlsx');
        }else if($DatosUpload["ExtensionArchivo"]=="xls"){
            $objReader = IOFactory::createReader('Xls');
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
        
        $ColumnasTabla= $this->ShowColums($db.".temporal_retenciones");
        
        //for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex(0);
            $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            
            if($columnas<>'H'){
                exit('E1;<h3>No se recibió el archivo de <strong>Retenciones e Impuestos de la EPS ASMET, Ultima Columna: '.$columnas.'</strong></h3>');
            }
           
            $sql= "INSERT INTO $db.`temporal_retenciones` ( ";
            $sql.="`ID`,`Cuentacontable`,`ObservacionCuenta`,`Nit_IPS`,`RazonSocial`,`FechaTransaccion`,`TipoOperacion`,`NumeroTransaccion`,`NumeroFactura`,";
            $sql.="`Descripcion`,`ValorDebito`,`ValorCredito`,`Saldo`,`Soporte`,`idUser`,`FlagUpdate`,`keyFile`,`FechaRegistro`";
            
            $sql.=") VALUES ";
            $r=0;
            
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA=='' or $FilaA=='TOTAL TERCERO'){
                    continue; 
                }
                
                if($FilaA=='*** FIN REPORTE ***'){
                    break; 
                }
                
                $c=0;  
                $r++;//Contador de filas a insertar
                
                if($FilaA=='CUENTA'){
                    
                    $CuentaContable=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $CuentaContable= str_replace("'", "", $CuentaContable);
                    
                    $NombreCuentaContable=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $NombreCuentaContable= str_replace("'", "", $NombreCuentaContable);
                    $NombreCuentaContable= str_replace("%", "", $NombreCuentaContable);
                      
                    continue; 
                }
                
                if($FilaA=='TERCERO'){
                    
                    $Tercero=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $Tercero= str_replace("'", "", $Tercero);
                    $Tercero= str_replace(" ", "", $Tercero);
                    
                    $RazonSocial=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $RazonSocial= str_replace("'", "", $RazonSocial);
                    
                    if($Tercero<>$idIPS){
                        exit("E1;El archivo tiene registros de una IPS diferente a la Selaccionada en la Fila $i");
                    }
                    continue; 
                }
                
                $cell = $objPHPExcel->getActiveSheet()->getCell('A'.$i);
                if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                    $FechaImpuesto=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue());
                    $FechaImpuesto=get_object_vars($FechaImpuesto);
                    $FechaImpuesto = $FechaImpuesto["date"];

                }else{
                    exit("E1;la celda A$i debe ser una Fecha");
                }
                    
                
                    $sql.="(";
                    $sql.="'',";
                    $sql.="'$CuentaContable',";
                    $sql.="'$NombreCuentaContable',";
                    $sql.="'$Tercero',";
                    $sql.="'$RazonSocial',";
                    $sql.="'$FechaImpuesto',";
                    
                    $TipoOperacion=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $TipoOperacion= str_replace("'", "", $TipoOperacion);
                    $sql.="'$TipoOperacion',";
                    
                    $NumeroOperacion=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $NumeroOperacion= str_replace("'", "", $NumeroOperacion);
                    $sql.="'$NumeroOperacion',";
                    
                    
                    $NumeroFactura=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $NumeroFactura= str_replace("'", "", $NumeroFactura);
                    $sql.="'$NumeroFactura',";
                    
                    $DescripcionComplement=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $DescripcionComplement= str_replace("'", "", $DescripcionComplement);
                    $sql.="'$DescripcionComplement',";
                    
                    $Debito=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $Debito= str_replace("'", "", $Debito);
                    $sql.="'$Debito',";
                    
                    $Credito=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $Credito= str_replace("'", "", $Credito);
                    $sql.="'$Credito',";
                    
                    $Saldo=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $Saldo= str_replace("'", "", $Saldo);
                    $sql.="'$Saldo',";
                    
                    $sql.="'$Soporte','$idUser','0','$keyArchivo','$FechaActual'),";
                    //continue;
                
                
                if($r==1000){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temporal_retenciones` ( ";
                    $sql.="`ID`,`Cuentacontable`,`ObservacionCuenta`,`Nit_IPS`,`RazonSocial`,`FechaTransaccion`,`TipoOperacion`,`NumeroTransaccion`,`NumeroFactura`,";
                    $sql.="`Descripcion`,`ValorDebito`,`ValorCredito`,`Saldo`,`Soporte`,`idUser`,`FlagUpdate`,`keyFile`,`FechaRegistro`";

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
    
    
    
    public function GuardeArchivoEnTemporalMutual($keyArchivo,$idIPS,$idEPS,$idUser) {
        clearstatcache();
        //require_once('../../../librerias/Excel/PHPExcel.php');
        //require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        require_once('../../../librerias/Excel/PHPExcel2.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchArray($Consulta);
        $FechaActual=date("Y-m-d H:i:s");
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        $Soporte=$DatosUpload["RutaArchivo"];
       
        if($DatosUpload["ExtensionArchivo"]=="xlsx"){
            $objReader = IOFactory::createReader('Xlsx');
        }else if($DatosUpload["ExtensionArchivo"]=="xls"){
            $objReader = IOFactory::createReader('Xls');
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
        
        $ColumnasTabla= $this->ShowColums($db.".temporal_retenciones");
        
        //for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex(0);
            $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            
            if($columnas<>'N'){
                exit('E1;<h3>No se recibió el archivo de <strong>Retenciones e Impuestos de la EPS ASMET, Ultima Columna: '.$columnas.'</strong></h3>');
            }
           
            $sql= "INSERT INTO $db.`temporal_retenciones` ( ";
            $sql.="`ID`,`Cuentacontable`,`ObservacionCuenta`,`Nit_IPS`,`RazonSocial`,`FechaTransaccion`,`TipoOperacion`,`NumeroTransaccion`,`NumeroFactura`,";
            $sql.="`Descripcion`,`ValorDebito`,`ValorCredito`,`Saldo`,`Soporte`,`idUser`,`FlagUpdate`,`keyFile`,`FechaRegistro`";
            
            $sql.=") VALUES ";
            $r=0;
            
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA=='' or $FilaA=='TOTAL TERCERO'){
                    continue; 
                }
                
                if($FilaA=='*** FIN REPORTE ***'){
                    break; 
                }
                
                $c=0;  
                $r++;//Contador de filas a insertar
                
                if($FilaA=='CUENTA'){
                    
                    $CuentaContable=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $CuentaContable= str_replace("'", "", $CuentaContable);
                    
                    $NombreCuentaContable=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $NombreCuentaContable= str_replace("'", "", $NombreCuentaContable);
                    $NombreCuentaContable= str_replace("%", "", $NombreCuentaContable);
                      
                    continue; 
                }
                
                if($FilaA=='TERCERO'){
                    
                    $Tercero=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $Tercero= str_replace("'", "", $Tercero);
                    $Tercero= str_replace(" ", "", $Tercero);
                    
                    $RazonSocial=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $RazonSocial= str_replace("'", "", $RazonSocial);
                    
                    if($Tercero<>$idIPS){
                        exit("E1;El archivo tiene registros de una IPS diferente a la Selaccionada en la Fila $i");
                    }
                    continue; 
                }
                
                $cell = $objPHPExcel->getActiveSheet()->getCell('A'.$i);
                if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                    $FechaImpuesto=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue());
                    $FechaImpuesto=get_object_vars($FechaImpuesto);
                    $FechaImpuesto = $FechaImpuesto["date"];

                }else{
                    exit("E1;la celda A$i debe ser una Fecha");
                }
                    
                
                    $sql.="(";
                    $sql.="'',";
                    $sql.="'$CuentaContable',";
                    $sql.="'$NombreCuentaContable',";
                    $sql.="'$Tercero',";
                    $sql.="'$RazonSocial',";
                    $sql.="'$FechaImpuesto',";
                    
                    $TipoOperacion=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $TipoOperacion= str_replace("'", "", $TipoOperacion);
                    $sql.="'$TipoOperacion',";
                    
                    $NumeroOperacion=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $NumeroOperacion= str_replace("'", "", $NumeroOperacion);
                    $sql.="'$NumeroOperacion',";
                    
                    
                    $NumeroFactura=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $NumeroFactura= str_replace("'", "", $NumeroFactura);
                    $sql.="'$NumeroFactura',";
                    
                    $DescripcionComplement=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $DescripcionComplement= str_replace("'", "", $DescripcionComplement);
                    $sql.="'$DescripcionComplement',";
                    
                    $Debito=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $Debito= str_replace("'", "", $Debito);
                    $sql.="'$Debito',";
                    
                    $Credito=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $Credito= str_replace("'", "", $Credito);
                    $sql.="'$Credito',";
                    
                    $Saldo=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $Saldo= str_replace("'", "", $Saldo);
                    $sql.="'$Saldo',";
                    
                    $sql.="'$Soporte','$idUser','0','$keyArchivo','$FechaActual'),";
                    //continue;
                
                
                if($r==1000){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temporal_retenciones` ( ";
                    $sql.="`ID`,`Cuentacontable`,`ObservacionCuenta`,`Nit_IPS`,`RazonSocial`,`FechaTransaccion`,`TipoOperacion`,`NumeroTransaccion`,`NumeroFactura`,";
                    $sql.="`Descripcion`,`ValorDebito`,`ValorCredito`,`Saldo`,`Soporte`,`idUser`,`FlagUpdate`,`keyFile`,`FechaRegistro`";

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
