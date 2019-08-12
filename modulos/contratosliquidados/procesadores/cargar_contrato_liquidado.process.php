<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/cargar_contrato_liquidado.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new CargarContratos($idUser);
    
    switch ($_REQUEST["Accion"]) {
       
        case 2: //Recibir el archivo
            
            //$FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            //$keyArchivo=$obCon->getKeyArchivo($FechaCorteCartera, $CmbIPS, $CmbEPS);
            //$keySoporte=$obCon->getKeySoporte($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            //$obCon->VaciarTabla("$db.temporal_comprobantesegresoasmet");
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());
                $ExtensionContrato=$Extension;
                   
                if($Extension=="xls" or $Extension=="xlsx"){
                    $carpeta="../../../soportes/contratos/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    $carpeta="../../../soportes/contratos/$CmbIPS/";
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777);
                    }
                    
                    opendir($carpeta); 
                    $DatosArchivo= explode("_", $_FILES['UpCartera']['name']);
                    if($DatosArchivo[0]<>$CmbIPS){
                        exit("E1;El archivo enviado no corresponde al NIT Seleccionado, ".$DatosArchivo[0]);
                    }
                    $NombreArchivoContrato=str_replace(' ','_',$_FILES['UpCartera']['name']);
                    
                    $destinoArchivo=$carpeta.$NombreArchivoContrato;
                    $DatosContratosSubidos=$obCon->DevuelveValores("control_cargue_contratos_liquidados", "NombreArchivo", $NombreArchivoContrato);
                    if($DatosContratosSubidos["NombreArchivo"]<>''){
                        exit("E1;El archivo ya fue cargado el día ".$DatosContratosSubidos["FechaRegistro"]);
                    }
                    move_uploaded_file($_FILES['UpCartera']['tmp_name'],$destinoArchivo);
                }else{
                    print("E1;La Extension: $Extension No está permitida");
                    exit();
                }
            }else{
                print("E1;No se envió ningún archivo");
                exit();
            }
            
            if(!empty($_FILES['UpSoporte']['name'])){
                
                $info = new SplFileInfo($_FILES['UpSoporte']['name']);
                $Extension=($info->getExtension());
                
                $carpeta="../../../soportes/contratos/$CmbIPS/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $NombreArchivo=str_replace(' ','_',$_FILES['UpSoporte']['name']);
                $destino=$carpeta.$NombreArchivo;
                $Soporte=$destino;
                move_uploaded_file($_FILES['UpSoporte']['tmp_name'],$destino);
                
            }else{
                print("E1;No se envió ningún archivo");
                exit();
            }
            
            $obCon->RegistreArchivo($CmbEPS,$CmbIPS,$NombreArchivoContrato,$destinoArchivo,$ExtensionContrato,$Soporte,$idUser);
            print("OK;Archivo Recibido");            
            
        break; //fin caso 2
             
        case 3://Guarda los encabezados
            
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $DatosEPS=$obCon->DevuelveValores("eps", "NIT", $CmbEPS);
            
            $sql="SELECT * FROM control_cargue_contratos_liquidados WHERE idUser='$idUser' AND Estado=0 LIMIT 1";
            $Consulta=$obCon->Query($sql);
            $DatosArchivo=$obCon->FetchAssoc($Consulta);
            if($DatosEPS["ID"]==1 or $DatosEPS["ID"]==2){       
                $idContrato=$obCon->RegistreEncabezadoContrato($CmbIPS,$CmbEPS,$DatosArchivo["NombreArchivo"],$DatosArchivo["Ruta"],$DatosArchivo["Soporte"],$idUser);
                print("Contrato: ".$idContrato);
            }
                        
            if($DatosEPS["ID"]>2 ){                
                exit("E1;EPS no ompatible");
                
            }
            
            print("OK;Encabezado del contrato realizado;$idContrato");
        break; //fin caso 3
        
        case 5://Insertar registros nuevos
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyArchivo($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.temporal_comprobantesegresoasmet t1 INNER JOIN $db.comprobantesegresoasmet t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FlagUpdate=1  "
                    . "WHERE t1.TipoOperacion=t2.TipoOperacion and t1.NumeroComprobante=t2.NumeroComprobante and"
                    . " t1.FechaComprobante=t2.FechaComprobante AND t1.NumeroInterno=t2.NumeroInterno AND t1.MesServicio=t2.MesServicio AND t1.Valor3=t2.Valor3 ;";
            $obCon->Query($sql);
            $sql="INSERT INTO $db.`comprobantesegresoasmet`  
                   SELECT *
                  FROM $db.`temporal_comprobantesegresoasmet` as t1 WHERE t1.FlagUpdate=0;
                    
                    ";
            //print($sql);
            
            $obCon->Query($sql);
            
            print("OK;Registros realizados correctamente");
        break; //fin caso 5
    
        case 6://Borrar temporales y registros mal hechos
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyArchivo($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            //$obCon->VaciarTabla("$db.temporal_comprobantesegresoasmet");
            $obCon->BorraReg("$db.controlcargueseps", "NombreCargue", $keyArchivo);
            print("OK;Temporales Borrados");
        break; //fin caso 5
    
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>