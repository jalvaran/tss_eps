<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/anticiposeps.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new AnticiposEPS($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Verificar si ya se cargó
            //print('OK;Verificando que no se haya cargado el archivo previamente');
            //exit();
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $NumeroAnticipo=$obCon->normalizar($_REQUEST["NumeroAnticipo"]);
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS,$NumeroAnticipo);
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
            $NumeroAnticipo=$obCon->normalizar($_REQUEST["NumeroAnticipo"]);
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS,$NumeroAnticipo);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.temporal_anticipos_asmet");
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());
                
                   
                if($Extension=="xls" or $Extension=="xlsx"){
                    $carpeta="../../../soportes/813001952/";
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
            
            $obCon->RegistreArchivo($keyArchivo,$CmbEPS,$CmbIPS,$NombreArchivo,$destino,$Extension,$idUser,$NumeroAnticipo);
            print("OK;Archivo Recibido");            
            
        break; //fin caso 2
             
        case 5://Insertar facturas nuevas
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $NumeroAnticipo=$obCon->normalizar($_REQUEST["NumeroAnticipo"]);
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS,$NumeroAnticipo);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.temporal_anticipos_asmet t1 INNER JOIN $db.anticipos_asmet t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FlagUpdate=1  
                    WHERE t1.ValorAnticipado=t2.ValorAnticipado AND t1.NumeroAnticipo=t2.NumeroAnticipo ;";
            $obCon->Query($sql);
           $sql= "INSERT INTO $db.`anticipos_asmet` ( `Nit_IPS`, `NumeroAnticipo`,`DescripcionNC`, `NumeroFactura`, `ValorFactura`, `ValorReteiva`, `ValorRetefuente`, `ValorMenosImpuestos`, `ValorSaldo`, `ValorAnticipado`,  `NumeroRadicado`, `Soporte`, `idUser`, `FechaRegistro`, `keyFile`) "
                   . " SELECT  `Nit_IPS`, `NumeroAnticipo`,`DescripcionNC`, `NumeroFactura`, `ValorFactura`, `ValorReteiva`, `ValorRetefuente`, `ValorMenosImpuestos`, `ValorSaldo`, `ValorAnticipado`,  `NumeroRadicado`, `Soporte`, `idUser`, `FechaRegistro`, `keyFile` "
                   . "FROM $db.temporal_anticipos_asmet WHERE FlagUpdate=0";
        
            $obCon->Query($sql);
            
            print("OK;Registros realizados correctamente");
        break; //fin caso 5
    
        case 6://Borrar temporales y registros mal hechos
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $NumeroAnticipo=$obCon->normalizar($_REQUEST["NumeroAnticipo"]);
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS,$NumeroAnticipo);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.temporal_anticipos_asmet");
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
            $NumeroAnticipo=$obCon->normalizar($_REQUEST["NumeroAnticipo"]);
            $keyArchivo=$obCon->getKeyPagosEPS($FechaCorteCartera, $CmbIPS, $CmbEPS,$NumeroAnticipo);
            if($DatosEPS["ID"]<=2){                
                $obCon->GuardeAnticiposASMETEnTemporal($keyArchivo,$CmbIPS,$CmbEPS,$idUser,$NumeroAnticipo);
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