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
        
class AnticiposEPS extends conexion{
        
    public function getKeyPagosEPS($FechaCorteCartera,$CmbIPS,$CmbEPS,$NumeroAnticipo) {
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return("ant_eps_"."$NumeroAnticipo"."_".$CmbEPS."_".$CmbIPS."_".$Fecha);
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
     
    public function GuardeAnticiposASMETEnTemporal($keyArchivo,$idIPS,$idEPS,$idUser,$NumeroAnticipo) {
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
            exit("Solo se permiten archivos con extension xls o xlsx, ext: ".$DatosUpload["ExtensionArchivo"]);
        }
        
        //$objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                 
        $objFecha = new PHPExcel_Shared_Date();      
        
        
        $hojas=$objPHPExcel->getSheetCount();
        
        date_default_timezone_set('UTC'); //establecemos la hora local
        $Proceso="";
        $DescripcionProceso="";
        $Estado="";
        $Cuenta="";
        $Banco="";
        $Cols=['A','B','C','D','E','F','G','H','I'];
        for ($h=0;$h<$hojas;$h++){
            
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
            if($columnas<>'I'){
                exit("E1;No se ha recibido el archivo correcto para los <strong>Anticipos de ASMET </strong>");
            }
            //print("$hojas, $filas, $columnas<br>");
            for ($i=0;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $FilaB=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                
                if($FilaA=='Total legalizado' or $FilaB=='Total legalizado'){
                    
                    continue;
                }
                
                
                    $_DATOS_EXCEL[$h][$i]['Nit_IPS'] = $idIPS;
                    $_DATOS_EXCEL[$h][$i]['NumeroAnticipo'] = $NumeroAnticipo;
                    
                    $c=0;
                    $_DATOS_EXCEL[$h][$i]['DescripcionNC']= $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['NumeroFactura'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorFactura'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorReteiva'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorRetefuente'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorMenosImpuestos'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorSaldo'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorAnticipado'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();

                    $_DATOS_EXCEL[$h][$i]['NumeroRadicado'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    
                    
                    $_DATOS_EXCEL[$h][$i]['Soporte'] = $Soporte;
                    $_DATOS_EXCEL[$h][$i]['idUser'] = $idUser;                    
                    $_DATOS_EXCEL[$h][$i]['FechaRegistro'] = $FechaActual;
                    $_DATOS_EXCEL[$h][$i]['keyFile'] = $keyArchivo;
                    

            } 
        
        }
        
        $sql= "INSERT INTO $db.`temporal_anticipos_asmet` ( `Nit_IPS`, `NumeroAnticipo`,`DescripcionNC`, `NumeroFactura`, `ValorFactura`, `ValorReteiva`, `ValorRetefuente`, `ValorMenosImpuestos`, `ValorSaldo`, `ValorAnticipado`,  `NumeroRadicado`, `Soporte`, `idUser`, `FechaRegistro`, `keyFile`) VALUES ";
        
        foreach($_DATOS_EXCEL as $campo1 => $valor1){
            $i=0;
            foreach($valor1 as $campo => $valor){
                $i++;
                if($i>=7){
                    
                    $sql.="('";
                    foreach ($valor as $campo2 => $valor2){
                        $VectoValor= explode(",", $valor2);
                        $ValorInsert= str_replace(".", "", $VectoValor[0]);
                        $campo2 == "keyFile" ? $sql.= $ValorInsert."')," : $sql.= $ValorInsert."','";
                    }

                    if($i==1000){

                        $sql=substr($sql, 0, -1);
                        //print($sql);

                        $this->Query($sql);
                        $sql= "INSERT INTO $db.`temporal_anticipos_asmet` ( `Nit_IPS`,`NumeroAnticipo`, `DescripcionNC`, `NumeroFactura`, `ValorFactura`, `ValorReteiva`, `ValorRetefuente`, `ValorMenosImpuestos`, `ValorSaldo`, `ValorAnticipado`, `NumeroRadicado`,  `Soporte`, `idUser`, `FechaRegistro`, `keyFile`) VALUES ";

                        $i=0;
                    }    

                }  
            }
        }
        $sql=substr($sql, 0, -1);
        $this->Query($sql);
        unset($objPHPExcel);
        unset($_DATOS_EXCEL);
        unset($sql);
        unset($ValorInsert);
        unset($VectoValor);
            
        
    }
    
    //Fin Clases
}
