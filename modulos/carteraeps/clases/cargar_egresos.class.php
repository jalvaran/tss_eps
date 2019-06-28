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
        
class CargarEgresos extends conexion{
    
    public function getKeyArchivo($FechaCorteCartera,$CmbIPS,$CmbEPS) {
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return("egresos_".$CmbEPS."_".$CmbIPS."_".$Fecha);
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
        require_once('../../../librerias/Excel/PHPExcel2.php');
        //require_once('../../../librerias/Excel/PHPExcel.php');
        //require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
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
        
        $ColumnasTabla= $this->ShowColums($db.".temporal_comprobantesegresoasmet");
        
        //for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex(0);
            $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            
            if($columnas<>'AC'){
                exit('E1;<h3>No se recibió el archivo de <strong>La Cartera por Edades de la EPS ASMET Mutual, Ultima Columna: '.$columnas.'</strong></h3>');
            }
           
            $sql= "INSERT INTO $db.`temporal_comprobantesegresoasmet` ( ";
            foreach ($ColumnasTabla["Field"] as $key => $value) {
                $sql.="`$value`,";
            }
            $sql=substr($sql, 0, -1);
            $sql.=") VALUES ";
            $r=0;
            
           $TipoOperacion="";
           $NumeroComprobante="";
           $FechaComprobante="";
           
           $EstadoCheque="";
           $Observacion="";
           $Descripcion="";
           $Estado="";
           $CuentaBancaria="";
           $Banco="";
            
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){
                    continue; 
                }
                $c=0;  
                $r++;//Contador de filas a insertar
                
                if($FilaA=='Tipo Oper. :'){
                    
                    $TipoOperacion=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $TipoOperacion= str_replace("'", "", $TipoOperacion);
                    
                    $NumeroComprobante=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $NumeroComprobante= str_replace("'", "", $NumeroComprobante);
                    
                    
                    $cell = $objPHPExcel->getActiveSheet()->getCell('G'.$i);
                    if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                        $FechaComprobante=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue());
                        $FechaComprobante=get_object_vars($FechaComprobante);
                        $FechaComprobante = $FechaComprobante["date"];
                        
                    }else{
                        $FechaComprobante='';
                    }
                    
                    $EstadoCheque=$objPHPExcel->getActiveSheet()->getCell('X'.$i)->getCalculatedValue();
                    $EstadoCheque= str_replace("'", "", $EstadoCheque);
                    
                    
                    $Observacion=$objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue();
                    $Observacion= str_replace("'", "", $Observacion);
                    
                    
                    $Descripcion=$objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getCalculatedValue();
                    $Descripcion= str_replace("'", "", $Descripcion);
                    
                    
                    $Estado=$objPHPExcel->getActiveSheet()->getCell('AC'.$i)->getCalculatedValue();
                    $Estado= str_replace("'", "", $Estado);
                                   
                    continue; 
                }
                
                if($FilaA=='Cuenta Bancaria Proveedor'){
                    
                    $CuentaBancaria=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $CuentaBancaria= str_replace("'", "", $CuentaBancaria);
                    
                    $Banco=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $Banco= str_replace("'", "", $Banco);
                    
                    continue;
                }
                
                
                if($FilaA=='SUBTOTAL'){
                    $sql.="(";
                    $sql.="'',";
                    $sql.="'$TipoOperacion',";
                    $sql.="'$NumeroComprobante',";
                    $sql.="'$FechaComprobante',";
                    $sql.="'$idIPS',";
                    $sql.="'$EstadoCheque',";
                    $sql.="'$Observacion',";
                    $sql.="'$Descripcion',";
                    $sql.="'$Estado',";     
                    $sql.="'$CuentaBancaria',";
                    $sql.="'$Banco',";
                    $NumeroInterno=$objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                    $NumeroInterno= str_replace("'", "", $NumeroInterno);
                    $sql.="'$NumeroInterno',";
                    
                    $NumeroFactura=$objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                    $NumeroFactura= str_replace("'", "", $NumeroFactura);
                    $sql.="'$NumeroFactura',";
                    
                    $TipoOperacion2=$objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                    $TipoOperacion2= str_replace("'", "", $TipoOperacion2);
                    $sql.="'$TipoOperacion2',";
                    
                    $MesServicio=$objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                    $MesServicio= str_replace("'", "", $MesServicio);
                    $sql.="'$MesServicio',";
                    
                    $Valor1=$objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                    $Valor1= str_replace("'", "", $Valor1);
                    $sql.="'$Valor1',";
                    
                    $Valor2=$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
                    $Valor2= str_replace("'", "", $Valor2);
                    $sql.="'$Valor2',";
                    
                    $Valor3=$objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getCalculatedValue();
                    $Valor3= str_replace("'", "", $Valor3);
                    $sql.="'$Valor3',";
                    
                    $sql.="'$Soporte','$idUser','0','$keyArchivo','$FechaActual',''),";
                    continue;
                }
                
                if($r==5000){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temporal_comprobantesegresoasmet` ( ";
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
