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
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
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
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.pagos_asmet_temporal");
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());
                
                   
                if($Extension=="xls" or $Extension=="xlsx"){
                    $carpeta="../../../soportes/$CmbIPS/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    opendir($carpeta);                
                    $destino=$carpeta.$keyArchivo.".".$Extension;
                    $NombreArchivo=$keyArchivo.".".$Extension;
                    move_uploaded_file($_FILES['UpCartera']['tmp_name'],$destino);
                }else{
                    print("E1;La Extension: $Extension No está permitida");
                    exit();
                }
            }else{
                print("E1;No se envió ningún archivo");
                exit();
            }
            
            $obCon->RegistreArchivo($keyArchivo,$CmbEPS,$CmbIPS,$NombreArchivo,$destino,$Extension,$idUser);
            print("OK;Archivo Recibido");            
            
        break; //fin caso 2
             
        case 5://Insertar facturas nuevas
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.pagos_asmet_temporal t1 INNER JOIN $db.pagos_asmet t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FlagUpdate=1  
                    WHERE t1.Proceso=t2.Proceso AND t1.Estado=t2.Estado AND t1.FechaPagoFactura=t2.FechaPagoFactura AND t1.NumeroPago=t2.NumeroPago;";
            $obCon->Query($sql);
            $sql="INSERT INTO $db.`pagos_asmet` ( `Nit_IPS`, `Nit_EPS`,`Proceso`, `DescripcionProceso`, `Estado`, `Cuenta`, `Banco`, `FechaPagoFactura`, `NumeroPago`, `TipoOperacion`, `NumeroFactura`, `ValorBrutoPagar`, `ValorDescuento`, `ValorIva`, `ValorRetefuente`, `ValorReteiva`, `ValorReteica`, `ValorOtrasRetenciones`, `ValorCruces`, `ValorAnticipos`, `ValorTotal`, `ValorTranferido`, `Regional`, `llaveCompuesta`, `idUser`, `Soporte`, `FechaRegistro`, `FechaActualizacion`) 
                   SELECT `Nit_IPS`, `Nit_EPS`,`Proceso`, `DescripcionProceso`, `Estado`, `Cuenta`, `Banco`, `FechaPagoFactura`, `NumeroPago`, `TipoOperacion`, `NumeroFactura`, `ValorBrutoPagar`, `ValorDescuento`, `ValorIva`, `ValorRetefuente`, `ValorReteiva`, `ValorReteica`, `ValorOtrasRetenciones`, `ValorCruces`, `ValorAnticipos`, `ValorTotal`, `ValorTranferido`, `Regional`, `llaveCompuesta`, `idUser`, `Soporte`, `FechaRegistro`, `FechaActualizacion`
                  FROM $db.`pagos_asmet_temporal` as t1 WHERE t1.FlagUpdate=0;
                    
                    ";
            //print($sql);
            
            $obCon->Query($sql);
            
            print("OK;Registros realizados correctamente");
        break; //fin caso 5
    
        case 6://Borrar temporales y registros mal hechos
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.pagos_asmet_temporal");
            $obCon->BorraReg("$db.controlcargueseps", "NombreCargue", $keyArchivo);
            print("OK;Temporales Borrados");
        break; //fin caso 5
    
        case 7://Guarda los archivos en la temporal
            
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $Separador=2;
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $DatosEPS=$obCon->DevuelveValores("eps", "NIT", $CmbEPS);
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            //if($DatosEPS["ID"]==1){                
              //  $obCon->GuardePagosASMETSASEnTemporal($keyArchivo,$CmbIPS,$CmbEPS,$idUser);
           // }
            if($DatosEPS["ID"]==2 or $DatosEPS["ID"]==1){                
                $obCon->GuardePagosASMETMutualEnTemporal($keyArchivo,$CmbIPS,$CmbEPS,$idUser);
            }
            
            if($DatosEPS["ID"]>2 ){                
                exit("E1;EPS no ompatible");
                
            }
            
            print("OK;El archivo Se guardó en la tabla temporal correctamente");
        break; //fin caso 7
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>