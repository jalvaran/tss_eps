<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/carteraeps.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new CarteraEPS($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Verificar si ya se cargó
            print('OK;Verificando que no se haya cargado el archivo previamente');
            exit();
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="SELECT * FROM $db.controlcargueseps WHERE NombreCargue='$keyArchivo'";
            $Consulta=$obCon->Query($sql);
            $DatosCargue=$obCon->FetchAssoc($Consulta);
            if($DatosCargue["ID"]==''){
                print("OK;Verificando que no se haya cargado el archivo previamente"); 
            }else{
                print("E1;Este archivo fue cargado el día $DatosCargue[FechaActualizacion] con un valor de: ". number_format($DatosCargue["ValorCargue"])."<br>Desea Actualizarlo?"); 
            }
                       
            
        break; //fin caso 1
        
        case 2: //Recibir el archivo
            
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            //$obCon->BorraReg("$db.temporalcarguecarteraeps", "idUser", $idUser);
            $obCon->VaciarTabla("$db.temporalcarguecarteraeps");
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());                
                $carpeta="../../../soportes/$CmbIPS/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $destino=$carpeta.$keyArchivo.".".$Extension;
                $NombreArchivo=$keyArchivo.".".$Extension;
                move_uploaded_file($_FILES['UpCartera']['tmp_name'],$destino);
            }else{
                print("E1;No se envió ningún archivo");
                exit();
            }
            $obCon->RegistreArchivo($keyArchivo,$CmbEPS,$CmbIPS,$NombreArchivo,$destino,$Extension,$idUser);
            print("OK;Archivo Recibido");            
            
        break; //fin caso 2
          
        case 3://Lee el archivo y lo sube a la temporal
            
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $LineaActual=$obCon->normalizar($_REQUEST["LineaActual"]);
            $Separador=$obCon->normalizar($_REQUEST["Separador"]);
            $DatosIPS=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosIPS["DataBase"];
            $DatosEPS=$obCon->DevuelveValores("eps", "NIT", $CmbEPS);
            $keyArchivo=$obCon->getKeyCarteraEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            if($DatosEPS["ID"]==1){
                $LineaActual=$obCon->LeerArchivoSAS($keyArchivo,$FechaCorteCartera,$CmbIPS,$LineaActual,$Separador,$idUser);
            }
            if($DatosEPS["ID"]==2){
                $LineaActual=$obCon->LeerArchivoMutual($keyArchivo,$FechaCorteCartera,$CmbIPS,$LineaActual,$Separador,$idUser);
            }
            print("OK;Archivo cargado;$LineaActual");
        break; //fin caso 3   
    
        case 4://LLevar elos registros nuevos en la temporal al historial de cargas
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.temporalcarguecarteraeps SET ValidaFactura=NumeroFactura WHERE NumeroFactura REGEXP ('^[0-9]+$');";
            $obCon->Query($sql);
            $sql="UPDATE $db.historial_carteracargada_eps cips INNER JOIN $db.temporalcarguecarteraeps t ON cips.NumeroFactura=t.NumeroFactura SET t.FlagUpdate=1  "
                    . "WHERE cips.NumeroOperacion=t.NumeroOperacion and cips.FechaFactura=t.FechaFactura and"
                    . " cips.TipoOperacion=t.TipoOperacion;";
            $obCon->Query($sql);
            $sql="INSERT INTO $db.`historial_carteracargada_eps` 
                    SELECT * FROM $db.`temporalcarguecarteraeps` as t1 WHERE t1.FlagUpdate=0 AND idUser='$idUser'";
            $obCon->Query($sql);
            $sql="UPDATE $db.temporalcarguecarteraeps SET FlagUpdate=0 WHERE idUser='$idUser'";
            $obCon->Query($sql);
            print("OK;Registros nuevos copiados al historial");
        break; //fin caso 4  
    
        case 5://Insertar facturas nuevas
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.temporalcarguecarteraeps cips INNER JOIN $db.carteraeps t ON cips.NumeroFactura=t.NumeroFactura SET cips.FlagUpdate=1;";
            $obCon->Query($sql);
            
            $sql="INSERT INTO $db.`carteraeps` (`NitEPS`,`CodigoSucursal`,`Sucursal`,`NumeroFactura`,`Descripcion`,`RazonSocial`,`Nit_IPS`,`NumeroContrato`,`Prefijo`,`DepartamentoRadicacion`,`ValorOriginal`,`ValorMenosImpuestos`,`idUser`,`FechaRegistro`,`FechaActualizacion`,`MesServicio`,`FechaRadicado`,`NumeroRadicado`,`TipoOperacion`) 
                    SELECT `Nit_EPS`,`CodigoSucursal`,`Sucursal`,`NumeroFactura`,`Descripcion`,`RazonSocial`,`Nit_IPS`,`NumeroContrato`,`Prefijo`,`DepartamentoRadicacion`,`ValorOriginal`,`ValorMenosImpuestos`,`idUser`,`FechaRegistro`,`FechaActualizacion` ,`MesServicio`,`FechaFactura`,`NumeroRadicado`,`TipoOperacion`
                    FROM $db.`temporalcarguecarteraeps` as t1 WHERE t1.FlagUpdate=0 AND t1.TipoOperacion NOT LIKE '25%' AND t1.TipoOperacion NOT LIKE '23%' AND t1.TipoOperacion<>829 AND t1.TipoOperacion<>0 AND t1.idUser='$idUser' GROUP BY NumeroFactura";
            $obCon->Query($sql);
            
            
            print("OK;Registros realizados correctamente");
        break; //fin caso 5
    
        case 6://Borrar temporales y registros mal hechos
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            //$obCon->VaciarTabla("$db.temporalcarguecarteraeps");
            $obCon->BorraReg("$db.controlcargueseps", "NombreCargue", $keyArchivo);
            print("OK;Temporales Borrados");
        break; //fin caso 5
    
        case 7://Devuelve el numero de lineas del archivo
            
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $Separador=$obCon->normalizar($_REQUEST["Separador"]);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $DatosEPS=$obCon->DevuelveValores("eps", "NIT", $CmbEPS);
            $NumeroColumnasEncabezado=54;// 51 para mutual y 52 para sas
            if($DatosEPS["ID"]==2){
                //exit("OK;Archivo de mutual;100");
                $NumeroColumnasEncabezado=51;
            }
            $keyArchivo=$obCon->getKeyCarteraEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $NumLineasArchivo=$obCon->CalcularNumeroRegistros($keyArchivo,$CmbIPS,$Separador,$NumeroColumnasEncabezado,$CmbEPS,$idUser);
            //$NumLineasArchivo=50;
            print("OK;El archivo contiene ".$NumLineasArchivo." Lineas;$NumLineasArchivo");
        break; //fin caso 7  
        
        case 8://Actualiza la Cartera
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            $sql="UPDATE $db.carteraeps t1 
                SET t1.ValorOriginal=(SELECT ValorOriginal FROM $db.historial_carteracargada_eps t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t2.TipoOperacion = t3.TipoOperacion AND Aplicacion='FACTURA' ) ORDER BY t2.FechaFactura DESC LIMIT 1),
                    t1.FechaRadicado=(SELECT FechaFactura FROM $db.historial_carteracargada_eps t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t2.TipoOperacion = t3.TipoOperacion AND Aplicacion='FACTURA' ) ORDER BY t2.FechaFactura DESC LIMIT 1),
                    t1.MesServicio=(SELECT MesServicio FROM $db.historial_carteracargada_eps t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t2.TipoOperacion = t3.TipoOperacion AND Aplicacion='FACTURA' ) ORDER BY t2.FechaFactura DESC LIMIT 1),
                    t1.NumeroRadicado=(SELECT NumeroRadicado FROM $db.historial_carteracargada_eps t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t2.TipoOperacion = t3.TipoOperacion AND Aplicacion='FACTURA' ) ORDER BY t2.FechaFactura DESC LIMIT 1),
                    t1.NumeroContrato=(SELECT NumeroContrato FROM $db.historial_carteracargada_eps t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t2.TipoOperacion = t3.TipoOperacion AND Aplicacion='FACTURA' ) ORDER BY t2.FechaFactura DESC LIMIT 1),
                    t1.NumeroOperacion=(SELECT NumeroOperacion FROM $db.historial_carteracargada_eps t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t2.TipoOperacion = t3.TipoOperacion AND Aplicacion='FACTURA' ) ORDER BY t2.FechaFactura DESC LIMIT 1), 
                    t1.TipoOperacion=(SELECT TipoOperacion FROM $db.historial_carteracargada_eps t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t2.TipoOperacion = t3.TipoOperacion AND Aplicacion='FACTURA' ) ORDER BY t2.FechaFactura DESC LIMIT 1),   
                    t1.CarteraEPSTipoNegociacion = IF(MOD(t1.TipoOperacion,2) = 0, 'EVENTO', 'CAPITA'),    
                    t1.ValorMenosImpuestos=(SELECT ValorMenosImpuestos FROM $db.historial_carteracargada_eps t2 WHERE t1.NumeroFactura=t2.NumeroFactura AND EXISTS (SELECT 1 FROM tipos_operacion t3 WHERE t2.TipoOperacion = t3.TipoOperacion AND Aplicacion='FACTURA' ) ORDER BY t2.FechaFactura DESC LIMIT 1);";
              
           $obCon->Query($sql);
                        
            print("OK;Registros Actualizados correctamente");
        break; //fin caso 8
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>