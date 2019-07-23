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
            
            if($ConceptoConciliacion==''){
                exit("E1;No se ha seleccionado un concepto de conciliación;CmbConcepto");
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
                if($ValorEPS=='' or !is_numeric($ValorEPS) or $ValorEPS<=0){
                    exit("E1;El Campo Valor EPS debe contener un valor Númerico mayor a Cero;ValorEPS");
                }
                $ValorAConciliar=$ValorEPS;
                if(($TotalesConciliacion["Total"]+$ValorEPS)>abs($DatosCruce["Diferencia"])){
                    exit("E1;El valor digitado supera al valor por conciliar;ValorEPS");
                }
            }
            if($TipoConciliacion==2){ //A favor de la IPS
                if($ValorIPS=='' or !is_numeric($ValorIPS) or $ValorIPS<=0){
                    exit("E1;El Campo Valor IPS debe contener un valor Númerico mayor a Cero;ValorIPS");
                }
                $ValorAConciliar=$ValorIPS;
                if(($TotalesConciliacion["Total"]+$ValorIPS)>abs($DatosCruce["Diferencia"])){
                    exit("E1;El valor digitado supera al valor por conciliar;ValorIPS");
                }
            }
            
            $obCon->AgregarConciliacion($db,$DatosCruce, $NumeroFactura, $ConceptoConciliacion, $TipoConciliacion, $Observaciones, $destino, $ValorAConciliar, $ConciliadorIPS, $FechaConciliacion, $MetodoConciliacion, $idUser);
            $TotalConciliacion=$TotalesConciliacion["Total"]+$ValorAConciliar;
           
            if($TotalConciliacion >= (abs($DatosCruce["Diferencia"]))){
                $obCon->ActualizaRegistro("$db.carteraeps", "Estado", 1, "NumeroFactura", $NumeroFactura);
            }
            print("OK;Conciliacion Guardada");
        break;    
            
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>