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
    
    public function CalcularNumeroRegistros($keyArchivo,$idIPS,$Separador,$idUser) {
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
            
            if(!isset($data[45])){
                print($z);
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
                 $Dato= str_replace(".", "", $data[$i]);
                 if($i==2){
                     $Dato=$FechaFactura;
                 }
                 if($i==31){
                     $Dato=="";
                 }
                 $sql.="'$Dato',";
             }
             for($i=31;$i<=50;$i++){
                 $Dato= str_replace(".", "", $data[$i]);
                 
                 $sql.="'$Dato',";
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
    //Fin Clases
}
