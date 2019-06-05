<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/carteraips.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new CarteraIPS($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Verificar si ya se cargó
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraIPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="SELECT * FROM $db.controlcarguesips WHERE NombreCargue='$keyArchivo'";
            $Consulta=$obCon->Query($sql);
            $DatosCargue=$obCon->FetchAssoc($Consulta);
            if($DatosCargue["ID"]==''){
                print("OK;Registros realizados"); 
            }else{
                print("E1;Este archivo fue cargado el día $DatosCargue[FechaActualizacion] con un valor de: ". number_format($DatosCargue["ValorCargue"])."<br>Desea Actualizarlo?"); 
            }
                       
            
        break; //fin caso 1
        
        case 2: //Recibir el archivo
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraIPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            
            $destino='';
            
            $Extension="";
            if(!empty($_FILES['UpCartera']['name'])){
                
                $info = new SplFileInfo($_FILES['UpCartera']['name']);
                $Extension=($info->getExtension());                
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
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $obCon->VaciarTabla("$db.temporalcarguecarteraips");
            $keyArchivo=$obCon->getKeyCarteraIPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $obCon->LeerArchivo($keyArchivo,$FechaCorteCartera,$CmbIPS,$idUser);
            print("OK;Archivo cargado");
        break; //fin caso 3   
    
        case 4://Verificar si hay facturas repetidas y si las hay las reemplaza y las lleva al registro de actualizaciones
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraIPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            $sql="UPDATE $db.carteracargadaips cips INNER JOIN $db.temporalcarguecarteraips t ON cips.NumeroFactura=t.NumeroFactura SET cips.FlagUpdate=1  "
                    . "WHERE cips.ValorDocumento<>t.ValorDocumento or cips.ValorGlosaInicial<>t.ValorGlosaInicial or cips.ValorGlosaAceptada<>t.ValorGlosaAceptada or"
                    . " cips.ValorGlosaConciliada<>t.ValorGlosaConciliada or cips.ValorDescuentoBdua<>t.ValorDescuentoBdua or cips.ValorAnticipos<>t.ValorAnticipos"
                    . " or cips.ValorRetencion<>t.ValorRetencion or cips.ValorTotalpagar<>t.ValorTotalpagar or cips.FechaHasta<>t.FechaHasta"
                    . " or cips.NumeroCuentaGlobal<>t.NumeroCuentaGlobal or cips.NumeroRadicado<>t.NumeroRadicado or cips.FechaRadicado<>t.FechaRadicado"
                    . " or cips.TipoNegociacion<>t.TipoNegociacion or cips.NumeroContrato<>t.NumeroContrato or cips.DiasPactados<>t.DiasPactados"
                    . " or cips.TipoRegimen<>t.TipoRegimen ;";
            $obCon->Query($sql);
            $sql="INSERT INTO $db.`actualizacioncarteracargadaips` 
                    SELECT * FROM $db.`carteracargadaips` as t1 WHERE t1.FlagUpdate=1";
            $obCon->Query($sql);
            $sql="UPDATE $db.carteracargadaips SET FlagUpdate=0 WHERE FlagUpdate=1";
            $obCon->Query($sql);
            print("OK;Analisis de Actualizaciones de Facturas Completo");
        break; //fin caso 4  
    
        case 5://Insertar facturas nuevas y reemplzar existentes
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraIPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            
            $sql="REPLACE INTO $db.`carteracargadaips`  
                    SELECT * FROM $db.`temporalcarguecarteraips`; ";
            $obCon->Query($sql);
            
            print("OK;Registros realizados correctamente");
        break; //fin caso 5
    
        case 6://Borrar temporales y registros mal hechos
            $FechaCorteCartera=$obCon->normalizar($_REQUEST["FechaCorteCartera"]);
            $CmbIPS=$obCon->normalizar($_REQUEST["CmbIPS"]);
            $CmbEPS=$obCon->normalizar($_REQUEST["CmbEPS"]);
            $keyArchivo=$obCon->getKeyCarteraIPS($FechaCorteCartera, $CmbIPS, $CmbEPS);
            $DatosCargas=$obCon->DevuelveValores("ips", "NIT", $CmbIPS);
            $db=$DatosCargas["DataBase"];
            //$obCon->VaciarTabla("$db.temporalcarguecarteraips");
            $obCon->BorraReg("$db.controlcarguesips", "NombreCargue", $keyArchivo);
            print("OK;Temporales Borrados");
        break; //fin caso 5
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>