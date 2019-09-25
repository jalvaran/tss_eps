<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/validaciones.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ValidacionesEPS($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Actualizar una factura
            
            $NumeroFacturaAnterior=$obCon->normalizar($_REQUEST["TxtNumeroFacturaEdit"]);
            $NumeroFacturaNueva=$obCon->normalizar($_REQUEST["TxtFacturaNueva"]);
            $Observaciones=$obCon->normalizar($_REQUEST["TxtObservacionesEdicioFactura"]);
            
            $idIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $obCon->ActualizarFactura($NumeroFacturaAnterior, $NumeroFacturaNueva, $idIPS, $Observaciones, $idUser);
            print("OK;La Factura $NumeroFacturaAnterior Fue reemplazada por la $NumeroFacturaNueva");
            
        break; //fin caso 1
        
        case 2: //se recibe el archivo
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.temporal_actualizacion_facturas");
            $destino='';
            $keyArchivo="ActFacts";
            $Extension="";
            if(!empty($_FILES['UpActualizaciones']['name'])){
                
                $info = new SplFileInfo($_FILES['UpActualizaciones']['name']);
                $Extension=($info->getExtension());  
                if($Extension=='xls' or $Extension=='xlsx'){
                    $carpeta="../../../soportes/$CmbIPS/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    opendir($carpeta);                
                    $destino=$carpeta.$keyArchivo.".".$Extension;
                    $NombreArchivo=$keyArchivo.".".$Extension;
                    move_uploaded_file($_FILES['UpActualizaciones']['tmp_name'],$destino);
                    
                }else{
                    exit("E1;Error el archivo debe ser tipo xls o xlsx");
                }
            }else{
                exit("E1;No se envió ningún archivo");
                
            }
            
            print("OK;Archivo Recibido;$destino;$Extension");   
        break;//Fin caso 2
        
        case 3://Lee el archivo y lo sube a la temporal
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $RutaArchivo=$obCon->normalizar($_REQUEST["RutaArchivo"]);
            $Extension=$obCon->normalizar($_REQUEST["Extension"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $keyArchivo="ActFacts";
            $obCon->LeerCargarTemporal($keyArchivo,$CmbIPS,$RutaArchivo,$Extension,$idUser);
            print("OK;Archivo cargado y listo para analizar");
            
        break; //fin caso 3  
    
        case 4://Validar Datos en temporal
            
           $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
           $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
           $db=$DatosIPS["DataBase"];
           
           $sql="SELECT count(t1.FacturaAnterior) as TotalRepeticiones FROM $db.temporal_actualizacion_facturas t1 INNER JOIN $db.carteracargadaips t2 ON t2.NumeroFactura=t1.FacturaNueva";
           $Consulta=$obCon->Query($sql); 
           $DatosRepetidos=$obCon->FetchAssoc($Consulta);
           $TotalDuplicados=$DatosRepetidos["TotalRepeticiones"];
           if($TotalDuplicados>0){
               exit("E1;Error: En la columna B del archivo hay $TotalDuplicados Facturas  que ya existen en la cartera de la IPS");
           }
           
           $sql="SELECT COUNT(*) Total FROM $db.temporal_actualizacion_facturas GROUP BY FacturaNueva HAVING COUNT(*) > 1";
           $Consulta=$obCon->Query($sql); 
           $DatosRepetidos=$obCon->FetchAssoc($Consulta);
           $TotalDuplicados=$DatosRepetidos["Total"];
           if($TotalDuplicados>0){
               exit("E1;Error: En la columna B del archivo hay $TotalDuplicados Facturas repetidas, los registros de esta columna deben ser únicos");
           }
           print("OK;Registros validados");
        break;    
        
        case 5://Copiar y actualizar facturas
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $sql="UPDATE $db.carteracargadaips t1 INNER JOIN $db.temporal_actualizacion_facturas t2 ON t1.NumeroFactura=t2.FacturaAnterior SET t1.NumeroFactura=t2.FacturaNueva";
            $obCon->Query($sql);
            
            $sql="INSERT INTO $db.registro_actualizacion_facturas SELECT * FROM $db.temporal_actualizacion_facturas";
            $obCon->Query($sql);
            print("OK;Facturas Actualizadas");
        break; //Fin caso 5
    
        case 6://Se recibe una conciliacion sobre una factura
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $NumeroFactura=$obCon->normalizar($_REQUEST["NumeroFactura"]);
            $TipoConciliacion=$obCon->normalizar($_REQUEST["TipoConciliacion"]);
            $sql="SELECT TipoUser FROM usuarios WHERE idUsuarios='$idUser'";
            $Consulta=$obCon->Query($sql);
            $DatosUsuario=$obCon->FetchAssoc($Consulta);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            if($DatosUsuario["TipoUser"]=='administrador' or $DatosUsuario["TipoUser"]=='eps'){
                $obCon->MarcarConciliacionXEPS($db, $NumeroFactura, $idUser, $TipoConciliacion);
            }
            if($DatosUsuario["TipoUser"]=='ips'){
                $obCon->MarcarConciliacionXIPS($db, $NumeroFactura, $idUser, $TipoConciliacion);
            }
            $Conciliacion="EPS";
            if($TipoConciliacion==2){
                $Conciliacion="IPS";
            }
            $obCon->RegistreConciliacionUsuario($db, $idUser, $NumeroFactura, $Conciliacion);
            print("OK;Facturas Actualizadas");
        break; //Fin caso 6
        
        case 7:// Se guarda una conciliacion
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $NumeroFactura=$obCon->normalizar($_REQUEST["TxtNumeroFactura"]);
            $TipoConciliacion=$obCon->normalizar($_REQUEST["CmbTipoConciliacion"]);
            $ConceptoConciliacion=$obCon->normalizar($_REQUEST["CmbConcepto"]);
            $ConceptoConciliacionAGS=$obCon->normalizar($_REQUEST["CmbConceptoAGS"]);
            $Observaciones=$obCon->normalizar($_REQUEST["TxtObservaciones"]);
            $ValorEPS=$obCon->normalizar($_REQUEST["ValorEPS"]);
            $ValorIPS=$obCon->normalizar($_REQUEST["ValorIPS"]);
            $FechaConciliacion=$obCon->normalizar($_REQUEST["FechaConciliacion"]);
            $ConciliadorIPS=$obCon->normalizar($_REQUEST["ConciliadorIPS"]);
            $MetodoConciliacion=$obCon->normalizar($_REQUEST["CmbMetodoConciliacion"]);
            $destino='';
            $keyArchivo=$NumeroFactura.date("YmdHis");
            $Extension="";
            if(!empty($_FILES['UpSoporte']['name'])){
                
                $info = new SplFileInfo($_FILES['UpSoporte']['name']);
                $Extension=($info->getExtension());  
                
                $carpeta="../../../soportes/$CmbIPS/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/$CmbIPS/Conciliaciones/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $destino=$carpeta.$keyArchivo.".".$Extension;
                $NombreArchivo=$keyArchivo.".".$Extension;
                move_uploaded_file($_FILES['UpSoporte']['tmp_name'],$destino);
                    
                
            }else{
                exit("E1;No se envió ningún archivo de Soporte;UpSoporte");
                
            }
            
            if($CmbIPS==''){
                exit("E1;No se recibió la IPS a la que pertenece la concialiacion;");
            }
            
            if($TipoConciliacion==''){
                exit("E1;No se ha seleccionado a favor de quien se concilia;CmbTipoConciliacion");
            }
            
            if($ConceptoConciliacion=='' AND $ConceptoConciliacionAGS==''){
                exit("E1;No se ha seleccionado un concepto de conciliación;CmbConcepto");
            }
            
            if($ConceptoConciliacion>0 AND $ConceptoConciliacionAGS>0){
                exit("E1;Sólo se puede seleccionar un concepto de conciliación;CmbConcepto");
            }
            
            if($ConceptoConciliacionAGS>0){
                $ConceptoConciliacion=$ConceptoConciliacionAGS;
            }
            if($Observaciones=='' or strlen($Observaciones)<=10){
                exit("E1;Debe escribir las observaciones de la conciliacion;TxtObservaciones");
            }
            
            
            if($FechaConciliacion==''){
                exit("E1;La Fecha de Conciliacion no puede estar vacía;FechaConciliacion");
            }
            
            if($ConciliadorIPS==''){
                exit("E1;Debe escribir quien fue la persona con quien se realiza la conciliación;ConciliadorIPS");
            }
            
            if($MetodoConciliacion==''){
                exit("E1;Debe elejir un metodo de conciliación;CmbMetodoConciliacion");
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $DatosCruce=$obCon->DevuelveValores("$db.vista_cruce_cartera_asmet", "NumeroFactura", $NumeroFactura);
            $ValorAConciliar=0;
            $sql="SELECT SUM(ValorConciliacion) as Total FROM $db.conciliaciones_cruces WHERE NumeroFactura='$NumeroFactura' AND Estado<>'ANULADO'";
            $TotalesConciliacion=$obCon->Query($sql);
            $TotalesConciliacion=$obCon->FetchAssoc($TotalesConciliacion);
            
            if($TipoConciliacion==1){ //A favor de la EPS
                if(!is_numeric($ValorEPS) or $ValorEPS==0){
                    exit("E1;El Campo Valor EPS debe contener un valor Númerico mayor a Cero;ValorEPS");
                }
                $ValorAConciliar=$ValorEPS;
                if(($TotalesConciliacion["Total"])>abs($DatosCruce["Diferencia"])){
                    exit("E1;El valor digitado supera al valor por conciliar;ValorEPS");
                }
            }
            if($TipoConciliacion==2){ //A favor de la IPS
                if(!is_numeric($ValorIPS) or $ValorIPS==0){
                    exit("E1;El Campo Valor IPS debe contener un valor Númerico mayor a Cero;ValorIPS");
                }
                $ValorAConciliar=$ValorIPS;
                if(($TotalesConciliacion["Total"])>abs($DatosCruce["Diferencia"])){
                    exit("E1;El valor digitado supera al valor por conciliar;ValorIPS");
                }
            }
            
            $obCon->AgregarConciliacion($db,$DatosCruce, $NumeroFactura, $ConceptoConciliacion, $TipoConciliacion, $Observaciones, $destino, $ValorAConciliar, $ConciliadorIPS, $FechaConciliacion, $MetodoConciliacion, $idUser);
            $TotalConciliacion=$TotalesConciliacion["Total"]+$ValorAConciliar;
           
            if($TotalConciliacion >= (abs($DatosCruce["Diferencia"]))){
                $obCon->ActualizaRegistro("$db.carteraeps", "Estado", 1, "NumeroFactura", $NumeroFactura);
            }
            $Condicion=" WHERE t1.NumeroFactura=t2.NumeroFactura;";
            //$obCon->ActualizarRegistroHojaDeTrabajo($db, $Condicion);
            
            print("OK;Conciliacion Guardada");
        break;    //Fin caso 7
        
        case 8://Anular una conciliacion
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $TxtIdAnulacionConciliacion=$obCon->normalizar($_REQUEST["TxtIdAnulacionConciliacion"]);
            $DatosConciliacion=$obCon->DevuelveValores("$db.conciliaciones_cruces", "ID", $TxtIdAnulacionConciliacion);
            $CmbTipoAnulacion=$obCon->normalizar($_REQUEST["CmbTipoAnulacion"]);
            $TxtObservacionesAnulacion=$obCon->normalizar($_REQUEST["TxtObservacionesAnulacion"]);
            $NumeroFactura=$DatosConciliacion["NumeroFactura"];
            if($TxtIdAnulacionConciliacion==''){
                exit("E1;No se recibió una Conciliación para anular");
                
            }
            if($CmbTipoAnulacion==''){
                exit("E1;Debes Seleccionar un tipo de Anulacion;CmbTipoAnulacion");
                
            }
            if($TxtObservacionesAnulacion=='' or strlen($TxtObservacionesAnulacion)<10){
                exit("E1;Debes Escribir el motivo por el cual se anula la conciliación;TxtObservacionesAnulacion");
                
            }
            
            $obCon->AnularConciliacion($db, $TxtIdAnulacionConciliacion, $CmbTipoAnulacion, $NumeroFactura, $DatosConciliacion["ValorConciliacion"],$TxtObservacionesAnulacion);
            
            print("OK;Se realizó la anulación de la Conciliación;$NumeroFactura");
        break;//Fin caso 8    
        
        case 9: //se recibe el archivo de conciliaciones masivas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $FechaConciliacionMasiva=$obCon->normalizar($_REQUEST["FechaConciliacionMasiva"]);
            $ConciliadorIPSMasivo=$obCon->normalizar($_REQUEST["ConciliadorIPSMasivo"]);
            $CmbMetodoConciliacionMasivo=$obCon->normalizar($_REQUEST["CmbMetodoConciliacionMasivo"]);
            $CmbConceptoConciliacion=$obCon->normalizar($_REQUEST["CmbConceptoConciliacion"]);
            $CmbConceptoConciliacionAGS=$obCon->normalizar($_REQUEST["CmbConceptoConciliacionAGS"]);
            
            if($CmbIPS==""){
                exit("E1;No se recibió una IPS;CmbIPS");
            }
            
            if($CmbConceptoConciliacion=="" AND $CmbConceptoConciliacionAGS==""){
                exit("E1;Debe Seleccionar un Concepto de Conciliacion;CmbConceptoConciliacion");
            }
            
            if($CmbConceptoConciliacion>0 AND $CmbConceptoConciliacionAGS>0){
                exit("E1;Solo es posible seleccionar un concepto de conciliación;CmbConceptoConciliacion");
            }
            
            if($CmbEPS==""){
                exit("E1;No se recibió una EPS;CmbEPS");
            }
            
            if($FechaConciliacionMasiva==""){
                exit("E1;No se recibió una Fecha de Conciliacion;FechaConciliacionMasiva");
            }
            
            if($ConciliadorIPSMasivo==""){
                exit("E1;Debe digitar el nombre del conciliador de la IPS;ConciliadorIPSMasivo");
            }
            
            if($CmbMetodoConciliacionMasivo==""){
                exit("E1;Debe Seleccionar el metodo utilizado para conciliar;CmbMetodoConciliacionMasivo");
            }
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.temp_conciliaciones_cruces");
            $destinoConciliacionMasiva='';
            $keyArchivo=$obCon->getKeyConciliacionMasiva($FechaConciliacionMasiva, $CmbIPS, $CmbEPS, $idUser);
            $Extension="";
            if(!empty($_FILES['UpConciliacionMasiva']['name'])){
                
                $info = new SplFileInfo($_FILES['UpConciliacionMasiva']['name']);
                $Extension=($info->getExtension());  
                if($Extension=='xls' or $Extension=='xlsx'){
                    $carpeta="../../../soportes/$CmbIPS/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    $carpeta="../../../soportes/$CmbIPS/Conciliaciones/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    opendir($carpeta);                
                    $destinoConciliacionMasiva=$carpeta.$keyArchivo.".xlsx";
                    $NombreArchivo=$keyArchivo.".".$Extension;
                    move_uploaded_file($_FILES['UpConciliacionMasiva']['tmp_name'],$destinoConciliacionMasiva);
                    
                }else{
                    exit("E1;Error el archivo debe ser tipo xls o xlsx;UpConciliacionMasiva");
                }
            }else{
                exit("E1;No se recibió el archivo de conciliaciones masivas;UpConciliacionMasiva");
                
            }
            
            $destino='';
            $keyArchivo=$obCon->getKeySoporteConciliacionMasiva($FechaConciliacionMasiva, $CmbIPS, $CmbEPS, $idUser);
            $Extension="";
            if(!empty($_FILES['UpSoporteConciliacionMasiva']['name'])){
                
                $info = new SplFileInfo($_FILES['UpSoporteConciliacionMasiva']['name']);
                $Extension=($info->getExtension());  
                
                $carpeta="../../../soportes/$CmbIPS/Conciliaciones/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $destino=$carpeta.$keyArchivo.".".$Extension;
                $NombreArchivo=$keyArchivo.".".$Extension;
                move_uploaded_file($_FILES['UpSoporteConciliacionMasiva']['tmp_name'],$destino);
                    
                
            }else{
                exit("E1;No se envió ningún archivo de Soporte;UpSoporteConciliacionMasiva");
                
            }
            
            print("OK;Archivo Recibido");   
        break;//Fin caso 9
        
        case 10: //se lee el archivo y carga en la temporal
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $FechaConciliacionMasiva=$obCon->normalizar($_REQUEST["FechaConciliacionMasiva"]);
            $ConciliadorIPSMasivo=$obCon->normalizar($_REQUEST["ConciliadorIPSMasivo"]);
            $CmbMetodoConciliacionMasivo=$obCon->normalizar($_REQUEST["CmbMetodoConciliacionMasivo"]);
            $CmbConceptoConciliacion=$obCon->normalizar($_REQUEST["CmbConceptoConciliacion"]);
            $CmbConceptoConciliacionAGS=$obCon->normalizar($_REQUEST["CmbConceptoConciliacionAGS"]);
            if($CmbIPS==""){
                exit("E1;No se recibió una IPS;CmbIPS");
            }
            if($CmbConceptoConciliacion=="" AND $CmbConceptoConciliacionAGS==""){
                exit("E1;Debe Seleccionar un Concepto de Conciliacion;CmbConceptoConciliacion");
            }
            
            if($CmbConceptoConciliacion>0 AND $CmbConceptoConciliacionAGS>0){
                exit("E1;Solo es posible seleccionar un concepto de conciliación;CmbConceptoConciliacion");
            }
            
            if($CmbConceptoConciliacionAGS>0){
                $CmbConceptoConciliacion=$CmbConceptoConciliacionAGS;
            }
            if($CmbEPS==""){
                exit("E1;No se recibió una EPS;CmbEPS");
            }
            
            if($FechaConciliacionMasiva==""){
                exit("E1;No se recibió una Fecha de Conciliacion;FechaConciliacionMasiva");
            }
            
            if($ConciliadorIPSMasivo==""){
                exit("E1;Debe digitar el nombre del conciliador de la IPS;ConciliadorIPSMasivo");
            }
            
            if($CmbMetodoConciliacionMasivo==""){
                exit("E1;Debe Seleccionar el metodo utilizado para conciliar;CmbMetodoConciliacionMasivo");
            }
            $obCon->GuardeConciliacionMasivaEnTemporal($FechaConciliacionMasiva, $CmbIPS, $CmbEPS, $idUser, $ConciliadorIPSMasivo, $CmbMetodoConciliacionMasivo,$CmbConceptoConciliacion);
                     
            print("OK;Archivo Guardado en la temporal");
            
        break;    //Fin caso 10
        
        case 11: //se actualizan los valores de las conciliaciones masivas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $FechaConciliacionMasiva=$obCon->normalizar($_REQUEST["FechaConciliacionMasiva"]);
            $ConciliadorIPSMasivo=$obCon->normalizar($_REQUEST["ConciliadorIPSMasivo"]);
            $CmbMetodoConciliacionMasivo=$obCon->normalizar($_REQUEST["CmbMetodoConciliacionMasivo"]);
            $CmbConceptoConciliacion=$obCon->normalizar($_REQUEST["CmbConceptoConciliacion"]);
            $CmbConceptoConciliacionAGS=$obCon->normalizar($_REQUEST["CmbConceptoConciliacionAGS"]);
            if($CmbIPS==""){
                exit("E1;No se recibió una IPS;CmbIPS");
            }
            if($CmbConceptoConciliacion=="" AND $CmbConceptoConciliacionAGS==""){
                exit("E1;Debe Seleccionar un Concepto de Conciliacion;CmbConceptoConciliacion");
            }
            
            if($CmbConceptoConciliacion>0 AND $CmbConceptoConciliacionAGS>0){
                exit("E1;Solo es posible seleccionar un concepto de conciliación;CmbConceptoConciliacion");
            }
            
            if($CmbEPS==""){
                exit("E1;No se recibió una EPS;CmbEPS");
            }
            
            if($FechaConciliacionMasiva==""){
                exit("E1;No se recibió una Fecha de Conciliacion;FechaConciliacionMasiva");
            }
            
            if($ConciliadorIPSMasivo==""){
                exit("E1;Debe digitar el nombre del conciliador de la IPS;ConciliadorIPSMasivo");
            }
            
            if($CmbMetodoConciliacionMasivo==""){
                exit("E1;Debe Seleccionar el metodo utilizado para conciliar;CmbMetodoConciliacionMasivo");
            }
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.temp_conciliaciones_cruces t1 INNER JOIN $db.vista_cruce_cartera_asmet t2 ON t1.NumeroFactura=t2.NumeroFactura "
                    . " SET t1.NumeroContrato=t2.NumeroContrato, t1.MesServicio=t2.MesServicio, t1.FechaFactura=t2.FechaFactura, "
                    . "  t1.NumeroRadicado=t2.NumeroRadicado,  t1.Pendientes=t2.Pendientes,  t1.FechaRadicado=t2.FechaRadicado,  t1.ValorOriginal=t2.ValorDocumento, "
                    . "  t1.ValorImpuestoCalculado=t2.Impuestos,  t1.ValorMenosImpuesto=t2.ValorMenosImpuestos,  t1.ValorPagos=t2.TotalPagos,  t1.ValorAnticipos=t2.TotalAnticipos, "
                    . "  t1.ValorCopagos=t2.TotalCopagos,  t1.ValorDevoluciones=t2.TotalDevoluciones,  t1.ValorGlosaInicial=t2.TotalGlosaInicial,  t1.ValorGlosaFavor=t2.TotalGlosaFavor, "
                    . "  t1.ValorGlosaContra=t2.TotalGlosaContra,  t1.ValorGlosaconciliar=t2.GlosaXConciliar,  t1.ValorSaldoEps=t2.ValorSegunEPS,  t1.ValorSaldoIps=t2.ValorSegunIPS,"
                    . "  t1.ValorDiferencia=t2.Diferencia,t1.TotalConciliaciones=(SELECT SUM(ValorConciliacion) FROM $db.conciliaciones_cruces t3 WHERE t3.NumeroFactura=t1.NumeroFactura)";  
            $obCon->Query($sql);
            
            $sql="SELECT NumeroFactura FROM $db.temp_conciliaciones_cruces WHERE (ValorConciliacion+TotalConciliaciones ) > ABS(ValorDiferencia) LIMIT 1";
            $Consulta=$obCon->Query($sql);
            $Datos=$obCon->FetchAssoc($Consulta);
            if($Datos["NumeroFactura"]<>''){
                exit("E1;La Conciliación de la Factura $Datos[NumeroFactura] excede el valor permitido");
            }
       
            print("OK;Actualizacion de la temporal realizado");
            
        break;    //Fin caso 11
        
        case 12: //se insertan los datos en la tabla de conciliaciones
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $FechaConciliacionMasiva=$obCon->normalizar($_REQUEST["FechaConciliacionMasiva"]);
            $ConciliadorIPSMasivo=$obCon->normalizar($_REQUEST["ConciliadorIPSMasivo"]);
            $CmbMetodoConciliacionMasivo=$obCon->normalizar($_REQUEST["CmbMetodoConciliacionMasivo"]);
            $CmbConceptoConciliacion=$obCon->normalizar($_REQUEST["CmbConceptoConciliacion"]);
            $CmbConceptoConciliacionAGS=$obCon->normalizar($_REQUEST["CmbConceptoConciliacionAGS"]);
            if($CmbIPS==""){
                exit("E1;No se recibió una IPS;CmbIPS");
            }
            if($CmbConceptoConciliacion=="" AND $CmbConceptoConciliacionAGS==""){
                exit("E1;Debe Seleccionar un Concepto de Conciliacion;CmbConceptoConciliacion");
            }
            
            if($CmbConceptoConciliacion>0 AND $CmbConceptoConciliacionAGS>0){
                exit("E1;Solo es posible seleccionar un concepto de conciliación;CmbConceptoConciliacion");
            }
            
            if($CmbEPS==""){
                exit("E1;No se recibió una EPS;CmbEPS");
            }
            
            if($FechaConciliacionMasiva==""){
                exit("E1;No se recibió una Fecha de Conciliacion;FechaConciliacionMasiva");
            }
            
            if($ConciliadorIPSMasivo==""){
                exit("E1;Debe digitar el nombre del conciliador de la IPS;ConciliadorIPSMasivo");
            }
            
            if($CmbMetodoConciliacionMasivo==""){
                exit("E1;Debe Seleccionar el metodo utilizado para conciliar;CmbMetodoConciliacionMasivo");
            }
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="INSERT INTO $db.conciliaciones_cruces (NumeroContrato,NumeroFactura,MesServicio,FechaFactura,NumeroRadicado,"
                    . "Pendientes,FechaRadicado,ValorOriginal,ValorImpuestoCalculado,ValorImpuestoRetenciones,ValorMenosImpuesto,"
                    . "ValorPagos,ValorAnticipos,ValorCopagos,ValorDevoluciones,ValorGlosaInicial,ValorGlosaFavor,ValorGlosaContra,"
                    . "ValorGlosaconciliar,ValorSaldoEps,ValorSaldoIps,ValorDiferencia,ConceptoConciliacion,ConciliacionAFavorDe,"
                    . "Observacion,Soportes,ValorConciliacion,ConciliadorIps,FechaConciliacion,ViaConciliacion,Estado,idUser,FechaRegistro) "
                    . "SELECT NumeroContrato,NumeroFactura,MesServicio,FechaFactura,NumeroRadicado,Pendientes,FechaRadicado,ValorOriginal,ValorImpuestoCalculado,ValorImpuestoRetenciones,ValorMenosImpuesto,"
                    . "ValorPagos,ValorAnticipos,ValorCopagos,ValorDevoluciones,ValorGlosaInicial,ValorGlosaFavor,ValorGlosaContra,"
                    . "ValorGlosaconciliar,ValorSaldoEps,ValorSaldoIps,ValorDiferencia,ConceptoConciliacion,ConciliacionAFavorDe,"
                    . "Observacion,Soportes,ValorConciliacion,ConciliadorIps,FechaConciliacion,ViaConciliacion,Estado,idUser,FechaRegistro "
                    . "FROM $db.temp_conciliaciones_cruces";  
            $obCon->Query($sql);
            
            $sql="UPDATE $db.carteraeps t1 INNER JOIN $db.vista_cruce_cartera_asmet t2 ON t1.NumeroFactura=t2.NumeroFactura "
                    . " SET t1.Estado=1 WHERE ABS(t2.TotalConciliaciones) >= ABS(t2.Diferencia) OR t2.Diferencia=0";
            $obCon->Query($sql);
            print("OK;Registros insertados correctamente");
            
        break;    //Fin caso 12
        
        case 13: //se insertan los datos en la tabla de conciliaciones desde las facturas no presentadas por la ips
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $VigenciaInicial=$obCon->normalizar($_REQUEST["VigenciaInicial"]);
            $VigenciaFinal=$obCon->normalizar($_REQUEST["VigenciaFinal"]);
            if(!is_numeric($VigenciaInicial) or strlen($VigenciaInicial)<>'6'){
                exit("E1;La Vigencia Inicial debe ser un valor númerico de 6 dígitos;VigenciaInicialFSF");
            }
            
            if(!is_numeric($VigenciaFinal) or strlen($VigenciaFinal)<>'6'){
                exit("E1;La Vigencia Final debe ser un valor númerico de 6 dígitos;VigenciaFinalFSF");
            }
            
            if($CmbEPS==''){
                exit("E1;No se ha seleccionado una EPS;select2-CmbEPS-container");
            }
            $Fecha=date("Y-m-d");
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            $sql="INSERT INTO $db.carteracargadaips (NitEPS,NitIPS,NumeroFactura,NumeroRadicado,FechaRadicado,
                  NumeroContrato,ValorDocumento,ValorTotalpagar,FechaRegistro,FechaActualizacion,NoRelacionada) 
                  SELECT '$CmbEPS','$CmbIPS', NumeroFactura,NumeroRadicado,FechaRadicado, NumeroContrato,
                      '0','0','$Fecha','$Fecha','1' FROM $db.vista_cruce_cartera_eps_no_relacionadas_ips  
                  WHERE MesServicio>='$VigenciaInicial' AND MesServicio<='$VigenciaFinal' ";
            $obCon->Query($sql);
            print("OK;Registros insertados correctamente");
            
        break;    //Fin caso 13
        
        case 14://Crear un Acta de Conciliacion
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $FechaActaConciliacion=$obCon->normalizar($_REQUEST["FechaActaConciliacion"]);
            $FechaActaInicial=$obCon->normalizar($_REQUEST["FechaActaInicial"]);
            $TxtRepresentanteLegalIPS=$obCon->normalizar($_REQUEST["TxtRepresentanteLegalIPS"]);
            $TxtEncargadoEPS=$obCon->normalizar($_REQUEST["TxtEncargadoEPS"]);
            if($FechaActaInicial==''){
                exit("E1;No se recibió una Fecha Inicial;FechaActaInicial");
                
            }
            if($FechaActaConciliacion==''){
                exit("E1;No se recibió una Fecha de Corte;FechaActaConciliacion");
                
            }
            if($TxtRepresentanteLegalIPS==''){
                exit("E1;Se debe digitar el Nombre del Representante Legal de la IPS;TxtRepresentanteLegalIPS");
                
            }
            if($TxtEncargadoEPS==''){
                exit("E1;Se debe digitar el Nombre del Encargado de la EPS;TxtEncargadoEPS");
                
            }
            
            $idActa=$obCon->CrearActaConciliacion($FechaActaInicial,$FechaActaConciliacion, $CmbIPS, $TxtRepresentanteLegalIPS, $TxtEncargadoEPS,$idUser);
            
            print("OK;Acta $idActa Creada Correctamente;$idActa");
        break;//Fin caso 14  
        
        case 15://Agregar Compromiso a Acta
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $TxtCompromisoNuevo=$obCon->normalizar($_REQUEST["TxtCompromisoNuevo"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            if($idActaConciliacion==''){
                exit("E1;Se debe seleccionar un Acta de conciliación;idActaConciliacion");
                
            }
            if($TxtCompromisoNuevo==''){
                exit("E1;El campo de Compromiso no puede estar vacío;TxtCompromisoNuevo");
                
            }
                        
            $obCon->AgregarCompromisoActaConciliacion($idActaConciliacion, $TxtCompromisoNuevo, "", $idUser);
            
            print("OK;Compromiso o Resultado Agregado");
        break;//Fin caso 15
        
        case 16://Edita Compromiso a Acta
            $idCompromiso=$obCon->normalizar($_REQUEST["idCompromiso"]);
            $idCajaEdicion="TxtCompromiso_".$idCompromiso;
            $TxtCompromisoEditado=$obCon->normalizar($_REQUEST["TxtCompromisoEditado"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            if($idCompromiso==''){
                exit("E1;No se recibió un compromiso a editar");
                
            }
            if($TxtCompromisoEditado==''){
                exit("E1;la caja de texto no puede estar vacía;$idCajaEdicion");
                
            }
                        
            $obCon->ActualizaRegistro("actas_conciliaciones_resultados_compromisos", "ResultadoCompromiso", $TxtCompromisoEditado, "ID", $idCompromiso, 0);
            print("OK;Compromiso o Resultado Editado");
        break;//Fin caso 16
        
        case 17://Editar el campo de un acta
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $idCampoTexto=$obCon->normalizar($_REQUEST["idCampoTexto"]);
            $NuevoValor=$obCon->normalizar($_REQUEST["NuevoValor"]);
            $CampoAEditar=$obCon->normalizar($_REQUEST["CampoAEditar"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
                        
            if($idActaConciliacion==''){
                exit("E1;No se recibió el id del Acta a Editar");
                
            }
            if($NuevoValor==''){
                exit("E1;la caja de texto no puede estar vacía;$idCampoTexto");
                
            }
                        
            $obCon->ActualizaRegistro("actas_conciliaciones", $CampoAEditar, $NuevoValor, "ID", $idActaConciliacion, 0);
            $obCon->ActualizaRegistro("actas_conciliaciones", "Updated", date("Y-m-d H:i:s"), "ID", $idActaConciliacion, 0);
            $obCon->ActualizaRegistro("actas_conciliaciones", "idUserUpdate",$idUser, "ID", $idActaConciliacion, 0);
            if($CampoAEditar=='FechaInicial'){
                $DatosMesServicio = explode("-", $NuevoValor);
                $MesServicioInicial=$DatosMesServicio[0].$DatosMesServicio[1];
                $obCon->ActualizaRegistro("actas_conciliaciones", "MesServicioInicial", $MesServicioInicial, "ID", $idActaConciliacion, 0);
            }
            if($CampoAEditar=='FechaCorte'){
                $DatosMesServicio = explode("-", $NuevoValor);
                $MesServicioFinal=$DatosMesServicio[0].$DatosMesServicio[1];
                $obCon->ActualizaRegistro("actas_conciliaciones", "MesServicioFinal", $MesServicioFinal, "ID", $idActaConciliacion, 0);
            }
            print("OK;Campo $CampoAEditar del Acta de conciliación Editado");
        break;//Fin caso 17
        
        case 18://Obtenga Valores Completos de la diferencia en las columnas
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $Diferencia=abs($obCon->normalizar($_REQUEST["Diferencia"]));
            if($idActaConciliacion==''){
                exit("E1;No se recibió el id del Acta a Editar");
                
            }
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $DetalleDiferencias=$obCon->CalculeDiferenciasProceso1($idActaConciliacion,$db,$Diferencia);
            $CamposDiferencias= json_encode($DetalleDiferencias, JSON_FORCE_OBJECT);
            print("OK;Proceso 1 terminado;$CamposDiferencias");
            unset($DetalleDiferencias);
            unset($CamposDiferencias);
        break;//Fin caso 18
        
        case 19://Calcula las diferencias que están en varias columnas
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DetalleDiferencias=get_object_vars(json_decode($_REQUEST["DetalleDiferencias"]));    
            if($idActaConciliacion==''){
                exit("E1;No se recibió el id del Acta a Editar");
                
            }
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            //$DetalleDiferenciasMultiples=$obCon->CalculeDiferenciasMultiplesAC($db);
            $CamposDiferencias= json_encode($DetalleDiferencias, JSON_FORCE_OBJECT);
            print("OK;Proceso 2 terminado;$CamposDiferencias");
        break;//Fin caso 19
        
        case 20://Guarda la firma
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $TipoFirma=$obCon->normalizar($_REQUEST["TipoFirma"]);
            $CmbFirmaUsual=$obCon->normalizar($_REQUEST["CmbFirmaUsual"]);
            $TxtNombreFirmaActa=$obCon->normalizar($_REQUEST["TxtNombreFirmaActa"]);
            $TxtCargoFirmaActa=$obCon->normalizar($_REQUEST["TxtCargoFirmaActa"]);
            $TxtEmpresaFirmaActa=$obCon->normalizar($_REQUEST["TxtEmpresaFirmaActa"]);
            $TxtRepresentanteActaConciliacion=$obCon->normalizar($_REQUEST["TxtRepresentanteActaConciliacion"]);
            
            if($idActaConciliacion==''){
                exit("E1;No se recibió el id del Acta");
                
            }
            
            if($TipoFirma==''){
                exit("E1;No se recibió el Tipo de Firma a agregar");
                
            }
            
            if($TipoFirma=='1'){
                if($CmbFirmaUsual==''){
                    exit("E1;Debe Seleccionar una Firma Usual;CmbFirmaUsual");
                }
                if($CmbFirmaUsual=='RI'){
                    if($TxtRepresentanteActaConciliacion==''){
                        exit("E1;No se ha escrito el nombre del representante de la IPS;TxtRepresentanteActaConciliacion");
                    }else{
                        $obCon->AgregueFirmaActa($idActaConciliacion, $TxtRepresentanteActaConciliacion, "REPRESENTANTE LEGAL", $DatosIPS["Nombre"]);
                    }
                }else{
                    $DatosFirmas=$obCon->DevuelveValores("actas_conciliaciones_firmas_usuales", "ID", $CmbFirmaUsual);
                    $obCon->AgregueFirmaActa($idActaConciliacion, $DatosFirmas["Nombre"], $DatosFirmas["Cargo"], $DatosFirmas["Empresa"]);
                }
            }
            if($TipoFirma=='2'){
                if($TxtNombreFirmaActa==''){
                    exit("E1;Debe Seleccionar el Nombre para la Firma;TxtNombreFirmaActa");
                }
                if($TxtCargoFirmaActa==''){
                    exit("E1;Debe Digitar el Cargo de quien firma;TxtCargoFirmaActa");
                }
                if($TxtEmpresaFirmaActa==''){
                    exit("E1;Debe Digitar La Empresa;TxtEmpresaFirmaActa");
                }
                $obCon->AgregueFirmaActa($idActaConciliacion, $TxtNombreFirmaActa, $TxtCargoFirmaActa, $TxtEmpresaFirmaActa);
            }
            
            print("OK;Firma Agregada");
        break;//Fin caso 20
        
        case 21://Editar las firmas de un acta
            $idFirma=$obCon->normalizar($_REQUEST["idFirma"]);
            $idCajaFirma=$obCon->normalizar($_REQUEST["idCajaFirma"]);
            $TxtValorNuevo=$obCon->normalizar($_REQUEST["TxtValorNuevo"]);
            $CampoEditar=$obCon->normalizar($_REQUEST["CampoEditar"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);            
            if($idFirma==''){
                exit("E1;No se recibió el id de la Firma a Editar");
                
            }
            if($TxtValorNuevo==''){
                exit("E1;la caja de texto no puede estar vacía;$idCajaFirma");
                
            }
            if($CampoEditar==''){
                exit("E1;No se recibió el campo a Editar;$CampoEditar");
                
            }
                        
            $obCon->ActualizaRegistro("actas_conciliaciones_firmas", $CampoEditar, $TxtValorNuevo, "ID", $idFirma, 0);
            $obCon->ActualizaRegistro("actas_conciliaciones", "Updated", date("Y-m-d H:i:s"), "ID", $idActaConciliacion, 1);
            $obCon->ActualizaRegistro("actas_conciliaciones", "idUserUpdate",$idUser, "ID", $idActaConciliacion, 1);
            print("OK;Campo $CampoEditar de las firmas ha sido Editado");
        break;//Fin caso 21
        
        case 22://Eliminar el item de una tabla
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $idItem=$obCon->normalizar($_REQUEST["idItem"]);
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            if($Tabla==1){
                $obCon->BorraReg("actas_conciliaciones_firmas", "ID", $idItem);
            }
            if($Tabla==2){
                $obCon->BorraReg("actas_conciliaciones_contratos", "NumeroContrato", $idItem);
            }
            
            $obCon->ActualizaRegistro("actas_conciliaciones", "Updated", date("Y-m-d H:i:s"), "ID", $idActaConciliacion, 1);
            $obCon->ActualizaRegistro("actas_conciliaciones", "idUserUpdate",$idUser, "ID", $idActaConciliacion, 1);
            print("OK;Registro eliminado");
        break;//Fin caso 22
        
        case 23://Agregar contrato a un acta
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $NumeroContrato=$obCon->normalizar($_REQUEST["NumeroContrato"]);
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            if($idActaConciliacion==''){
                exit("E1;No se recibió un acta de conciliacion");
            }
            if($NumeroContrato==''){
                exit("E1;No se recibió un número de contrato");
            }
            $sql="SELECT NumeroContrato FROM actas_conciliaciones_contratos WHERE NumeroContrato='$NumeroContrato' AND idActaConciliacion='$idActaConciliacion'";
            $DatosContratos=$obCon->FetchAssoc($obCon->Query($sql));
            
            //$DatosContratos=$obCon->DevuelveValores("actas_conciliaciones_contratos", "NumeroContrato", $NumeroContrato);
            if($DatosContratos["NumeroContrato"]<>''){
                exit("E1;El contrato $DatosContratos[NumeroContrato] ya se encuentra agregado al Acta de conciliación No. $idActaConciliacion");
            }
             
            $obCon->AgregueContratoActaConciliacion($idActaConciliacion, $NumeroContrato);
            
            print("OK;Contrato $NumeroContrato Agregado al Acta $idActaConciliacion");
        break;//Fin caso 23
        
        case 24:// Ajusta los valores iniciales del acta de conciliacion
            
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $TotalesActa=$obCon->obtengaValoresGeneralesActaConciliacion($db, $idActaConciliacion);            
            $obCon->AjusteValoresInicialesActaConciliacion($db, $idActaConciliacion, $TotalesActa);
            
            print("OK;Valores Generales Inicializados");
        break; //Fin caso 24    
    
        case 25:// guarde el soporte
            
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            
            $destino='';
            $keyArchivo="Acta_$idActaConciliacion"."_";
            $Extension="";
            if(!empty($_FILES['UpSoporteActaConciliacionCierre']['name'])){
                
                $info = new SplFileInfo($_FILES['UpSoporteActaConciliacionCierre']['name']);
                $Extension=($info->getExtension());  
                if($Extension=='pdf'){
                    $carpeta="../../../soportes/$CmbIPS/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    $carpeta="../../../soportes/$CmbIPS/actas_conciliaciones/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    opendir($carpeta);                
                    $destino=$carpeta.$keyArchivo.".".$Extension;
                    $NombreArchivo=$keyArchivo.".".$Extension;
                    move_uploaded_file($_FILES['UpSoporteActaConciliacionCierre']['tmp_name'],$destino);
                    $obCon->ActualizaRegistro("actas_conciliaciones", "Soporte", $destino, "ID", $idActaConciliacion);
                }else{
                    exit("E1;Error el archivo debe ser tipo pdf;UpSoporteActaConciliacionCierre");
                }
            }else{
                exit("E1;No se envió ningún archivo;UpSoporteActaConciliacionCierre");
                
            }
            
            if($idActaConciliacion==''){
                exit("E1;No se recibió un acta de conciliación");
            }
            
            if($CmbIPS==''){
                exit("E1;No se recibió una IPS");
            }
            
            print("OK;Soporte Guardado");
        break; //Fin caso 25  
        
        case 26:// obtener total de items que cruzan
            
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($idActaConciliacion==''){
                exit("E1;No se recibió un acta de conciliación");
            }
            
            if($CmbIPS==''){
                exit("E1;No se recibió una IPS");
            }
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion);
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $sql="SELECT COUNT(*) as TotalRegistros FROM $db.vista_reporte_ips 
                  t1 WHERE 
                EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') 
                AND NOT EXISTS (SELECT 1 FROM $db.actas_conciliaciones_items t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND t3.idActaConciliacion='$idActaConciliacion') 

                AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal'
                    ";
            $DatosRegistros=$obCon->FetchAssoc($obCon->Query($sql));
            $TotalRegistros=$DatosRegistros["TotalRegistros"];            
            print("OK;Registros del cruce que deben copiarse: $TotalRegistros;$TotalRegistros");
            
        break; //Fin caso 26
        
        case 27:// COPIAR items del acta de la vista que cruza
            $TotalItemsACopiar=$obCon->normalizar($_REQUEST["TotalRegistros"]);
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($idActaConciliacion==''){
                exit("E1;No se recibió un acta de conciliación");
            }
            
            if($CmbIPS==''){
                exit("E1;No se recibió una IPS");
            }
            
            $obCon->CopiarItemsAlActaConciliacion($db, $idActaConciliacion,$idUser);
            $sql="SELECT COUNT(*) as TotalItems FROM $db.actas_conciliaciones_items WHERE idActaConciliacion='$idActaConciliacion' AND NoRelacionada='0'";
            $DatosTotales=$obCon->FetchAssoc($obCon->Query($sql));
            $TotalRegistros=$DatosTotales["TotalItems"];
            $Divisor=$TotalItemsACopiar;
            if($TotalItemsACopiar==0){
                $Divisor=1;
            }
            $porcentaje=round((100/$Divisor)*$TotalRegistros);
            if($TotalRegistros>=$TotalItemsACopiar){
                print("FIN;Registros del cruce copiados éxitosamente, Proceso Terminado");
            }else{
                print("OK;$TotalRegistros de $TotalItemsACopiar registros del cruce Copiados;$TotalItemsACopiar;$porcentaje");
            }
            
        break; //Fin caso 27
        
        case 28:// obtener total de items que no cruzan
            
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($idActaConciliacion==''){
                exit("E1;No se recibió un acta de conciliación");
            }
            
            if($CmbIPS==''){
                exit("E1;No se recibió una IPS");
            }
            $DatosActa=$obCon->DevuelveValores("actas_conciliaciones", "ID", $idActaConciliacion);
            $MesServicioInicial=$DatosActa["MesServicioInicial"];
            $MesServicioFinal=$DatosActa["MesServicioFinal"];
            $sql="SELECT COUNT(*) as TotalRegistros FROM $db.vista_cruce_cartera_eps_sin_relacion_segun_ags  
                  t1 WHERE 
                EXISTS (SELECT 1 FROM actas_conciliaciones_contratos t2 WHERE t2.NumeroContrato=t1.NumeroContrato AND t2.idActaConciliacion='$idActaConciliacion') 
                AND NOT EXISTS (SELECT 1 FROM $db.actas_conciliaciones_items t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND t3.idActaConciliacion='$idActaConciliacion') 

                AND t1.MesServicio>='$MesServicioInicial' AND t1.MesServicio<='$MesServicioFinal'
                    ";
            $DatosRegistros=$obCon->FetchAssoc($obCon->Query($sql));
            $TotalRegistros=$DatosRegistros["TotalRegistros"];            
            print("OK;Registros de los archivos que no cruzan que deben copiarse: $TotalRegistros;$TotalRegistros");
            
        break; //Fin caso 28
        
        case 29:// COPIAR items del acta de la vista que no cruza
            $TotalItemsACopiar=$obCon->normalizar($_REQUEST["TotalRegistros"]);
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($idActaConciliacion==''){
                exit("E1;No se recibió un acta de conciliación");
            }
            
            if($CmbIPS==''){
                exit("E1;No se recibió una IPS");
            }
            
            $obCon->CopiarItemsNoCruceAlActaConciliacion($db, $idActaConciliacion,$idUser);
            $sql="SELECT COUNT(*) as TotalItems FROM $db.actas_conciliaciones_items WHERE idActaConciliacion='$idActaConciliacion' AND NoRelacionada='1'";
            $DatosTotales=$obCon->FetchAssoc($obCon->Query($sql));
            $TotalRegistros=$DatosTotales["TotalItems"];
            $Divisor=$TotalItemsACopiar;
            if($TotalItemsACopiar==0){
                $Divisor=1;
            }
            $porcentaje=round((100/$Divisor)*$TotalRegistros);
            if($TotalRegistros>=$TotalItemsACopiar){
                print("FIN;Registros no relacionados por la ips copiados éxitosamente, Proceso Terminado;Copia de Registros Finalizada");
            }else{
                print("OK;$TotalRegistros de $TotalItemsACopiar registros no relacionados por la ips copiados;$TotalItemsACopiar;$porcentaje");
            }
            
        break; //Fin caso 29
        
        case 30:// Actualizar el estado de las Facturas y Acta
            
            $idActaConciliacion=$obCon->normalizar($_REQUEST["idActaConciliacion"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($idActaConciliacion==''){
                exit("E1;No se recibió un acta de conciliación");
            }
            
            if($CmbIPS==''){
                exit("E1;No se recibió una IPS");
            }
            
            $obCon->ActualizaRegistro("actas_conciliaciones", "Estado", 1, "ID", $idActaConciliacion);
            $sql="UPDATE $db.carteraeps t1 INNER JOIN $db.actas_conciliaciones_items t2 
                    ON t1.NumeroFactura = t2.Numerofactura SET t1.Estado=2 
                    WHERE t2.idActaConciliacion='$idActaConciliacion'";
            $obCon->Query($sql);
            $Ruta="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=36&idActaConciliacion=$idActaConciliacion";
            $html=("<br><a href='$Ruta' target='_BLANK'><button class='btn btn-success'>Imprimir PDF</button></a>");
            $Ruta="../../general/procesadores/GeneradorCSV.process.php?Opcion=6&idActaConciliacion=$idActaConciliacion&NIT_IPS=$CmbIPS";
            $html.=(" <a href='$Ruta' target='_BLANK'><button class='btn btn-primary'>Anexo del Acta</button></a>");
            print("OK;Se realizó la Actualización de los estados para cerrar el Acta $html");
            
        break; //Fin caso 30
        
        case 31: //Actualice las diferencias
            $Diferencia=$obCon->normalizar($_REQUEST["ACDiferencia"]);
            
            $TotalesDiferencias["TxtACDiferenciaXPagos"]=$obCon->normalizar($_REQUEST["TxtACDiferenciaXPagos"]);
            $TotalesDiferencias["TxtACFacturasIPSNoRelacionadasEPS"]=$obCon->normalizar($_REQUEST["TxtACFacturasIPSNoRelacionadasEPS"]);
            $TotalesDiferencias["TxtACGlosasPendientesXConciliar"]=$obCon->normalizar($_REQUEST["TxtACGlosasPendientesXConciliar"]);
            $TotalesDiferencias["TxtACFacturasDevueltas"]=$obCon->normalizar($_REQUEST["TxtACFacturasDevueltas"]);
            $TotalesDiferencias["TxtACDiferenciaXImpuestos"]=$obCon->normalizar($_REQUEST["TxtACDiferenciaXImpuestos"]);
            $TotalesDiferencias["TxtACDescuentoXRetefuente"]=$obCon->normalizar($_REQUEST["TxtACDescuentoXRetefuente"]);
            $TotalesDiferencias["TxtACFacturasNoRelacionadasXIPS"]=$obCon->normalizar($_REQUEST["TxtACFacturasNoRelacionadasXIPS"]);
            $TotalesDiferencias["TxtACRetencionesImpuestosNoProcedentes"]=$obCon->normalizar($_REQUEST["TxtACRetencionesImpuestosNoProcedentes"]);
            $TotalesDiferencias["TxtACAjustesDeCartera"]=$obCon->normalizar($_REQUEST["TxtACAjustesDeCartera"]);
            $TotalesDiferencias["TxtACDiferenciaXValorFacturado"]=$obCon->normalizar($_REQUEST["TxtACDiferenciaXValorFacturado"]);
            $TotalesDiferencias["TxtACDiferenciaXUPC"]=$obCon->normalizar($_REQUEST["TxtACDiferenciaXUPC"]);
            $TotalesDiferencias["TxtACGlosasPendientesXDescargarIPS"]=$obCon->normalizar($_REQUEST["TxtACGlosasPendientesXDescargarIPS"]);
            $TotalesDiferencias["TxtACAnticiposPendientesXCruzar"]=$obCon->normalizar($_REQUEST["TxtACAnticiposPendientesXCruzar"]);
            $TotalesDiferencias["TxtACDescuentosLMA"]=$obCon->normalizar($_REQUEST["TxtACDescuentosLMA"]);
            $TotalesDiferencias["TxtACPendientesAuditoria"]=$obCon->normalizar($_REQUEST["TxtACPendientesAuditoria"]);
            
            foreach ($TotalesDiferencias as $key => $value) {
                if(!is_numeric($TotalesDiferencias[$key]) or $TotalesDiferencias[$key]<0){
                    exit("E1;El campo $key debe ser un número igual o mayor a Cero;$key");
                }
            }
            
            $GranTotal=array_sum($TotalesDiferencias);
            if($GranTotal==abs($Diferencia)){
                print("OK;Valores Verificados correctamente");
            }else{
                print("E1;La sumatoria de los valores (".number_format($GranTotal).") no es igual a la diferencia (".number_format($Diferencia).")");
            }
        break;   //Fin caso 31
        
        case 32:// Copiar los datos de la vista cruce de cartera a la tabla hoja de trabajo
            
            $TotalRegistros=$obCon->normalizar($_REQUEST["TotalRegistros"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($CmbIPS==''){
                exit("E1;No hay una IPS Seleccionada");
            }
            $HojaDeTrabajo=$db.".hoja_de_trabajo";
            
            $VistaACopiar=$db.".vista_cruce_cartera_asmet";
            $Limit=" LIMIT 10";
            $sql="UPDATE carteraeps SET NumeroContrato=trim(NumeroContrato)";
            $obCon->Query2($sql, HOST, USER, PW, $db, "");
            $sql="UPDATE carteraeps SET NumeroContrato= REPLACE(NumeroContrato,'\n','')";
            $obCon->Query2($sql, HOST, USER, PW, $db, "");            
            $sql="DROP TABLE IF EXISTS $HojaDeTrabajo";
            $obCon->Query($sql);
            
            $sql="CREATE TABLE hoja_de_trabajo AS
                SELECT t2.ID,t2.NumeroFactura,t2.Estado,t2.DepartamentoRadicacion,t1.NoRelacionada,

                    (SELECT FechaFactura FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1) as FechaFactura,
        t2.MesServicio,
		t2.NumeroRadicado,
        (SELECT IFNULL((SELECT 'SI' FROM pendientes_de_envio WHERE pendientes_de_envio.NumeroRadicado=t2.NumeroRadicado LIMIT 1),'NO')) AS Pendientes,
        (SELECT FechaRegistro FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND t2.Estado=1 ORDER BY FechaRegistro DESC LIMIT 1) AS FechaConciliacion,
        t2.FechaRadicado,
		t2.NumeroContrato,
		t2.ValorOriginal as ValorDocumento,
        (t2.ValorOriginal-t2.ValorMenosImpuestos) as Impuestos,
        (SELECT IFNULL((SELECT (Creditos-Debitos) FROM vista_retenciones_facturas WHERE vista_retenciones_facturas.NumeroFactura=t2.NumeroFactura ),0)) AS ImpuestosSegunASMET,
		t2.ValorMenosImpuestos,
		(SELECT IFNULL((SELECT SUM(ValorPago) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3070' OR notas_db_cr_2.TipoOperacion2='3071' OR notas_db_cr_2.TipoOperacion2='3072' OR notas_db_cr_2.TipoOperacion2='3086' OR notas_db_cr_2.TipoOperacion2='3089' OR notas_db_cr_2.TipoOperacion2='3090' OR notas_db_cr_2.TipoOperacion2='3091' OR notas_db_cr_2.TipoOperacion2='2260') AND (notas_db_cr_2.TipoOperacion!='2103') ),0)) AS TotalPagosNotas,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2299' ),0)) AS Capitalizacion,
        ((SELECT ABS(TotalPagosNotas))+(SELECT ABS(Capitalizacion) ) ) AS TotalPagos,
		(SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND (NumeroInterno='2216' or NumeroInterno='2117' or NumeroInterno='2254') ),0)) AS TotalAnticipos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2275' ),0)) AS DescuentoPGP,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2259' ),0)) AS FacturasDevueltasAnticipos,

        
        (SELECT IFNULL((SELECT COUNT((NumeroFactura)) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND (NumeroInterno='2039' or NumeroInterno='2259') ),0)) AS NumeroFacturasDevueltasAnticipos,
	  
	(SELECT IFNULL((SELECT SUM(ValorTotal) FROM vista_copagos_asmet WHERE vista_copagos_asmet.NumeroFactura=t2.NumeroFactura ),0)) AS TotalCopagos,
        
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND (NumeroInterno='2215' OR NumeroInterno='2601' OR NumeroInterno='2214') ),0)) AS OtrosDescuentos,
        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2260' ),0)) AS AjustesCartera,
        (SELECT IFNULL((SELECT (ValorTotalGlosa) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaInicial,
        (SELECT IFNULL((SELECT (ValorGlosaFavor) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaFavor,
        (SELECT IFNULL((SELECT (ValorGlosaContra) FROM glosaseps_asmet WHERE glosaseps_asmet.NumeroFactura=t2.NumeroFactura ORDER BY FechaRegistro DESC LIMIT 1),0)) AS TotalGlosaContra,
        ((SELECT TotalGlosaInicial)-(SELECT TotalGlosaFavor)-(SELECT TotalGlosaContra) ) AS GlosaXConciliar,
        (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039' ) ),0)) AS DevolucionesPresentadas,
        (SELECT IFNULL((SELECT COUNT(DISTINCT NumeroTransaccion) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND notas_db_cr_2.C13<>'N' AND notas_db_cr_2.TipoOperacion LIKE '20%'  ),0)) AS FacturasPresentadas,
        (SELECT IF(((SELECT DevolucionesPresentadas ) >= ((SELECT FacturasPresentadas)) OR (SELECT NumeroFacturasDevueltasAnticipos ) >= ((SELECT FacturasPresentadas) ) ),'NO','SI')) AS FacturaActiva,

        (SELECT IFNULL((SELECT SUM(ValorAnticipado) FROM anticipos2 WHERE anticipos2.NumeroFactura=t2.NumeroFactura AND NumeroInterno='2039' ),0)) AS FacturasDevueltas,
        (SELECT IF(FacturaActiva='SI',0,(SELECT IFNULL((SELECT (ValorTotal) FROM notas_db_cr_2 WHERE notas_db_cr_2.NumeroFactura=t2.NumeroFactura AND (notas_db_cr_2.TipoOperacion='2259' OR notas_db_cr_2.TipoOperacion='2269' OR notas_db_cr_2.TipoOperacion='2039') AND notas_db_cr_2.FechaTransaccion>=t2.FechaRadicado LIMIT 1),0))) ) AS TotalDevolucionesNotas,
        (SELECT IF(FacturaActiva='SI',0,((SELECT ABS(TotalDevolucionesNotas))+(SELECT ABS(FacturasDevueltas)) ))) AS TotalDevolucionesParciales,

        (SELECT IF(TotalDevolucionesParciales <> 0,(SELECT(TotalDevolucionesParciales)), IF(FacturaActiva='SI',0,(SELECT FacturasDevueltasAnticipos)) ) ) AS TotalDevoluciones,
        (SELECT IFNULL((SELECT SUM(ValorTotalcartera) FROM carteraxedades WHERE carteraxedades.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS CarteraXEdades,
        
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=1),0)) AS ConciliacionesAFavorEPS,
        (SELECT IFNULL((SELECT SUM(ValorConciliacion) FROM conciliaciones_cruces WHERE conciliaciones_cruces.NumeroFactura=t2.NumeroFactura AND conciliaciones_cruces.ConciliacionAFavorDe=2),0)) AS ConciliacionesAFavorIPS,

	    (t2.ValorMenosImpuestos - (SELECT TotalPagos)-(SELECT TotalAnticipos)-(SELECT TotalGlosaFavor)-(SELECT GlosaXConciliar)-(SELECT OtrosDescuentos)-(SELECT ABS(TotalCopagos))-(SELECT ABS(TotalDevoluciones)/*+(FacturasDevueltas)*/)-(SELECT ABS(DescuentoPGP))-(SELECT ABS(ConciliacionesAFavorEPS))+(SELECT ABS(ConciliacionesAFavorIPS)) ) AS ValorSegunEPS,
        (SELECT IFNULL((SELECT ROUND(ValorTotalpagar) FROM carteracargadaips WHERE carteracargadaips.NumeroFactura=t2.NumeroFactura LIMIT 1),0)) AS ValorSegunIPS,
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
        '0' AS DiferenciaVariada

                FROM carteracargadaips t1 INNER JOIN carteraeps t2 ON t1.NumeroFactura=t2.NumeroFactura WHERE t2.Estado<2;
                         ";
            $obCon->Query2($sql, HOST, USER, PW, $db, "");
            
            $sql="ALTER TABLE `hoja_de_trabajo` ADD PRIMARY KEY(`ID`),
                    CHANGE `ID` `ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
                    ADD INDEX(`NumeroFactura`),
                    ADD INDEX(`MesServicio`),
                    ADD INDEX(`NumeroRadicado`),
                    ADD INDEX(`NumeroContrato`),
                    CHANGE `DiferenciaXPagosNoDescargados` `DiferenciaXPagosNoDescargados` DOUBLE NOT NULL,
                    CHANGE `DiferenciaXGlosasPendientesXConciliar` `DiferenciaXGlosasPendientesXConciliar` DOUBLE NOT NULL,                    
                    CHANGE `DiferenciaXFacturasDevueltas` `DiferenciaXFacturasDevueltas` DOUBLE NOT NULL,
                    CHANGE `DiferenciaXDiferenciaXImpuestos` `DiferenciaXDiferenciaXImpuestos` DOUBLE NOT NULL,
                    CHANGE `DiferenciaXFacturasNoRelacionadasXIPS` `DiferenciaXFacturasNoRelacionadasXIPS` DOUBLE NOT NULL,                    
                    CHANGE `DiferenciaXAjustesDeCartera` `DiferenciaXAjustesDeCartera` DOUBLE NOT NULL,
                    CHANGE `DiferenciaXValorFacturado` `DiferenciaXValorFacturado` DOUBLE NOT NULL,
                    CHANGE `DiferenciaXGlosasPendientesXDescargarIPS` `DiferenciaXGlosasPendientesXDescargarIPS` DOUBLE NOT NULL,                    
                    CHANGE `DiferenciaVariada` `DiferenciaVariada` DOUBLE NOT NULL,
                    ENGINE = MyISAM                                    

                    ;  
                    

                ";
                $obCon->Query2($sql, HOST, USER, PW, $db, "");
                
            
                print("FIN;Se ha creado la hoja de trabajo correctamente;");
            
        break; //Fin caso 32
        
        case 33:// Actualizar Hoja de Trabajo
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($CmbIPS==''){
                exit("E1;No hay una IPS Seleccionada");
            }
            $HojaDeTrabajo=$db.".hoja_de_trabajo";      
            
            $VistaACopiar=$db.".vista_cruce_cartera_asmet";            
            $obCon->ActualizarRegistroHojaDeTrabajo($db, "");                   
            print("OK;Se ha actualizado la hoja de trabajo");
            
        break;    //fin caso 33
        
        case 34://Obtener el numero de registros a copiar en la hoja de trabajo
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($CmbIPS==''){
                exit("E1;No hay una IPS Seleccionada");
            }
            $HojaDeTrabajo=$db.".hoja_de_trabajo";
            //$obCon->VaciarTabla($HojaDeTrabajo);           
            $VistaACopiar=$db.".vista_cruce_cartera_asmet";
            $sql="SELECT COUNT(*) as TotalRegistros FROM $VistaACopiar;";
            
            $DatosRegistros=$obCon->FetchAssoc($obCon->Query($sql));
            $TotalRegistros=$DatosRegistros["TotalRegistros"];
            
            print("OK;Total de registros a Copiar: $TotalRegistros;$TotalRegistros");
        break;//Fin caso 34
        
        case 35://Encontrar diferencias variadas
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($CmbIPS==''){
                exit("E1;No hay una IPS Seleccionada");
            }
            $HojaDeTrabajo="$db.hoja_de_trabajo";
            $sql="UPDATE $HojaDeTrabajo t1 INNER JOIN $db.vista_cruce_diferencias t2 ON t1.NumeroFactura=t2.NumeroFactura 
                    SET t1.DiferenciaXPagosNoDescargados=(abs(t2.DiferenciaXPagos)+abs(t2.DiferenciaXAnticipos)+abs(t2.DiferenciaXGlosaContraEPS)),
                        t1.DiferenciaXGlosasPendientesXConciliar=(abs(t2.DiferenciaXGlosaXConciliar)),
                        t1.DiferenciaXFacturasDevueltas=(abs(t2.DiferenciaXDevoluciones)+abs(t2.DiferenciaXDevolucionesNoIPS)),
                        t1.DiferenciaXDiferenciaXImpuestos=(abs(t2.DiferenciaXImpuestos)),
                        
                        t1.DiferenciaXAjustesDeCartera=(abs(t2.DiferenciaXCopagos)+abs(t2.DiferenciaXOtrosDescuentos)+abs(t2.DiferenciaXAjustesCartera)),
                        t1.DiferenciaXValorFacturado=(abs(t2.DiferenciaXValorFacturado)),
                        t1.DiferenciaXGlosasPendientesXDescargarIPS=(abs(t2.DiferenciaXDescuentoPGP)+abs(t2.DiferenciaXGlosaFavorEPS)),
                        t1.DiferenciaVariada=abs(t2.DiferenciaVariada);
                    ";
            $obCon->Query($sql);
            
            $sql="UPDATE $db.hoja_de_trabajo 
                    SET DiferenciaVariada=0 
                    WHERE 
              DiferenciaXPagosNoDescargados<>0 
              OR DiferenciaXGlosasPendientesXConciliar<>0 
              OR DiferenciaXFacturasDevueltas<>0 
              OR DiferenciaXDiferenciaXImpuestos<>0 
              OR DiferenciaXFacturasNoRelacionadasXIPS<>0 
              OR DiferenciaXAjustesDeCartera<>0 
              OR DiferenciaXValorFacturado<>0 
              OR DiferenciaXGlosasPendientesXDescargarIPS<>0 
              ;";
            $obCon->Query($sql);
            $obCon->EncontrarDiferenciasVariadas($db);            
            print("OK;Diferencias Encontradas;");
        break;//Fin caso 35
        
        case 36:// Copiar los datos de la vista cruce de cartera a la tabla hoja de trabajo opcion 2
            
            $TotalRegistros=$obCon->normalizar($_REQUEST["TotalRegistros"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
                     
            if($CmbIPS==''){
                exit("E1;No hay una IPS Seleccionada");
            }
            $HojaDeTrabajo=$db.".hoja_de_trabajo";
            //$obCon->VaciarTabla($HojaDeTrabajo);
            $VistaACopiar=$db.".vista_cruce_cartera_asmet";
            $Limit=" LIMIT 1000";
            
            $sql="INSERT INTO $HojaDeTrabajo (`NumeroFactura`,`Estado`,`DepartamentoRadicacion`,`NoRelacionada`,`FechaFactura`,`MesServicio`,
                    `NumeroRadicado`,`Pendientes`,`FechaConciliacion`,`FechaRadicado`,`NumeroContrato`,`ValorDocumento`,
                    `Impuestos`,`ImpuestosSegunASMET`,`ValorMenosImpuestos`,`TotalPagosNotas`,`Capitalizacion`,`TotalPagos`,
                    `TotalAnticipos`,`DescuentoPGP`,`FacturasDevueltas`,`NumeroFacturasDevueltasAnticipos`,
                    `TotalCopagos`,`OtrosDescuentos`,`AjustesCartera`,`TotalGlosaInicial`,`TotalGlosaFavor`,`TotalGlosaContra`,
                    `GlosaXConciliar`,`DevolucionesPresentadas`,`FacturasPresentadas`,`FacturaActiva`,`TotalDevolucionesNotas`,`TotalDevoluciones`,
                    `CarteraXEdades`,`ConciliacionesAFavorEPS`,`ConciliacionesAFavorIPS`,`ValorSegunEPS`,`ValorSegunIPS`,`Diferencia`,
                    `ValorIPSMenor`,`TotalConciliaciones`,`TotalAPagar`,`ConciliacionesPendientes`,`DiferenciaXPagos`)
                    SELECT 
                    `NumeroFactura`,`Estado`,`DepartamentoRadicacion`,`NoRelacionada`,`FechaFactura`,`MesServicio`,
                    `NumeroRadicado`,`Pendientes`,`FechaConciliacion`,`FechaRadicado`,`NumeroContrato`,`ValorDocumento`,
                    `Impuestos`,`ImpuestosSegunASMET`,`ValorMenosImpuestos`,`TotalPagosNotas`,`Capitalizacion`,`TotalPagos`,
                    `TotalAnticipos`,`DescuentoPGP`,`FacturasDevueltas`,`NumeroFacturasDevueltasAnticipos`,
                    `TotalCopagos`,`OtrosDescuentos`,`AjustesCartera`,`TotalGlosaInicial`,`TotalGlosaFavor`,`TotalGlosaContra`,
                    `GlosaXConciliar`,`DevolucionesPresentadas`,`FacturasPresentadas`,`FacturaActiva`,`TotalDevolucionesNotas`,`TotalDevoluciones`,
                    `CarteraXEdades`,`ConciliacionesAFavorEPS`,`ConciliacionesAFavorIPS`,`ValorSegunEPS`,`ValorSegunIPS`,`Diferencia`,
                    `ValorIPSMenor`,`TotalConciliaciones`,`TotalAPagar`,`ConciliacionesPendientes`,`DiferenciaXPagos`
                    FROM $VistaACopiar WHERE NOT EXISTS (SELECT 1 FROM $HojaDeTrabajo WHERE $HojaDeTrabajo.NumeroFactura=$VistaACopiar.NumeroFactura) $Limit;
                         ";
            
             
            $obCon->Query($sql);
            
           
            $sql="SELECT COUNT(ID) AS TotalRegistros FROM $HojaDeTrabajo";
            //$sql="SELECT MAX(ID) as TotalRegistros FROM $HojaDeTrabajo";
            $DatosHoja=$obCon->FetchAssoc($obCon->Query($sql));
            $TotalRegistrosCopiados=$DatosHoja["TotalRegistros"];
            $Divisor=$TotalRegistros;
            if($TotalRegistros==0){
                $Divisor=1;
            }
            $Porcentaje=round((100/$Divisor)*$TotalRegistrosCopiados);
            if($TotalRegistrosCopiados>=$TotalRegistros){
                $sql="UPDATE $HojaDeTrabajo t1 INNER JOIN $db.vista_cruce_diferencias t2 ON t1.NumeroFactura=t2.NumeroFactura 
                    SET t1.DiferenciaXPagosNoDescargados=ABS(t2.DiferenciaXPagos+t2.DiferenciaXAnticipos+t2.DiferenciaXGlosaContraEPS+t2.XPagos2),
                        t1.DiferenciaXGlosasPendientesXConciliar=ABS(t2.DiferenciaXGlosaXConciliar+t2.GlosasXConciliar2),
                        t1.DiferenciaXFacturasDevueltas=ABS(t2.DiferenciaXDevoluciones+t2.DiferenciaXDevolucionesNoIPS),
                        t1.DiferenciaXDiferenciaXImpuestos=ABS(t2.DiferenciaXImpuestos),
                        t1.DiferenciaXFacturasNoRelacionadasXIPS=ABS(SELECT Diferencia FROM $db.vista_cruce_cartera_asmet t3 WHERE t3.NumeroFactura=t1.NumeroFactura AND t1.NoRelacionada=1),
                        t1.DiferenciaXAjustesDeCartera=ABS(t2.DiferenciaXCopagos+t2.DiferenciaXOtrosDescuentos+t2.DiferenciaXAjustesCartera),
                        t1.DiferenciaXValorFacturado=ABS(t2.DiferenciaXValorFacturado),
                        t1.DiferenciaXGlosasPendientesXDescargarIPS=ABS(t2.DiferenciaXDescuentoPGP+t2.DiferenciaXGlosaFavorEPS),
                        t1.DiferenciaVariada=ABS(t2.DiferenciaVariada);
                    ";
                $obCon->Query($sql);
                print("FIN;Se copiaron todos los registros $TotalRegistros;$Porcentaje");
            }else{
               print("OK;<h2>$TotalRegistrosCopiados copiados de $TotalRegistros</h2>;$Porcentaje");             
            }
                        
        break; //Fin caso 36
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>