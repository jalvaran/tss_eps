<?php
error_reporting(0);
session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/CargarReporteContratoLiquidacion.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new ReporteContratos($idUser);
    $db="ts_eps_resoluciones_glosas";
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Verificar si ya se cargó
            
            $NumeroContrato=$obCon->normalizar($_REQUEST["NumeroContrato"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $NumeroContrato=$obCon->getNumContract($NumeroContrato);
            $sql="SELECT * FROM $db.resoluciones_glosas_revision_contrato_glosa WHERE Nit_IPS='$CmbIPS' AND NumeroContrato='$NumeroContrato' LIMIT 1";
            $Consulta=$obCon->Query($sql);
            $DatosContrato=$obCon->FetchAssoc($Consulta);
            if($DatosContrato["ID"]<>''){
                exit("E1; El contrato $NumeroContrato de la IPS ya existe en el registro con ID: ".$DatosContrato["ID"]);
            }
            $keyArchivo=$obCon->getKeyArchivo($NumeroContrato, $CmbIPS);
            
            $sql="SELECT * FROM $db.resoluciones_glosas_control_envio_glosa WHERE NombreArchivo='$keyArchivo'";
            $Consulta=$obCon->Query($sql);
            $DatosCargue=$obCon->FetchAssoc($Consulta);
            if($DatosCargue["ID"]==''){
                print("OK;Verificando que no se haya cargado el archivo previamente"); 
            }else{
                print("E1;Este archivo fue cargado el día $DatosCargue[FechaRegistro]"); 
            }
                       
            
        break; //fin caso 1
        
        case 2: //Recibir el archivo
            
            $NumeroContrato=$obCon->normalizar($_REQUEST["NumeroContrato"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $NumeroContrato=$obCon->getNumContract($NumeroContrato);
            $keyArchivo=$obCon->getKeyArchivo($NumeroContrato, $CmbIPS);
            
            //$obCon->VaciarTabla("$db.temp_resoluciones_glosas_revision_contrato_glosa");
            $obCon->BorraReg("$db.temp_resoluciones_glosas_revision_contrato_glosa", "idUser", $idUser);
            $destino='';            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());
                
                   
                if($Extension=="xls" or $Extension=="xlsx"){
                    $carpeta="../../../soportes/ts_eps_resoluciones_glosas/";
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
            
            $obCon->RegistreArchivo($db,$keyArchivo,$CmbIPS,$NombreArchivo,$destino,$Extension,$idUser);
            print("OK;Archivo Recibido");            
            
        break; //fin caso 2
             
        case 5://Insertar registros nuevos
            $NumeroContrato=$obCon->normalizar($_REQUEST["NumeroContrato"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $NumeroContrato=$obCon->getNumContract($NumeroContrato);
            $keyArchivo=$obCon->getKeyArchivo($NumeroContrato, $CmbIPS);
            
            $sql="UPDATE $db.temp_resoluciones_glosas_revision_contrato_glosa t1 INNER JOIN $db.resoluciones_glosas_revision_contrato_glosa t2 ON t1.NumeroFactura=t2.NumeroFactura SET t1.FlagUpdate=1  "
                    . "WHERE t1.NumeroRadicado=t2.NumeroRadicado AND t1.idUser='$idUser'";
                    
            $obCon->Query($sql);
            $sql="INSERT INTO $db.`resoluciones_glosas_revision_contrato_glosa`   
                   (`Nit_IPS`,`RazonSocial`,`NumeroContrato`,`NumeroRadicado`,`NumeroFactura`,`ValorGlosa`,`ValorGlosaAFavorAsmet`,`Soporte`,`idUser`,`FechaRegistro`) 
                   SELECT `Nit_IPS`,`RazonSocial`,`NumeroContrato`,`NumeroRadicado`,`NumeroFactura`,`ValorGlosa`,`ValorGlosaAFavorAsmet`,`Soporte`,`idUser`,`FechaRegistro`
                  FROM $db.`temp_resoluciones_glosas_revision_contrato_glosa` as t1 WHERE t1.FlagUpdate=0 AND t1.idUser='$idUser';
                    
                    ";
            //print($sql);
            
            $obCon->Query($sql);
            
            print("OK;Registros realizados correctamente");
        break; //fin caso 5
    
        case 6://Borrar temporales y registros mal hechos
            $NumeroContrato=$obCon->normalizar($_REQUEST["NumeroContrato"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $NumeroContrato=$obCon->getNumContract($NumeroContrato);
            $keyArchivo=$obCon->getKeyArchivo($NumeroContrato, $CmbIPS);
            
            $obCon->BorraReg("$db.resoluciones_glosas_control_envio_glosa", "NombreArchivo", $keyArchivo);
            print("OK;Temporales Borrados");
        break; //fin caso 5
    
        case 7://Guarda los archivos en la temporal
            
            $NumeroContrato=$obCon->normalizar($_REQUEST["NumeroContrato"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $NumeroContrato=$obCon->getNumContract($NumeroContrato);
            $keyArchivo=$obCon->getKeyArchivo($NumeroContrato, $CmbIPS);
                       
            $obCon->GuardeArchivoEnTemporal($db,$keyArchivo,$CmbIPS,$NumeroContrato,$idUser);
            
            
            print("OK;El archivo Se guardó en la tabla temporal correctamente");
        break; //fin caso 7
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>