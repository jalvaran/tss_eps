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
        
class CarteraEPS extends conexion{
    
    public function getKeyCarteraEPS($FechaCorteCartera,$CmbIPS,$CmbEPS) {
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return("eps_".$CmbEPS."_".$CmbIPS."_".$Fecha);
    }
    
    public function getKeyPagosEPS($FechaCorteCartera,$CmbIPS,$CmbEPS) {
        $Fecha= str_replace("-", "", $FechaCorteCartera);
        return("pago_eps_".$CmbEPS."_".$CmbIPS."_".$Fecha);
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
    
    public function CalcularNumeroRegistros($keyArchivo,$idIPS,$Separador,$NumeroColumnasEncabezado,$NitEPS,$idUser) {
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchAssoc($Consulta);
        
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        $i=0;
        if($Separador==1){
           $Separador=";"; 
        }else{
           $Separador=",";  
        }
        
            
            $handle = fopen($RutaArchivo, "r");
            
            while (($data = fgetcsv($handle, 1000, $Separador)) !== FALSE) {
                
                $i++;
               if($i==2){
                   if(count($data)<>$NumeroColumnasEncabezado){
                       exit("E1;<h3>El archivo enviado no corresponde al NIT $NitEPS</h3>");
                   }
               }
                
            }
            
            fclose($handle); 
        
        return $i;
    }
    
    public function LeerArchivoSAS($keyArchivo,$FechaCorte,$idIPS,$LineaActual,$Separador,$idUser) {
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchAssoc($Consulta);
        $Soporte=$DatosUpload["Soporte"];
        $EPS=$DatosUpload["Nit_EPS"];
        $FechaRegistro=$DatosUpload["FechaRegistro"];
        $FechaActualizacion=$DatosUpload["FechaActualizacion"];
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        //print("Linea Actual: $LineaActual");
        if($LineaActual==0 or $LineaActual==''){
            $LineaActual=2;
        }
        
        $i=0;
        if($Separador==1){
           $Separador=";"; 
        }else{
           $Separador=",";  
        }
        $MaxRegistros=10000+$LineaActual;
            
        $handle = fopen($RutaArchivo, "r");
        
        $sql="INSERT INTO $db.`temporalcarguecarteraeps` (`ID`, `TipoOperacion`, `NumeroOperacion`, `FechaFactura`, `CodigoSucursal`, `Sucursal`,"
                . "`NumeroFactura`, `Descripcion`, `RazonSocial`, `Nit_IPS`, `NumeroContrato`, `Prefijo`, `DepartamentoRadicacion`, "
                . "`NumeroRadicado`, `MesServicio`, `ValorOriginal`, `ValorMenosImpuestos`, `ValorPagado`, `ValorCruce`, `ValorCruceAnticipo`, "
                . "`ValorCruceAuditoria`, `SaldoFactura`, `ValorAutorizado`, "
                . "`AnticiposRelacionados`, `ValorGlosaTotalMutual`,`CrucesMutual`,`SaldoMutual`,`TotalValorGlosadoD2702`,"
                . " `ValorPagosGlosadoD2702`, `ValorCruceGlosadoD2702`, `SaldoGlosaD2702`, `ValorAutorizadoGlosado`, "
                . "`Original29`, `TipoOperacionCF`, `NumeroTransaccionCF`, `FechaTransaccionCF`, `ValorCruceTransaccionCF`, `TipoOperacionPF`, "
                . "`NumeroTransaccionPF`, `FechaTransaccionPF`, `ValorPagadoPF`, `NumeroPlanoPF`, `FechaPlanoPF`, `TipoOperacionGA2702`, `FechaTransaccionGA2702`, "
                . "`NumeroTransaccionGA2702`, `ValorCruceTransaccionGA2702`, `TipoOperacionGD2702`, `FechaTransaccionGD2702`, `NumeroTransaccionGD2702`, "
                . "`ValorCruceTransaccionGD2702`, `NumeroPlanoGD2702`, `DescuentoBdua`, `Previsado`, `EnGiro`, `ValorGiro`, `Soporte`, `Nit_EPS`, `idUser`, `FechaRegistro`, "
                . "`FechaActualizacion`) VALUES ";
        $r=0;
        $z=0;
        while ( ($data = fgetcsv($handle, 1000, $Separador)) !== FALSE) {
            $r++;
            $z++;
            if(!isset($data[2])){
                continue;
            }
            if(!isset($data[5])){
                continue;
            }
            if(!isset($data[6])){
                continue;
            }
            if(!isset($data[7])){
                continue;
            }
            if(!isset($data[8])){
                continue;
            }
            
            if(!isset($data[8])){
                continue;
            }
            
            
            if($data[5]==""){
                continue;
            }
            if(!is_numeric($data[8])){
                continue;
            }
            if($data[2]<>""){
                $FechaArchivo= explode("/", $data[2]);
                if(count($FechaArchivo)>1){
                    $FechaFactura= $FechaArchivo[2]."-".$FechaArchivo[1]."-".$FechaArchivo[0];
                }else{
                    $FechaFactura=$data[2];
                }

             }else{
                $FechaFactura="0000-00-00";
             }
             $sql.="('',";
             for($i=0;$i<=26;$i++){
                 if($i==8){
                     if($data[$i]<>$idIPS){
                         exit("E1;El archivo contiene registros de otra ips con NIT: $data[$i]");
                     }
                 }
                 $Dato= str_replace(".", "", $data[$i]);
                 if($i==2){
                     $Dato=$FechaFactura;
                 }
                 if($i>=23 and $i<=25){
                     $Dato=="";
                 }
                 $sql.="'$Dato',";
             }
             for($i=24;$i<=51;$i++){
                 $Dato="";
                 if(isset($data[$i])){
                     $Dato= str_replace(".", "", $data[$i]);
                 }
                 
                 $sql.="'$Dato',";
             }
             $sql.="'$Soporte','$EPS','$idUser','$FechaRegistro','$FechaActualizacion'),";
             
             if($r==1000){
                 $r=0;
                 $sql=substr($sql, 0, -1);
                //print("<pre>".$sql."</pre>");
                $this->Query($sql);
                
                $sql="INSERT INTO $db.`temporalcarguecarteraeps` (`ID`, `TipoOperacion`, `NumeroOperacion`, `FechaFactura`, `CodigoSucursal`, `Sucursal`,"
                . "`NumeroFactura`, `Descripcion`, `RazonSocial`, `Nit_IPS`, `NumeroContrato`, `Prefijo`, `DepartamentoRadicacion`, "
                . "`NumeroRadicado`, `MesServicio`, `ValorOriginal`, `ValorMenosImpuestos`, `ValorPagado`, `ValorCruce`, `ValorCruceAnticipo`, "
                . "`ValorCruceAuditoria`, `SaldoFactura`, `ValorAutorizado`, "
                . "`AnticiposRelacionados`, `ValorGlosaTotalMutual`,`CrucesMutual`,`SaldoMutual`,`TotalValorGlosadoD2702`,"
                . " `ValorPagosGlosadoD2702`, `ValorCruceGlosadoD2702`, `SaldoGlosaD2702`, `ValorAutorizadoGlosado`, "
                . "`Original29`, `TipoOperacionCF`, `NumeroTransaccionCF`, `FechaTransaccionCF`, `ValorCruceTransaccionCF`, `TipoOperacionPF`, "
                . "`NumeroTransaccionPF`, `FechaTransaccionPF`, `ValorPagadoPF`, `NumeroPlanoPF`, `FechaPlanoPF`, `TipoOperacionGA2702`, `FechaTransaccionGA2702`, "
                . "`NumeroTransaccionGA2702`, `ValorCruceTransaccionGA2702`, `TipoOperacionGD2702`, `FechaTransaccionGD2702`, `NumeroTransaccionGD2702`, "
                . "`ValorCruceTransaccionGD2702`, `NumeroPlanoGD2702`, `DescuentoBdua`, `Previsado`, `EnGiro`, `ValorGiro`, `Soporte`, `Nit_EPS`, `idUser`, `FechaRegistro`, "
                . "`FechaActualizacion`) VALUES "; 
                
             }
             
        }
        
        fclose($handle); 
        
        $sql=substr($sql, 0, -1);
        //print("<pre>".$sql."</pre>");
        $this->Query($sql);
        $sql="";
        
        
        return $z;
    }
    
    public function LeerArchivoMutual($keyArchivo,$FechaCorte,$idIPS,$LineaActual,$Separador,$idUser) {
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchAssoc($Consulta);
        $Soporte=$DatosUpload["Soporte"];
        $EPS=$DatosUpload["Nit_EPS"];
        $FechaRegistro=$DatosUpload["FechaRegistro"];
        $FechaActualizacion=$DatosUpload["FechaActualizacion"];
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        //print("Linea Actual: $LineaActual");
        if($LineaActual==0 or $LineaActual==''){
            $LineaActual=2;
        }
        
        $i=0;
        if($Separador==1){
           $Separador=";"; 
        }else{
           $Separador=",";  
        }
        $MaxRegistros=12000+$LineaActual;
            
        $handle = fopen($RutaArchivo, "r");
        
        $sql="INSERT INTO $db.`temporalcarguecarteraeps` (`ID`, `TipoOperacion`, `NumeroOperacion`, `FechaFactura`, `CodigoSucursal`, `Sucursal`,"
                . "`NumeroFactura`, `Descripcion`, `RazonSocial`, `Nit_IPS`, `NumeroContrato`, `Prefijo`, `DepartamentoRadicacion`, "
                . "`NumeroRadicado`, `MesServicio`, `ValorOriginal`, `ValorMenosImpuestos`, `ValorPagado`, `ValorCruce`, `ValorCruceAnticipo`, "
                . "`ValorCruceAuditoria`, `SaldoFactura`, `ValorAutorizado`, "
                . "`AnticiposRelacionados`, `ValorGlosaTotalMutual`,`CrucesMutual`,`SaldoMutual`,`TotalValorGlosadoD2702`,"
                . " `ValorPagosGlosadoD2702`, `ValorCruceGlosadoD2702`, `SaldoGlosaD2702`, `ValorAutorizadoGlosado`, "
                . "`Original29`, `TipoOperacionCF`, `NumeroTransaccionCF`, `FechaTransaccionCF`, `ValorCruceTransaccionCF`, `TipoOperacionPF`, "
                . "`NumeroTransaccionPF`, `FechaTransaccionPF`, `ValorPagadoPF`, `NumeroPlanoPF`, `FechaPlanoPF`, `TipoOperacionGA2702`, `FechaTransaccionGA2702`, "
                . "`NumeroTransaccionGA2702`, `ValorCruceTransaccionGA2702`, `TipoOperacionGD2702`, `FechaTransaccionGD2702`, `NumeroTransaccionGD2702`, "
                . "`ValorCruceTransaccionGD2702`, `NumeroPlanoGD2702`, `DescuentoBdua`, `Previsado`, `EnGiro`, `ValorGiro`, `Soporte`, `Nit_EPS`, `idUser`, `FechaRegistro`, "
                . "`FechaActualizacion`) VALUES ";
        $z=0;
        $r=0;
        while (($data = fgetcsv($handle, 1000, $Separador)) !== FALSE) {
            
            $z++;
            $r++;
            if(!isset($data[8])){ //Se encuentra el nit de la ips
                continue;
            }
            if(!is_numeric($data[8])){
                continue;
            }
            
            if($data[2]<>""){
                $FechaArchivo= explode("/", $data[2]);
                if(count($FechaArchivo)>1){
                    $FechaFactura= $FechaArchivo[2]."-".$FechaArchivo[1]."-".$FechaArchivo[0];
                }else{
                    $FechaFactura=$data[2];
                }

             }else{
                $FechaFactura="0000-00-00";
             }
             $sql.="('',";
             for($i=0;$i<=31;$i++){
                 
                 if(isset($data[$i])){
                     if($i==8){
                        if($data[$i]<>$idIPS){
                            exit("E1;El archivo contiene registros de otra ips con NIT: $data[$i]");
                        }
                    }
                    $Dato= str_replace(".", "", $data[$i]);
                    if($i==2){
                        $Dato=$FechaFactura;
                    }
                    if($i==31){
                        $Dato=="";
                    }
                    $sql.="'$Dato',";
                 }else{
                    
                    $sql.="'',";
                 }
             }
             for($i=31;$i<=50;$i++){
                 if(isset($data[$i])){
                    $Dato= str_replace(".", "", $data[$i]);

                    $sql.="'$Dato',";
                 }else{
                    $sql.="'',"; 
                 }
             }
             $sql.="'','','','$Soporte','$EPS','$idUser','$FechaRegistro','$FechaActualizacion'),";
             
             if($r==1000){
                 $r=0;
                 $sql=substr($sql, 0, -1);
                //print("<pre>".$sql."</pre>");
                $this->Query($sql);
                $sql="INSERT INTO $db.`temporalcarguecarteraeps` (`ID`, `TipoOperacion`, `NumeroOperacion`, `FechaFactura`, `CodigoSucursal`, `Sucursal`,"
                . "`NumeroFactura`, `Descripcion`, `RazonSocial`, `Nit_IPS`, `NumeroContrato`, `Prefijo`, `DepartamentoRadicacion`, "
                . "`NumeroRadicado`, `MesServicio`, `ValorOriginal`, `ValorMenosImpuestos`, `ValorPagado`, `ValorCruce`, `ValorCruceAnticipo`, "
                . "`ValorCruceAuditoria`, `SaldoFactura`, `ValorAutorizado`, "
                . "`AnticiposRelacionados`, `ValorGlosaTotalMutual`,`CrucesMutual`,`SaldoMutual`,`TotalValorGlosadoD2702`,"
                . " `ValorPagosGlosadoD2702`, `ValorCruceGlosadoD2702`, `SaldoGlosaD2702`, `ValorAutorizadoGlosado`, "
                . "`Original29`, `TipoOperacionCF`, `NumeroTransaccionCF`, `FechaTransaccionCF`, `ValorCruceTransaccionCF`, `TipoOperacionPF`, "
                . "`NumeroTransaccionPF`, `FechaTransaccionPF`, `ValorPagadoPF`, `NumeroPlanoPF`, `FechaPlanoPF`, `TipoOperacionGA2702`, `FechaTransaccionGA2702`, "
                . "`NumeroTransaccionGA2702`, `ValorCruceTransaccionGA2702`, `TipoOperacionGD2702`, `FechaTransaccionGD2702`, `NumeroTransaccionGD2702`, "
                . "`ValorCruceTransaccionGD2702`, `NumeroPlanoGD2702`, `DescuentoBdua`, `Previsado`, `EnGiro`, `ValorGiro`, `Soporte`, `Nit_EPS`, `idUser`, `FechaRegistro`, "
                . "`FechaActualizacion`) VALUES ";
             }
        }
        
        fclose($handle); 
        
        $sql=substr($sql, 0, -1);
        //print("<pre>".$sql."</pre>");
        $this->Query($sql);
        $sql="";
        
        
        return $z;
    }
    
    public function LeerPagosEPS($keyArchivo,$FechaCorte,$idIPS,$LineaActual,$CmbEPS,$idUser) {
        require_once('../../../librerias/Excel/PHPExcel.php');
        require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcarguesips WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchAssoc($Consulta);
        $Fecha=$DatosUpload["FechaRegistro"];
        $Soporte=$DatosUpload["Soporte"];
        $RutaArchivo=$DatosUpload["RutaArchivo"];
        if($DatosUpload["ExtensionArchivo"]=="xlsx"){
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }else if($DatosUpload["ExtensionArchivo"]=="xls"){
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
        //$objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($RutaArchivo);
        $objFecha = new PHPExcel_Shared_Date();       
        $objPHPExcel->setActiveSheetIndex(0);
        
        $count=0;
        $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        date_default_timezone_set('UTC'); //establecemos la hora local
        for ($i=2;$i<=$filas;$i++){
            if($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()<>''){
                $data=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue());
                $FechaRadicado=date("Y-m-d",$data); 
                $data=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue());
                $FechaFactura=date("Y-m-d",$data); 
                $_DATOS_EXCEL[$i]['FechaFactura']=$FechaFactura;
                $_DATOS_EXCEL[$i]['FechaRadicado']=$FechaRadicado;
                $_DATOS_EXCEL[$i]['NitEPS']= $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NitIPS']= $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroFactura'] = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroCuentaGlobal'] = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroRadicado'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['TipoNegociacion'] = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['NumeroContrato'] = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['DiasPactados'] = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['TipoRegimen'] = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                
                $_DATOS_EXCEL[$i]['ValorDocumento'] = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorGlosaInicial'] = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorGlosaAceptada'] = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorGlosaConciliada'] = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorDescuentoBdua'] = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorAnticipos'] = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
                
                $_DATOS_EXCEL[$i]['ValorRetencion'] = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['ValorTotalpagar'] = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['FechaHasta'] = $FechaCorte;
                $_DATOS_EXCEL[$i]['Soporte'] = $Soporte;
                $_DATOS_EXCEL[$i]['idUser'] = $idUser;
                
                $_DATOS_EXCEL[$i]['FechaRegistro'] = $Fecha;
                $_DATOS_EXCEL[$i]['FechaActualizacion'] = $Fecha;
                
            }
        } 
        $sql="";
        
        foreach($_DATOS_EXCEL as $campo => $valor){
            $sql= "INSERT INTO $db.temporalcarguecarteraips (FechaFactura,FechaRadicado,NitEPS,NitIPS,NumeroFactura,NumeroCuentaGlobal,NumeroRadicado,TipoNegociacion,NumeroContrato,DiasPactados,TipoRegimen,ValorDocumento,ValorGlosaInicial,ValorGlosaAceptada,ValorGlosaConciliada,ValorDescuentoBdua,ValorAnticipos,ValorRetencion,ValorTotalpagar,FechaHasta,Soporte,idUser,FechaRegistro,FechaActualizacion)  VALUES ('";
            foreach ($valor as $campo2 => $valor2){
                $campo2 == "FechaActualizacion" ? $sql.= $valor2."');" : $sql.= $valor2."','";
            }
            
                
            $this->Query($sql);
        }    
        
        $errores=0;

        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($_DATOS_EXCEL);
        unset($objFecha);
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
    }
    
    
    public function GuardePagosASMETMutualEnTemporal($keyArchivo,$idIPS,$idEPS,$idUser) {
        clearstatcache();
        require_once('../../../librerias/Excel/PHPExcel2.php');
        //require_once('../../../librerias/Excel/PHPExcel.php');
        //require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        //print("Clase Pagos Mutual");
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
        $Cols=['B','C','D','E','F','G','H','I','J','K','L','M','N','O','P'];
        for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
            if($columnas<>'P'){
                exit('E1;<h3>No se recibió el archivo de <strong>Pagos de la EPS ASMET Mutual</strong></h3>');
            }
            
            for ($i=10;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $FilaB=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                if($FilaA=='' or $FilaA=='Fecha' or $FilaA=='Proveedor :'){

                    continue; 

                }
                if($FilaA=='Proceso :'){
                    $c=0;
                    $Cols=['B','C','D','E','F','G','H','I','J','K','L','M','N','O','P'];
                    $Proceso=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $DescripcionProceso=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $Estado=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $Cuenta=$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    if($Cuenta==""){
                        $Cuenta=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    }
                    $Banco=$objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                    continue;
                }
                    $cell = $objPHPExcel->getActiveSheet()->getCell('A'.$i);
                    if(!\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)){
                        continue;
                    }
                    $data=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue());
                    $data=get_object_vars($data);
                    $FechaPago=$data["date"];
                    $_DATOS_EXCEL[$i]['Nit_IPS'] = $idIPS;
                    $_DATOS_EXCEL[$i]['Nit_EPS'] = $idEPS;
                    $_DATOS_EXCEL[$i]['Proceso']=$Proceso;
                    $_DATOS_EXCEL[$i]['DescripcionProceso']=$DescripcionProceso;
                    $_DATOS_EXCEL[$i]['Estado']=$Estado;
                    $_DATOS_EXCEL[$i]['Cuenta']=$Cuenta;
                    $_DATOS_EXCEL[$i]['Banco']=$Banco;
                    $_DATOS_EXCEL[$i]['FechaPagoFactura']=$FechaPago;


                    $c=0;
                    $_DATOS_EXCEL[$i]['NumeroPago']= $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['TipoOperacion'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['NumeroFactura'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorBrutoPagar'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorDescuento'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorIva'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorRetefuente'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorReteiva'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();

                    $_DATOS_EXCEL[$i]['ValorReteica'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorOtrasRetenciones'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorCruces'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorAnticipos'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorTotal'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['ValorTranferido'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();

                    $_DATOS_EXCEL[$i]['Regional'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();

                    $_DATOS_EXCEL[$i]['llaveCompuesta'] = $keyArchivo;
                    $_DATOS_EXCEL[$i]['idUser'] = $idUser;
                    $_DATOS_EXCEL[$i]['Soporte'] = $Soporte;
                    $_DATOS_EXCEL[$i]['FechaRegistro'] = $FechaActual;
                    $_DATOS_EXCEL[$i]['FechaActualizacion'] = $FechaActual;

            } 
        
        }
        
        $sql= "INSERT INTO $db.`pagos_asmet_temporal` ( `Nit_IPS`, `Nit_EPS`,`Proceso`, `DescripcionProceso`, `Estado`, `Cuenta`, `Banco`, `FechaPagoFactura`, `NumeroPago`, `TipoOperacion`, `NumeroFactura`, `ValorBrutoPagar`, `ValorDescuento`, `ValorIva`, `ValorRetefuente`, `ValorReteiva`, `ValorReteica`, `ValorOtrasRetenciones`, `ValorCruces`, `ValorAnticipos`, `ValorTotal`, `ValorTranferido`, `Regional`, `llaveCompuesta`, `idUser`, `Soporte`, `FechaRegistro`, `FechaActualizacion`) VALUES ";
        $i=0;    
        foreach($_DATOS_EXCEL as $campo => $valor){
            $i++;
            $sql.="('";
            foreach ($valor as $campo2 => $valor2){
                $campo2 == "FechaActualizacion" ? $sql.= $valor2."')," : $sql.= $valor2."','";
            }
            
            if($i==1000){
                
                $sql=substr($sql, 0, -1);
                //print($sql);
                $this->Query($sql);
                $sql= "INSERT INTO $db.`pagos_asmet_temporal` ( `Nit_IPS`, `Nit_EPS`,`Proceso`, `DescripcionProceso`, `Estado`, `Cuenta`, `Banco`, `FechaPagoFactura`, `NumeroPago`, `TipoOperacion`, `NumeroFactura`, `ValorBrutoPagar`, `ValorDescuento`, `ValorIva`, `ValorRetefuente`, `ValorReteiva`, `ValorReteica`, `ValorOtrasRetenciones`, `ValorCruces`, `ValorAnticipos`, `ValorTotal`, `ValorTranferido`, `Regional`, `llaveCompuesta`, `idUser`, `Soporte`, `FechaRegistro`, `FechaActualizacion`) VALUES ";
        
                $i=0;
            }    
            
        }   
        $sql=substr($sql, 0, -1);
        $this->Query($sql);
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($_DATOS_EXCEL);
        unset($objFecha);
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
    }
    
    
 
    public function GuardePagosASMETSASEnTemporal($keyArchivo,$idIPS,$idEPS,$idUser) {
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
        
        for ($h=0;$h<$hojas;$h++){
            
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
            if($columnas<>'IU'){
                exit("E1;No se ha recibido el archivo correcto para los <strong>pagos de ASMET SAS</strong>");
            }
            //print("$hojas, $filas, $columnas<br>");
            for ($i=0;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $FilaB=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                
                if($FilaB=='Proceso :'){
                    $c=0;
                    $Cols=['C','E','F','H','J','L','M','O','P','S','T','V','Z','AE','AI'];
                    $Proceso=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $DescripcionProceso=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $Estado=$objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                    $Cuenta=$objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();
                    
                    $Banco=$objPHPExcel->getActiveSheet()->getCell('AD'.$i)->getCalculatedValue();
                    continue;
                }
                
                $cell = $objPHPExcel->getActiveSheet()->getCell('A'.$i);
                if(!PHPExcel_Shared_Date::isDateTime($cell)){
                    continue;
                }
                    $data=PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue());
                    $FechaPago=date("Y-m-d",$data); 
                    $_DATOS_EXCEL[$h][$i]['Nit_IPS'] = $idIPS;
                    $_DATOS_EXCEL[$h][$i]['Nit_EPS'] = $idEPS;
                    $_DATOS_EXCEL[$h][$i]['Proceso']=$Proceso;
                    $_DATOS_EXCEL[$h][$i]['DescripcionProceso']=$DescripcionProceso;
                    $_DATOS_EXCEL[$h][$i]['Estado']=$Estado;
                    $_DATOS_EXCEL[$h][$i]['Cuenta']=$Cuenta;
                    $_DATOS_EXCEL[$h][$i]['Banco']=$Banco;
                    $_DATOS_EXCEL[$h][$i]['FechaPagoFactura']=$FechaPago;


                    $c=0;
                    $_DATOS_EXCEL[$h][$i]['NumeroPago']= $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['TipoOperacion'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['NumeroFactura'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorBrutoPagar'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorDescuento'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorIva'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorRetefuente'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorReteiva'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();

                    $_DATOS_EXCEL[$h][$i]['ValorReteica'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorOtrasRetenciones'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorCruces'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorAnticipos'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorTotal'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$h][$i]['ValorTranferido'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();

                    $_DATOS_EXCEL[$h][$i]['Regional'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();

                    $_DATOS_EXCEL[$h][$i]['llaveCompuesta'] = $keyArchivo;
                    $_DATOS_EXCEL[$h][$i]['idUser'] = $idUser;
                    $_DATOS_EXCEL[$h][$i]['Soporte'] = $Soporte;
                    $_DATOS_EXCEL[$h][$i]['FechaRegistro'] = $FechaActual;
                    $_DATOS_EXCEL[$h][$i]['FechaActualizacion'] = $FechaActual;

            } 
        
        }
        
        $sql= "INSERT INTO $db.`pagos_asmet_temporal` ( `Nit_IPS`, `Nit_EPS`,`Proceso`, `DescripcionProceso`, `Estado`, `Cuenta`, `Banco`, `FechaPagoFactura`, `NumeroPago`, `TipoOperacion`, `NumeroFactura`, `ValorBrutoPagar`, `ValorDescuento`, `ValorIva`, `ValorRetefuente`, `ValorReteiva`, `ValorReteica`, `ValorOtrasRetenciones`, `ValorCruces`, `ValorAnticipos`, `ValorTotal`, `ValorTranferido`, `Regional`, `llaveCompuesta`, `idUser`, `Soporte`, `FechaRegistro`, `FechaActualizacion`) VALUES ";
        
        foreach($_DATOS_EXCEL as $campo1 => $valor1){
            $i=0;
            foreach($valor1 as $campo => $valor){
                $i++;
                $sql.="('";
                foreach ($valor as $campo2 => $valor2){
                    $campo2 == "FechaActualizacion" ? $sql.= $valor2."')," : $sql.= $valor2."','";
                }

                if($i==1000){

                    $sql=substr($sql, 0, -1);
                    //print($sql);
                    $this->Query($sql);
                    $sql= "INSERT INTO $db.`pagos_asmet_temporal` ( `Nit_IPS`, `Nit_EPS`,`Proceso`, `DescripcionProceso`, `Estado`, `Cuenta`, `Banco`, `FechaPagoFactura`, `NumeroPago`, `TipoOperacion`, `NumeroFactura`, `ValorBrutoPagar`, `ValorDescuento`, `ValorIva`, `ValorRetefuente`, `ValorReteiva`, `ValorReteica`, `ValorOtrasRetenciones`, `ValorCruces`, `ValorAnticipos`, `ValorTotal`, `ValorTranferido`, `Regional`, `llaveCompuesta`, `idUser`, `Soporte`, `FechaRegistro`, `FechaActualizacion`) VALUES ";

                    $i=0;
                }    

            }   
        }
        $sql=substr($sql, 0, -1);
        $this->Query($sql);
        $objPHPExcel->disconnectWorksheets();// Good to disconnect
        $objPHPExcel->garbageCollect(); 
        clearstatcache();
        unset($objPHPExcel);
        unset($_DATOS_EXCEL);
        unset($objFecha);
        unset($sql);
        unset($Cols);
        unset($value);
        unset($key);
        unset($ColumnasTabla);
            
        
    }
    
    //Fin Clases
}
