<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/glosaseps.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new GlosasEPS($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Verificar si ya se cargó
            print('OK;Verificando que no se haya cargado el archivo previamente');
            exit();
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyArchivoEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
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
            $keyArchivo=$obCon->getKeyArchivoEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.temporal_glosaseps_asmet");
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());
                if($Extension<>'csv'){
                    exit("E1;El archivo enviado no corresponde a un archivo de glosas para ASMET, debe ser un CSV y se recibió un $Extension");
                }                
                $carpeta="../../../soportes/813001952/";
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
            $keyArchivo=$obCon->getKeyArchivoEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            if($DatosEPS["ID"]<=2){
                $LineaActual=$obCon->LeerArchivo($DatosEPS["ID"],$keyArchivo,$FechaCorteCartera,$CmbIPS,$LineaActual,$Separador,$idUser);
            }
            
            print("OK;Archivo cargado;$LineaActual");
        break; //fin caso 3   
    
        case 4://LLevar elos registros nuevos en la temporal al historial de cargas
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyArchivoEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.temporal_glosaseps_asmet t1 INNER JOIN $db.glosaseps_asmet t2 ON t1.NumeroFactura=t2.NumeroFactura SET t.FlagUpdate=1  "
                    . "WHERE t1.ValorTotalGlosa=t2.ValorTotalGlosa and t1.ValorGlosaFavor=t2.ValorGlosaFavor and t1.ValorGlosaContra=t2.ValorGlosaContra and t1.ValorPendienteResolver=t2.ValorPendienteResolver ";
            $obCon->Query($sql);
            //$obCon->VaciarTabla("$db.glosaseps_asmet");
            $sql="INSERT INTO $db.`glosaseps_asmet` 
                    SELECT * FROM $db.`temporal_glosaseps_asmet` as t1 WHERE t1.FlagUpdate=0";
            $obCon->Query($sql);
            
            print("OK;Registros nuevos copiados al historial");
        break; //fin caso 4  
    
        case 5://Insertar facturas nuevas
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyArchivoEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.temporal_glosaseps_asmet t1 INNER JOIN $db.glosaseps_asmet t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FlagUpdate=1  "
                    . "WHERE t1.ValorTotalGlosa=t2.ValorTotalGlosa and t1.ValorGlosaFavor=t2.ValorGlosaFavor and t1.ValorGlosaContra=t2.ValorGlosaContra and t1.ValorPendienteResolver=t2.ValorPendienteResolver ";
            $obCon->Query($sql);
            $sql="INSERT INTO $db.`glosaseps_asmet` (  `Sede`,`Nit_IPS`,`RazonSocial`, `NumeroRadicado`,  `NumeroFactura`,`FechaRadicado`, `ValorFactura`, `ValorTotalGlosa`, `ValorGlosaFavor`, `ValorGlosaContra`, `ValorPendienteResolver`,  `Soporte`,`idUser`, `FechaRegistro`, `FechaActualizacion`)
                    SELECT   `Sede`,`Nit_IPS`,`RazonSocial`, `NumeroRadicado`,  `NumeroFactura`,`FechaRadicado`, `ValorFactura`, `ValorTotalGlosa`, `ValorGlosaFavor`, `ValorGlosaContra`, `ValorPendienteResolver`,  `Soporte`,`idUser`, `FechaRegistro`, `FechaActualizacion` FROM $db.`temporal_glosaseps_asmet` as t1 WHERE t1.FlagUpdate=0";
            $obCon->Query($sql);
            print("OK;Registros realizados correctamente");
        break; //fin caso 5
    
        case 6://Borrar temporales y registros mal hechos
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyArchivoEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.temporal_glosaseps_asmet");
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
            if($DatosEPS["ID"]==1){
                $NumeroColumnasEncabezado=13;// 51 para mutual y 52 para sas
            }else{
                $NumeroColumnasEncabezado=12;// 51 para mutual y 52 para sas
            }
                
            
            
            $keyArchivo=$obCon->getKeyArchivoEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $NumLineasArchivo=$obCon->CalcularNumeroRegistros($keyArchivo,$CmbIPS,$Separador,$NumeroColumnasEncabezado,$CmbEPS,$idUser);
            //$NumLineasArchivo=50;
            print("OK;El archivo contiene ".$NumLineasArchivo." Lineas;$NumLineasArchivo");
        break; //fin caso 7  
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>