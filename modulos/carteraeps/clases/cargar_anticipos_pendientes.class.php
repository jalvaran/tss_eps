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
        
class CargarAnticiposPendientes extends conexion{
    
    public function getKeyArchivo($FechaCorteCartera,$CmbIPS,$CmbEPS) {
        $idUser=$_SESSION["idUser"];
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return("anticipos_pendientes_"."$idUser"."_".$CmbEPS."_".$CmbIPS."_".$Fecha);
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
        
        $ColumnasTabla= $this->ShowColums($db.".temporal_anticipos_pendientes_por_legalizar");
        
        //for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex(0);
            $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            
            if($columnas<>'H'){
                exit('E1;<h3>No se recibió el archivo de <strong>Anticipos Pendientes por legalizar de la EPS ASMET, Ultima Columna: '.$columnas.', se esperaba hasta la H</strong></h3>');
            }
           
            $sql= "INSERT INTO $db.`temporal_anticipos_pendientes_por_legalizar` ( ";
            
            $sql.="`TipoOperacion`,`IdentificadorInternoFactura`,`NumeroAnticipo`,`FechaAnticipo`,`Sucursal`,
                   `NIT_IPS`,`Total`,`Saldo`,`MesServicio`,`KeyArchivo`,`FechaRegistro`,`idUser`,`Soporte`
                    ";
            
            $sql.=") VALUES ";
            $r=0;
            
                  
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if(!is_numeric($FilaA) or $FilaA==''){
                    continue; 
                }
                
                
                $c=0;  
                $r++;//Contador de filas a insertar
                     
               
                $sql.="(";
                
                
                $TipoOperacion=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $TipoOperacion= str_replace("'", "", $TipoOperacion);
                $sql.="'$TipoOperacion',";

                
                $IdentificadorInternoFactura=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                $IdentificadorInternoFactura= str_replace("'", "", $IdentificadorInternoFactura);
                $sql.="'$IdentificadorInternoFactura',";
                
                $NumeroAnticipo=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $NumeroAnticipo= str_replace("'", "", $NumeroAnticipo);
                $sql.="'$NumeroAnticipo',";

                $cell = $objPHPExcel->getActiveSheet()->getCell('D'.$i);
                if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                    $Fecha=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue());
                    $Fecha=get_object_vars($Fecha);
                    $Fecha = $Fecha["date"];

                }else{
                    exit("E1;El Archivo no contiene una Fecha en el campo D$i");
                }
                $sql.="'$Fecha',";
                
                $DatosMesServicio = explode("-", $Fecha);
                $MesServicio=$DatosMesServicio[0].$DatosMesServicio[1];
                
                $Sucursal=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                $Sucursal= str_replace("'", "", $Sucursal);
                $sql.="'$Sucursal',";

                $NIT_IPS=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $NIT_IPS= str_replace("'", "", $NIT_IPS);
                $sql.="'$NIT_IPS',";

                $Total=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                $Total= str_replace("'", "", $Total);
                $sql.="'$Total',";
                
                $Saldo=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                $Saldo= str_replace("'", "", $Saldo);
                $sql.="'$Saldo',";
                
                $sql.="'$MesServicio',";
                $sql.="'$keyArchivo','$FechaActual','$idUser','$Soporte'),";
                   
                
                if($r==5000){
                
                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`temporal_anticipos_pendientes_por_legalizar` ( ";
            
                    $sql.="`TipoOperacion`,`IdentificadorInternoFactura`,`NumeroAnticipo`,`FechaAnticipo`,`Sucursal`,
                           `NIT_IPS`,`Total`,`Saldo`,`MesServicio`,`KeyArchivo`,`FechaRegistro`,`idUser`,`Soporte`
                            ";

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
