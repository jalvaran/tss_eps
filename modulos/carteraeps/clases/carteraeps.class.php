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
        $z=0;
        while ($z < $MaxRegistros && ($data = fgetcsv($handle, 1000, $Separador)) !== FALSE) {
            
            $z++;
            if($z<=$LineaActual){
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
                 $Dato= str_replace(".", "", $data[$i]);
                 if($i==2){
                     $Dato=$FechaFactura;
                 }
                 if($i>=23 and $i<=25){
                     $Dato=="";
                 }
                 $sql.="'$Dato',";
             }
             for($i=23;$i<=50;$i++){
                 $Dato= str_replace(".", "", $data[$i]);
                 
                 $sql.="'$Dato',";
             }
             $sql.="'$Soporte','$EPS','$idUser','$FechaRegistro','$FechaActualizacion'),";
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
        while ($z < $MaxRegistros && ($data = fgetcsv($handle, 1000, $Separador)) !== FALSE) {
            
            $z++;
            if($z<=$LineaActual){
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

        //print($DatosUpload["Soporte"]);
    }
    
    
    public function obtengaHojasFilas($keyArchivo,$idIPS,$idEPS,$idUser) {
        require_once('../../../librerias/Excel/PHPExcel.php');
        require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo' AND idUser='$idUser'";
        $Consulta=$this->Query($sql);
        $DatosUpload=$this->FetchArray($Consulta);
        
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
        $objPHPExcel->setActiveSheetIndex(0);
        
        $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $hojas=$objPHPExcel->getSheetCount();
        $DatosArchivo[0]["Filas"]=$filas;
        $DatosArchivo[0]["Columnas"]=$columnas;
        $DatosArchivo[0]["Hojas"]=$hojas;
        print(json_encode($DatosArchivo));
    }
    
    //Fin Clases
}
