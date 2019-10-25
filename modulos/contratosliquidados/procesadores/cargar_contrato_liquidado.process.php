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
            $obCon->VaciarTabla("$db.temporal_registro_liquidacion_contratos_items");
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
                //print("Contrato: ".$idContrato);
            }
                        
            if($DatosEPS["ID"]>2 ){                
                exit("E1;EPS no ompatible");
                
            }
            
            print("OK;Encabezado del contrato realizado;$idContrato");
        break; //fin caso 3
        
        case 4://Guarda los items en la temporal
            
            $idContrato=$obCon->normalizar($_REQUEST["idContrato"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $DatosEPS=$obCon->DevuelveValores("eps", "NIT", $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            $sql="SELECT * FROM control_cargue_contratos_liquidados WHERE idUser='$idUser' AND Estado=0 LIMIT 1";
            $Consulta=$obCon->Query($sql);
            $DatosArchivo=$obCon->FetchAssoc($Consulta);
            if($DatosEPS["ID"]==1 or $DatosEPS["ID"]==2){       
                $obCon->GuardeArchivoEnTemporal($idContrato,$CmbIPS,$CmbEPS,$DatosArchivo["NombreArchivo"],$DatosArchivo["Ruta"],$DatosArchivo["Soporte"],$idUser);
                
            }
                        
            if($DatosEPS["ID"]>2 ){                
                exit("E1;EPS no ompatible");
                
            }
            
            print("OK;Encabezado del contrato realizado;$idContrato");
        break; //fin caso 4
        
        case 5://Insertar items del contrato
            $idContrato=$obCon->normalizar($_REQUEST["idContrato"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            $sql="INSERT INTO $db.`registro_liquidacion_contratos_items`  
                   SELECT *
                  FROM $db.`temporal_registro_liquidacion_contratos_items`;                    
                    ";
            //print($sql);
            
            $obCon->Query($sql);
            $TotalPagado=$obCon->SumeColumna("$db.registro_liquidacion_contratos_items", "ValorPagado", "idContrato", $idContrato);
            $TotalFacturado=$obCon->SumeColumna("$db.registro_liquidacion_contratos_items", "ValorFacturado", "idContrato", $idContrato);
            
            $obCon->update("control_cargue_contratos_liquidados", "Estado", 1, " WHERE idUser='$idUser' AND Estado='0'");
            $obCon->update("registro_liquidacion_contratos", "ValorPagado", $TotalPagado, " WHERE ID='$idContrato'");
            $obCon->update("registro_liquidacion_contratos", "TotalFacturado", $TotalFacturado, " WHERE ID='$idContrato'");
            print("OK;Registros realizados correctamente");
        break; //fin caso 5
    
        case 6://Borrar temporales y registros mal hechos
            
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            $sql="DELETE FROM control_cargue_contratos_liquidados WHERE Estado=0 AND idUser='$idUser'";
            $obCon->Query($sql);
            print("OK;Temporales Borrados");
        break; //fin caso 5
    
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>