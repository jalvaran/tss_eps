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
        //$Datos["Pendientes"]=$DatosCruce["Pendientes"];
        $Datos["FechaRadicado"]=$DatosCruce["FechaRadicado"];
        $Datos["ValorOriginal"]=$DatosCruce["ValorDocumento"];
        $Datos["ValorImpuestoCalculado"]=$DatosCruce["ImpuestosCalculados"];
        $Datos["ValorImpuestoRetenciones"]=$DatosCruce["Impuestos"];
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
    
    public function CrearActaConciliacion($CmbTipoNegocionActa,$FechaInicial,$FechaCorte,$CmbIPS,$RepresentanteLegalIPS,$EncargadoEPS,$idUser,$TamanoFuente=0) {
        $FechaRegistro=date("Y-m-d H:i:s");
        $FechaFirma=date("Y-m-d");
        $DatosIPS=$this->DevuelveValores("ips", "NIT", $CmbIPS);
        $DatosMesServicio= explode("-", $FechaInicial);
        $MesServicioInicial=$DatosMesServicio[0].$DatosMesServicio[1];
        $DatosMesServicio= explode("-", $FechaCorte);
        $MesServicioFinal=$DatosMesServicio[0].$DatosMesServicio[1];
        
        $Datos["FechaCorte"]=$FechaCorte;
        $Datos["FechaInicial"]=$FechaInicial;
        $Datos["MesServicioInicial"]=$MesServicioInicial;
        $Datos["MesServicioFinal"]=$MesServicioFinal;
        $Datos["TamanoFuente"]=$TamanoFuente;
        $Datos["RazonSocialIPS"]=$DatosIPS["Nombre"];
        $Datos["NIT_IPS"]=$CmbIPS;
        $Datos["RepresentanteLegal"]=$RepresentanteLegalIPS;
        $Datos["Departamento"]=$DatosIPS["Departamento"];
        $Datos["EncargadoEPS"]=$EncargadoEPS;
        $Datos["FechaFirma"]=$FechaFirma;
        $Datos["CiudadFirma"]='Popayán';   
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$FechaRegistro;
        $Datos["TipoActa"]=$CmbTipoNegocionActa;
        
        $sql=$this->getSQLInsert("actas_conciliaciones", $Datos);
        $this->Query($sql);
        $idActa=$this->ObtenerMAX("actas_conciliaciones", "ID", 1, "");
        
        $sql="SELECT * FROM actas_conciliaciones_resultados_compromisos_predeterminados ORDER BY ID ASC";
        $consulta=$this->Query($sql);
        while($datos_consulta=$this->FetchAssoc($consulta)){
            $this->AgregarCompromisoActaConciliacion($idActa, utf8_encode($datos_consulta["Texto"]), "", $idUser);
        }
        return($idActa);
    }
    
    public function AgregarCompromisoActaConciliacion($idActaConciliacion,$TxtCompromisoNuevo,$Responsable,$idUser) {
        $FechaRegistro=date("Y-m-d H:i:s");
        $FechaFirma=date("Y-m-d");
        $Datos["idActaConciliacion"]=$idActaConciliacion;
        $Datos["ResultadoCompromiso"]=$TxtCompromisoNuevo;
        $Datos["Responsable"]=$Responsable;            
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$FechaRegistro;
        
        $sql=$this->getSQLInsert("actas_conciliaciones_resultados_compromisos", $Datos);
        $this->Query($sql);
        
    }
    
        
    public function CalculeDiferenciasProceso1($idActaConciliacion,$db,$Diferencia) {
        $DatosActa= $this->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion);
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
        /*
        $sql="SELECT SUM(DiferenciaXPagos) as DiferenciaXPagos,
                     SUM(DiferenciaXAnticipos) as DiferenciaXAnticipos,
                     SUM(DiferenciaXCopagos) as DiferenciaXCopagos,
                     SUM(DiferenciaXDescuentoPGP) as DiferenciaXDescuentoPGP,
                     SUM(DiferenciaXOtrosDescuentos) as DiferenciaXOtrosDescuentos,
                     SUM(DiferenciaXAjustesCartera) as DiferenciaXAjustesCartera,
                     SUM(DiferenciaXGlosaFavorEPS) as DiferenciaXGlosaFavorEPS,
                     SUM(DiferenciaXGlosaContraEPS) as DiferenciaXGlosaContraEPS,
                     SUM(DiferenciaXDevoluciones) as DiferenciaXDevoluciones,
                     SUM(DiferenciaXImpuestos) as DiferenciaXImpuestos,
                     SUM(DiferenciaVariada) as DiferenciaVariada,        
                     SUM(DiferenciaXGlosaXConciliar) AS DiferenciaXGlosaXConciliar,
                     SUM(DiferenciaXValorFacturado) AS DiferenciaXValorFacturado,
                     SUM(DiferenciaXDevolucionesNoIPS) AS DiferenciaXDevolucionesNoIPS,
                     SUM(GlosasXConciliar2) AS GlosasXConciliar2,
                     SUM(XPagos2) AS XPagos2,
                     SUM(DiferenciaVariada) AS DiferenciaVariada
                 FROM $db.vista_cruce_totales_actas_conciliaciones WHERE MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal'";
        */
        $sql="SELECT SUM(t1.DiferenciaXPagosNoDescargados) as DiferenciaXPagosNoDescargados,
                     SUM(t1.DiferenciaXGlosasPendientesXConciliar) as DiferenciaXGlosasPendientesXConciliar,
                     SUM(t1.DiferenciaXFacturasDevueltas) as DiferenciaXFacturasDevueltas,
                     SUM(t1.DiferenciaXDiferenciaXImpuestos) as DiferenciaXDiferenciaXImpuestos,
                     SUM(t1.DiferenciaXFacturasNoRelacionadasXIPS) as DiferenciaXFacturasNoRelacionadasXIPS,
                     SUM(t1.DiferenciaXAjustesDeCartera) as DiferenciaXAjustesDeCartera,
                     SUM(t1.DiferenciaXValorFacturado) as DiferenciaXValorFacturado,
                     SUM(t1.DiferenciaXGlosasPendientesXDescargarIPS) as DiferenciaXGlosasPendientesXDescargarIPS,
                     SUM(t1.DiferenciaVariada) as DiferenciaVariada
                 FROM $db.hoja_de_trabajo t1 WHERE  
                    EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') 
                    AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal' ";
        $DatosTotales=$this->FetchAssoc($this->Query($sql));
        //$TotalPendientesRadicados= $this->SumeColumna("$db.vista_pendientes", "Total", "Radicados", "Radicados");
        $sql="SELECT SUM(Total) as TotalPendientes FROM $db.vista_pendientes";
        $TotalPendientes = $this->FetchAssoc($this->Query($sql));
        $TotalPendientesRadicados=$TotalPendientes["TotalPendientes"];
        if(!is_numeric($TotalPendientesRadicados)){
            $TotalPendientesRadicados=0;
        }
        $FacturasNoRelacionadasXIPS= $this->SumeColumna("$db.hoja_de_trabajo", "ValorSegunEPS", "NoRelacionada", 1);
        $sql="SELECT SUM(ValorSegunEPS) as Total FROM $db.hoja_de_trabajo t1 WHERE EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') 
                    AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal' AND t1.NoRelacionada='1' ";
        $TotalIPSDato=$this->FetchAssoc($this->Query($sql));
        
        $sql="SELECT SUM(ValorTotalpagar) as Total FROM $db.carteracargadaips t3 "
                . "INNER JOIN $db.hoja_de_trabajo t1 ON t1.NumeroFactura=t3.NumeroFactura WHERE EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE  t2.NumeroContrato = t1.NumeroContrato and t2.idActaConciliacion='$idActaConciliacion' ) AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal'";
        $DatosIPS= $this->FetchAssoc($this->Query($sql));
        $TotalIPSEnCruce=$DatosIPS["Total"];
        
        $sql="SELECT SUM(ValorTotalpagar) as Total FROM $db.carteracargadaips t3 ";
        $DatosIPS= $this->FetchAssoc($this->Query($sql));
        $TotalIPS=$DatosIPS["Total"];
        
        //$FacturasNoRelacionadasXIPS=$TotalIPSDato["Total"];
        
        $FacturasNoRelacionadasXIPS=$TotalIPS-$TotalIPSEnCruce;
        if(!is_numeric($FacturasNoRelacionadasXIPS)){
            $FacturasNoRelacionadasXIPS=0;
        }
        $sql="SELECT SUM(ValorSegunIPS) AS Total FROM $db.hoja_de_trabajo t1 WHERE EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') 
                    AND NOT EXISTS(SELECT 1 FROM $db.carteracargadaips t3 WHERE t1.NumeroFactura=t3.NumeroFactura)
                    AND MesServicio>='$MesServicioInicial' AND MesServicio<='$MesServicioFinal' ";
        $TotalConsulta= $this->FetchAssoc($this->Query($sql));
        $TotalFacturasSinRelacionsrXIPS=$TotalConsulta["Total"];
        //print($TotalFacturasSinRelacionsrXIPS);
        
        /*
        $DetalleDiferencias["DiferenciaXPagos"]=(round(abs($DatosTotales["DiferenciaXPagos"])+abs($DatosTotales["DiferenciaXAnticipos"])+abs($DatosTotales["DiferenciaXGlosaContraEPS"])+abs($DatosTotales["XPagos2"])));
        $DetalleDiferencias["FacturasIPSNoRelacionadasEPS"]=abs(round($TotalFacturasSinRelacionsrXIPS));        
        $DetalleDiferencias["GlosasPendientesXConciliar"]=(round(abs($DatosTotales["DiferenciaXGlosaXConciliar"])+abs($DatosTotales["GlosasXConciliar2"])));
        $DetalleDiferencias["FacturasDevueltas"]=(round(abs($DatosTotales["DiferenciaXDevoluciones"])+abs($DatosTotales["DiferenciaXDevolucionesNoIPS"])));
        $DetalleDiferencias["DiferenciaXImpuestos"]=abs(round($DatosTotales["DiferenciaXImpuestos"]));
        $DetalleDiferencias["DescuentoXRetefuente"]=0;
        $DetalleDiferencias["FacturasNoRelacionadasXIPS"]=abs(round($FacturasNoRelacionadasXIPS));
        $DetalleDiferencias["RetencionesImpuestosNoProcedentes"]=0;
        $DetalleDiferencias["AjustesDeCartera"]=(round(abs($DatosTotales["DiferenciaXCopagos"])+abs($DatosTotales["DiferenciaXOtrosDescuentos"])+abs($DatosTotales["DiferenciaXAjustesCartera"])));
        $DetalleDiferencias["DiferenciaXValorFacturado"]=abs(round($DatosTotales["DiferenciaXValorFacturado"]));
        $DetalleDiferencias["DiferenciaXUPC"]=0;        
        $DetalleDiferencias["GlosasPendientesXDescargarIPS"]=(round(abs($DatosTotales["DiferenciaXDescuentoPGP"])+abs($DatosTotales["DiferenciaXGlosaFavorEPS"])));
        $DetalleDiferencias["AnticiposPendientesXCruzar"]=0;
        $DetalleDiferencias["DescuentosLMA"]=0;
        $DetalleDiferencias["PendientesAuditoria"]=abs(round($TotalPendientesRadicados));
         * 
         */
        //$DetalleDiferencias["DiferenciaVariada"]=abs(round($DatosTotales["DiferenciaVariada"]));
        
        $DetalleDiferencias["DiferenciaXPagos"]=(abs($DatosTotales["DiferenciaXPagosNoDescargados"]));// + abs($DatosTotales["DiferenciaVariada"]));
        //$DetalleDiferencias["FacturasIPSNoRelacionadasEPS"]=abs(round($TotalFacturasSinRelacionsrXIPS ));  
        $DetalleDiferencias["FacturasIPSNoRelacionadasEPS"]=abs(round($FacturasNoRelacionadasXIPS ));
        $DetalleDiferencias["GlosasPendientesXConciliar"]=$DatosTotales["DiferenciaXGlosasPendientesXConciliar"];
        $DetalleDiferencias["FacturasDevueltas"]=$DatosTotales["DiferenciaXFacturasDevueltas"];
        $DetalleDiferencias["DiferenciaXImpuestos"]=abs(round($DatosTotales["DiferenciaXDiferenciaXImpuestos"]));
        $DetalleDiferencias["DescuentoXRetefuente"]=0;
        //$DetalleDiferencias["FacturasNoRelacionadasXIPS"]=abs(round($TotalFacturasSinRelacionsrXIPS));
        $DetalleDiferencias["FacturasNoRelacionadasXIPS"]=0;
        $DetalleDiferencias["RetencionesImpuestosNoProcedentes"]=0;
        $DetalleDiferencias["AjustesDeCartera"]=$DatosTotales["DiferenciaXAjustesDeCartera"];
        $DetalleDiferencias["DiferenciaXValorFacturado"]=abs(round($DatosTotales["DiferenciaXValorFacturado"]));
        $DetalleDiferencias["DiferenciaXUPC"]=0;        
        $DetalleDiferencias["GlosasPendientesXDescargarIPS"]=($DatosTotales["DiferenciaXGlosasPendientesXDescargarIPS"]);
        $DetalleDiferencias["AnticiposPendientesXCruzar"]=0;
        $DetalleDiferencias["DescuentosLMA"]=0;
        $DetalleDiferencias["PendientesAuditoria"]=abs(round($TotalPendientesRadicados));
        $DiferenciaVariada=$DatosTotales["DiferenciaVariada"];
        $DetalleDiferencias["DiferenciaXPagos"]=$DetalleDiferencias["DiferenciaXPagos"]+$DiferenciaVariada;
        $TotalDiferencias= array_sum($DetalleDiferencias);
        //print($TotalDiferencias);
        
        //print($TotalDiferencias);
        $DiferenciaFaltante=abs($Diferencia)-abs($TotalDiferencias);
        //print($DiferenciaFaltante);
        //$DiferenciaTemporal=$DetalleDiferencias["DiferenciaXPagos"]+$DiferenciaFaltante;
        if($DiferenciaFaltante<>0){
            $keyMax=array_keys($DetalleDiferencias,max($DetalleDiferencias));            
            $idKey=$keyMax[0];
            $DetalleDiferencias[$idKey]=$DetalleDiferencias[$idKey]+$DiferenciaFaltante;
            //$DetalleDiferencias["DiferenciaXPagos"]=0;
        }else{
           // $DetalleDiferencias["DiferenciaXPagos"]=$DetalleDiferencias["DiferenciaXPagos"]+$DiferenciaFaltante;
        }
        $DetalleDiferencias["TotalDiferencias"]= array_sum($DetalleDiferencias);
         
        //$DetalleDiferencias["TotalDiferencias"]=$TotalDiferencias;
        return($DetalleDiferencias);
    }
    
    public function CalculeDiferenciasMultiplesAC($db) {
        
        $sql="SELECT NumeroFactura
                 FROM $db.vista_cruce_totales_actas_conciliaciones WHERE DiferenciaVariada<>0 LIMIT 1";
        
        $Consulta=$this->Query($sql);
        while($Facturas=$this->FetchAssoc($Consulta)){
            $DatosDiferencias=BusqueDiferenciasEnVector($Facturas["NumeroFactura"]);
        }
        $DetalleDiferencias["DiferenciaXPagos"]=abs(round($DatosTotales["DiferenciaXPagos"]+$DatosTotales["DiferenciaXAnticipos"]));
        $DetalleDiferencias["FacturasIPSNoRelacionadasEPS"]=abs(round($TotalFacturasSinRelacionsrXIPS));        
        $DetalleDiferencias["GlosasPendientesXConciliar"]=abs(round($DatosTotales["DiferenciaXGlosaXConciliar"]));
        $DetalleDiferencias["FacturasDevueltas"]=abs(round($DatosTotales["DiferenciaXDevoluciones"]));
        $DetalleDiferencias["DiferenciaXImpuestos"]=abs(round($DatosTotales["DiferenciaXImpuestos"]));
        $DetalleDiferencias["DescuentoXRetefuente"]=0;
        $DetalleDiferencias["FacturasNoRelacionadasXIPS"]=abs(round($FacturasNoRelacionadasXIPS));
        $DetalleDiferencias["RetencionesImpuestosNoProcedentes"]=0;
        $DetalleDiferencias["AjustesDeCartera"]=abs(round($DatosTotales["DiferenciaXCopagos"]+$DatosTotales["DiferenciaXOtrosDescuentos"]+$DatosTotales["DiferenciaXAjustesCartera"]));
        $DetalleDiferencias["DiferenciaXValorFacturado"]=abs(round($DatosTotales["DiferenciaXValorFacturado"]));
        $DetalleDiferencias["DiferenciaXUPC"]=0;        
        $DetalleDiferencias["GlosasPendientesXDescargarIPS"]=abs(round($DatosTotales["DiferenciaXDescuentoPGP"]+$DatosTotales["DiferenciaXGlosaFavorEPS"]));
        $DetalleDiferencias["AnticiposPendientesXCruzar"]=0;
        $DetalleDiferencias["DescuentosLMA"]=0;
        $DetalleDiferencias["PendientesAuditoria"]=abs(round($TotalPendientesRadicados));
        return($DetalleDiferencias);
    }
    
    public function AgregueFirmaActa($idActa,$Nombre,$Cargo,$Empresa) {
        $Datos["idActaConciliacion"]=$idActa;
        $Datos["Nombre"]=$Nombre;
        $Datos["Cargo"]=$Cargo;
        $Datos["Empresa"]=$Empresa;
        $sql=$this->getSQLInsert("actas_conciliaciones_firmas", $Datos);
        $this->Query($sql);
        
    }
    
    public function AgregueContratoActaConciliacion($idActa,$NumeroContrato) {
        $Datos["idActaConciliacion"]=$idActa;
        $Datos["NumeroContrato"]=$NumeroContrato;        
        $sql=$this->getSQLInsert("actas_conciliaciones_contratos", $Datos);
        $this->Query($sql);        
    }
    
    public function obtengaValoresGeneralesActaConciliacion($db,$idActaConciliacion) {
        $DatosActa=$this->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion);
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
        $TipoNegociacion=$DatosActa["TipoActa"];
        /*
        $sql="SELECT SUM(Total) as TotalPendientes FROM $db.vista_pendientes";
        $TotalPendientes = $this->FetchAssoc($this->Query($sql));
        
         * 
         */
        $sql="SELECT SUM(ValorDocumento) AS TotalPendientes FROM $db.hoja_de_trabajo WHERE (PendientesPorRadicados='SI' OR PendientesPorDevoluciones='SI' OR PendientesPorCopagos='SI' OR PendientesPorNotas='SI' ) AND TipoNegociacion='$TipoNegociacion'";
        $TotalPendientes = $this->FetchAssoc($this->Query($sql));
        
        //$TotalPendientesRadicados=$this->SumeColumna("$db.vista_pendientes", "Total", 1, "");
        //print("Total Pendientes: ".$TotalPendientes["TotalPendientes"]);
        $TotalFacturasSinRelacionsrXIPS= $this->SumeColumna("$db.vista_facturas_sr_ips", "ValorTotalpagar", 1, "");   
            
        $sql="SELECT SUM(ValorSegunEPS) AS TotalEPS,SUM(ValorSegunIPS) AS TotalIPS FROM $db.`hoja_de_trabajo` t1 WHERE "
                . "EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE  t2.NumeroContrato = t1.NumeroContrato and t2.idActaConciliacion='$idActaConciliacion' ) AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal' AND t1.TipoNegociacion<='$TipoNegociacion'";
                
        $TotalesCruce=$this->FetchAssoc($this->Query($sql));
        /*
        $sql="SELECT SUM(ValorTotalpagar) as Total FROM $db.carteracargadaips t3 "
                . "INNER JOIN $db.hoja_de_trabajo t1 ON t1.NumeroFactura=t3.NumeroFactura WHERE EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE  t2.NumeroContrato = t1.NumeroContrato and t2.idActaConciliacion='$idActaConciliacion' ) AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal'";
        $DatosIPS= $this->FetchAssoc($this->Query($sql));
        
         * 
         */
        
        $sql="SELECT SUM(ValorTotalpagar) as Total FROM $db.carteracargadaips WHERE TipoNegociacion='$TipoNegociacion'";
        $DatosIPS= $this->FetchAssoc($this->Query($sql));
        
        $ValorSegunEPS=$TotalesCruce["TotalEPS"]-$TotalPendientes["TotalPendientes"];
        
        $ValorSegunIPS=$DatosIPS["Total"];
        $Diferencia=$ValorSegunEPS-$ValorSegunIPS;
        $SaldoConciliadoParaPago=$ValorSegunEPS;
        
        $TotalesActa["ValorSegunEPS"]=$ValorSegunEPS;
        $TotalesActa["ValorSegunIPS"]=$ValorSegunIPS;
        $TotalesActa["Diferencia"]=$Diferencia;
        $TotalesActa["SaldoConciliadoParaPago"]=$SaldoConciliadoParaPago;
        $TotalesActa["TotalPendientesRadicados"]=$TotalPendientes["TotalPendientes"];
        $TotalesActa["TotalFacturasSinRelacionsrXIPS"]=$TotalFacturasSinRelacionsrXIPS;
        //print_r($TotalesActa);
        return($TotalesActa);
        
    }
    
    public function AjusteValoresInicialesActaConciliacion($db,$idActaConciliacion,$TotalesActa) {
        $Datos["ValorSegunEPS"]=$TotalesActa["ValorSegunEPS"];
        $Datos["ValorSegunIPS"]=$TotalesActa["ValorSegunIPS"];
        $Datos["Diferencia"]=$TotalesActa["Diferencia"];
        $Datos["SaldoConciliadoPago"]=$TotalesActa["SaldoConciliadoParaPago"];

        $Datos["DiferenciaXPagos"]=0;
        $Datos["FacturasNoRegistradasXEPS"]=0;
        $Datos["GlosasPendientesXConciliar"]=0;
        $Datos["TotalDevoluciones"]=0;
        $Datos["ImpuestosNoRelacionadosIPS"]=0;
        $Datos["RetefuenteNoMerecida"]=0;
        $Datos["FacturasSinRelacionIPS"]=0;
        $Datos["RetencionesImpuestosNoProcedentes"]=0;
        $Datos["AjustesDeCartera"]=0;
        $Datos["FacturasConValorDiferente"]=0;
        $Datos["FacturasConReajusteUPC"]=0;
        $Datos["GlosasConciliadasPendientesDescargaIPS"]=0;
        $Datos["TotalAnticipos"]=0;
        $Datos["DescuentosReconocimientosLMA"]=0;
        $Datos["FacturasPendienteAuditoria"]=0;

        $sql=$this->getSQLUpdate("actas_conciliaciones", $Datos);
        $sql.=" WHERE ID='$idActaConciliacion'";
        //print($sql);
        $this->Query($sql);
    }
    
    
    public function CopiarItemsAlActaConciliacion($db,$idActa,$idUser) {
        $FechaRegisto=date("Y-m-d H:i:s");
        $DatosActa=$this->DevuelveValores("actas_conciliaciones", "ID", $idActa);
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
                
        $sql="INSERT INTO $db.actas_conciliaciones_items 
                (`idActaConciliacion`,`FechaFactura`,`MesServicio`,`DepartamentoRadicacion`,`NumeroRadicado`,`FechaRadicado`,`NumeroContrato`,`NumeroFactura`,`ValorDocumento`,`Impuestos`,`TotalPagos`,
                `TotalAnticipos`,`TotalCopagos`,`DescuentoPGP`,`OtrosDescuentos`,`AjustesCartera`,`TotalGlosaInicial`,`TotalGlosaFavor`,`TotalGlosaContra`,`GlosaXConciliar`,`TotalDevoluciones`,`ValorSegunEPS`,
                `ValorSegunIPS`,`Diferencia`,`NoRelacionada`,`FechaRegistro`,`idUser`,`DescuentoBDUA`,`NumeroDiasLMA`,`ValorAPagarLMA`,`CodigoSucursal`,`NumeroOperacion`,`ImpuestosPorRecuperar`)  
                SELECT '$idActa',`FechaFactura`,`MesServicio`,`DepartamentoRadicacion`,`NumeroRadicado`,`FechaRadicado`,`NumeroContrato`,`NumeroFactura`,`ValorDocumento`,`Impuestos`,`TotalPagos`,
                `TotalAnticipos`,`TotalCopagos`,`DescuentoPGP`,`OtrosDescuentos`,`AjustesCartera`,`TotalGlosaInicial`,`TotalGlosaFavor`,`TotalGlosaContra`,`GlosaXConciliar`,`TotalDevoluciones`,`ValorSegunEPS`,
                `ValorSegunIPS`,`Diferencia` ,'0','$FechaRegisto','$idUser',`DescuentoReconocimientoBDUA` ,`NumeroDiasLMA`,`ValorAPagarLMA`,`CodigoSucursal`,`NumeroOperacion`,`ImpuestosPorRecuperar`   
                 FROM $db.hoja_de_trabajo t1 WHERE 
                EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActa') 
                AND NOT EXISTS (SELECT 1 FROM $db.actas_conciliaciones_items t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND t3.idActaConciliacion='$idActa') 

                AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal' limit 1000";
        
        $this->Query($sql);
                
    }
    
    public function CopiarItemsNoCruceAlActaConciliacion($db,$idActa,$idUser) {
        $FechaRegisto=date("Y-m-d H:i:s");
        $DatosActa=$this->DevuelveValores("actas_conciliaciones", "ID", $idActa);
        $MesServicioInicial=$DatosActa["MesServicioInicial"];
        $MesServicioFinal=$DatosActa["MesServicioFinal"];
                
        $sql="INSERT INTO $db.actas_conciliaciones_items 
                (`idActaConciliacion`,`FechaFactura`,`MesServicio`,`DepartamentoRadicacion`,`NumeroRadicado`,`FechaRadicado`,`NumeroContrato`,`NumeroFactura`,`ValorDocumento`,`Impuestos`,`TotalPagos`,
                `TotalAnticipos`,`TotalCopagos`,`DescuentoPGP`,`OtrosDescuentos`,`AjustesCartera`,`TotalGlosaInicial`,`TotalGlosaFavor`,`TotalGlosaContra`,`GlosaXConciliar`,`TotalDevoluciones`,`ValorSegunEPS`,
                `ValorSegunIPS`,`Diferencia`,`NoRelacionada`,`FechaRegistro`,`idUser`,`DescuentoBDUA`,`NumeroDiasLMA`,`ValorAPagarLMA`,`CodigoSucursal`,`NumeroOperacion`,`ImpuestosPorRecuperar`)  
                SELECT '$idActa',`FechaFactura`,`MesServicio`,`DepartamentoRadicacion`,`NumeroRadicado`,`FechaRadicado`,`NumeroContrato`,`NumeroFactura`,`ValorDocumento`,`Impuestos`,`TotalPagos`,
                `TotalAnticipos`,`TotalCopagos`,`DescuentoPGP`,`OtrosDescuentos`,`AjustesCartera`,`TotalGlosaInicial`,`TotalGlosaFavor`,`TotalGlosaContra`,`GlosaXConciliar`,`TotalDevoluciones`,`ValorSegunEPS`,
                `ValorSegunIPS`,`Diferencia` ,'1','$FechaRegisto','$idUser',`DescuentoReconocimientoBDUA`,`NumeroDiasLMA`,`ValorAPagarLMA`,`CodigoSucursal`,`NumeroOperacion`,`ImpuestosPorRecuperar`    
                 FROM $db.vista_cruce_cartera_eps_sin_relacion_segun_ags t1 WHERE 
                EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActa') 
                AND NOT EXISTS (SELECT 1 FROM $db.actas_conciliaciones_items t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND t3.idActaConciliacion='$idActa') 

                AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal' limit 10000";
        
        $this->Query($sql);
                
    }
    
    public function ActualizarColumnasHojaDeTrabajo($db,$VistaACopiar,$Condicion) {
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.Impuestos=t2.Impuestos $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ImpuestosSegunASMET=t2.ImpuestosSegunASMET $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ValorMenosImpuestos=t2.ValorMenosImpuestos $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalPagosNotas=t2.TotalPagosNotas $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.Capitalizacion=t2.Capitalizacion $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalPagos=t2.TotalPagos $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalAnticipos=t2.TotalAnticipos $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.DescuentoPGP=t2.DescuentoPGP $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FacturasDevueltas=t2.FacturasDevueltas $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.NumeroFacturasDevueltasAnticipos=t2.NumeroFacturasDevueltasAnticipos $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ValorFacturasDevueltascxpvsant=t2.ValorFacturasDevueltascxpvsant $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FacturasDevueltasCXPVSANT=t2.FacturasDevueltasCXPVSANT $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalCopagos=t2.TotalCopagos $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.OtrosDescuentos=t2.OtrosDescuentos $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.AjustesCartera=t2.AjustesCartera $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalGlosaInicial=t2.TotalGlosaInicial $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalGlosaFavor=t2.TotalGlosaFavor $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalGlosaContra=t2.TotalGlosaContra $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.GlosaXConciliar=t2.GlosaXConciliar $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.DevolucionesPresentadas=t2.DevolucionesPresentadas $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FacturasPresentadas=t2.FacturasPresentadas $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FacturaActiva=t2.FacturaActiva $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalDevolucionesNotas=t2.TotalDevolucionesNotas $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalDevoluciones=t2.TotalDevoluciones $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.CarteraXEdades=t2.CarteraXEdades $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ConciliacionesAFavorEPS=t2.ConciliacionesAFavorEPS $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ConciliacionesAFavorIPS=t2.ConciliacionesAFavorIPS $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ValorSegunEPS=t2.ValorSegunEPS $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ValorSegunIPS=t2.ValorSegunIPS $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.Diferencia=t2.Diferencia $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ValorIPSMenor=t2.ValorIPSMenor $Condicion";
        $this->Query($sql);    
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalConciliaciones=t2.TotalConciliaciones $Condicion";
        $this->Query($sql);        
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.TotalAPagar=t2.TotalAPagar $Condicion";
        $this->Query($sql);        
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.ConciliacionesPendientes=t2.ConciliacionesPendientes $Condicion";
        $this->Query($sql);
        
        $sql="UPDATE $db.hoja_de_trabajo t1 INNER JOIN $VistaACopiar t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.DiferenciaXPagos=t2.DiferenciaXPagos $Condicion";
        $this->Query($sql);
        
    }
    
    public function EncontrarDiferenciasVariadas($db) {
        $sql="SELECT NumeroFactura,Diferencia,TotalPagos,TotalAnticipos,TotalCopagos,DescuentoPGP,
               OtrosDescuentos,AjustesCartera,TotalGlosaFavor,TotalGlosaContra,GlosaXConciliar,
               TotalDevoluciones,Impuestos FROM $db.hoja_de_trabajo WHERE ABS(DiferenciaVariada)>1;
                 ";
        
        $Consulta=$this->Query($sql);
        $d=0;
        $ColumnaActualizar["TotalPagos"]="DiferenciaXPagosNoDescargados";
        $ColumnaActualizar["TotalAnticipos"]="DiferenciaXPagosNoDescargados";
        $ColumnaActualizar["TotalCopagos"]="DiferenciaXPagosNoDescargados";
        $ColumnaActualizar["DescuentoPGP"]="DiferenciaXGlosasPendientesXDescargarIPS";
        $ColumnaActualizar["OtrosDescuentos"]="DiferenciaXAjustesDeCartera";
        
        $ColumnaActualizar["AjustesCartera"]="DiferenciaXAjustesDeCartera";
        $ColumnaActualizar["TotalGlosaFavor"]="DiferenciaXGlosasPendientesXDescargarIPS";
        $ColumnaActualizar["TotalGlosaContra"]="DiferenciaXPagosNoDescargados";
        $ColumnaActualizar["GlosaXConciliar"]="DiferenciaXGlosasPendientesXConciliar";
        $ColumnaActualizar["TotalDevoluciones"]="DiferenciaXFacturasDevueltas";
        $ColumnaActualizar["Impuestos"]="DiferenciaXDiferenciaXImpuestos";
        
        while($DatosHoja= $this->FetchAssoc($Consulta)){
           
            $keyComparacion1="TotalPagos";
            $keyComparacion2="TotalAnticipos";
            $keyComparacion3="TotalCopagos";
            $keyComparacion4="DescuentoPGP";
            $keyComparacion5="OtrosDescuentos";
            $keyComparacion6="AjustesCartera";
            $keyComparacion7="TotalGlosaFavor";
            $keyComparacion8="TotalGlosaContra";
            $keyComparacion9="GlosaXConciliar";
            $keyComparacion10="TotalDevoluciones";
            $keyComparacion11="Impuestos";
            
            $flag=0;
            
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion1]) + abs($value) ) ==(abs($DatosHoja["Diferencia"])) ){
                        
                            $flag=1;
                            $d=$d+1;                         
                            $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion1]."= $ColumnaActualizar[$keyComparacion1] + ABS($DatosHoja[$keyComparacion1]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                            $sql.=",DiferenciaVariada=0 ";
                            $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                            $this->Query($sql);
                            
                        break;
                        
                    }
                }
            }   
            
            if($flag==0){
            
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion2]) + abs($value) ) ==(abs($DatosHoja["Diferencia"])) ){
                        $flag=1;         
                        $d=$d+1;                        
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion2]."= $ColumnaActualizar[$keyComparacion2] + ABS($DatosHoja[$keyComparacion2]),".$ColumnaActualizar[$key]." =  $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                       break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion2: ".$DatosHoja[$keyComparacion2]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
            } 
             
            }
            
            if($flag==0){
                
         
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>$keyComparacion3 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion3]) + abs($value) ) ==(abs($DatosHoja["Diferencia"])) ){
                        $flag=1;         
                        $d=$d+1;
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion3]."= $ColumnaActualizar[$keyComparacion3] + ABS($DatosHoja[$keyComparacion3]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                       break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion3: ".$DatosHoja[$keyComparacion3]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
            } 
            
            }
            
            if($flag==0){
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>$keyComparacion3 and $key<>$keyComparacion4 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion4]) + abs($value) ) ==(abs($DatosHoja["Diferencia"])) ){
                        $flag=1;              
                        $d=$d+1;
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion4]."= $ColumnaActualizar[$keyComparacion4] + ABS($DatosHoja[$keyComparacion4]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                       break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion4: ".$DatosHoja[$keyComparacion4]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
            } 
            
            }
            
            if($flag==0){
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>$keyComparacion3 and $key<>$keyComparacion4 and $key<>$keyComparacion5 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion5]) + abs($value) ) == (abs($DatosHoja["Diferencia"])) ){
                        $flag=1;               
                        $d=$d+1;
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion5]."= $ColumnaActualizar[$keyComparacion5] + ABS($DatosHoja[$keyComparacion5]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                        break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion5: ".$DatosHoja[$keyComparacion5]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
            } 
            
            }
            
            if($flag==0){
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>$keyComparacion3 and $key<>$keyComparacion4 and $key<>$keyComparacion5 and $key<>$keyComparacion6 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion6]) + abs($value) ) == (abs($DatosHoja["Diferencia"])) ){
                        $flag=1;
                        $d=$d+1;
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion6]."= $ColumnaActualizar[$keyComparacion6] + ABS($DatosHoja[$keyComparacion6]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                       break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion6: ".$DatosHoja[$keyComparacion6]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
            } 
             
            }
            
            if($flag==0){
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>$keyComparacion3 and $key<>$keyComparacion4 and $key<>$keyComparacion5 and $key<>$keyComparacion6 and $key<>$keyComparacion7 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion7]) + abs($value) ) == (abs($DatosHoja["Diferencia"])) ){
                        $flag=1;                  
                        $d=$d+1;
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion7]."= $ColumnaActualizar[$keyComparacion7] + ABS($DatosHoja[$keyComparacion7]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                       break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion7: ".$DatosHoja[$keyComparacion7]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
            } 
            
            }
            
            if($flag==0){
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>$keyComparacion3 and $key<>$keyComparacion4 and $key<>$keyComparacion5 and $key<>$keyComparacion6 and $key<>$keyComparacion7 and $key<>$keyComparacion8 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion8]) + abs($value) ) == (abs($DatosHoja["Diferencia"])) ){
                        $flag=1;            
                        $d=$d+1;
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion8]."= $ColumnaActualizar[$keyComparacion8] + ABS($DatosHoja[$keyComparacion8]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                       break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion8: ".$DatosHoja[$keyComparacion8]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
            }
            
            }
            
            if($flag==0){
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>$keyComparacion3 and $key<>$keyComparacion4 and $key<>$keyComparacion5 and $key<>$keyComparacion6 and $key<>$keyComparacion7 and $key<>$keyComparacion8 and $key<>$keyComparacion9 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion9]) + abs($value) ) == (abs($DatosHoja["Diferencia"])) ){
                        $flag=1;                     
                        $d=$d+1;
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion9]."=$ColumnaActualizar[$keyComparacion9] + ABS($DatosHoja[$keyComparacion9]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                        break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion9: ".$DatosHoja[$keyComparacion9]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
            }
             
            }
            
            if($flag==0){
            foreach ($DatosHoja as $key => $value) {
                if($key<>$keyComparacion1 and $key<>$keyComparacion2 and $key<>$keyComparacion3 and $key<>$keyComparacion4 and $key<>$keyComparacion5 and $key<>$keyComparacion6 and $key<>$keyComparacion7 and $key<>$keyComparacion8 and $key<>$keyComparacion9 and $key<>$keyComparacion10 and $key<>'NumeroFactura' and $key<>'Diferencia'){
                    if((abs($DatosHoja[$keyComparacion10]) + abs($value) ) == (abs($DatosHoja["Diferencia"])) ){
                        $flag=1;             
                        $d=$d+1;
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$keyComparacion10]."=$ColumnaActualizar[$keyComparacion10] + ABS($DatosHoja[$keyComparacion10]),".$ColumnaActualizar[$key]." = $ColumnaActualizar[$key]+ ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                        break;
                        //print("$d . Diferencia $Diferencia encontrada en llaves $keyComparacion10: ".$DatosHoja[$keyComparacion10]." y $key: ".$DatosHoja[$key]." en Factura: ".$DatosHoja["NumeroFactura"]."<br>");
                    }
                }
                }
            }
            
            
                    
        }
        
        return($d);
    }
    
    
    public function EncontrarDiferenciasComunes($db) {
        $sql="SELECT NumeroFactura,Diferencia,TotalPagos,TotalAnticipos,TotalCopagos,DescuentoPGP,
               OtrosDescuentos,AjustesCartera,TotalGlosaFavor,TotalGlosaContra,GlosaXConciliar,
               TotalDevoluciones,Impuestos FROM $db.hoja_de_trabajo;
                ";
        
        $Consulta=$this->Query($sql);
        $d=0;
        $ColumnaActualizar["TotalPagos"]="DiferenciaXPagosNoDescargados";
        $ColumnaActualizar["TotalAnticipos"]="DiferenciaXPagosNoDescargados";
        $ColumnaActualizar["TotalCopagos"]="DiferenciaXPagosNoDescargados";
        $ColumnaActualizar["DescuentoPGP"]="DiferenciaXGlosasPendientesXDescargarIPS";
        $ColumnaActualizar["OtrosDescuentos"]="DiferenciaXAjustesDeCartera";
        
        $ColumnaActualizar["AjustesCartera"]="DiferenciaXAjustesDeCartera";
        $ColumnaActualizar["TotalGlosaFavor"]="DiferenciaXGlosasPendientesXDescargarIPS";
        $ColumnaActualizar["TotalGlosaContra"]="DiferenciaXPagosNoDescargados";
        $ColumnaActualizar["GlosaXConciliar"]="DiferenciaXGlosasPendientesXConciliar";
        $ColumnaActualizar["TotalDevoluciones"]="DiferenciaXFacturasDevueltas";
        $ColumnaActualizar["Impuestos"]="DiferenciaXDiferenciaXImpuestos";
        
        while($DatosHoja= $this->FetchAssoc($Consulta)){
                       
            $flag=0;
            
            foreach ($DatosHoja as $key => $value) {
                
                if($key<>'NumeroFactura' and $key<>'Diferencia' and $flag==0){
                    
                    if(( abs($value) ) ==(abs($DatosHoja["Diferencia"])) ){
                        $flag=1;
                        
                        $sql="UPDATE $db.hoja_de_trabajo SET ".$ColumnaActualizar[$key]." = ABS(".$DatosHoja[$key].")";
                        $sql.=",DiferenciaVariada=0 ";
                        $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                        $this->Query($sql);
                        
                    }
                }
            }
            
            if($flag==0){
                $sql="UPDATE $db.hoja_de_trabajo SET ";
                $sql.=" DiferenciaVariada=$DatosHoja[Diferencia] ";
                $sql.=" WHERE NumeroFactura= '".$DatosHoja["NumeroFactura"]."'";
                $this->Query($sql);
            }
                   
        }
        
        return($d);
    }
    
    public function ActualizarRegistroHojaDeTrabajo($db,$Condicion) {
        $HojaDeTrabajo=$db.".hoja_de_trabajo";
        $Columnas=$this->ShowColums($HojaDeTrabajo);
        $sql="UPDATE $HojaDeTrabajo t1 INNER JOIN $db.vista_cruce_cartera_asmet t2 SET ";
        foreach ($Columnas["Field"] as $key => $value) {            
            $sql.=" t1.$value=t2.$value,";
        }
        $sql= substr($sql, 0,-1);
        $sql.=" $Condicion";
        $this->Query($sql);
    }
    //Fin Clases
    
    public function CrearTablaHojaTrabajo($db) {
        $sql="CREATE TABLE $db.`hoja_de_trabajo` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT,
            `NumeroFactura` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'NÃºmero de la factura del prestador',
            `Estado` int(11) NOT NULL,
            `DepartamentoRadicacion` varchar(25) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Departamento donde se radica la cuenta',
            `CodigoSucursal` int(6) DEFAULT NULL COMMENT 'Codigo Dane sucursal',
            `NumeroOperacion` bigint(20) NOT NULL,
            `TipoNegociacion` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL,
            `TipoNegociacionContrato` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL,
            `NoRelacionada` bigint(11) DEFAULT NULL,
            `ConciliacionEPSXPagos1` double(19,2) NOT NULL,
            `ConciliacionEPSXPagos2` double(19,2) NOT NULL,
            `ConciliacionEPSXGlosas1` double(19,2) NOT NULL,
            `ConciliacionEPSXCopagos` double(19,2) NOT NULL,
            `ConciliacionEPSXImpuestos` double(19,2) NOT NULL,
            `ConciliacionEPSXGlosas2` double(19,2) NOT NULL,
            `ConciliacionEPSXDevolucion` double(19,2) NOT NULL,
            `ConciliacionEPSXPagos` double(19,2) NOT NULL,
            `ConciliacionEPSXGlosas` double(19,2) NOT NULL,
            `ContratoPadre` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL,
            `DivisorMesServicio` bigint(21) DEFAULT NULL,
            `NumeroAfiliadosLMA` decimal(32,0) DEFAULT NULL,
            `NumeroDiasLMA` decimal(36,4) DEFAULT NULL,
            `ValorPercapita` double(15,2) DEFAULT NULL,
            `PorcentajePoblacional` double(5,2) DEFAULT NULL,
            `ValorAPagarLMA` double(19,2) NOT NULL,
            `FechaFactura` date DEFAULT NULL,
            `MesServicio` int(6) NOT NULL,
            `NumeroRadicado` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
            `FechaConciliacion` datetime DEFAULT NULL,
            `FechaRadicado` date NOT NULL,
            `NumeroContrato` varchar(40) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Numero de contrato al que esta sujeto la factura',
            `ValorDocumento` double(15,2) NOT NULL COMMENT 'Valor de la factura que emitio la IPS',
            `ImpuestosCalculados` double(19,2) NOT NULL,
            `Impuestos` double(19,2) NOT NULL,
            `ValorMenosImpuestos` double(15,2) NOT NULL,
            `TotalPagosNotas` double(19,2) NOT NULL,
            `Capitalizacion` double(19,2) NOT NULL,
            `ConciliacionesAFavorEPS` double(19,2) NOT NULL,
            `ConciliacionesAFavorIPS` double(19,2) NOT NULL,
            `TotalPagos` double(19,2) NOT NULL,
            `DescuentoReconocimientoBDUA` double(19,2) DEFAULT NULL,
            `TotalAnticipos` double(19,2) NOT NULL,
            `DescuentoPGP` double(19,2) NOT NULL,
            `FacturasDevueltasAnticipos` double(19,2) NOT NULL,
            `NumeroFacturasDevueltasAnticipos` bigint(21) NOT NULL,
            `CopagosEnNotas` double(19,2) NOT NULL,
            `CopagosEnAnticipos` double(19,2) NOT NULL,
            `TotalCopagos` double(19,2) NOT NULL,
            `OtrosDescuentos` double(19,2) NOT NULL,
            `AjustesCartera` double(19,2) NOT NULL,
            `DevolucionesPresentadas` bigint(21) NOT NULL,
            `FacturasPresentadas` bigint(21) NOT NULL,
            `FacturaActiva` varchar(2) CHARACTER SET latin1 NOT NULL,
            `ImpuestosPorRecuperar` double(19,2) NOT NULL,
            `TotalGlosaInicial` double(15,2) NOT NULL,
            `TotalGlosaFavor` double(19,2) NOT NULL,
            `TotalGlosaContra` double(15,2) NOT NULL,
            `GlosaXConciliar` double(15,2) NOT NULL,
            `TotalDevoluciones` double(19,2) NOT NULL,
            `CarteraXEdades` double(19,2) NOT NULL,
            `ValorSegunEPS` double(19,2) DEFAULT NULL,
            `ValorSegunIPS` double(17,0) NOT NULL,
            `PendientesPorRadicados` varchar(2) CHARACTER SET latin1 NOT NULL,
            `PendientesPorDevoluciones` varchar(2) CHARACTER SET latin1 NOT NULL,
            `PendientesPorNotas` varchar(2) CHARACTER SET latin1 NOT NULL,
            `PendientesPorCopagos` varchar(2) CHARACTER SET latin1 NOT NULL,
            `Diferencia` double(19,2) DEFAULT NULL,
            `ValorIPSMenor` varchar(2) CHARACTER SET latin1 DEFAULT NULL,
            `TotalConciliaciones` double(19,2) NOT NULL,
            `TotalAPagar` double(19,2) DEFAULT NULL,
            `ConciliacionesPendientes` varchar(2) CHARACTER SET latin1 DEFAULT NULL,
            `DiferenciaXPagos` int(1) DEFAULT NULL,
            `DiferenciaXPagosNoDescargados` double NOT NULL,
            `DiferenciaXGlosasPendientesXConciliar` double NOT NULL,
            `DiferenciaXFacturasDevueltas` double NOT NULL,
            `DiferenciaXDiferenciaXImpuestos` double NOT NULL,
            `DiferenciaXFacturasNoRelacionadasXIPS` double NOT NULL,
            `DiferenciaXAjustesDeCartera` double NOT NULL,
            `DiferenciaXValorFacturado` double NOT NULL,
            `DiferenciaXGlosasPendientesXDescargarIPS` double NOT NULL,
            `DiferenciaXDescuentoReconocimientoLMA` double NOT NULL,
            `DiferenciaVariada` double NOT NULL,
            PRIMARY KEY (`ID`),
            KEY `NumeroFactura` (`NumeroFactura`),
            KEY `MesServicio` (`MesServicio`),
            KEY `NumeroRadicado` (`NumeroRadicado`),
            KEY `NumeroContrato` (`NumeroContrato`),
            KEY `CodigoSucursal` (`CodigoSucursal`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";
        
        $this->Query($sql);
    }
    
    public function CrearVistaCarteraCruce($db) {
        $sql="DROP VIEW IF EXISTS `vista_cruce_cartera_asmet`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        //usleep(500);
        $sql="CREATE VIEW `vista_cruce_cartera_asmet` AS
            SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,
                    t2.CodigoSucursal,t2.NumeroOperacion,
                    (SELECT TipoNegociacionOperacion FROM ts_eps.tipos_operacion t5 WHERE t5.TipoOperacion=t2.TipoOperacion LIMIT 1 ) AS TipoNegociacion,
                    (SELECT TipoNegociacionOperacion FROM ts_eps.tipos_operacion t5 WHERE t5.TipoOperacion=t2.TipoOperacion LIMIT 1 ) AS TipoNegociacionContrato,
                    (SELECT NoRelacionada FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as NoRelacionada,

                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=12),0)) as ConciliacionEPSXPagos1,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=15),0)) as ConciliacionEPSXPagos2,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=16),0)) as ConciliacionEPSXGlosas1,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=13),0)) as ConciliacionEPSXCopagos,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=14),0)) as ConciliacionEPSXImpuestos,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=25),0)) as ConciliacionEPSXGlosas2,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=26),0)) as ConciliacionEPSXDevolucion,

                    ((SELECT ConciliacionEPSXPagos1) + (SELECT ConciliacionEPSXPagos2) ) AS ConciliacionEPSXPagos, 
                    ((SELECT ConciliacionEPSXGlosas1) + (SELECT ConciliacionEPSXGlosas2) ) AS ConciliacionEPSXGlosas, 


                    (SELECT Contrato FROM ts_eps.contratos c WHERE c.ContratoEquivalente=t2.NumeroContrato LIMIT 1) AS ContratoPadre,

                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT COUNT(NumeroFactura) FROM carteraeps ce WHERE ce.NumeroContrato= t2.NumeroContrato AND ce.MesServicio= t2.MesServicio AND ce.CarteraEPSTipoNegociacion='CAPITA' AND ce.CodigoSucursal=t2.CodigoSucursal) ,
                                              1)) AS DivisorMesServicio,    
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT SUM(NumeroAfiliadosPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio),
                                              0)) AS NumeroAfiliadosLMA,
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT SUM(DiasLiquidadosSubsidioPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio)/(SELECT DivisorMesServicio) ,
                                              0)) AS NumeroDiasLMA,
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT (ValorPercapitaXDia) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                              0)) AS ValorPercapita, 
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT (PorcentajePoblacional) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                              0)) AS PorcentajePoblacional, 

                    (SELECT IFNULL((SELECT ROUND((SELECT NumeroDiasLMA) * (SELECT ValorPercapita) * ((SELECT PorcentajePoblacional)/100),2 )),0)) AS ValorAPagarLMA,

                    (SELECT FechaFactura FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as FechaFactura,
                    t2.MesServicio,
                        t2.NumeroRadicado,

                    (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
                    t2.FechaRadicado,
                            t2.NumeroContrato,
                            t2.ValorOriginal as ValorDocumento,
                    (t2.ValorOriginal-t2.ValorMenosImpuestos) as ImpuestosCalculados,
                    (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXImpuestos)) AS Impuestos,
                            t2.ValorMenosImpuestos,
                            (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos')  AND (notas_db_cr_2.TipoOperacion!='2103' and notas_db_cr_2.TipoOperacion!='2117' and notas_db_cr_2.TipoOperacion!='2351' and notas_db_cr_2.TipoOperacion!='2122' and notas_db_cr_2.TipoOperacion!='3130') ),0)) AS TotalPagosNotas,
                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='Capitalizacion') ),0)) AS Capitalizacion,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,

                    ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) + (SELECT ConciliacionEPSXPagos)  ) ) AS TotalPagos,

                    (SELECT IF( (SELECT TipoNegociacionContrato)='CAPITA', ((SELECT ValorAPagarLMA)-(t2.ValorOriginal)),0)) AS DescuentoReconocimientoBDUA,

                            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='anticipos') ),0)) AS TotalAnticipos,
                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='descuentospgp') ),0)) AS DescuentoPGP,
                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS FacturasDevueltasAnticipos,


                    (SELECT IFNULL((SELECT COUNT((NumeroFactura)) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS NumeroFacturasDevueltasAnticipos,

                    (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXCopagos)   ) AS CopagosEnNotas,

                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='copagos') ),0)) as CopagosEnAnticipos,
                    (SELECT IF( (SELECT CopagosEnNotas)>0,(SELECT CopagosEnNotas), (SELECT CopagosEnAnticipos) )  ) AS TotalCopagos,

                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='otrosdescuentos') ),0)) AS OtrosDescuentos,
                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='ajustescartera') ),0)) AS AjustesCartera,

                    (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS DevolucionesPresentadas,
                    (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroRadicado) FROM historial_carteracargada_eps WHERE historial_carteracargada_eps.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND historial_carteracargada_eps.TipoOperacion=t1.TipoOperacion AND Aplicacion='FACTURA')  ),0)) AS FacturasPresentadas,
                    (SELECT IF(((SELECT DevolucionesPresentadas ) >= ((SELECT FacturasPresentadas)) OR (SELECT NumeroFacturasDevueltasAnticipos ) >= ((SELECT FacturasPresentadas) ) ),'NO','SI')) AS FacturaActiva,

                    (SELECT IF(FacturaActiva='SI',0, (SELECT Impuestos)   )) AS ImpuestosPorRecuperar,


                    (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS TotalGlosaInicial,
                    (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0) + (SELECT ConciliacionEPSXGlosas) ,0)) AS TotalGlosaFavor,
                    (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS TotalGlosaContra,
                    (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorPendienteResolver) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS GlosaXConciliar,

                    (SELECT IF(FacturaActiva='SI',(0+(SELECT ConciliacionEPSXDevolucion)),(t2.ValorOriginal + (SELECT ConciliacionEPSXDevolucion) )) ) AS TotalDevoluciones,
                    (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura  LIMIT 1),0)) AS CarteraXEdades,
        
                    (t2.ValorOriginal - (SELECT Impuestos) - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))-(SELECT ABS(DescuentoPGP)) + (SELECT DescuentoReconocimientoBDUA) + (SELECT ConciliacionesAFavorIPS) ) AS ValorSegunEPS,
                    (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,

                    (SELECT IFNULL((SELECT 'SI' FROM radicadospendientes t4 WHERE EstadoAuditoria LIKE '%AUDITORIA%' AND EstadoAuditoria NOT LIKE '%FINALIZAD%'  AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorRadicados,
                    (SELECT IFNULL( (SELECT 'SI' FROM devoluciones_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorDevoluciones,
                    (SELECT IFNULL( (SELECT 'SI' FROM notas_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorNotas,
                    (SELECT IFNULL( (SELECT 'SI' FROM copagos_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorCopagos,


                    ((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
                    (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura ),0)) AS TotalConciliaciones,

                    ((SELECT ValorSegunEPS)  ) AS TotalAPagar, 
                    (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes,
                    (SELECT IF( (SELECT ABS(TotalPagos)) = (SELECT ABS(Diferencia)),1,0)) as DiferenciaXPagos,
                    '0' AS DiferenciaXPagosNoDescargados,
                    '0' AS DiferenciaXGlosasPendientesXConciliar,
                    '0' AS DiferenciaXFacturasDevueltas,
                    '0' AS DiferenciaXDiferenciaXImpuestos,
                    '0' AS DiferenciaXFacturasNoRelacionadasXIPS,
                    '0' AS DiferenciaXAjustesDeCartera,
                    '0' AS DiferenciaXValorFacturado,
                    '0' AS DiferenciaXGlosasPendientesXDescargarIPS,
                    '0' AS DiferenciaXDescuentoReconocimientoLMA,
                    '0' AS DiferenciaVariada

                    FROM carteraeps t2 WHERE t2.Estado<2 AND EXISTS (SELECT 1 FROM carteracargadaips t1 WHERE t1.NumeroFactura=t2.NumeroFactura);

        ";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
       // $this->Query($sql);
    }
    
    public function CrearVistaCarteraCruceCompleta($db) {
        $sql="DROP VIEW IF EXISTS `vista_cruce_cartera_completa`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="CREATE VIEW `vista_cruce_cartera_completa` AS
            SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,
                    t2.CodigoSucursal,t2.NumeroOperacion,
                    (SELECT TipoNegociacionOperacion FROM ts_eps.tipos_operacion t5 WHERE t5.TipoOperacion=t2.TipoOperacion LIMIT 1 ) AS TipoNegociacion,
                    (SELECT TipoNegociacionOperacion FROM ts_eps.tipos_operacion t5 WHERE t5.TipoOperacion=t2.TipoOperacion LIMIT 1 ) AS TipoNegociacionContrato,
                    (SELECT NoRelacionada FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as NoRelacionada,

                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=12),0)) as ConciliacionEPSXPagos1,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=15),0)) as ConciliacionEPSXPagos2,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=16),0)) as ConciliacionEPSXGlosas1,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=13),0)) as ConciliacionEPSXCopagos,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=14),0)) as ConciliacionEPSXImpuestos,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=25),0)) as ConciliacionEPSXGlosas2,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=26),0)) as ConciliacionEPSXDevolucion,

                    ((SELECT ConciliacionEPSXPagos1) + (SELECT ConciliacionEPSXPagos2) ) AS ConciliacionEPSXPagos, 
                    ((SELECT ConciliacionEPSXGlosas1) + (SELECT ConciliacionEPSXGlosas2) ) AS ConciliacionEPSXGlosas, 


                    (SELECT Contrato FROM ts_eps.contratos c WHERE c.ContratoEquivalente=t2.NumeroContrato LIMIT 1) AS ContratoPadre,

                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT COUNT(NumeroFactura) FROM carteraeps ce WHERE ce.NumeroContrato= t2.NumeroContrato AND ce.MesServicio= t2.MesServicio AND ce.CarteraEPSTipoNegociacion='CAPITA' AND ce.CodigoSucursal=t2.CodigoSucursal) ,
                                              1)) AS DivisorMesServicio,    
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT SUM(NumeroAfiliadosPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio),
                                              0)) AS NumeroAfiliadosLMA,
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT SUM(DiasLiquidadosSubsidioPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio)/(SELECT DivisorMesServicio) ,
                                              0)) AS NumeroDiasLMA,
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT (ValorPercapitaXDia) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                              0)) AS ValorPercapita, 
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT (PorcentajePoblacional) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                              0)) AS PorcentajePoblacional, 

                    (SELECT IFNULL((SELECT ROUND((SELECT NumeroDiasLMA) * (SELECT ValorPercapita) * ((SELECT PorcentajePoblacional)/100),2 )),0)) AS ValorAPagarLMA,

                    (SELECT FechaFactura FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as FechaFactura,
                    t2.MesServicio,
                        t2.NumeroRadicado,

                    (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
                    t2.FechaRadicado,
                            t2.NumeroContrato,
                            t2.ValorOriginal as ValorDocumento,
                    (t2.ValorOriginal-t2.ValorMenosImpuestos) as ImpuestosCalculados,
                    (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXImpuestos)) AS Impuestos,
                            t2.ValorMenosImpuestos,
                            (SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion2=t1.TipoOperacion AND Aplicacion='TotalPagos')  AND (notas_db_cr_2.TipoOperacion!='2103' and notas_db_cr_2.TipoOperacion!='2117' and notas_db_cr_2.TipoOperacion!='2351' and notas_db_cr_2.TipoOperacion!='2122' and notas_db_cr_2.TipoOperacion!='3130') ),0)) AS TotalPagosNotas,
                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='Capitalizacion') ),0)) AS Capitalizacion,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,

                    ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) + (SELECT ConciliacionEPSXPagos)  ) ) AS TotalPagos,

                    (SELECT IF( (SELECT TipoNegociacionContrato)='CAPITA', ((SELECT ValorAPagarLMA)-(t2.ValorOriginal)),0)) AS DescuentoReconocimientoBDUA,

                            (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='anticipos') ),0)) AS TotalAnticipos,
                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='descuentospgp') ),0)) AS DescuentoPGP,
                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS FacturasDevueltasAnticipos,


                    (SELECT IFNULL((SELECT COUNT((NumeroFactura)) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS NumeroFacturasDevueltasAnticipos,

                    (SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXCopagos)   ) AS CopagosEnNotas,

                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='copagos') ),0)) as CopagosEnAnticipos,
                    (SELECT IF( (SELECT CopagosEnNotas)>0,(SELECT CopagosEnNotas), (SELECT CopagosEnAnticipos) )  ) AS TotalCopagos,

                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='otrosdescuentos') ),0)) AS OtrosDescuentos,
                    (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND anticipos2.NumeroInterno=t1.TipoOperacion AND Aplicacion='ajustescartera') ),0)) AS AjustesCartera,

                    (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND notas_db_cr_2.TipoOperacion=t1.TipoOperacion AND Aplicacion='devoluciones') ),0)) AS DevolucionesPresentadas,
                    (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroRadicado) FROM historial_carteracargada_eps WHERE historial_carteracargada_eps.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM ts_eps.tipos_operacion t1 WHERE Estado=1 AND historial_carteracargada_eps.TipoOperacion=t1.TipoOperacion AND Aplicacion='FACTURA')  ),0)) AS FacturasPresentadas,
                    (SELECT IF(((SELECT DevolucionesPresentadas ) >= ((SELECT FacturasPresentadas)) OR (SELECT NumeroFacturasDevueltasAnticipos ) >= ((SELECT FacturasPresentadas) ) ),'NO','SI')) AS FacturaActiva,

                    (SELECT IF(FacturaActiva='SI',0, (SELECT Impuestos)   )) AS ImpuestosPorRecuperar,


                    (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS TotalGlosaInicial,
                    (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0) + (SELECT ConciliacionEPSXGlosas) ,0)) AS TotalGlosaFavor,
                    (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS TotalGlosaContra,
                    (SELECT IF(FacturaActiva='SI',IFNULL((SELECT (ValorPendienteResolver) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0),0)) AS GlosaXConciliar,

                    (SELECT IF(FacturaActiva='SI',(0+(SELECT ConciliacionEPSXDevolucion)),(t2.ValorOriginal + (SELECT ConciliacionEPSXDevolucion) )) ) AS TotalDevoluciones,
                    (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura  LIMIT 1),0)) AS CarteraXEdades,
        
                    (t2.ValorOriginal - (SELECT Impuestos) - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones))-(SELECT ABS(DescuentoPGP)) + (SELECT DescuentoReconocimientoBDUA) + (SELECT ConciliacionesAFavorIPS) ) AS ValorSegunEPS,
                    (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,

                    (SELECT IFNULL((SELECT 'SI' FROM radicadospendientes t4 WHERE EstadoAuditoria LIKE '%AUDITORIA%' AND EstadoAuditoria NOT LIKE '%FINALIZAD%'  AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorRadicados,
                    (SELECT IFNULL( (SELECT 'SI' FROM devoluciones_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorDevoluciones,
                    (SELECT IFNULL( (SELECT 'SI' FROM notas_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorNotas,
                    (SELECT IFNULL( (SELECT 'SI' FROM copagos_pendientes t4 WHERE NoEnviados > '0' AND t4.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS PendientesPorCopagos,


                    ((SELECT ValorSegunEPS)-(SELECT ValorSegunIPS)) AS Diferencia,
                    (SELECT IF((SELECT Diferencia>0),'SI','NO')) AS ValorIPSMenor,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura ),0)) AS TotalConciliaciones,

                    ((SELECT ValorSegunEPS)  ) AS TotalAPagar, 
                    (SELECT IF((SELECT ROUND(TotalConciliaciones,2)<>(SELECT ROUND(ABS(Diferencia),2)) AND (SELECT TotalConciliaciones > 0)),'SI','NO')) AS ConciliacionesPendientes,
                    (SELECT IF( (SELECT ABS(TotalPagos)) = (SELECT ABS(Diferencia)),1,0)) as DiferenciaXPagos,
                    '0' AS DiferenciaXPagosNoDescargados,
                    '0' AS DiferenciaXGlosasPendientesXConciliar,
                    '0' AS DiferenciaXFacturasDevueltas,
                    '0' AS DiferenciaXDiferenciaXImpuestos,
                    '0' AS DiferenciaXFacturasNoRelacionadasXIPS,
                    '0' AS DiferenciaXAjustesDeCartera,
                    '0' AS DiferenciaXValorFacturado,
                    '0' AS DiferenciaXGlosasPendientesXDescargarIPS,
                    '0' AS DiferenciaXDescuentoReconocimientoLMA,
                    '0' AS DiferenciaVariada

                    FROM carteraeps t2 WHERE EXISTS (SELECT 1 FROM carteracargadaips t1 WHERE t1.NumeroFactura=t2.NumeroFactura);

        ";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function CrearVistaInicialCarteraCruce($db) {
        $sql="DROP VIEW IF EXISTS `vista_inicial_cruce_cartera_asmet`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        //usleep(500);
        $sql="CREATE VIEW `vista_inicial_cruce_cartera_asmet` AS
            SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,
                    t2.CodigoSucursal,t2.NumeroOperacion,
                    (SELECT TipoNegociacionOperacion FROM ts_eps.tipos_operacion t5 WHERE t5.TipoOperacion=t2.TipoOperacion LIMIT 1 ) AS TipoNegociacion,
                    (SELECT TipoNegociacionOperacion FROM ts_eps.tipos_operacion t5 WHERE t5.TipoOperacion=t2.TipoOperacion LIMIT 1 ) AS TipoNegociacionContrato,
                    (SELECT NoRelacionada FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as NoRelacionada,

                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=12),0)) as ConciliacionEPSXPagos1,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=15),0)) as ConciliacionEPSXPagos2,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=16),0)) as ConciliacionEPSXGlosas1,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=13),0)) as ConciliacionEPSXCopagos,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=14),0)) as ConciliacionEPSXImpuestos,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=25),0)) as ConciliacionEPSXGlosas2,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces t3 WHERE t2.NumeroFactura=t3.NumeroFactura AND t3.ConceptoConciliacion=26),0)) as ConciliacionEPSXDevolucion,

                    ((SELECT ConciliacionEPSXPagos1) + (SELECT ConciliacionEPSXPagos2) ) AS ConciliacionEPSXPagos, 
                    ((SELECT ConciliacionEPSXGlosas1) + (SELECT ConciliacionEPSXGlosas2) ) AS ConciliacionEPSXGlosas, 


                    (SELECT Contrato FROM ts_eps.contratos c WHERE c.ContratoEquivalente=t2.NumeroContrato LIMIT 1) AS ContratoPadre,

                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT COUNT(NumeroFactura) FROM carteraeps ce WHERE ce.NumeroContrato= t2.NumeroContrato AND ce.MesServicio= t2.MesServicio AND ce.CarteraEPSTipoNegociacion='CAPITA' AND ce.CodigoSucursal=t2.CodigoSucursal) ,
                                              1)) AS DivisorMesServicio,    
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT SUM(NumeroAfiliadosPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio),
                                              0)) AS NumeroAfiliadosLMA,
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT SUM(DiasLiquidadosSubsidioPleno) FROM ts_eps.lma_asmet la WHERE la.CodigoDane= (t2.CodigoSucursal) AND la.MesServicio=t2.MesServicio)/(SELECT DivisorMesServicio) ,
                                              0)) AS NumeroDiasLMA,
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT (ValorPercapitaXDia) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                              0)) AS ValorPercapita, 
                    (SELECT IF(TipoNegociacionContrato='CAPITA',
                                             (SELECT (PorcentajePoblacional) FROM ts_eps.contrato_percapita cp WHERE cp.Contrato= (SELECT ContratoPadre) AND cp.NIT_IPS=t2.Nit_IPS AND cp.CodigoDane=t2.CodigoSucursal AND (t2.MesServicio BETWEEN cp.CodigoFechaInicioPercapita AND cp.CodigoFechaFinPercapita) LIMIT 1 ),
                                              0)) AS PorcentajePoblacional, 

                    (SELECT IFNULL((SELECT ROUND((SELECT NumeroDiasLMA) * (SELECT ValorPercapita) * ((SELECT PorcentajePoblacional)/100),2 )),0)) AS ValorAPagarLMA,

                    (SELECT FechaFactura FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as FechaFactura,
                    t2.MesServicio,
                        t2.NumeroRadicado,

                    (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
                    t2.FechaRadicado,
                            t2.NumeroContrato,
                            t2.ValorOriginal as ValorDocumento,
                    (t2.ValorOriginal-t2.ValorMenosImpuestos) as ImpuestosCalculados,
                    (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t2.NumeroFactura ),0) + (SELECT ConciliacionEPSXImpuestos)) AS Impuestos,
                            t2.ValorMenosImpuestos,
                            '0' AS TotalPagosNotas,
                    '0' AS Capitalizacion,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
                    (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,

                    '0' AS TotalPagos,

                    '0' AS DescuentoReconocimientoBDUA,

                    '0' AS TotalAnticipos,
                    '0' AS DescuentoPGP,
                    '0' AS FacturasDevueltasAnticipos,


                    '0' AS NumeroFacturasDevueltasAnticipos,

                    '0' AS CopagosEnNotas,

                    '0' as CopagosEnAnticipos,
                    '0' AS TotalCopagos,

                    '0' AS OtrosDescuentos,
                    '0' AS AjustesCartera,

                    '0' AS DevolucionesPresentadas,
                    '0' AS FacturasPresentadas,
                    '0' AS FacturaActiva,

                    '0' AS ImpuestosPorRecuperar,


                    '0' AS TotalGlosaInicial,
                    '0' AS TotalGlosaFavor,
                    '0' AS TotalGlosaContra,
                    '0' AS GlosaXConciliar,

                    '0' AS TotalDevoluciones,
                    '0' AS CarteraXEdades,
        
                    '0' AS ValorSegunEPS,
                    '0' AS ValorSegunIPS,

                    '0' AS PendientesPorRadicados,
                    '0' AS PendientesPorDevoluciones,
                    '0' AS PendientesPorNotas,
                    '0' AS PendientesPorCopagos,


                    '0' AS Diferencia,
                    '0' AS ValorIPSMenor,
                    '0' AS TotalConciliaciones,

                    '0' AS TotalAPagar, 
                    '0' AS ConciliacionesPendientes,
                    '0' as DiferenciaXPagos,
                    '0' AS DiferenciaXPagosNoDescargados,
                    '0' AS DiferenciaXGlosasPendientesXConciliar,
                    '0' AS DiferenciaXFacturasDevueltas,
                    '0' AS DiferenciaXDiferenciaXImpuestos,
                    '0' AS DiferenciaXFacturasNoRelacionadasXIPS,
                    '0' AS DiferenciaXAjustesDeCartera,
                    '0' AS DiferenciaXValorFacturado,
                    '0' AS DiferenciaXGlosasPendientesXDescargarIPS,
                    '0' AS DiferenciaXDescuentoReconocimientoLMA,
                    '0' AS DiferenciaVariada

                    FROM carteraeps t2 WHERE EXISTS (SELECT 1 FROM carteracargadaips t1 WHERE t1.NumeroFactura=t2.NumeroFactura);

        ";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
       // $this->Query($sql);
    }
    
}
