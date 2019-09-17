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
    
    public function CrearActaConciliacion($FechaInicial,$FechaCorte,$CmbIPS,$RepresentanteLegalIPS,$EncargadoEPS,$idUser) {
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
        
        $Datos["RazonSocialIPS"]=$DatosIPS["Nombre"];
        $Datos["NIT_IPS"]=$CmbIPS;
        $Datos["RepresentanteLegal"]=$RepresentanteLegalIPS;
        $Datos["Departamento"]=$DatosIPS["Departamento"];
        $Datos["EncargadoEPS"]=$EncargadoEPS;
        $Datos["FechaFirma"]=$FechaFirma;
        $Datos["CiudadFirma"]='Popayán';   
        $Datos["idUser"]=$idUser;
        $Datos["FechaRegistro"]=$FechaRegistro;
        
        $sql=$this->getSQLInsert("actas_conciliaciones", $Datos);
        $this->Query($sql);
        $idActa=$this->ObtenerMAX("actas_conciliaciones", "ID", 1, "");
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
        
        $DatosTotales=$this->FetchAssoc($this->Query($sql));
        $TotalPendientesRadicados= $this->SumeColumna("$db.vista_pendientes", "Total", "Radicados", "Radicados");
        if(!is_numeric($TotalPendientesRadicados)){
            $TotalPendientesRadicados=0;
        }
        $FacturasNoRelacionadasXIPS= $this->SumeColumna("$db.vista_cruce_cartera_asmet", "ValorSegunEPS", "NoRelacionada", 1);
        if(!is_numeric($FacturasNoRelacionadasXIPS)){
            $FacturasNoRelacionadasXIPS=0;
        }
        $sql="SELECT SUM(ValorTotalpagar) AS Total FROM $db.vista_facturas_sr_ips";
        $TotalConsulta= $this->FetchAssoc($this->Query($sql));
        $TotalFacturasSinRelacionsrXIPS=$TotalConsulta["Total"];
        //$TotalFacturasSinRelacionsrXIPS= $this->SumeColumna("$db.vista_facturas_sr_ips", "ValorTotalpagar", 1, "");   
       // print("prueba :".$TotalFacturasSinRelacionsrXIPS);
        //if(!is_numeric($TotalFacturasSinRelacionsrXIPS)){
         //   $TotalFacturasSinRelacionsrXIPS=0;
        //}
        
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
        //$DetalleDiferencias["DiferenciaVariada"]=abs(round($DatosTotales["DiferenciaVariada"]));
        
        $TotalDiferencias= array_sum($DetalleDiferencias);
        $DiferenciaFaltante=abs($Diferencia)-abs($TotalDiferencias);
        $DiferenciaTemporal=$DetalleDiferencias["DiferenciaXPagos"]+$DiferenciaFaltante;
        if($DiferenciaTemporal<0){
            $keyMax=array_keys($DetalleDiferencias,max($DetalleDiferencias));            
            $idKey=$keyMax[0];
            $DetalleDiferencias[$idKey]=$DetalleDiferencias[$idKey]+$DiferenciaTemporal;
            $DetalleDiferencias["DiferenciaXPagos"]=0;
        }else{
            $DetalleDiferencias["DiferenciaXPagos"]=$DetalleDiferencias["DiferenciaXPagos"]+$DiferenciaFaltante;
        }
        $DetalleDiferencias["TotalDiferencias"]= array_sum($DetalleDiferencias);
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
        $TotalPendientesRadicados=$this->SumeColumna("$db.vista_pendientes", "Total", "Radicados", "Radicados");
        $TotalFacturasSinRelacionsrXIPS= $this->SumeColumna("$db.vista_facturas_sr_ips", "ValorTotalpagar", 1, "");   
            
        $sql="SELECT SUM(ValorSegunEPS) AS TotalEPS,SUM(ValorSegunIPS) AS TotalIPS FROM $db.`vista_cruce_cartera_asmet` t1 WHERE "
                . "EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato) AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal'";
                
        $TotalesCruce=$this->FetchAssoc($this->Query($sql));

        //$sql="SELECT SUM(ValorConciliacion) as ValorConciliaciones FROM $db.conciliaciones_cruces t1 WHERE ConciliacionAFavorDe='2' AND EXISTS (SELECT 1 FROM $db.vista_cruce_cartera_asmet t2 WHERE t2.NumeroFactura=t1.NumeroFactura AND t2.MesServicio>='$MesServicioInicial' AND t2.MesServicio<='$MesServicioFinal') AND EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato); ";
        //$TotalesConciliacionesFactura=$this->FetchAssoc($this->Query($sql));

        //$sql="SELECT SUM(ValorConciliacion) as ValorConciliaciones FROM $db.conciliaciones_cruces t1 WHERE ConciliacionAFavorDe='1' AND EXISTS (SELECT 1 FROM $db.vista_cruce_cartera_asmet t2 WHERE t2.NumeroFactura=t1.NumeroFactura AND t2.MesServicio>='$MesServicioInicial' AND t2.MesServicio<='$MesServicioFinal') AND EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato); ";
        //$TotalesConciliacionesFavorEPS=$this->FetchAssoc($this->Query($sql));

        $ValorSegunEPS=$TotalesCruce["TotalEPS"]-$TotalPendientesRadicados;
        $ValorSegunIPS=$TotalesCruce["TotalIPS"]+(abs($TotalFacturasSinRelacionsrXIPS));
        $Diferencia=$ValorSegunEPS-$ValorSegunIPS;
        $SaldoConciliadoParaPago=$ValorSegunEPS;
        
        $TotalesActa["ValorSegunEPS"]=$ValorSegunEPS;
        $TotalesActa["ValorSegunIPS"]=$ValorSegunIPS;
        $TotalesActa["Diferencia"]=$Diferencia;
        $TotalesActa["SaldoConciliadoParaPago"]=$SaldoConciliadoParaPago;
        $TotalesActa["TotalPendientesRadicados"]=$TotalPendientesRadicados;
        $TotalesActa["TotalFacturasSinRelacionsrXIPS"]=$TotalFacturasSinRelacionsrXIPS;
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
        $sql." WHERE ID='$idActaConciliacion'";
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
                `ValorSegunIPS`,`Diferencia`,`NoRelacionada`,`FechaRegistro`,`idUser`)  
                SELECT '$idActa',`FechaFactura`,`MesServicio`,`DepartamentoRadicacion`,`NumeroRadicado`,`FechaRadicado`,`NumeroContrato`,`NumeroFactura`,`ValorDocumento`,`Impuestos`,`TotalPagos`,
                `TotalAnticipos`,`TotalCopagos`,`DescuentoPGP`,`OtrosDescuentos`,`AjustesCartera`,`TotalGlosaInicial`,`TotalGlosaFavor`,`TotalGlosaContra`,`GlosaXConciliar`,`TotalDevoluciones`,`ValorSegunEPS`,
                `ValorSegunIPS`,`Diferencia` ,'0','$FechaRegisto','$idUser'   
                 FROM $db.vista_reporte_ips t1 WHERE 
                EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActa') 
                AND NOT EXISTS (SELECT 1 FROM $db.actas_conciliaciones_items t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND t3.idActaConciliacion='$idActa') 

                AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal' limit 20";
        
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
                `ValorSegunIPS`,`Diferencia`,`NoRelacionada`,`FechaRegistro`,`idUser`)  
                SELECT '$idActa',`FechaFactura`,`MesServicio`,`DepartamentoRadicacion`,`NumeroRadicado`,`FechaRadicado`,`NumeroContrato`,`NumeroFactura`,`ValorDocumento`,`Impuestos`,`TotalPagos`,
                `TotalAnticipos`,`TotalCopagos`,`DescuentoPGP`,`OtrosDescuentos`,`AjustesCartera`,`TotalGlosaInicial`,`TotalGlosaFavor`,`TotalGlosaContra`,`GlosaXConciliar`,`TotalDevoluciones`,`ValorSegunEPS`,
                `ValorSegunIPS`,`Diferencia` ,'1','$FechaRegisto','$idUser'   
                 FROM $db.vista_cruce_cartera_eps_sin_relacion_segun_ags t1 WHERE 
                EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActa') 
                AND NOT EXISTS (SELECT 1 FROM $db.actas_conciliaciones_items t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND t3.idActaConciliacion='$idActa') 

                AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal' limit 100";
        
        $this->Query($sql);
                
    }
    
    
    //Fin Clases
}