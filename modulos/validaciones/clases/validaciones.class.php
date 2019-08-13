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
        
class ValidacionesEPS extends conexion{
    
    public function getKeyConciliacionMasiva($FechaConciliacion,$CmbIPS,$CmbEPS,$idUser) {
        $Fecha= str_replace("-", "", $FechaConciliacion);
        return("Conciliacion_Masiva_".$CmbIPS."_".$CmbEPS."_".$Fecha."_".$idUser);
    }
    
    public function getKeySoporteConciliacionMasiva($FechaConciliacion,$CmbIPS,$CmbEPS,$idUser) {
        $Fecha= str_replace("-", "", $FechaConciliacion);
        return("Soporte_Conciliacion_Masiva_".$CmbIPS."_".$CmbEPS."_".$Fecha."_".$idUser);
    }
    
    public function ActualizarFactura($NumeroFacturaAnterior,$NumeroFacturaNueva,$idIPS,$Observaciones,$idUser) {
        
        
        $Fecha=date("Y-m-d H:i:s");
        $DatosCargas=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosCargas["DataBase"];
        
        $this->ActualizaRegistro("$db.carteracargadaips", "NumeroFactura", $NumeroFacturaNueva, "NumeroFactura", $NumeroFacturaAnterior);
        
        $Datos["FacturaAnterior"]=$NumeroFacturaAnterior;        
        $Datos["FacturaNueva"]=$NumeroFacturaNueva;
        $Datos["Observaciones"]=$Observaciones;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$Fecha;
        
        $sql=$this->getSQLInsert("registro_actualizacion_facturas", $Datos);
        //$this->Query($sql);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }
    
    function LeerCargarTemporal($keyArchivo,$idIPS,$RutaArchivo,$Extension,$idUser) {
        
        require_once('../../../librerias/Excel/PHPExcel.php');
        require_once('../../../librerias/Excel/PHPExcel/Reader/Excel2007.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        
        $FechaActual=date("Y-m-d H:i:s");
        
        if($Extension=="xlsx"){
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }else if($Extension=="xls"){
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }else{
            exit("Solo se permiten archivos con extension xls o xlsx");
        }
        
        //$objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                 
        $objFecha = new PHPExcel_Shared_Date();    
        
        date_default_timezone_set('UTC'); //establecemos la hora local
        $Proceso="";
        $DescripcionProceso="";
        $Estado="";
        $Cuenta="";
        $Banco="";
        $Cols=['A','B','C'];
        for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex($h);
            $columnas = $objPHPExcel->setActiveSheetIndex($h)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex($h)->getHighestRow();
            if($columnas<>'C'){
                exit('E1;<h3>No se recibió el archivo de <strong>Actualización de facturas para IPS</strong></h3>');
            }
            
            for ($i=2;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){

                    continue; 

                }
                

                    $c=0;
                    $_DATOS_EXCEL[$i]['FacturaAnterior']= $objPHPExcel->getActiveSheet()->getCell($Cols[$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['FacturaNueva'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['Observaciones'] = $objPHPExcel->getActiveSheet()->getCell($Cols[++$c].$i)->getCalculatedValue();
                    
                    $_DATOS_EXCEL[$i]['idUser'] = $idUser;                    
                    $_DATOS_EXCEL[$i]['FechaRegistro'] = $FechaActual;
                    

            } 
        
        }
        
        $sql= "INSERT INTO $db.`temporal_actualizacion_facturas` ( `FacturaAnterior`, `FacturaNueva`,`Observaciones`, `idUser`, `FechaRegistro`) VALUES ";
        $i=0;    
        foreach($_DATOS_EXCEL as $campo => $valor){
            $i++;
            $sql.="('";
            foreach ($valor as $campo2 => $valor2){
                $campo2 == "FechaRegistro" ? $sql.= $valor2."')," : $sql.= $valor2."','";
            }
            
            if($i==1000){
                
                $sql=substr($sql, 0, -1);
                //print($sql);
                $this->Query($sql);
                $sql= "INSERT INTO $db.`temporal_actualizacion_facturas` ( `FacturaAnterior`, `FacturaNueva`,`Observaciones`, `idUser`, `FechaRegistro`) VALUES ";
                $i=0;
            }    
            
        }   
        $sql=substr($sql, 0, -1);
        $this->Query($sql);
        unset($objPHPExcel);
        unset($_DATOS_EXCEL);
        unset($sql);
    }
    
    public function MarcarConciliacionXEPS($db,$NumeroFactura,$idUser,$TipoConciliacion) {
        if($TipoConciliacion==1){
            $Tabla1="carteraeps";
            $Tabla2="carteracargadaips";
        }else{
            $Tabla2="carteraeps";
            $Tabla1="carteracargadaips";
        }
        
        $sql="UPDATE $db.$Tabla1 SET ConciliadoXEPS=1 WHERE NumeroFactura='$NumeroFactura' ";
        $this->Query($sql);
        
        $sql="UPDATE $db.$Tabla2 SET ConciliadoXEPS=0 WHERE NumeroFactura='$NumeroFactura' ";
        $this->Query($sql);
    }
    
    public function MarcarConciliacionXIPS($db,$NumeroFactura,$idUser,$TipoConciliacion) {
        if($TipoConciliacion==1){
            $Tabla1="carteraeps";
            $Tabla2="carteracargadaips";
        }else{
            $Tabla2="carteraeps";
            $Tabla1="carteracargadaips";
        }
        
        $sql="UPDATE $db.$Tabla1 SET ConciliadoXIPS=1 WHERE NumeroFactura='$NumeroFactura' ";
        $this->Query($sql);
        
        $sql="UPDATE $db.$Tabla2 SET ConciliadoXIPS=0 WHERE NumeroFactura='$NumeroFactura' ";
        $this->Query($sql);
                
    }
    
    public function RegistreConciliacionUsuario($db,$idUser,$NumeroFactura,$TipoConciliacion) {
        $sql="SELECT NumeroContrato FROM $db.carteraeps WHERE NumeroFactura='$NumeroFactura'";
        $Consulta=$this->Query($sql);
        $DatosContrato= $this->FetchAssoc($Consulta);
        $Contrato=$DatosContrato["NumeroContrato"];
        $key="$Contrato $NumeroFactura";
        $FechaRegistro=date("Y-m-d H:i:s");
        $Datos["ID"]=$key;
        $Datos["NumeroFactura"]=$NumeroFactura;
        $Datos["NumeroContrato"]=$Contrato;
        $Datos["TipoConciliacion"]=$TipoConciliacion;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$FechaRegistro;
        
        $sql=$this->getSQLReeplace("$db.registro_conciliaciones_ips_eps", $Datos);
        $this->Query($sql);
        
    }
    
    
    /**
     * Obtiene las columnas disponibles en una tabla
     * @param type $Tabla
     * @param type $vector
     * @return type
     */
    public function getColumnasDisponibles($Tabla,$vector){
        unset($Columnas);
        $Columnas= $this->ShowColums($Tabla);
        
        $i=0;
        $z=0;
        $ColumnasSeleccionadas["Field"]=[];
        foreach ($Columnas["Field"] as $key => $value) {

            $Consulta=$this->ConsultarTabla("tablas_campos_control", "WHERE NombreTabla='$Tabla' AND Campo='$value' AND Habilitado=0");
            $DatosExcluidas=$this->FetchAssoc($Consulta);
            if($DatosExcluidas["ID"]=='' AND $value<>'Updated' AND $value<>'Sync'){
                $DatosNombres=$this->DevuelveValores("configuraciones_nombres_campos", "NombreDB", $value);
                $ColumnasSeleccionadas["Field"][$i]=$value;
                $ColumnasSeleccionadas["Visualiza"][$i]=$value;
                if($DatosNombres["Visualiza"]<>''){
                    $ColumnasSeleccionadas["Visualiza"][$i]=$DatosNombres["Visualiza"];
                }

                $ColumnasSeleccionadas["Type"][$i]=$Columnas["Type"][$z];

            }
            $i++;
            $z++;
        }
        
        return($ColumnasSeleccionadas);
            
    }
    
    public function AgregarConciliacion($db,$DatosCruce,$NumeroFactura,$Concepto,$TipoConciliacion,$Observaciones,$Soporte,$ValorConciliacion,$ConciliadorIPS,$FechaConciliacion,$MetodoConciliacion,$idUser) {
        
        $Datos["NumeroContrato"]=$DatosCruce["NumeroContrato"];
        $Datos["NumeroFactura"]=$NumeroFactura;
        $Datos["MesServicio"]=$DatosCruce["MesServicio"];
        $Datos["FechaFactura"]=$DatosCruce["FechaFactura"];
        $Datos["NumeroRadicado"]=$DatosCruce["NumeroRadicado"];
        $Datos["Pendientes"]=$DatosCruce["Pendientes"];
        $Datos["FechaRadicado"]=$DatosCruce["FechaRadicado"];
        $Datos["ValorOriginal"]=$DatosCruce["ValorDocumento"];
        $Datos["ValorImpuestoCalculado"]=$DatosCruce["Impuestos"];
        $Datos["ValorImpuestoRetenciones"]=$DatosCruce["ImpuestosSegunASMET"];
        $Datos["ValorMenosImpuesto"]=$DatosCruce["ValorMenosImpuestos"];
        $Datos["ValorPagos"]=$DatosCruce["TotalPagos"];
        $Datos["ValorAnticipos"]=$DatosCruce["TotalAnticipos"];
        $Datos["ValorCopagos"]=$DatosCruce["TotalCopagos"];
        $Datos["ValorDevoluciones"]=$DatosCruce["TotalDevoluciones"];
        $Datos["ValorGlosaInicial"]=$DatosCruce["TotalGlosaInicial"];
        
        $Datos["ValorGlosaFavor"]=$DatosCruce["TotalGlosaFavor"];
        $Datos["ValorGlosaContra"]=$DatosCruce["TotalGlosaContra"];
        $Datos["ValorGlosaconciliar"]=$DatosCruce["GlosaXConciliar"];
        $Datos["ValorSaldoEps"]=$DatosCruce["ValorSegunEPS"];
        $Datos["ValorSaldoIps"]=$DatosCruce["ValorSegunIPS"];
        $Datos["ValorDiferencia"]=$DatosCruce["Diferencia"];
        $Datos["ConceptoConciliacion"]=$Concepto;
        $Datos["ConciliacionAFavorDe"]=$TipoConciliacion;
        
        $Datos["Observacion"]=$Observaciones;
        $Datos["Soportes"]=$Soporte;
        $Datos["ValorConciliacion"]=$ValorConciliacion;
        $Datos["ConciliadorIps"]=$ConciliadorIPS;        
        $Datos["FechaConciliacion"]=$FechaConciliacion;
        $Datos["ViaConciliacion"]=$MetodoConciliacion;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=date("Y-m-d H:i:s");
        
        $sql= $this->getSQLInsert("$db.conciliaciones_cruces", $Datos);
        $this->Query($sql);
    }
    
    public function AnularConciliacion($db,$idConciliacion,$TipoAnulacion,$NumeroFactura,$ValorAnulado,$Observaciones) {
        
        $Datos["Fecha"]=date("Y-m-d H:i:s");
        $Datos["idConciliacion"]=$idConciliacion;
        $Datos["TipoAnulacion"]=$TipoAnulacion;
        $Datos["NumeroFactura"]=$NumeroFactura;
        $Datos["ValorAnulado"]=$ValorAnulado;
        $Datos["Observaciones"]=$Observaciones;
        $sql=$this->getSQLInsert("$db.conciliaciones_anulaciones", $Datos);
        $this->Query($sql);
        $this->ActualizaRegistro("$db.carteraeps", "Estado", 0, "NumeroFactura", $NumeroFactura);
        $this->ActualizaRegistro("$db.conciliaciones_cruces", "ValorConciliacion", 0, "ID", $idConciliacion);
            
    }
    
    public function GuardeConciliacionMasivaEnTemporal($FechaConciliacionMasiva,$idIPS,$idEPS,$idUser,$ConciliadorIPS,$ViaConciliacion,$ConceptoConciliacion) {
        clearstatcache();
        require_once('../../../librerias/Excel/PHPExcel2.php');
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $idIPS);
        $db=$DatosIPS["DataBase"];
        $carpeta="../../../soportes/$idIPS/Conciliaciones/";
        $keyArchivoSoporte=$this->getKeySoporteConciliacionMasiva($FechaConciliacionMasiva, $idIPS, $idEPS, $idUser);
        $keyArchivoConciliacion=$this->getKeyConciliacionMasiva($FechaConciliacionMasiva, $idIPS, $idEPS, $idUser);
        $Extension='xlsx';
        $RutaArchivo=$carpeta.$keyArchivoConciliacion.".".$Extension;
        $FechaActual=date("Y-m-d H:i:s");
        
        $Soporte=$carpeta.$keyArchivoSoporte.".".$Extension;
       
        
        $objReader = IOFactory::createReader('Xlsx');
        
        
       
        $objPHPExcel = $objReader->load($RutaArchivo);   
        $hojas=$objPHPExcel->getSheetCount();
                 
              
        $hojas=$objPHPExcel->getSheetCount();
        
        date_default_timezone_set('UTC'); //establecemos la hora local
        
        $Cols=[ 'ZZ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ'];
        
        
        
        //for ($h=0;$h<$hojas;$h++){
            $objPHPExcel->setActiveSheetIndex(0);
            $columnas = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            
            if($columnas<>'AB'){
                exit('E1;<h3>No se recibió el archivo de <strong>Actualizaciones Masivas: '.$columnas.'</strong></h3>');
            }
           
            $sql= "INSERT INTO $db.`temp_conciliaciones_cruces` ( ";
            $sql.="`NumeroFactura`,";
            $sql.="`ConceptoConciliacion`,";
            $sql.="`ConciliacionAFavorDe`,";
            $sql.="`Observacion`,";
            $sql.="`Soportes`,";
            $sql.="`ValorConciliacion`,";
            $sql.="`ConciliadorIps`,";
            $sql.="`FechaConciliacion`,";
            $sql.="`ViaConciliacion`,";
            $sql.="`idUser`,";
            $sql.="`FechaRegistro`";
            
            $sql.=") VALUES ";
            $r=0;
            
            for ($i=1;$i<=$filas;$i++){
                $FilaA=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                
                if($FilaA==''){
                    continue; 
                }
                                         
                if(is_numeric($FilaA)){
                    $r++;//Contador de filas a insertar
                    if($FilaA<>$idIPS){
                        exit("E1;El archivo contiene registros de una IPS diferente en la Fila A$i, $FilaA");
                    }
                    $NumeroFactura=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $NumeroFactura= str_replace("'", "", $NumeroFactura);
                    
                    $ConciliacionAFavorDe=$objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue();
                    $ConciliacionAFavorDe= str_replace("'", "", $ConciliacionAFavorDe);
                    $Observaciones=$objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getCalculatedValue();
                    $Observaciones= str_replace("'", "", $Observaciones);
                    $ValorConciliacion=$objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getCalculatedValue();
                    $ValorConciliacion= str_replace("'", "", $ValorConciliacion);
                    if($NumeroFactura==''){
                        exit("E1;El archivo no contiene el Numero de la Factura en el Campo B$i");
                    }
                    
                    if($ConciliacionAFavorDe==''){
                        exit("E1;El archivo no especifíca a favor de quien se realiza la conciliacion en el Campo Y$i");
                    }
                    if(strtoupper($ConciliacionAFavorDe)<>"IPS" and strtoupper($ConciliacionAFavorDe)<>"EPS"){
                        exit("E1;El archivo debe especificar si es a favor de la EPS o IPS en el Campo Campo Y$i");
                    }
                    if(strtoupper($ConciliacionAFavorDe)=="EPS"){
                        $ConciliacionAFavorDe=1;
                    }
                    if(strtoupper($ConciliacionAFavorDe)=="IPS"){
                        $ConciliacionAFavorDe=2;
                    }
                    
                        
                    if($Observaciones==''){
                        exit("E1;El archivo no contiene observaciones en el Campo Z$i");
                    }
                    
                    if($ValorConciliacion==''){
                        exit("E1;El archivo no contiene el valor de la Conciliacion en el Campo AA$i");
                    }
                    
                    if(!is_numeric($ValorConciliacion) or $ValorConciliacion<=0){
                        exit("E1;El archivo contiene un valor de la Conciliación no admitido, solo se permiten valores númericos positivos en el Campo AA$i");
                    }
                    
                    $sql.="(";
                    $sql.="'$NumeroFactura',";
                    $sql.="'$ConceptoConciliacion',";
                    $sql.="'$ConciliacionAFavorDe',";
                    $sql.="'$Observaciones',";
                    $sql.="'$Soporte',";
                    $sql.="'$ValorConciliacion',";
                    $sql.="'$ConciliadorIPS',";
                    $sql.="'$FechaConciliacionMasiva',";
                    $sql.="'$ViaConciliacion',";
                    $sql.="'$idUser',";
                    $sql.="'$FechaActual'),";
                    
                    if($r==200){
                
                        $sql=substr($sql, 0, -1);
                        //print($sql);
                        $this->Query($sql);
                        $sql= "INSERT INTO $db.`temp_conciliaciones_cruces` ( ";
                        $sql.="`NumeroFactura`,";
                        $sql.="`ConceptoConciliacion`,";
                        $sql.="`ConciliacionAFavorDe`,";
                        $sql.="`Observacion`,";
                        $sql.="`Soportes`,";
                        $sql.="`ValorConciliacion`,";
                        $sql.="`ConciliadorIps`,";
                        $sql.="`FechaConciliacion`,";
                        $sql.="`ViaConciliacion`,";
                        $sql.="`idUser`,";
                        $sql.="`FechaRegistro`";

                        $sql.=") VALUES ";
                        $r=0;
                    }  
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
    
    public function CrearActaConciliacion($FechaCorte,$CmbIPS,$RepresentanteLegalIPS,$EncargadoEPS,$idUser) {
        $FechaRegistro=date("Y-m-d H:i:s");
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $CmbIPS);
        
        $Datos["FechaCorte"]=$FechaCorte;
        $Datos["RazonSocialIPS"]=$DatosIPS["Nombre"];
        $Datos["NIT_IPS"]=$CmbIPS;
        $Datos["RepresentanteLegal"]=$RepresentanteLegalIPS;
        $Datos["Departamento"]=$DatosIPS["Departamento"];
        $Datos["EncargadoEPS"]=$EncargadoEPS;
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$FechaRegistro;
        $sql=$this->getSQLInsert("actas_conciliaciones", $Datos);
        $this->Query($sql);
        $idActa=$this->ObtenerMAX("actas_conciliaciones", "ID", 1, "");
        return($idActa);
    }
    
    //Fin Clases
}
